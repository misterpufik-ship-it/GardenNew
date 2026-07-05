/* global XLSX */

let CONFIG = null;
let DATA = null;
let ARCHIVE = { sessions: [], activeKey: null };
let ACTIVE_SESSION_KEY = null;
let currentFilter = 'all';
let configReady = false;

const MONTH_NAMES = ['', 'январь', 'февраль', 'март', 'апрель', 'май', 'июнь', 'июль', 'август', 'сентябрь', 'октябрь', 'ноябрь', 'декабрь'];

const DEFAULT_CONFIG = {
  entity: 'lounge',
  ignoreKopecks: true,
  paymentFilter: 'бн',
  lounge: {
    report: {
      sheetIndex: 0,
      dateCol: 1,
      categoryCol: 6,
      sumCol: 7,
      paymentCol: 8,
      commentCol: 9,
    },
    statement: {
      sheetIndex: 0,
      dateCol: 1,
      opNumCol: 2,
      counterpartyCol: 3,
      purposeCol: 6,
      debitCol: 10,
      creditCol: 11,
    },
  },
  vympel: {
    reportSheets: [0, 1],
    dateCol: 1,
    categoryCol: 6,
    sumCol: 7,
    paymentCol: 8,
    commentCol: 9,
    statement: {
      sheetIndex: 0,
      headerRow: 1,
      dateCol: 1,
      accountCol: 2,
      counterpartyCol: 3,
      debitCol: 4,
      creditCol: null,
      docNumCol: 5,
      purposeCol: 6,
    },
  },
};

const $ = (sel) => document.querySelector(sel);
const $$ = (sel) => document.querySelectorAll(sel);

function deepClone(obj) {
  return JSON.parse(JSON.stringify(obj));
}

function mergeConfig(stored) {
  const cfg = deepClone(DEFAULT_CONFIG);
  if (!stored || typeof stored !== 'object' || Array.isArray(stored)) return cfg;
  if (stored.entity) cfg.entity = stored.entity;
  if (stored.paymentFilter) cfg.paymentFilter = stored.paymentFilter;
  if (stored.ignoreKopecks != null) cfg.ignoreKopecks = stored.ignoreKopecks;
  if (stored.lounge) cfg.lounge = { ...cfg.lounge, ...stored.lounge, report: { ...cfg.lounge.report, ...(stored.lounge.report || {}) }, statement: { ...cfg.lounge.statement, ...(stored.lounge.statement || {}) } };
  if (stored.vympel) {
    cfg.vympel = { ...cfg.vympel, ...stored.vympel, statement: { ...cfg.vympel.statement, ...(stored.vympel.statement || {}) } };
    if (Array.isArray(stored.vympel.reportSheets)) cfg.vympel.reportSheets = stored.vympel.reportSheets;
  }
  return cfg;
}

async function readJsonResponse(res) {
  const text = await res.text();
  try {
    return JSON.parse(text);
  } catch (_) {
    throw new Error(res.ok ? 'Сервер вернул некорректный ответ' : `Ошибка сервера (${res.status})`);
  }
}

async function loadServerConfig() {
  const res = await fetch('api/get-config.php');
  if (res.ok) return readJsonResponse(res);

  const fallback = await fetch('config.json');
  if (fallback.ok) return readJsonResponse(fallback);

  console.warn('get-config.php', res.status, 'config.json', fallback.status);
  return null;
}

function ensureConfig() {
  if (!CONFIG || typeof CONFIG !== 'object' || Array.isArray(CONFIG)) {
    CONFIG = deepClone(DEFAULT_CONFIG);
  }
  return CONFIG;
}

function fmt(n) {
  if (n == null || n === '') return '—';
  return Number(n).toLocaleString('ru-RU', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function rub(n) {
  return Math.floor(Math.abs(Number(n) || 0));
}

function cellVal(ws, r, c) {
  if (!c || c < 1) return null;
  const cell = ws[XLSX.utils.encode_cell({ r, c: c - 1 })];
  return cell ? cell.v : null;
}

function parseExcelDate(v) {
  if (v == null || v === '') return null;
  if (v instanceof Date && !isNaN(v)) return v;
  if (typeof v === 'number') {
    const d = XLSX.SSF.parse_date_code(v);
    if (d) return new Date(d.y, d.m - 1, d.d);
  }
  const s = String(v).trim();
  const dm = s.match(/(\d{1,2})[.\-/](\d{1,2})[.\-/](\d{2,4})/);
  if (dm) {
    let y = +dm[3];
    if (y < 100) y += 2000;
    return new Date(y, +dm[2] - 1, +dm[1]);
  }
  const d = new Date(s);
  return isNaN(d) ? null : d;
}

function isoDate(d) {
  if (!d) return null;
  const y = d.getFullYear();
  const m = String(d.getMonth() + 1).padStart(2, '0');
  const day = String(d.getDate()).padStart(2, '0');
  return `${y}-${m}-${day}`;
}

function monthLabel(ym) {
  if (!ym) return '—';
  const [y, m] = ym.split('-');
  return `${MONTH_NAMES[parseInt(m, 10)] || m} ${y}`;
}

function sessionLabel(s) {
  const ent = s.entity === 'vympel' ? 'Вымпел' : 'Лаундж';
  return `${monthLabel(s.month)} · ${ent}`;
}

function guessMonth(expenses, statements) {
  const dates = [...(expenses || []), ...(statements || [])].map((x) => x.date).filter(Boolean).sort();
  if (!dates.length) return new Date().toISOString().slice(0, 7);
  return dates[Math.floor(dates.length / 2)].slice(0, 7);
}

function isReportSummaryRow(category, dateCell) {
  const t = `${category || ''} ${dateCell || ''}`.toLowerCase();
  return /итого|оборот|выручка|на начало|на конец|всего расход|итого расход|сальдо/.test(t);
}

function stmtDebitAmount(row) {
  return row.debit != null ? row.debit : row.amount;
}

function detectStatementColumns(ws) {
  const range = XLSX.utils.decode_range(ws['!ref'] || 'A1');
  for (let r = range.s.r; r <= Math.min(range.e.r, range.s.r + 30); r++) {
    let debitCol = null;
    let creditCol = null;
    let opNumCol = null;
    let counterpartyCol = null;
    let purposeCol = null;
    for (let c = 1; c <= Math.min(15, range.e.col + 1); c++) {
      const v = String(cellVal(ws, r, c) || '').trim().toLowerCase();
      if (v === 'дебет') debitCol = c;
      if (v === 'кредит') creditCol = c;
      if (v === 'номер') opNumCol = c;
      if (v.includes('контрагент') && !counterpartyCol) counterpartyCol = c;
      if (v.includes('назначение')) purposeCol = c;
    }
    if (debitCol && creditCol) {
      return { headerRow: r, debitCol, creditCol, opNumCol, counterpartyCol, purposeCol };
    }
  }
  return null;
}

function toNum(v) {
  if (v == null || v === '') return null;
  const n = parseFloat(String(v).replace(/\s/g, '').replace(',', '.'));
  return isNaN(n) ? null : Math.round(n * 100) / 100;
}

function isBnPayment(v, filter) {
  return String(v || '').trim().toLowerCase() === String(filter || 'бн').toLowerCase();
}

function readReportLounge(wb, cfg, paymentFilter) {
  const sheetCfg = cfg.lounge.report;
  const name = wb.SheetNames[sheetCfg.sheetIndex] || wb.SheetNames[0];
  const ws = wb.Sheets[name];
  if (!ws) return [];
  const range = XLSX.utils.decode_range(ws['!ref'] || 'A1');
  const out = [];
  let currentDate = null;
  let id = 0;

  for (let r = range.s.r; r <= range.e.r; r++) {
    const aVal = cellVal(ws, r, sheetCfg.dateCol);
    const maybeDate = parseExcelDate(aVal);
    if (maybeDate) currentDate = maybeDate;

    const sum = toNum(cellVal(ws, r, sheetCfg.sumCol));
    const payment = cellVal(ws, r, sheetCfg.paymentCol);
    const category = String(cellVal(ws, r, sheetCfg.categoryCol) || '').trim();
    const dateCell = cellVal(ws, r, sheetCfg.dateCol);
    if (!isBnPayment(payment, paymentFilter) || sum == null) continue;
    if (isReportSummaryRow(category, dateCell)) continue;

    out.push({
      id: 'e' + (++id),
      date: isoDate(currentDate),
      sheet: name,
      category,
      amount: sum,
      comment: String(cellVal(ws, r, sheetCfg.commentCol) || '').trim(),
      status: 'unmatched',
      groupNum: null,
      matchId: null,
    });
  }
  return out;
}

function readReportVympel(wb, cfg, paymentFilter) {
  const sheetCfg = cfg.vympel;
  const out = [];
  let id = 0;

  for (const sheetIndex of sheetCfg.reportSheets) {
    const name = wb.SheetNames[sheetIndex];
    if (!name) continue;
    const ws = wb.Sheets[name];
    const range = XLSX.utils.decode_range(ws['!ref'] || 'A1');
    let currentDate = null;

    for (let r = range.s.r; r <= range.e.r; r++) {
      const aVal = cellVal(ws, r, sheetCfg.dateCol);
      const maybeDate = parseExcelDate(aVal);
      if (maybeDate) currentDate = maybeDate;

      const sum = toNum(cellVal(ws, r, sheetCfg.sumCol));
      const payment = cellVal(ws, r, sheetCfg.paymentCol);
      const category = String(cellVal(ws, r, sheetCfg.categoryCol) || '').trim();
      const dateCell = cellVal(ws, r, sheetCfg.dateCol);
      if (!isBnPayment(payment, paymentFilter) || sum == null) continue;
      if (isReportSummaryRow(category, dateCell)) continue;

      out.push({
        id: 'e' + (++id),
        date: isoDate(currentDate),
        sheet: name,
        category,
        amount: sum,
        comment: String(cellVal(ws, r, sheetCfg.commentCol) || '').trim(),
        status: 'unmatched',
        groupNum: null,
        matchId: null,
      });
    }
  }
  return out;
}

function isStatementSummaryRow(purpose, counterparty, opNum) {
  const t = `${purpose || ''} ${counterparty || ''} ${opNum || ''}`.toLowerCase();
  return /итого|оборот|на конец дня|на начало|сальдо|остаток/.test(t);
}

function readStatementTable(ws, table, sheetCfg) {
  const range = XLSX.utils.decode_range(ws['!ref'] || 'A1');
  const out = [];
  let id = 0;
  const debitCol = table.debitCol || sheetCfg.debitCol;
  const creditCol = table.creditCol || sheetCfg.creditCol;

  for (let r = table.headerRow + 1; r <= range.e.r; r++) {
    const debit = toNum(cellVal(ws, r, debitCol));
    const credit = creditCol ? toNum(cellVal(ws, r, creditCol)) : null;
    if ((debit == null || debit <= 0) && (credit == null || credit <= 0)) continue;

    const purpose = String(cellVal(ws, r, table.purposeCol || sheetCfg.purposeCol) || '').trim();
    const counterparty = String(cellVal(ws, r, table.counterpartyCol || sheetCfg.counterpartyCol) || '').trim();
    const opNum = String(cellVal(ws, r, table.opNumCol || sheetCfg.opNumCol) || '').trim();
    if (isStatementSummaryRow(purpose, counterparty, opNum)) continue;

    let d = parseExcelDate(cellVal(ws, r, sheetCfg.dateCol));
    if (!d) d = parseExcelDate(cellVal(ws, r, 1));

    out.push({
      id: 's' + (++id),
      date: isoDate(d),
      opNum,
      counterparty,
      purpose,
      debit: debit || 0,
      credit: credit || 0,
      amount: debit || 0,
      status: 'unmatched',
      groupNum: null,
      matchId: null,
    });
  }
  return out;
}

function readStatementLounge(wb, cfg) {
  const sheetCfg = cfg.lounge.statement;
  const name = wb.SheetNames[sheetCfg.sheetIndex] || wb.SheetNames[0];
  const ws = wb.Sheets[name];
  if (!ws) return [];

  const table = detectStatementColumns(ws);
  if (table) return readStatementTable(ws, table, sheetCfg);

  const range = XLSX.utils.decode_range(ws['!ref'] || 'A1');
  const out = [];
  let currentDate = null;
  let id = 0;
  const creditCol = sheetCfg.creditCol;

  for (let r = range.s.r; r <= range.e.r; r++) {
    for (let c = 1; c <= 3; c++) {
      const raw = cellVal(ws, r, c);
      if (raw == null) continue;
      const text = String(raw);
      const dm = text.match(/Дата:\s*(\d{1,2}[.\-/]\d{1,2}[.\-/]\d{2,4})/i);
      if (dm) {
        currentDate = parseExcelDate(dm[1]);
        break;
      }
      const d = parseExcelDate(raw);
      if (d && text.length < 20) {
        currentDate = d;
        break;
      }
    }

    const purpose = String(cellVal(ws, r, sheetCfg.purposeCol) || '').trim();
    const counterparty = String(cellVal(ws, r, sheetCfg.counterpartyCol) || '').trim();
    const opNum = String(cellVal(ws, r, sheetCfg.opNumCol) || '').trim();
    const debit = toNum(cellVal(ws, r, sheetCfg.debitCol));
    const credit = creditCol ? toNum(cellVal(ws, r, creditCol)) : null;
    if ((debit == null || debit <= 0) && (credit == null || credit <= 0)) continue;
    if (isStatementSummaryRow(purpose, counterparty, opNum)) continue;

    out.push({
      id: 's' + (++id),
      date: isoDate(currentDate),
      opNum,
      counterparty,
      purpose,
      debit: debit || 0,
      credit: credit || 0,
      amount: debit || 0,
      status: 'unmatched',
      groupNum: null,
      matchId: null,
    });
  }
  return out;
}

function readStatementVympel(wb, cfg) {
  const sheetCfg = cfg.vympel.statement;
  const name = wb.SheetNames[sheetCfg.sheetIndex] || wb.SheetNames[0];
  const ws = wb.Sheets[name];
  if (!ws) return [];

  const table = detectStatementColumns(ws);
  if (table) return readStatementTable(ws, table, sheetCfg);

  const range = XLSX.utils.decode_range(ws['!ref'] || 'A1');
  const out = [];
  let id = 0;
  const startRow = (sheetCfg.headerRow || 1) - 1;
  const creditCol = sheetCfg.creditCol;

  for (let r = Math.max(range.s.r, startRow); r <= range.e.r; r++) {
    const purpose = String(cellVal(ws, r, sheetCfg.purposeCol) || '').trim();
    const counterparty = String(cellVal(ws, r, sheetCfg.counterpartyCol) || '').trim();
    const opNum = String(cellVal(ws, r, sheetCfg.docNumCol) || '').trim();
    const debit = toNum(cellVal(ws, r, sheetCfg.debitCol));
    const credit = creditCol ? toNum(cellVal(ws, r, creditCol)) : null;
    if ((debit == null || debit <= 0) && (credit == null || credit <= 0)) continue;
    if (isStatementSummaryRow(purpose, counterparty, opNum)) continue;

    const dateRaw = cellVal(ws, r, sheetCfg.dateCol);
    const d = parseExcelDate(dateRaw);

    out.push({
      id: 's' + (++id),
      date: isoDate(d),
      opNum,
      counterparty,
      purpose,
      debit: debit || 0,
      credit: credit || 0,
      amount: debit || 0,
      status: 'unmatched',
      groupNum: null,
      matchId: null,
    });
  }
  return out;
}

function findSubset(items, target, minLen) {
  const arr = items.slice();
  let result = null;

  function dfs(idx, sum, picked) {
    if (result) return;
    if (sum === target && picked.length >= minLen) {
      result = picked.slice();
      return;
    }
    if (sum > target || idx >= arr.length) return;
    dfs(idx + 1, sum, picked);
    picked.push(arr[idx]);
    dfs(idx + 1, sum + rub(arr[idx].amount), picked);
    picked.pop();
  }

  dfs(0, 0, []);
  return result;
}

function reconcile(expenses, statements) {
  const usedExp = new Set();
  const usedStmt = new Set();
  const matches = [];
  let groupNum = 1;

  const expSorted = [...expenses].sort((a, b) => (a.date || '').localeCompare(b.date || ''));

  for (const exp of expSorted) {
    if (usedExp.has(exp.id)) continue;
    const candidates = statements
      .filter((s) => !usedStmt.has(s.id) && stmtDebitAmount(s) > 0 && rub(stmtDebitAmount(s)) === rub(exp.amount))
      .sort((a, b) => {
        const as = a.date === exp.date ? 0 : 1;
        const bs = b.date === exp.date ? 0 : 1;
        return as - bs;
      });
    if (!candidates.length) continue;

    const stmt = candidates[0];
    const matchId = 'm' + (matches.length + 1);
    usedExp.add(exp.id);
    usedStmt.add(stmt.id);
    exp.status = 'exact';
    stmt.status = 'exact';
    exp.matchId = matchId;
    stmt.matchId = matchId;
    matches.push({ id: matchId, type: 'exact', groupNum: null, expenseIds: [exp.id], statementId: stmt.id });
  }

  const stmtByDate = {};
  for (const stmt of statements) {
    if (usedStmt.has(stmt.id)) continue;
    if (stmtDebitAmount(stmt) <= 0) continue;
    const key = stmt.date || '_';
    if (!stmtByDate[key]) stmtByDate[key] = [];
    stmtByDate[key].push(stmt);
  }

  for (const [date, stmts] of Object.entries(stmtByDate)) {
    for (const stmt of stmts) {
      if (usedStmt.has(stmt.id)) continue;
      const dayExp = expenses.filter((e) => !usedExp.has(e.id) && (e.date || '_') === date);
      if (dayExp.length < 2) continue;

      const combo = findSubset(dayExp, rub(stmtDebitAmount(stmt)), 2);
      if (!combo) continue;

      const matchId = 'm' + (matches.length + 1);
      const g = groupNum++;
      for (const exp of combo) {
        usedExp.add(exp.id);
        exp.status = 'group';
        exp.groupNum = g;
        exp.matchId = matchId;
      }
      usedStmt.add(stmt.id);
      stmt.status = 'group';
      stmt.groupNum = g;
      stmt.matchId = matchId;
      matches.push({
        id: matchId,
        type: 'group',
        groupNum: g,
        expenseIds: combo.map((e) => e.id),
        statementId: stmt.id,
      });
    }
  }

  const stats = {
    exact: matches.filter((m) => m.type === 'exact').length,
    group: matches.filter((m) => m.type === 'group').length,
    unmatchedExp: expenses.filter((e) => e.status === 'unmatched').length,
    unmatchedStmt: statements.filter((s) => s.status === 'unmatched' && stmtDebitAmount(s) > 0).length,
  };

  return { expenses, statements, matches, stats };
}

function renderConfigForm() {
  ensureConfig();
  const entity = $('#entity').value;
  const box = $('#configForm');
  const fields = entity === 'lounge'
    ? [
      ['Отчёт: лист', 'lounge.report.sheetIndex'],
      ['Отчёт: дата col', 'lounge.report.dateCol'],
      ['Отчёт: категория col', 'lounge.report.categoryCol'],
      ['Отчёт: сумма col', 'lounge.report.sumCol'],
      ['Отчёт: оплата col', 'lounge.report.paymentCol'],
      ['Отчёт: комментарий col', 'lounge.report.commentCol'],
      ['Выписка: лист', 'lounge.statement.sheetIndex'],
      ['Выписка: дата col', 'lounge.statement.dateCol'],
      ['Выписка: № col', 'lounge.statement.opNumCol'],
      ['Выписка: контрагент col', 'lounge.statement.counterpartyCol'],
      ['Выписка: назначение col', 'lounge.statement.purposeCol'],
      ['Выписка: дебет col', 'lounge.statement.debitCol'],
      ['Выписка: кредит col', 'lounge.statement.creditCol'],
    ]
    : [
      ['Отчёт: лист 1', 'vympel.reportSheets.0'],
      ['Отчёт: лист 2', 'vympel.reportSheets.1'],
      ['Отчёт: дата col', 'vympel.dateCol'],
      ['Отчёт: категория col', 'vympel.categoryCol'],
      ['Отчёт: сумма col', 'vympel.sumCol'],
      ['Отчёт: оплата col', 'vympel.paymentCol'],
      ['Выписка: лист', 'vympel.statement.sheetIndex'],
      ['Выписка: строка заголовка', 'vympel.statement.headerRow'],
      ['Выписка: дата col', 'vympel.statement.dateCol'],
      ['Выписка: дебет col', 'vympel.statement.debitCol'],
      ['Выписка: контрагент col', 'vympel.statement.counterpartyCol'],
      ['Выписка: назначение col', 'vympel.statement.purposeCol'],
    ];

  box.innerHTML = fields.map(([label, key]) =>
    `<div class="row"><span>${label}</span><input type="number" data-cfg="${key}" min="0"></div>`
  ).join('');

  fields.forEach(([_, key]) => {
    const input = box.querySelector(`[data-cfg="${key}"]`);
    if (!input) return;
    const parts = key.split('.');
    let val = CONFIG;
    for (const p of parts) val = val?.[p];
    input.value = val ?? 0;
  });
}

function collectConfigFromForm() {
  const cfg = deepClone(ensureConfig());
  $$('#configForm [data-cfg]').forEach((input) => {
    const parts = input.dataset.cfg.split('.');
    let ref = cfg;
    for (let i = 0; i < parts.length - 1; i++) {
      const p = parts[i];
      if (ref[p] == null) ref[p] = /^\d+$/.test(parts[i + 1]) ? [] : {};
      ref = ref[p];
    }
    const last = parts[parts.length - 1];
    ref[last] = parseInt(input.value, 10) || 0;
  });
  cfg.entity = $('#entity').value;
  return cfg;
}

function statusBadge(status) {
  const labels = { exact: 'Точное', group: 'Группа', unmatched: 'Нет пары' };
  return `<span class="badge ${status}">${labels[status] || status}</span>`;
}

function passFilter(row) {
  const s = typeof row === 'string' ? row : row.status;
  if (currentFilter === 'all') return true;
  if (currentFilter === 'unmatched') return s === 'unmatched' || s === 'unmatched_stmt';
  if (currentFilter === 'unmatched_exp') return s === 'unmatched';
  if (currentFilter === 'unmatched_stmt') return s === 'unmatched_stmt';
  return s === currentFilter;
}

function buildPairRows(expenses, statements, matches) {
  const expMap = Object.fromEntries(expenses.map((e) => [e.id, e]));
  const stmtMap = Object.fromEntries(statements.map((s) => [s.id, s]));
  const rows = [];
  let linkNum = 0;

  for (const m of matches) {
    linkNum += 1;
    const stmt = stmtMap[m.statementId];
    m.expenseIds.forEach((eid, i) => {
      const exp = expMap[eid];
      rows.push({
        linkNum: i === 0 ? linkNum : null,
        matchId: m.id,
        status: m.type,
        groupNum: m.groupNum,
        matchType: m.type === 'exact' ? 'Точное' : 'Группа',
        groupSize: m.expenseIds.length,
        isFirstInGroup: i === 0,
        expDate: exp.date,
        category: exp.category,
        reportAmount: exp.amount,
        comment: exp.comment,
        sheet: exp.sheet,
        stmtDate: i === 0 ? stmt.date : null,
        opNum: i === 0 ? stmt.opNum : null,
        counterparty: i === 0 ? stmt.counterparty : null,
        statementAmount: i === 0 ? stmtDebitAmount(stmt) : null,
        statementCredit: i === 0 ? (stmt.credit || 0) : null,
        purpose: i === 0 ? stmt.purpose : null,
      });
    });
  }

  for (const exp of expenses) {
    if (exp.status !== 'unmatched') continue;
    linkNum += 1;
    rows.push({
      linkNum,
      matchId: null,
      status: 'unmatched',
      groupNum: null,
      matchType: 'Не найдено',
      groupSize: 1,
      isFirstInGroup: true,
      expDate: exp.date,
      category: exp.category,
      reportAmount: exp.amount,
      comment: exp.comment,
      sheet: exp.sheet,
      stmtDate: null,
      opNum: null,
      counterparty: null,
      statementAmount: null,
      statementCredit: null,
      purpose: null,
    });
  }

  for (const stmt of statements) {
    if (stmt.status !== 'unmatched') continue;
    if (stmtDebitAmount(stmt) <= 0) continue;
    linkNum += 1;
    rows.push({
      linkNum,
      matchId: null,
      status: 'unmatched_stmt',
      groupNum: null,
      matchType: 'Не найдено',
      groupSize: 1,
      isFirstInGroup: true,
      expDate: null,
      category: null,
      reportAmount: null,
      comment: null,
      sheet: null,
      stmtDate: stmt.date,
      opNum: stmt.opNum,
      counterparty: stmt.counterparty,
      statementAmount: stmtDebitAmount(stmt),
      statementCredit: stmt.credit || 0,
      purpose: stmt.purpose,
    });
  }

  rows.sort((a, b) => {
    const da = a.expDate || a.stmtDate || '';
    const db = b.expDate || b.stmtDate || '';
    return da.localeCompare(db) || (a.linkNum || 9999) - (b.linkNum || 9999);
  });
  return rows;
}

function amountPairCell(reportAmount, statementAmount) {
  const ra = reportAmount != null ? reportAmount : null;
  const sa = statementAmount != null ? statementAmount : null;
  let cls = 'cell-missing';
  let diff = '';
  if (ra != null && sa != null) {
    cls = rub(ra) === rub(sa) ? 'cell-match' : 'cell-mismatch';
    const d = Math.round((sa - ra) * 100) / 100;
    if (d !== 0) diff = `<span class="diff">Δ ${fmt(d)}</span>`;
  } else if (ra != null || sa != null) {
    cls = 'cell-mismatch';
  }
  return `<td class="${cls}">
    <div class="val-pair">
      <span class="side-a">Отчёт: ${fmt(ra)}</span>
      <span class="side-b">Выписка: ${fmt(sa)}</span>
      ${diff}
    </div>
  </td>`;
}

function renderPairsTable() {
  if (!DATA || !DATA.pairRows) return;
  const filtered = DATA.pairRows.filter(passFilter);

  $('#pairsTable tbody').innerHTML = filtered.map((r) => {
    const rowCls = `row-${r.status === 'unmatched_stmt' ? 'unmatched' : r.status}`;
    const badge = statusBadge(r.status === 'unmatched_stmt' ? 'unmatched' : r.status);

    if (r.status === 'unmatched_stmt') {
      return `<tr class="${rowCls}">
        <td>—</td><td>—</td>${amountPairCell(null, r.statementAmount)}<td>—</td><td>—</td>
        <td class="link-cell">${r.linkNum}</td>
        <td>${r.stmtDate || '—'}</td><td>${r.opNum || '—'}</td><td>${r.counterparty || '—'}</td>
        <td class="cell-mismatch">${fmt(r.statementAmount)}</td>
        <td>${r.statementCredit ? fmt(r.statementCredit) : '—'}</td>
        <td>${r.purpose || '—'}</td>
        <td>${badge}</td><td>—</td>
      </tr>`;
    }

    if (r.status === 'group' && !r.isFirstInGroup) {
      return `<tr class="${rowCls}">
        <td>${r.expDate || '—'}</td><td>${r.category || '—'}</td>${amountPairCell(r.reportAmount, null)}
        <td>${r.comment || '—'}</td><td>${r.sheet || '—'}</td>
        <td class="link-cell sub">↳</td>
        <td colspan="6" class="cell-missing" style="font-size:0.75rem">↳ часть группы ${r.groupNum || ''}</td>
        <td>${badge}</td><td>${r.groupNum || '—'}</td>
      </tr>`;
    }

    const stmtPart = r.statementAmount != null
      ? `<td>${r.stmtDate || '—'}</td><td>${r.opNum || '—'}</td><td>${r.counterparty || '—'}</td>
         <td class="${rub(r.reportAmount) === rub(r.statementAmount) ? 'cell-match' : 'cell-mismatch'}">${fmt(r.statementAmount)}</td>
         <td>${r.statementCredit ? fmt(r.statementCredit) : '—'}</td>
         <td>${r.purpose || '—'}</td>`
      : `<td>—</td><td>—</td><td>—</td><td class="cell-missing">—</td><td>—</td><td>—</td>`;

    const linkLabel = `${r.linkNum}${r.groupNum ? `<div class="link-cell sub">гр.${r.groupNum}</div>` : ''}`;

    return `<tr class="${rowCls}">
      <td>${r.expDate || '—'}</td><td>${r.category || '—'}</td>${amountPairCell(r.reportAmount, r.statementAmount)}
      <td>${r.comment || '—'}</td><td>${r.sheet || '—'}</td>
      <td class="link-cell">${linkLabel}</td>
      ${stmtPart}
      <td>${badge}</td><td>${r.groupNum || '—'}</td>
    </tr>`;
  }).join('');
}

/** @deprecated kept as alias */
function buildDetailRows(expenses, statements, matches) {
  return buildPairRows(expenses, statements, matches);
}

function renderSummary() {
  if (!DATA) return;
  const s = DATA.stats;
  const entityLabel = DATA.entity === 'vympel' ? 'Вымпел' : 'Лаундж';
  const monthPart = DATA.month ? ` · ${monthLabel(DATA.month)}` : '';
  $('#subtitle').textContent =
    `${entityLabel}${monthPart} · Расходов: ${DATA.expenses.length} · Операций: ${DATA.statements.length}` +
    (DATA.reportFile ? ` · ${DATA.reportFile}` : '');

  $('#summary').innerHTML = `
    <div class="card accent"><div class="num">${DATA.expenses.length}</div><div class="lbl">Расходов БН</div></div>
    <div class="card green"><div class="num">${s.exact}</div><div class="lbl">Точных</div></div>
    <div class="card yellow"><div class="num">${s.group}</div><div class="lbl">Групповых</div></div>
    <div class="card orange"><div class="num">${s.unmatchedExp}</div><div class="lbl">Расход без пары</div></div>
    <div class="card orange"><div class="num">${s.unmatchedStmt}</div><div class="lbl">Выписка без пары</div></div>
  `;
}

function renderTables() {
  if (!DATA) return;

  $('#expTable tbody').innerHTML = DATA.expenses
    .filter((e) => passFilter(e.status))
    .map((e) => `<tr class="row-${e.status}">
      <td>${e.date || '—'}</td>
      <td>${e.sheet || '—'}</td>
      <td>${e.category || '—'}</td>
      <td>${fmt(e.amount)}</td>
      <td>${e.comment || '—'}</td>
      <td>${statusBadge(e.status)}</td>
      <td>${e.groupNum || '—'}</td>
    </tr>`).join('');

  $('#stmtTable tbody').innerHTML = DATA.statements
    .filter((s) => passFilter(s.status))
    .map((s) => `<tr class="row-${s.status}">
      <td>${s.date || '—'}</td>
      <td>${s.opNum || '—'}</td>
      <td>${s.counterparty || '—'}</td>
      <td>${s.purpose || '—'}</td>
      <td>${stmtDebitAmount(s) > 0 ? fmt(stmtDebitAmount(s)) : '—'}</td>
      <td>${s.credit ? fmt(s.credit) : '—'}</td>
      <td>${statusBadge(s.status)}</td>
      <td>${s.groupNum || '—'}</td>
    </tr>`).join('');
}

function renderAll() {
  renderSummary();
  renderPairsTable();
  renderTables();
  updateDownloadBtn();
}

function statusExportLabel(s) {
  const map = {
    exact: 'Точное',
    group: 'Группа',
    unmatched: 'Без пары',
    unmatched_stmt: 'Выписка без расхода',
  };
  return map[s] || s;
}

async function downloadExcel() {
  if (!DATA || !DATA.expenses?.length) return;

  if (!DATA.pairRows) {
    DATA.pairRows = buildPairRows(DATA.expenses, DATA.statements, DATA.matches || []);
  }

  const entityLabel = DATA.entity === 'vympel' ? 'Вымпел' : 'Лаундж';
  const pairHeaders = [
    '№ связки', 'Статус', 'Тип', 'Группа', 'Дата отчёт', 'Категория', 'Сумма отчёт',
    'Комментарий', 'Лист', 'Дата выписки', '№ операции', 'Контрагент', 'Дебет', 'Кредит', 'Назначение',
  ];
  const pairRows = DATA.pairRows.map((r) => [
    r.linkNum ?? '',
    statusExportLabel(r.status),
    r.matchType ?? '',
    r.groupNum ?? '',
    r.expDate ?? '',
    r.category ?? '',
    r.reportAmount ?? '',
    r.comment ?? '',
    r.sheet ?? '',
    r.stmtDate ?? '',
    r.opNum ?? '',
    r.counterparty ?? '',
    r.statementAmount ?? '',
    r.statementCredit ?? '',
    r.purpose ?? '',
  ]);

  const expHeaders = ['Дата', 'Лист', 'Категория', 'Сумма', 'Комментарий', 'Статус', 'Группа'];
  const expRows = DATA.expenses.map((e) => [
    e.date ?? '', e.sheet ?? '', e.category ?? '', e.amount ?? '', e.comment ?? '',
    statusExportLabel(e.status), e.groupNum ?? '',
  ]);

  const stmtHeaders = ['Дата', '№', 'Контрагент', 'Назначение', 'Дебет', 'Кредит', 'Статус', 'Группа'];
  const stmtRows = DATA.statements.map((s) => [
    s.date ?? '', s.opNum ?? '', s.counterparty ?? '', s.purpose ?? '',
    stmtDebitAmount(s) || '', s.credit || '',
    statusExportLabel(s.status), s.groupNum ?? '',
  ]);

  const entity = DATA.entity === 'vympel' ? 'vympel' : 'lounge';
  await CrmExport.download(`sverka-rs-${entity}-${new Date().toISOString().slice(0, 10)}.xlsx`, [
    {
      name: 'Сверка',
      title: 'Сверка расходов БН с выпиской',
      subtitle: `${entityLabel} · ${DATA.reportFile || 'без файла отчёта'}`,
      headers: pairHeaders,
      rows: pairRows,
      rowStatus: (_, i) => DATA.pairRows[i].status,
      numberCols: [6, 12, 13],
      wrapCols: [7, 14],
      colWidths: [10, 16, 12, 8, 12, 20, 14, 32, 14, 12, 10, 24, 14, 14, 40],
    },
    {
      name: 'Расходы',
      title: 'Расходы отчёта (БН)',
      headers: expHeaders,
      rows: expRows,
      rowStatus: (_, i) => DATA.expenses[i].status,
      numberCols: [3],
      wrapCols: [4],
    },
    {
      name: 'Выписка',
      title: 'Дебетовые операции выписки',
      headers: stmtHeaders,
      rows: stmtRows,
      rowStatus: (_, i) => DATA.statements[i].status,
      numberCols: [4, 5],
      wrapCols: [3],
    },
  ]);
}

function updateDownloadBtn() {
  const btn = $('#downloadExcel');
  if (btn) btn.disabled = !(DATA && DATA.expenses && DATA.expenses.length);
}

async function loadArchive() {
  try {
    const res = await fetch('api/archive.php');
    if (!res.ok) return;
    const out = await res.json();
    ARCHIVE = { sessions: out.sessions || [], activeKey: out.activeKey || null };
  } catch (e) {
    console.warn('archive', e);
  }
}

function renderSessionTabs() {
  const el = $('#sessionTabs');
  if (!el) return;
  if (!ARCHIVE.sessions.length) {
    el.innerHTML = '<span class="session-tabs-empty">Нет сохранённых сверок — загрузите файлы и нажмите «Сверить»</span>';
    return;
  }
  el.innerHTML = ARCHIVE.sessions.map((s) => {
    const active = s.key === ACTIVE_SESSION_KEY ? ' active' : '';
    const stats = s.stats ? ` · ${(s.stats.exact || 0) + (s.stats.group || 0)}/${(s.stats.exact || 0) + (s.stats.group || 0) + (s.stats.unmatchedExp || 0)}` : '';
    return `<button type="button" class="session-tab${active}" data-key="${s.key}" title="${s.reportFile || ''}">${sessionLabel(s)}${stats}</button>`;
  }).join('');
}

async function loadSession(key) {
  const msg = $('#actionMsg');
  try {
    const res = await fetch('api/archive.php?key=' + encodeURIComponent(key));
    if (!res.ok) throw new Error('Сессия не найдена');
    const out = await res.json();
    DATA = out.session;
    ACTIVE_SESSION_KEY = key;
    if (!DATA.pairRows) {
      DATA.pairRows = buildPairRows(DATA.expenses, DATA.statements, DATA.matches || []);
    }
    $('#entity').value = DATA.entity || 'lounge';
    const monthEl = $('#sessionMonth');
    if (monthEl) monthEl.value = DATA.month || key.split('|')[0];
    renderSessionTabs();
    renderAll();
    msg.textContent = 'Загружена сверка: ' + sessionLabel({ month: DATA.month, entity: DATA.entity });
    msg.className = 'msg ok';
  } catch (e) {
    msg.textContent = e.message;
    msg.className = 'msg err';
  }
}

async function loadInstructions() {
  const ta = $('#instructionsText');
  if (!ta) return;
  try {
    const res = await fetch('api/instructions.php');
    const out = await res.json();
    if (out.ok) ta.value = out.text || '';
  } catch (e) {
    console.warn('instructions', e);
  }
}

async function saveInstructions() {
  const ta = $('#instructionsText');
  const msg = $('#instructionsMsg');
  if (!ta) return;
  try {
    const res = await fetch('api/save-instructions.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ text: ta.value }),
    });
    const out = await readJsonResponse(res);
    msg.textContent = out.ok ? 'Инструкция сохранена на сервере' : (out.error || 'Ошибка');
    msg.className = 'msg ' + (out.ok ? 'ok' : 'err');
  } catch (e) {
    msg.textContent = e.message || 'Сервер недоступен';
    msg.className = 'msg err';
  }
}

function applySessionData(saved) {
  if (!saved || !saved.expenses || !saved.expenses.length) return false;
  DATA = saved;
  ACTIVE_SESSION_KEY = saved.sessionKey || saved.month + '|' + (saved.entity || 'lounge');
  if (!DATA.pairRows) {
    DATA.pairRows = buildPairRows(DATA.expenses, DATA.statements, DATA.matches || []);
  }
  $('#entity').value = DATA.entity || 'lounge';
  const monthEl = $('#sessionMonth');
  if (monthEl) monthEl.value = DATA.month || ACTIVE_SESSION_KEY.split('|')[0];
  renderSessionTabs();
  renderAll();
  return true;
}

async function loadInitial() {
  const msg = $('#actionMsg');
  try {
    const [serverCfg, archiveRes] = await Promise.all([
      loadServerConfig(),
      fetch('api/archive.php'),
    ]);

    CONFIG = mergeConfig(serverCfg);
    configReady = true;

    if (archiveRes.ok) {
      const arch = await readJsonResponse(archiveRes);
      ARCHIVE = { sessions: arch.sessions || [], activeKey: arch.activeKey || null };
    }
    renderSessionTabs();

    $('#entity').value = CONFIG.entity || 'lounge';
    renderConfigForm();
    $('#runBtn').disabled = false;

    await loadInstructions();

    if (ARCHIVE.activeKey) {
      await loadSession(ARCHIVE.activeKey);
    } else {
      $('#subtitle').textContent = 'Загрузите отчёт и выписку для сверки';
    }
  } catch (err) {
    console.error(err);
    CONFIG = deepClone(DEFAULT_CONFIG);
    configReady = true;
    $('#entity').value = CONFIG.entity;
    renderConfigForm();
    $('#runBtn').disabled = false;
    $('#subtitle').textContent = 'Загрузите отчёт и выписку для сверки';
    msg.textContent = err.message || 'Не удалось загрузить данные с сервера';
    msg.className = 'msg err';
    await loadInstructions();
  }
}

function readFileAsArrayBuffer(file) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onload = (e) => resolve(e.target.result);
    reader.onerror = reject;
    reader.readAsArrayBuffer(file);
  });
}

async function uploadSourceFiles(entity, reportFile, statementFile) {
  const fd = new FormData();
  fd.append('entity', entity);
  fd.append('report', reportFile);
  fd.append('statement', statementFile);
  const res = await fetch('api/upload-files.php', { method: 'POST', body: fd });
  const out = await res.json().catch(() => ({}));
  if (!res.ok || !out.ok) throw new Error(out.error || 'Не удалось сохранить файлы на сервере');
  return out.files;
}

async function runReconciliation() {
  const msg = $('#actionMsg');
  const reportInput = $('#reportFile');
  const stmtInput = $('#statementFile');

  if (!configReady) {
    msg.textContent = 'Подождите загрузку настроек…';
    msg.className = 'msg err';
    return;
  }

  if (!reportInput.files[0] || !stmtInput.files[0]) {
    msg.textContent = 'Выберите оба файла: отчёт и выписку';
    msg.className = 'msg err';
    return;
  }

  try {
    $('#runBtn').disabled = true;
    msg.textContent = 'Сверка и сохранение на сервер…';
    msg.className = 'msg';

    CONFIG = collectConfigFromForm();
    const entity = CONFIG.entity;
    const [reportBuf, stmtBuf, uploaded] = await Promise.all([
      readFileAsArrayBuffer(reportInput.files[0]),
      readFileAsArrayBuffer(stmtInput.files[0]),
      uploadSourceFiles(entity, reportInput.files[0], stmtInput.files[0]),
    ]);

    const reportWb = XLSX.read(reportBuf, { type: 'array', cellDates: true });
    const stmtWb = XLSX.read(stmtBuf, { type: 'array', cellDates: true });

    const expenses = entity === 'vympel'
      ? readReportVympel(reportWb, CONFIG, CONFIG.paymentFilter)
      : readReportLounge(reportWb, CONFIG, CONFIG.paymentFilter);

    const statements = entity === 'vympel'
      ? readStatementVympel(stmtWb, CONFIG)
      : readStatementLounge(stmtWb, CONFIG);

    const result = reconcile(expenses, statements);
    const monthEl = $('#sessionMonth');
    const month = (monthEl && monthEl.value) ? monthEl.value : guessMonth(expenses, statements);
    if (monthEl && !monthEl.value) monthEl.value = month;

    DATA = {
      entity,
      month,
      sessionKey: month + '|' + entity,
      reportFile: reportInput.files[0].name,
      statementFile: stmtInput.files[0].name,
      uploadedFiles: uploaded,
      reconciledAt: new Date().toISOString(),
      ...result,
    };
    DATA.pairRows = buildPairRows(DATA.expenses, DATA.statements, DATA.matches);

    ACTIVE_SESSION_KEY = DATA.sessionKey;
    renderAll();
    try {
      await saveData({ silent: true });
      await loadArchive();
      renderSessionTabs();
      msg.textContent = `Сверка выполнена и сохранена на сервере: ${reportInput.files[0].name} + ${stmtInput.files[0].name}`;
      msg.className = 'msg ok';
    } catch (saveErr) {
      msg.textContent = `Сверка выполнена, но сохранение не удалось: ${saveErr.message}`;
      msg.className = 'msg err';
    }
  } catch (err) {
    msg.textContent = 'Ошибка: ' + err.message;
    msg.className = 'msg err';
  } finally {
    $('#runBtn').disabled = false;
  }
}

async function saveConfig() {
  CONFIG = collectConfigFromForm();
  const msg = $('#configMsg');
  try {
    const res = await fetch('api/save-config.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(CONFIG),
    });
    const out = await readJsonResponse(res);
    msg.textContent = out.ok ? 'Настройки сохранены' : (out.error || 'Ошибка');
    msg.className = 'msg ' + (out.ok ? 'ok' : 'err');
  } catch (e) {
    msg.textContent = e.message || 'Сервер недоступен';
    msg.className = 'msg err';
  }
}

async function saveData(opts = {}) {
  if (!DATA) {
    if (!opts.silent) {
      $('#actionMsg').textContent = 'Нет данных для сохранения';
      $('#actionMsg').className = 'msg err';
    }
    return false;
  }
  const monthEl = $('#sessionMonth');
  if (monthEl && monthEl.value) {
    DATA.month = monthEl.value;
  } else if (!DATA.month) {
    DATA.month = guessMonth(DATA.expenses, DATA.statements);
  }
  DATA.sessionKey = DATA.month + '|' + (DATA.entity || 'lounge');
  ACTIVE_SESSION_KEY = DATA.sessionKey;

  const msg = $('#actionMsg');
  try {
    const res = await fetch('api/save-data.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(DATA),
    });
    const out = await readJsonResponse(res);
    if (out.ok) {
      await loadArchive();
      renderSessionTabs();
    }
    if (!opts.silent) {
      msg.textContent = out.ok ? 'Данные сверки сохранены на сервере' : (out.error || 'Ошибка');
      msg.className = 'msg ' + (out.ok ? 'ok' : 'err');
    }
    return !!out.ok;
  } catch (e) {
    if (!opts.silent) {
      msg.textContent = e.message || 'Сервер недоступен';
      msg.className = 'msg err';
    }
    throw e;
  }
}

document.addEventListener('DOMContentLoaded', () => {
  $('#runBtn').disabled = true;
  loadInitial();

  $('#entity').addEventListener('change', () => {
    ensureConfig().entity = $('#entity').value;
    renderConfigForm();
  });

  const sessionTabs = $('#sessionTabs');
  if (sessionTabs) {
    sessionTabs.addEventListener('click', (e) => {
      const btn = e.target.closest('.session-tab[data-key]');
      if (!btn) return;
      loadSession(btn.dataset.key);
    });
  }

  $('#filters').addEventListener('click', (e) => {
    const btn = e.target.closest('button[data-f]');
    if (!btn) return;
    $$('#filters button').forEach((b) => b.classList.remove('active'));
    btn.classList.add('active');
    currentFilter = btn.dataset.f;
    renderPairsTable();
    renderTables();
  });

  $('#runBtn').addEventListener('click', runReconciliation);
  $('#saveConfigBtn').addEventListener('click', saveConfig);
  $('#saveDataBtn').addEventListener('click', saveData);
  $('#downloadExcel').addEventListener('click', downloadExcel);
  const saveInstrBtn = $('#saveInstructionsBtn');
  if (saveInstrBtn) saveInstrBtn.addEventListener('click', saveInstructions);
});
