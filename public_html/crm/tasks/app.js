/* global XLSX */

let TASKS = [];
let currentFilter = 'all';

const $ = (sel) => document.querySelector(sel);
const $$ = (sel) => document.querySelectorAll(sel);

const STATUS_LABELS = {
  todo: 'К выполнению',
  progress: 'В работе',
  done: 'Готово',
};

function uid() {
  return Array.from(crypto.getRandomValues(new Uint8Array(8))).map((b) => b.toString(16).padStart(2, '0')).join('');
}

function fmtDate(d) {
  if (!d) return '—';
  try {
    return new Date(d + 'T12:00:00').toLocaleDateString('ru-RU');
  } catch (_) {
    return d;
  }
}

function filteredTasks() {
  if (currentFilter === 'all') return TASKS;
  return TASKS.filter((t) => t.status === currentFilter);
}

async function loadTasks() {
  const res = await fetch('api/manifest.php');
  const data = await res.json();
  TASKS = data.tasks || [];
  render();
}

function renderSummary() {
  const counts = { todo: 0, progress: 0, done: 0 };
  TASKS.forEach((t) => { if (counts[t.status] != null) counts[t.status] += 1; });

  $('#subtitle').textContent = TASKS.length ? `Всего задач: ${TASKS.length}` : 'Добавьте первую задачу';
  $('#summary').innerHTML = `
    <div class="card accent"><div class="num">${TASKS.length}</div><div class="lbl">Всего</div></div>
    <div class="card yellow"><div class="num">${counts.todo}</div><div class="lbl">К выполнению</div></div>
    <div class="card accent"><div class="num">${counts.progress}</div><div class="lbl">В работе</div></div>
    <div class="card green"><div class="num">${counts.done}</div><div class="lbl">Готово</div></div>
  `;
  $('#downloadExcel').disabled = !TASKS.length;
}

function renderTable() {
  const list = filteredTasks();
  $('#emptyMsg').style.display = list.length ? 'none' : 'block';

  $('#tasksTable tbody').innerHTML = list.map((t) => `
    <tr class="row-${t.status === 'done' ? 'done' : t.status}">
      <td><input class="task-title-input" data-f="title" data-id="${t.id}" value="${escapeAttr(t.title)}"></td>
      <td><input data-f="assignee" data-id="${t.id}" value="${escapeAttr(t.assignee || '')}"></td>
      <td><input type="date" data-f="due" data-id="${t.id}" value="${t.due || ''}"></td>
      <td>
        <select data-f="status" data-id="${t.id}">
          <option value="todo" ${t.status === 'todo' ? 'selected' : ''}>К выполнению</option>
          <option value="progress" ${t.status === 'progress' ? 'selected' : ''}>В работе</option>
          <option value="done" ${t.status === 'done' ? 'selected' : ''}>Готово</option>
        </select>
      </td>
      <td><input data-f="notes" data-id="${t.id}" value="${escapeAttr(t.notes || '')}"></td>
      <td><button type="button" class="btn danger btn-sm" data-del="${t.id}">×</button></td>
    </tr>
  `).join('');

  $('#tasksTable tbody').querySelectorAll('[data-f]').forEach((el) => {
    el.addEventListener('change', () => updateTaskField(el.dataset.id, el.dataset.f, el.value));
  });
  $('#tasksTable tbody').querySelectorAll('[data-del]').forEach((btn) => {
    btn.addEventListener('click', () => deleteTask(btn.dataset.del));
  });
}

function escapeAttr(s) {
  return String(s).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;');
}

function render() {
  renderSummary();
  renderTable();
}

function updateTaskField(id, field, value) {
  const task = TASKS.find((t) => t.id === id);
  if (!task) return;
  task[field] = value;
  task.updatedAt = new Date().toISOString();
  renderSummary();
}

function addTask() {
  const title = $('#taskTitle').value.trim();
  const msg = $('#formMsg');
  if (!title) {
    msg.textContent = 'Введите название задачи';
    msg.className = 'msg err';
    return;
  }

  TASKS.unshift({
    id: uid(),
    title,
    assignee: $('#taskAssignee').value.trim(),
    due: $('#taskDue').value,
    status: $('#taskStatus').value,
    notes: $('#taskNotes').value.trim(),
    createdAt: new Date().toISOString(),
    updatedAt: new Date().toISOString(),
  });

  $('#taskTitle').value = '';
  $('#taskAssignee').value = '';
  $('#taskDue').value = '';
  $('#taskNotes').value = '';
  $('#taskStatus').value = 'todo';
  msg.textContent = 'Задача добавлена';
  msg.className = 'msg ok';
  render();
}

function deleteTask(id) {
  if (!confirm('Удалить задачу?')) return;
  TASKS = TASKS.filter((t) => t.id !== id);
  render();
}

async function saveTasks() {
  const msg = $('#formMsg');
  try {
    const res = await fetch('api/save.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ tasks: TASKS }),
    });
    const out = await res.json();
    msg.textContent = out.ok ? 'Задачи сохранены на сервере' : (out.error || 'Ошибка');
    msg.className = 'msg ' + (out.ok ? 'ok' : 'err');
  } catch (e) {
    msg.textContent = 'Сервер недоступен';
    msg.className = 'msg err';
  }
}

async function downloadExcel() {
  if (!TASKS.length) return;
  const headers = ['Задача', 'Ответственный', 'Срок', 'Статус', 'Примечание', 'Создана', 'Обновлена'];
  const rows = TASKS.map((t) => [
    t.title ?? '',
    t.assignee ?? '',
    t.due ?? '',
    STATUS_LABELS[t.status] || t.status,
    t.notes ?? '',
    t.createdAt ?? '',
    t.updatedAt ?? '',
  ]);
  await CrmExport.download(`tasks-${new Date().toISOString().slice(0, 10)}.xlsx`, [{
    name: 'Задачи',
    title: 'Список задач AI-CRM',
    headers,
    rows,
    rowStatus: (_, i) => TASKS[i].status,
    wrapCols: [0, 4],
  }]);
}

document.addEventListener('DOMContentLoaded', () => {
  loadTasks();
  $('#addTaskBtn').addEventListener('click', addTask);
  $('#saveTasksBtn').addEventListener('click', saveTasks);
  $('#downloadExcel').addEventListener('click', downloadExcel);

  $('#filters').addEventListener('click', (e) => {
    const btn = e.target.closest('button[data-f]');
    if (!btn) return;
    $$('#filters button').forEach((b) => b.classList.remove('active'));
    btn.classList.add('active');
    currentFilter = btn.dataset.f;
    renderTable();
  });
});
