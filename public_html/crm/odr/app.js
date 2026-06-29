/* global XLSX */

let REPORTS = [];

const $ = (sel) => document.querySelector(sel);

function fmtSize(bytes) {
  if (!bytes) return '—';
  if (bytes < 1024) return bytes + ' B';
  if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
  return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
}

function fmtDate(iso) {
  if (!iso) return '—';
  try {
    return new Date(iso).toLocaleString('ru-RU', {
      day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit',
    });
  } catch (_) {
    return iso;
  }
}

function monthLabel(ym) {
  if (!ym) return '—';
  const [y, m] = ym.split('-');
  const names = ['', 'январь', 'февраль', 'март', 'апрель', 'май', 'июнь', 'июль', 'август', 'сентябрь', 'октябрь', 'ноябрь', 'декабрь'];
  return `${names[parseInt(m, 10)] || m} ${y}`;
}

async function loadReports() {
  const res = await fetch('api/manifest.php');
  const data = await res.json();
  REPORTS = data.reports || [];
  render();
}

function render() {
  const tbody = $('#reportsTable tbody');
  $('#subtitle').textContent = REPORTS.length
    ? `В реестре: ${REPORTS.length} ${REPORTS.length === 1 ? 'отчёт' : 'отчёта'}`
    : 'Загрузите первый отчёт ОДР';

  $('#downloadRegistry').disabled = !REPORTS.length;
  $('#emptyMsg').style.display = REPORTS.length ? 'none' : 'block';

  tbody.innerHTML = REPORTS.map((r) => `
    <tr>
      <td>${monthLabel(r.month)}</td>
      <td>${r.originalName || '—'}</td>
      <td>${r.note || '—'}</td>
      <td>${fmtSize(r.size)}</td>
      <td>${fmtDate(r.uploadedAt)}</td>
      <td>
        <div class="table-actions">
          <a class="btn secondary btn-sm" href="api/file.php?id=${r.id}">Скачать</a>
          <button type="button" class="btn danger btn-sm" data-del="${r.id}">Удалить</button>
        </div>
      </td>
    </tr>
  `).join('');

  tbody.querySelectorAll('[data-del]').forEach((btn) => {
    btn.addEventListener('click', () => deleteReport(btn.dataset.del));
  });
}

async function uploadReport() {
  const msg = $('#uploadMsg');
  const month = $('#reportMonth').value;
  const file = $('#reportFile').files[0];
  const note = $('#reportNote').value.trim();

  if (!month) {
    msg.textContent = 'Выберите месяц';
    msg.className = 'msg err';
    return;
  }
  if (!file) {
    msg.textContent = 'Выберите файл .xlsx';
    msg.className = 'msg err';
    return;
  }

  const fd = new FormData();
  fd.append('month', month);
  fd.append('file', file);
  fd.append('note', note);

  msg.textContent = 'Загрузка…';
  msg.className = 'msg';

  try {
    const res = await fetch('api/upload.php', { method: 'POST', body: fd });
    const out = await res.json();
    if (!out.ok) throw new Error(out.error || 'Ошибка');
    msg.textContent = 'Отчёт загружен';
    msg.className = 'msg ok';
    $('#reportFile').value = '';
    $('#reportNote').value = '';
    await loadReports();
  } catch (e) {
    msg.textContent = e.message || 'Ошибка загрузки';
    msg.className = 'msg err';
  }
}

async function deleteReport(id) {
  if (!confirm('Удалить отчёт из реестра?')) return;
  try {
    const res = await fetch('api/delete.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id }),
    });
    const out = await res.json();
    if (!out.ok) throw new Error(out.error || 'Ошибка');
    await loadReports();
  } catch (e) {
    alert(e.message || 'Не удалось удалить');
  }
}

async function downloadRegistry() {
  if (!REPORTS.length) return;
  const headers = ['Месяц', 'Файл', 'Примечание', 'Размер (байт)', 'Загружен', 'Путь'];
  const rows = REPORTS.map((r) => [
    r.month,
    r.originalName ?? '',
    r.note ?? '',
    r.size ?? '',
    r.uploadedAt ?? '',
    r.path ?? '',
  ]);
  await CrmExport.download(`odr-reestr-${new Date().toISOString().slice(0, 10)}.xlsx`, [{
    name: 'ОДР',
    title: 'Реестр отчётов ОДР',
    headers,
    rows,
    wrapCols: [1, 2, 5],
    numberCols: [3],
  }]);
}

document.addEventListener('DOMContentLoaded', () => {
  const now = new Date();
  $('#reportMonth').value = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;
  loadReports();
  $('#uploadBtn').addEventListener('click', uploadReport);
  $('#downloadRegistry').addEventListener('click', downloadRegistry);
});
