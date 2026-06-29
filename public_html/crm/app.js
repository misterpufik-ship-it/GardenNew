/* global XLSX */

let DATA = null;
let ALGO = null;
let currentFilter = 'all';

const $ = (sel) => document.querySelector(sel);
const $$ = (sel) => document.querySelectorAll(sel);

function fmt(n) {
  if (n === null || n === undefined) return '—';
  return n.toLocaleString('ru-RU', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
}

function statusLabel(s) {
  return { match: 'Совпало', partial: 'Частично', mismatch: 'Расхождение', no_admir: 'Нет в АДМИР' }[s] || s;
}

function parseExcelDate(v) {
  if (v == null || v === '') return null;
  if (v instanceof Date && !isNaN(v)) return v;
  if (typeof v === 'number') {
    const d = XLSX.SSF.parse_date_code(v);
    if (d) return new Date(d.y, d.m - 1, d.d);
  }
  const s = String(v).trim();
  const m = s.match(/^(\d{4})-(\d{2})-(\d{2})/);
  if (m) return new Date(+m[1], +m[2] - 1, +m[3]);
  const d = new Date(s);
  return isNaN(d) ? null : d;
}

function toNum(v) {
  if (v == null || v === '') return null;
  const n = parseFloat(String(v).replace(/\s/g, '').replace(',', '.'));
  return isNaN(n) ? null : Math.round(n * 100) / 100;
}

function eq(a, b, tol) {
  if (a == null && b == null) return true;
  if (a == null || b == null) return false;
  return Math.abs(a - b) < tol;
}

function dm(d) {
  return [d.getMonth() + 1, d.getDate()];
}

function readSheetRows(wb, sheetKey, algo) {
  const cfg = algo.sheets[sheetKey];
  const name = wb.SheetNames[cfg.index];
  if (!name) return {};
  const ws = wb.Sheets[name];
  const range = XLSX.utils.decode_range(ws['!ref'] || 'A1');
  const out = {};

  for (let r = cfg.startRow - 1; r <= range.e.r; r++) {
    const dateCell = ws[XLSX.utils.encode_cell({ r, c: cfg.dateCol - 1 })];
    const d = parseExcelDate(dateCell ? dateCell.v : null);
    if (!d) continue;
    const row = {};
    for (const [key, col] of Object.entries(cfg.columns)) {
      const cell = ws[XLSX.utils.encode_cell({ r, c: col - 1 })];
      row[key] = toNum(cell ? cell.v : null);
    }
    out[d.toISOString().slice(0, 10)] = { date: d, values: row, iso: d.toISOString().slice(0, 10) };
  }
  return out;
}

function indexByDayMonth(rowsByIso) {
  const out = {};
  for (const item of Object.values(rowsByIso)) {
    const key = item.date.getMonth() + 1 + '-' + item.date.getDate();
    out[key] = item;
  }
  return out;
}

function analyzeWorkbook(wb, algo, fileName) {
  const admirRaw = readSheetRows(wb, 'admir', algo);
  const s1Raw = readSheetRows(wb, 's1', algo);
  const s2Raw = readSheetRows(wb, 's2', algo);

  const admir = indexByDayMonth(admirRaw);
  const s1 = indexByDayMonth(s1Raw);
  const s2 = indexByDayMonth(s2Raw);

  const allKeys = [...new Set([...Object.keys(admir), ...Object.keys(s1), ...Object.keys(s2)])].sort((a, b) => {
    const [am, ad] = a.split('-').map(Number);
    const [bm, bd] = b.split('-').map(Number);
    return am !== bm ? am - bm : ad - bd;
  });

  const fieldStats = {};
  for (const m of algo.mappings) fieldStats[m.id] = { match: 0, mismatch: 0 };

  const dayStats = { match: 0, partial: 0, mismatch: 0, no_admir: 0 };
  const rows = [];
  const tol = algo.tolerance ?? 0.01;

  for (const key of allKeys) {
    const [month, day] = key.split('-').map(Number);
    const admEntry = admir[key];
    const s1Entry = s1[key];
    const s2Entry = s2[key];
    const a = admEntry ? admEntry.values : {};
    const v1 = s1Entry ? s1Entry.values : {};
    const v2 = s2Entry ? s2Entry.values : {};

    const comparisons = [];
    let rowOk = true;

    for (const m of algo.mappings) {
      const shiftVals = m.shiftSheet === 's1' ? v1 : v2;
      const admVal = a[m.admKey] ?? null;
      const shiftVal = shiftVals[m.shiftKey] ?? null;
      const source = `${algo.sheets[m.shiftSheet]?.index != null ? (wb.SheetNames[algo.sheets[m.shiftSheet].index] || m.shiftSheet) : m.shiftSheet} → ${m.description.replace(/^→\s*/, '')}`;

      let status, ok;
      if (admVal == null && shiftVal == null) {
        status = 'empty';
        ok = true;
      } else if (admVal == null || shiftVal == null) {
        status = 'missing';
        ok = false;
        rowOk = false;
      } else if (eq(admVal, shiftVal, tol)) {
        status = 'match';
        ok = true;
        fieldStats[m.id].match += 1;
      } else {
        status = 'mismatch';
        ok = false;
        rowOk = false;
        fieldStats[m.id].mismatch += 1;
      }

      const diff = admVal != null && shiftVal != null ? Math.round((shiftVal - admVal) * 100) / 100 : null;
      comparisons.push({ id: m.id, label: m.label, adm: admVal, shift: shiftVal, diff, source, status, ok });
    }

    let rowStatus;
    if (!admEntry) rowStatus = 'no_admir';
    else if (rowOk) rowStatus = 'match';
    else if (comparisons.some((c) => c.status === 'match')) rowStatus = 'partial';
    else rowStatus = 'mismatch';

    dayStats[rowStatus] += 1;

    rows.push({
      dm: `${String(day).padStart(2, '0')}.${String(month).padStart(2, '0')}`,
      adm_date: admEntry ? admEntry.iso : null,
      s1_date: s1Entry ? s1Entry.iso : null,
      s2_date: s2Entry ? s2Entry.iso : null,
      row_status: rowStatus,
      comparisons,
    });
  }

  return {
    file: fileName,
    sheets: {
      admir: wb.SheetNames[algo.sheets.admir.index] || 'АДМИР',
      s1: wb.SheetNames[algo.sheets.s1.index] || '1 смена',
      s2: wb.SheetNames[algo.sheets.s2.index] || '2 смена',
    },
    day_stats: dayStats,
    field_stats: fieldStats,
    total_days: rows.length,
    rows,
  };
}

function cellHtml(c) {
  const cls = c.status === 'match' ? 'cell-match' : (c.status === 'mismatch' ? 'cell-mismatch' : 'cell-missing');
  const diff = c.diff != null && c.diff !== 0 ? `<span class="diff">Δ ${fmt(c.diff)}</span>` : '';
  return `<td class="${cls}">
    <div class="val-pair">
      <span class="adm-val">АДМИР: ${fmt(c.adm)}</span>
      <span class="shift-val">Смена: ${fmt(c.shift)}</span>
      ${diff}
    </div>
  </td>`;
}

function renderSummary() {
  if (!DATA) return;
  const s = DATA.day_stats;
  $('#subtitle').textContent =
    `Файл: ${DATA.file} · Листы: «${DATA.sheets.admir}», «${DATA.sheets.s1}», «${DATA.sheets.s2}» · Всего дней: ${DATA.total_days}`;

  $('#summary').innerHTML = `
    <div class="card accent"><div class="num">${DATA.total_days}</div><div class="lbl">Дней в сверке</div></div>
    <div class="card green"><div class="num">${s.match}</div><div class="lbl">Полное совпадение</div></div>
    <div class="card yellow"><div class="num">${s.partial}</div><div class="lbl">Частичное</div></div>
    <div class="card red"><div class="num">${s.mismatch + s.no_admir}</div><div class="lbl">Расхождение / нет данных</div></div>
  `;

  const labels = {};
  for (const m of ALGO.mappings) labels[m.id] = m.label;

  $('#fieldStats').innerHTML = Object.entries(DATA.field_stats).map(([k, v]) => {
    const total = v.match + v.mismatch;
    const pct = total ? Math.round(v.match / total * 100) : 0;
    return `<div class="fs-item">
      <strong>${labels[k] || k}</strong>: ${v.match}/${total} совпало
      <div class="bar"><div class="fill" style="width:${pct}%"></div></div>
    </div>`;
  }).join('');
}

function renderMapping() {
  $('#mappingRows').innerHTML = ALGO.mappings.map((m) =>
    `<div class="row"><span class="adm">${m.label}</span><span>${m.description}</span></div>`
  ).join('');
  $('#mappingNote').textContent = ALGO.notes || '';
}

function renderTable() {
  if (!DATA) return;
  const tbody = $('#tbody');
  const rows = DATA.rows.filter((r) => currentFilter === 'all' || r.row_status === currentFilter);
  tbody.innerHTML = rows.map((r) => {
    const badgeCls = r.row_status === 'match' ? 'match' : (r.row_status === 'partial' ? 'partial' : 'mismatch');
    const dateInfo = `${r.dm}<br><span style="font-size:0.7rem;color:var(--muted)">А:${r.adm_date || '—'}<br>1:${r.s1_date || '—'} 2:${r.s2_date || '—'}</span>`;
    return `<tr class="day-${r.row_status}">
      <td>${dateInfo}</td>
      <td><span class="status-badge ${badgeCls}">${statusLabel(r.row_status)}</span></td>
      ${r.comparisons.map(cellHtml).join('')}
    </tr>`;
  }).join('');
}

function renderAll() {
  renderMapping();
  renderSummary();
  renderTable();
}

function collectAlgoFromForm() {
  const algo = JSON.parse(JSON.stringify(ALGO));
  algo.tolerance = parseFloat($('#tol').value) || 0.01;
  algo.notes = $('#notes').value;

  for (const key of ['admir', 's1', 's2']) {
    algo.sheets[key].index = parseInt($(`#sheet-${key}-index`).value, 10) || 0;
    algo.sheets[key].startRow = parseInt($(`#sheet-${key}-start`).value, 10) || 1;
    algo.sheets[key].dateCol = parseInt($(`#sheet-${key}-date`).value, 10) || 1;
  }

  $$('#mappingTable tbody tr').forEach((tr, i) => {
    if (!algo.mappings[i]) return;
    algo.mappings[i].id = tr.querySelector('[data-f="id"]').value.trim();
    algo.mappings[i].label = tr.querySelector('[data-f="label"]').value.trim();
    algo.mappings[i].admKey = tr.querySelector('[data-f="admKey"]').value.trim();
    algo.mappings[i].shiftSheet = tr.querySelector('[data-f="shiftSheet"]').value;
    algo.mappings[i].shiftKey = tr.querySelector('[data-f="shiftKey"]').value.trim();
    algo.mappings[i].description = tr.querySelector('[data-f="description"]').value.trim();
  });

  return algo;
}

function renderAlgoForm() {
  $('#tol').value = ALGO.tolerance;
  $('#notes').value = ALGO.notes || '';

  for (const key of ['admir', 's1', 's2']) {
    const s = ALGO.sheets[key];
    $(`#sheet-${key}-index`).value = s.index;
    $(`#sheet-${key}-start`).value = s.startRow;
    $(`#sheet-${key}-date`).value = s.dateCol;
  }

  const tbody = $('#mappingTable tbody');
  tbody.innerHTML = ALGO.mappings.map((m) => `
    <tr>
      <td><input data-f="id" value="${m.id}"></td>
      <td><input data-f="label" value="${m.label}"></td>
      <td><input data-f="admKey" value="${m.admKey}"></td>
      <td>
        <select data-f="shiftSheet">
          <option value="s1" ${m.shiftSheet === 's1' ? 'selected' : ''}>1 смена</option>
          <option value="s2" ${m.shiftSheet === 's2' ? 'selected' : ''}>2 смена</option>
        </select>
      </td>
      <td><input data-f="shiftKey" value="${m.shiftKey}"></td>
      <td><input data-f="description" value="${m.description}"></td>
      <td><button type="button" class="btn danger btn-sm del-row">×</button></td>
    </tr>
  `).join('');

  tbody.querySelectorAll('.del-row').forEach((btn) => {
    btn.addEventListener('click', () => {
      btn.closest('tr').remove();
    });
  });
}

async function loadInitial() {
  const [dataRes, algoRes] = await Promise.all([
    fetch('comparison_data.json'),
    fetch('algorithm.json'),
  ]);
  DATA = await dataRes.json();
  ALGO = await algoRes.json();
  renderAlgoForm();
  renderAll();
}

async function saveAlgorithm() {
  ALGO = collectAlgoFromForm();
  const msg = $('#saveMsg');
  try {
    const res = await fetch('api/save-algorithm.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(ALGO),
    });
    const out = await res.json();
    msg.textContent = out.ok ? 'Алгоритм сохранён на сервере' : (out.error || 'Ошибка сохранения');
    msg.className = 'msg ' + (out.ok ? 'ok' : 'err');
  } catch (e) {
    msg.textContent = 'Сохранено локально (сервер недоступен)';
    msg.className = 'msg ok';
    localStorage.setItem('crm-algorithm', JSON.stringify(ALGO));
  }
}

function handleFile(file) {
  const msg = $('#uploadMsg');
  if (!file) return;
  if (!/\.xlsx$/i.test(file.name)) {
    msg.textContent = 'Нужен файл .xlsx';
    msg.className = 'msg err';
    return;
  }

  const reader = new FileReader();
  reader.onload = (e) => {
    try {
      ALGO = collectAlgoFromForm();
      const wb = XLSX.read(e.target.result, { type: 'array', cellDates: true });
      DATA = analyzeWorkbook(wb, ALGO, file.name);
      renderAll();
      msg.textContent = `Обработан: ${file.name}`;
      msg.className = 'msg ok';
    } catch (err) {
      msg.textContent = 'Ошибка разбора: ' + err.message;
      msg.className = 'msg err';
    }
  };
  reader.readAsArrayBuffer(file);
}

function addMappingRow() {
  ALGO = collectAlgoFromForm();
  ALGO.mappings.push({
    id: 'field' + (ALGO.mappings.length + 1),
    label: 'Новое поле',
    admKey: 'nal1',
    shiftSheet: 's1',
    shiftKey: 'nal',
    description: '→ описание',
  });
  renderAlgoForm();
}

function rerunWithAlgo() {
  const fileInput = $('#xlsxFile');
  if (fileInput.files && fileInput.files[0]) {
    handleFile(fileInput.files[0]);
    return;
  }
  ALGO = collectAlgoFromForm();
  renderMapping();
  $('#applyMsg').textContent = 'Загрузите xlsx для пересчёта с новым алгоритмом';
  $('#applyMsg').className = 'msg';
}

document.addEventListener('DOMContentLoaded', () => {
  loadInitial();

  $('#filters').addEventListener('click', (e) => {
    if (e.target.tagName !== 'BUTTON') return;
    $$('.filters button').forEach((b) => b.classList.remove('active'));
    e.target.classList.add('active');
    currentFilter = e.target.dataset.f;
    renderTable();
  });

  $('#xlsxFile').addEventListener('change', (e) => handleFile(e.target.files[0]));
  $('#saveAlgo').addEventListener('click', saveAlgorithm);
  $('#addMapping').addEventListener('click', addMappingRow);
  $('#applyAlgo').addEventListener('click', rerunWithAlgo);
});
