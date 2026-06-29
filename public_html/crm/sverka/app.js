/* global XLSX */

let CONFIG = null;
let DATA = null;
let currentFilter = 'all';

const $ = (sel) => document.querySelector(sel);
const $$ = (sel) => document.querySelectorAll(sel);

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
  return d.toISOString().slice(0, 10);
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
    if (!isBnPayment(payment, paymentFilter) || sum == null) continue;

    out.push({
      id: 'e' + (++id),
      date: isoDate(currentDate),
      sheet: name,
      category: String(cellVal(ws, r, sheetCfg.categoryCol) || '').trim(),
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
      if (!isBnPayment(payment, paymentFilter) || sum == null) continue;

      out.push({
        id: 'e' + (++id),
        date: isoDate(currentDate),
        sheet: name,
        category: String(cellVal(ws, r, sheetCfg.categoryCol) || '').trim(),
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

function readStatementLounge(wb, cfg) {
  const sheetCfg = cfg.lounge.statement;
  const name = wb.SheetNames[sheetCfg.sheetIndex] || wb.SheetNames[0];
  const ws = wb.Sheets[name];
  if (!ws) return [];
  const range = XLSX.utils.decode_range(ws['!ref'] || 'A1');
  const out = [];
  let currentDate = null;
  let id = 0;

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

    const debit = toNum(cellVal(ws, r, sheetCfg.debitCol));
    if (debit == null || debit <= 0) continue;

    out.push({
      id: 's' + (++id),
      date: isoDate(currentDate),
      opNum: String(cellVal(ws, r, sheetCfg.opNumCol) || '').trim(),
      counterparty: String(cellVal(ws, r, sheetCfg.counterpartyCol) || '').trim(),
      purpose: String(cellVal(ws, r, sheetCfg.purposeCol) || '').trim(),
      amount: debit,
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
  const range = XLSX.utils.decode_range(ws['!ref'] || 'A1');
  const out = [];
  let id = 0;
  const startRow = (sheetCfg.headerRow || 1) - 1;

  for (let r = Math.max(range.s.r, startRow); r <= range.e.r; r++) {
    const debit = toNum(cellVal(ws, r, sheetCfg.debitCol));
    if (debit == null || debit <= 0) continue;

    const dateRaw = cellVal(ws, r, sheetCfg.dateCol);
    const d = parseExcelDate(dateRaw);

    out.push({
      id: 's' + (++id),
      date: isoDate(d),
      opNum: String(cellVal(ws, r, sheetCfg.docNumCol) || '').trim(),
      counterparty: String(cellVal(ws, r, sheetCfg.counterpartyCol) || '').trim(),
      purpose: String(cellVal(ws, r, sheetCfg.purposeCol) || '').trim(),
      amount: debit,
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
      .filter((s) => !usedStmt.has(s.id) && rub(s.amount) === rub(exp.amount))
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
    const key = stmt.date || '_';
    if (!stmtByDate[key]) stmtByDate[key] = [];
    stmtByDate[key].push(stmt);
  }

  for (const [date, stmts] of Object.entries(stmtByDate)) {
    for (const stmt of stmts) {
      if (usedStmt.has(stmt.id)) continue;
      const dayExp = expenses.filter((e) => !usedExp.has(e.id) && (e.date || '_') === date);
      if (dayExp.length < 2) continue;

      const combo = findSubset(dayExp, rub(stmt.amount), 2);
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
    unmatchedStmt: statements.filter((s) => s.status === 'unmatched').length,
  };

  return { expenses, statements, matches, stats };
}

function renderConfigForm() {
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
  const cfg = JSON.parse(JSON.stringify(CONFIG));
  $$('#configForm [data-cfg]').forEach((input) => {
    const parts = input.dataset.cfg.split('.');
    let ref = cfg;
    for (let i = 0; i < parts.length - 1; i++) {
      const p = parts[i];
      if (!(p in ref)) ref[p] = /^\d+$/.test(parts[i + 1]) ? [] : {};
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

function passFilter(status) {
  if (currentFilter === 'all') return true;
  if (currentFilter === 'unmatched') return status === 'unmatched';
  return status === currentFilter;
}

function renderSummary() {
  if (!DATA) return;
  const s = DATA.stats;
  const entityLabel = DATA.entity === 'vympel' ? 'Вымпел' : 'Лаундж';
  $('#subtitle').textContent =
    `${entityLabel} · Расходов: ${DATA.expenses.length} · Операций: ${DATA.statements.length}` +
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
      <td>${fmt(s.amount)}</td>
      <td>${statusBadge(s.status)}</td>
      <td>${s.groupNum || '—'}</td>
    </tr>`).join('');
}

function renderAll() {
  renderSummary();
  renderTables();
}

async function loadInitial() {
  const [cfgRes, dataRes] = await Promise.all([
    fetch('config.json'),
    fetch('data.json'),
  ]);
  CONFIG = await cfgRes.json();
  const saved = await dataRes.json();
  $('#entity').value = CONFIG.entity || 'lounge';
  renderConfigForm();

  if (saved && saved.expenses && saved.expenses.length) {
    DATA = saved;
    renderAll();
    $('#actionMsg').textContent = 'Загружены сохранённые данные с сервера';
    $('#actionMsg').className = 'msg ok';
  } else {
    $('#subtitle').textContent = 'Загрузите отчёт и выписку для сверки';
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

async function runReconciliation() {
  const msg = $('#actionMsg');
  const reportInput = $('#reportFile');
  const stmtInput = $('#statementFile');

  if (!reportInput.files[0] || !stmtInput.files[0]) {
    msg.textContent = 'Выберите оба файла: отчёт и выписку';
    msg.className = 'msg err';
    return;
  }

  try {
    CONFIG = collectConfigFromForm();
    const entity = CONFIG.entity;
    const [reportBuf, stmtBuf] = await Promise.all([
      readFileAsArrayBuffer(reportInput.files[0]),
      readFileAsArrayBuffer(stmtInput.files[0]),
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

    DATA = {
      entity,
      reportFile: reportInput.files[0].name,
      statementFile: stmtInput.files[0].name,
      ...result,
    };

    renderAll();
    msg.textContent = `Сверка выполнена: ${reportInput.files[0].name} + ${stmtInput.files[0].name}`;
    msg.className = 'msg ok';
  } catch (err) {
    msg.textContent = 'Ошибка: ' + err.message;
    msg.className = 'msg err';
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
    const out = await res.json();
    msg.textContent = out.ok ? 'Настройки сохранены' : (out.error || 'Ошибка');
    msg.className = 'msg ' + (out.ok ? 'ok' : 'err');
  } catch (e) {
    msg.textContent = 'Сервер недоступен';
    msg.className = 'msg err';
  }
}

async function saveData() {
  if (!DATA) {
    $('#actionMsg').textContent = 'Нет данных для сохранения';
    $('#actionMsg').className = 'msg err';
    return;
  }
  const msg = $('#actionMsg');
  try {
    const res = await fetch('api/save-data.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(DATA),
    });
    const out = await res.json();
    msg.textContent = out.ok ? 'Данные сверки сохранены на сервере' : (out.error || 'Ошибка');
    msg.className = 'msg ' + (out.ok ? 'ok' : 'err');
  } catch (e) {
    msg.textContent = 'Сервер недоступен';
    msg.className = 'msg err';
  }
}

document.addEventListener('DOMContentLoaded', () => {
  loadInitial();

  $('#entity').addEventListener('change', () => {
    CONFIG.entity = $('#entity').value;
    renderConfigForm();
  });

  $('#filters').addEventListener('click', (e) => {
    if (e.target.tagName !== 'BUTTON') return;
    $$('#filters button').forEach((b) => b.classList.remove('active'));
    e.target.classList.add('active');
    currentFilter = e.target.dataset.f;
    renderTables();
  });

  $('#runBtn').addEventListener('click', runReconciliation);
  $('#saveConfigBtn').addEventListener('click', saveConfig);
  $('#saveDataBtn').addEventListener('click', saveData);
});
