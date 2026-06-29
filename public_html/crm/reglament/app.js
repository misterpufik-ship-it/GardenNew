/* global CrmExport */

let ITEMS = [];
let currentFilter = 'all';

const $ = (sel) => document.querySelector(sel);
const $$ = (sel) => document.querySelectorAll(sel);

const CAT_LABELS = {
  operations: 'Операционные',
  finance: 'Финансы',
  hr: 'Персонал',
  safety: 'Безопасность',
  service: 'Сервис',
  other: 'Прочее',
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
    ? `Документов в реестре: ${ITEMS.length}`
    : 'Загрузите первый регламент';

  $('#downloadRegistry').disabled = !ITEMS.length;
  $('#emptyMsg').style.display = list.length ? 'none' : 'block';

  $('#docsTable tbody').innerHTML = list.map((r) => `
    <tr>
      <td>${CAT_LABELS[r.category] || r.category}</td>
      <td>${r.originalName || '—'}</td>
      <td>${r.version || '—'}</td>
      <td>${r.note || '—'}</td>
      <td>${fmtDate(r.uploadedAt)}</td>
      <td>
        <div class="table-actions">
          <a class="btn secondary btn-sm" href="${r.path}" download="${r.originalName || 'document'}">Скачать</a>
          <button type="button" class="btn danger btn-sm" data-del="${r.id}">Удалить</button>
        </div>
      </td>
    </tr>
  `).join('');

  $('#docsTable tbody').querySelectorAll('[data-del]').forEach((btn) => {
    btn.addEventListener('click', () => deleteItem(btn.dataset.del));
  });
}

async function uploadItem() {
  const msg = $('#uploadMsg');
  const file = $('#docFile').files[0];
  const note = $('#docNote').value.trim();
  const version = $('#docVersion').value.trim();
  const category = $('#docCategory').value;

  if (!file) {
    msg.textContent = 'Выберите файл';
    msg.className = 'msg err';
    return;
  }

  const fd = new FormData();
  fd.append('category', category);
  fd.append('file', file);
  fd.append('note', note);
  fd.append('version', version);

  msg.textContent = 'Загрузка…';
  msg.className = 'msg';

  try {
    const res = await fetch('api/upload.php', { method: 'POST', body: fd });
    const out = await res.json();
    if (!out.ok) throw new Error(out.error || 'Ошибка');
    msg.textContent = 'Регламент загружен';
    msg.className = 'msg ok';
    $('#docFile').value = '';
    $('#docNote').value = '';
    $('#docVersion').value = '';
    await loadItems();
  } catch (e) {
    msg.textContent = e.message || 'Ошибка загрузки';
    msg.className = 'msg err';
  }
}

async function deleteItem(id) {
  if (!confirm('Удалить документ?')) return;
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

async function downloadRegistry() {
  if (!ITEMS.length) return;
  const headers = ['Категория', 'Документ', 'Версия', 'Примечание', 'Размер (байт)', 'Загружен', 'Путь'];
  const rows = ITEMS.map((r) => [
    CAT_LABELS[r.category] || r.category,
    r.originalName ?? '',
    r.version ?? '',
    r.note ?? '',
    r.size ?? '',
    r.uploadedAt ?? '',
    r.path ?? '',
  ]);
  await CrmExport.download(`reglament-reestr-${new Date().toISOString().slice(0, 10)}.xlsx`, [{
    name: 'Регламенты',
    title: 'Реестр регламентов Garden Lounge',
    headers,
    rows,
    wrapCols: [1, 3, 6],
    numberCols: [4],
  }]);
}

document.addEventListener('DOMContentLoaded', () => {
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
