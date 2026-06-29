/* global XLSX */

let ITEMS = [];
let currentFilter = 'all';

const $ = (sel) => document.querySelector(sel);
const $$ = (sel) => document.querySelectorAll(sel);

const CAT_LABELS = {
  ads: 'Реклама',
  social: 'Соцсети',
  analytics: 'Аналитика',
  other: 'Другое',
};

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

function filteredItems() {
  if (currentFilter === 'all') return ITEMS;
  return ITEMS.filter((i) => i.category === currentFilter);
}

async function loadItems() {
  const res = await fetch('reports.json');
  const data = await res.json();
  ITEMS = data.items || [];
  render();
}

function render() {
  const list = filteredItems();
  $('#subtitle').textContent = ITEMS.length
    ? `Всего материалов: ${ITEMS.length}`
    : 'Загрузите первый маркетинговый отчёт';

  $('#downloadRegistry').disabled = !ITEMS.length;
  $('#emptyMsg').style.display = list.length ? 'none' : 'block';

  $('#reportsTable tbody').innerHTML = list.map((r) => `
    <tr>
      <td>${monthLabel(r.month)}</td>
      <td>${CAT_LABELS[r.category] || r.category}</td>
      <td>${r.originalName || '—'}</td>
      <td>${r.note || '—'}</td>
      <td>${fmtDate(r.uploadedAt)}</td>
      <td>
        <div class="table-actions">
          <a class="btn secondary btn-sm" href="${r.path}" download="${r.originalName || 'report.xlsx'}">Скачать</a>
          <button type="button" class="btn danger btn-sm" data-del="${r.id}">Удалить</button>
        </div>
      </td>
    </tr>
  `).join('');

  $('#reportsTable tbody').querySelectorAll('[data-del]').forEach((btn) => {
    btn.addEventListener('click', () => deleteItem(btn.dataset.del));
  });
}

async function uploadItem() {
  const msg = $('#uploadMsg');
  const month = $('#reportMonth').value;
  const file = $('#reportFile').files[0];
  const note = $('#reportNote').value.trim();
  const category = $('#reportCategory').value;

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
  fd.append('category', category);
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
    await loadItems();
  } catch (e) {
    msg.textContent = e.message || 'Ошибка загрузки';
    msg.className = 'msg err';
  }
}

async function deleteItem(id) {
  if (!confirm('Удалить отчёт?')) return;
  try {
    const res = await fetch('api/delete.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id }),
    });
    const out = await res.json();
    if (!out.ok) throw new Error(out.error || 'Ошибка');
    await loadItems();
  } catch (e) {
    alert(e.message || 'Не удалось удалить');
  }
}

function downloadRegistry() {
  if (!ITEMS.length) return;
  const headers = ['Месяц', 'Категория', 'Файл', 'Примечание', 'Размер (байт)', 'Загружен', 'Путь'];
  const rows = ITEMS.map((r) => [
    r.month,
    CAT_LABELS[r.category] || r.category,
    r.originalName ?? '',
    r.note ?? '',
    r.size ?? '',
    r.uploadedAt ?? '',
    r.path ?? '',
  ]);
  const wb = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet([headers, ...rows]), 'Маркетинг');
  XLSX.writeFile(wb, `marketing-reestr-${new Date().toISOString().slice(0, 10)}.xlsx`);
}

document.addEventListener('DOMContentLoaded', () => {
  const now = new Date();
  $('#reportMonth').value = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;
  loadItems();
  $('#uploadBtn').addEventListener('click', uploadItem);
  $('#downloadRegistry').addEventListener('click', downloadRegistry);

  $('#filters').addEventListener('click', (e) => {
    const btn = e.target.closest('button[data-f]');
    if (!btn) return;
    $$('#filters button').forEach((b) => b.classList.remove('active'));
    btn.classList.add('active');
    currentFilter = btn.dataset.f;
    render();
  });
});
