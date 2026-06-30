/* global OdrAnalyzer, Chart, ExcelJS */
(function () {
  'use strict';

  const $ = (s) => document.querySelector(s);
  const $$ = (s) => document.querySelectorAll(s);
  const A = OdrAnalyzer;

  let REPORTS = [];
  let ANALYSES = {};
  let CURRENT = null;
  let DATA = null;
  let charts = [];
  let activeKpi = null;
  let structureView = 'branches';
  let incomeLoc = 'total';
  let expenseLoc = 'total';

  const DATASETS = {
    branches: [
      { id: 'revenue', label: 'Выручка', color: '#C5A059', default: true },
      { id: 'profit', label: 'Чистая прибыль', color: '#81c784', default: true },
      { id: 'ttk', label: 'Фудкост по ТТК', color: '#5a7a9a', default: true },
      { id: 'actual', label: 'Фудкост факт', color: '#a05252', default: true },
    ],
    categories: [
      { id: 'rev_m', label: 'Выручка Мойка', color: '#C5A059', default: true },
      { id: 'rev_a', label: 'Выручка Аккуратова', color: '#8e7037', default: true },
      { id: 'ttk', label: 'ТТК (сумма)', color: '#5a7a9a', default: true },
      { id: 'actual', label: 'Факт (сумма)', color: '#a05252', default: true },
    ],
  };

  let enabledDatasets = {};

  function fmt(n) { return A.fmtMoney(n); }
  function pct(n) { return A.fmtPct(n); }

  function unwrapAnalysis(payload) {
    if (!payload) return null;
    if (payload.analysis && (payload.analysis.pnl || payload.analysis.executive)) return payload.analysis;
    if (payload.pnl || payload.executive) return payload;
    return payload.analysis || null;
  }

  async function fetchCachedAnalysis(month) {
    try {
      const res = await fetch(`api/analyze-get.php?month=${encodeURIComponent(month)}`);
      if (!res.ok) return null;
      const json = await res.json();
      return json.ok ? unwrapAnalysis(json.data) : null;
    } catch (_) {
      return null;
    }
  }

  function locData(loc) {
    const pnl = DATA?.pnl;
    if (!pnl) return null;
    if (loc === 'moyka') return pnl.moyka;
    if (loc === 'akku') return pnl.akkuartova;
    return pnl.total;
  }

  function initDatasetToggles() {
    const defs = DATASETS[structureView];
    enabledDatasets = {};
    defs.forEach((d) => { enabledDatasets[d.id] = d.default; });
    const el = $('#datasetToggles');
    el.innerHTML = defs.map((d) => `
      <label class="dataset-toggle">
        <input type="checkbox" data-ds="${d.id}" ${d.default ? 'checked' : ''}>
        <span style="color:${d.color}">■</span> ${d.label}
      </label>
    `).join('');
    el.querySelectorAll('input').forEach((inp) => {
      inp.addEventListener('change', () => {
        enabledDatasets[inp.dataset.ds] = inp.checked;
        renderStructureChart();
      });
    });
  }

  async function loadData() {
    const [repRes, anaRes] = await Promise.all([
      fetch('api/manifest.php').then((r) => r.json()),
      fetch('api/analyze-list.php').then((r) => r.json()),
    ]);
    REPORTS = repRes.reports || [];
    ANALYSES = {};
    (anaRes.items || []).forEach((i) => { ANALYSES[i.month] = i; });
    renderTabs();
    const month = new URLSearchParams(location.search).get('month') || REPORTS[0]?.month;
    if (month) await selectMonth(month);
  }

  function renderTabs() {
    const months = [...new Set(REPORTS.map((r) => r.month))].sort().reverse();
    const el = $('#monthTabs');
    if (!months.length) {
      el.innerHTML = '<p class="msg">Сначала загрузите отчёт ОДР.</p>';
      return;
    }
    el.innerHTML = months.map((m) => {
      const has = ANALYSES[m] ? ' analyzed' : '';
      return `<button type="button" class="month-tab${has}" data-month="${m}">${A.monthLabel(m)}</button>`;
    }).join('');
    el.querySelectorAll('.month-tab').forEach((btn) => {
      btn.addEventListener('click', () => selectMonth(btn.dataset.month));
    });
  }

  async function selectMonth(month) {
    CURRENT = month;
    $$('.month-tab').forEach((b) => b.classList.toggle('active', b.dataset.month === month));
    const rep = REPORTS.find((r) => r.month === month);
    $('#runAnalysisBtn').disabled = !rep;
    $('#pageSubtitle').textContent = rep
      ? `${A.monthLabel(month)} · ${rep.originalName}`
      : A.monthLabel(month);

    DATA = await fetchCachedAnalysis(month);
    if (DATA) {
      $('#dashboard').classList.remove('hidden');
      renderAll();
      setStatus('Дашборд загружен', 'ok');
    } else {
      $('#dashboard').classList.add('hidden');
      setStatus('Нет анализа — нажмите «Обновить анализ»', '');
    }
  }

  function setStatus(t, c) {
    const el = $('#statusMsg');
    el.textContent = t;
    el.className = 'msg' + (c ? ` ${c}` : '');
  }

  async function runAnalysis() {
    const rep = REPORTS.find((r) => r.month === CURRENT);
    if (!rep) return;
    setStatus('Анализируем Excel…', '');
    $('#runAnalysisBtn').disabled = true;
    try {
      const buf = await fetch(`api/file.php?id=${encodeURIComponent(rep.id)}`).then((r) => r.arrayBuffer());
      const analysis = await A.runAnalysis(buf, { fileName: rep.originalName, month: rep.month });
      const safe = JSON.parse(JSON.stringify(analysis));
      const saveRes = await fetch('api/analyze-save.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ month: rep.month, reportId: rep.id, analysis: safe }),
      });
      if (!saveRes.ok) throw new Error('Ошибка сохранения');
      ANALYSES[rep.month] = { month: rep.month };
      renderTabs();
      DATA = safe;
      renderAll();
      setStatus('Анализ обновлён', 'ok');
    } catch (e) {
      setStatus(e.message || 'Ошибка', 'err');
    } finally {
      $('#runAnalysisBtn').disabled = false;
    }
  }

  function destroyCharts() {
    charts.forEach((c) => c.destroy());
    charts = [];
  }

  function renderAll() {
    if (!DATA) return;
    if (!DATA.pnl?.moyka?.foodcost) {
      setStatus('Для полного дашборда нажмите «Обновить анализ»', 'err');
    }
    renderBranchHero();
    renderKpiGrid();
    initDatasetToggles();
    renderStructureChart();
    renderFlowBlocks();
  }

  function renderBranchHero() {
    const pnl = DATA.pnl || {};
    const m = pnl.moyka || {};
    const a = pnl.akkuartova || {};
    const t = pnl.total || {};
    const card = (title, loc, extra) => `
      <div class="branch-card ${extra || ''}">
        <h3>${title}</h3>
        <div class="branch-metric"><span class="lbl">Выручка</span><span class="val">${fmt(loc.revenue?.total)}</span></div>
        <div class="branch-metric"><span class="lbl">Чистая прибыль</span><span class="val profit">${fmt(loc.profit?.net)}</span></div>
        <div class="branch-metric"><span class="lbl">Маржа</span><span class="val pct">${pct(loc.profit?.margin)}</span></div>
        <div class="branch-metric"><span class="lbl">Фудкост факт</span><span class="val pct">${pct(loc.foodcost?.actualPct)} · ${fmt(loc.foodcost?.actual?.total)}</span></div>
        <div class="branch-metric"><span class="lbl">Фудкост ТТК</span><span class="val pct">${pct(loc.foodcost?.ttkPct)} · ${fmt(loc.foodcost?.ttk?.total)}</span></div>
        <div class="branch-metric"><span class="lbl">Гости</span><span class="val">${(loc.analytics?.guests || 0).toLocaleString('ru-RU')}</span></div>
      </div>`;
    $('#branchHero').innerHTML = `<div class="branch-cards">
      ${card('Мойка · Адмиралтейская', m)}
      ${card('Аккуратова · Удельная', a)}
      ${card('Всего', t, 'total')}
    </div>`;
  }

  function renderKpiGrid() {
    const ex = DATA.executive || {};
    const kpis = [
      { id: 'revenue', label: 'Выручка', value: fmt(ex.revenue) },
      { id: 'profit', label: 'Чистая прибыль', value: fmt(ex.profit) },
      { id: 'margin', label: 'Маржа', value: pct(ex.margin) },
      { id: 'guests', label: 'Гости', value: (ex.guests || 0).toLocaleString('ru-RU') },
      { id: 'avgCheck', label: 'Средний чек', value: fmt(ex.avgCheck) },
      { id: 'writeoffs', label: 'Списания', value: fmt(ex.writeoffs) },
      { id: 'inventory', label: 'Инв. расхожд.', value: fmt(ex.inventoryAbsVariance) },
    ];
    const details = A.buildKpiDetails(DATA.pnl, ex, DATA.writeoffs, DATA.inventories);
    $('#kpiGrid').innerHTML = kpis.map((k) => `
      <div class="kpi-card" data-kpi="${k.id}" role="button" tabindex="0">
        <div class="kpi-label">${k.label}</div>
        <div class="kpi-value">${k.value}</div>
      </div>
    `).join('');

    $('#kpiGrid').querySelectorAll('.kpi-card').forEach((card) => {
      const open = () => toggleKpi(card.dataset.kpi, details, card);
      card.addEventListener('click', open);
      card.addEventListener('keydown', (e) => { if (e.key === 'Enter') open(); });
    });
  }

  function toggleKpi(id, details, cardEl) {
    if (activeKpi === id) {
      activeKpi = null;
      $('#kpiDetail').classList.add('hidden');
      $$('.kpi-card').forEach((c) => c.classList.remove('active'));
      return;
    }
    activeKpi = id;
    $$('.kpi-card').forEach((c) => c.classList.remove('active'));
    cardEl.classList.add('active');
    const d = details[id];
    if (!d) return;
    $('#kpiDetail').classList.remove('hidden');
    $('#kpiDetail').innerHTML = renderKpiDetail(id, d);
  }

  function renderKpiDetail(id, d) {
    if (id === 'writeoffs') {
      return `<h3>${d.title}</h3>
        <div class="kpi-detail-grid">
          <div class="kpi-detail-card"><div class="title">Всего</div><div class="big">${fmt(d.total)}</div></div>
          <div class="kpi-detail-card"><div class="title">Мойка</div><div class="big">${fmt(d.moyka)}</div></div>
          <div class="kpi-detail-card"><div class="title">Аккуратова</div><div class="big">${fmt(d.akku)}</div></div>
        </div>
        <div class="kpi-detail-list">${(d.top || []).map((r) => `<div><span>${r.reason}</span><span>${fmt(r.total)}</span></div>`).join('')}</div>`;
    }
    if (id === 'inventory') {
      return `<h3>${d.title}</h3><div class="kpi-detail-list">${(d.sheets || []).map((s) =>
        `<div><span>${s.name}</span><span>${fmt(s.abs)}</span></div>`).join('')}</div>`;
    }
    const branches = (d.branches || []).map((b) => {
      let extra = '';
      if (b.items) {
        extra = `<div class="kpi-detail-list">${b.items.map((i) =>
          `<div><span>${i.label}</span><span>${fmt(i.amount)} (${pct(i.pct)})</span></div>`).join('')}</div>`;
      } else if (b.count != null) {
        extra = `<div class="kpi-detail-list"><div><span>Гостей</span><span>${b.count}</span></div>
          <div><span>Ср. чек</span><span>${fmt(b.avgCheck)}</span></div></div>`;
      } else if (b.pct != null) {
        extra = `<div class="kpi-detail-list"><div><span>Выручка</span><span>${fmt(b.revenue)}</span></div>
          <div><span>ЧП</span><span>${fmt(b.profit)}</span></div></div>`;
      }
      const main = b.amount != null ? fmt(b.amount) : (b.pct != null ? pct(b.pct) : (b.count != null ? b.count.toLocaleString('ru-RU') : '—'));
      return `<div class="kpi-detail-card"><div class="title">${b.name}</div><div class="big">${main}</div>${extra}</div>`;
    }).join('');
    const totalVal = d.total != null
      ? (typeof d.total === 'number' && d.total < 1 && d.total > -1 ? pct(d.total) : (typeof d.total === 'number' ? fmt(d.total) : d.total))
      : '';
    return `<h3>${d.title}</h3><div class="kpi-detail-grid">${branches}
      <div class="kpi-detail-card"><div class="title">Итого</div><div class="big">${totalVal}</div></div>
    </div>`;
  }

  function chartOpts() {
    return {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: (ctx) => `${ctx.dataset.label}: ${fmt(ctx.raw)}`,
          },
        },
      },
      scales: {
        x: { ticks: { color: '#9c958a' }, grid: { color: 'rgba(197,160,89,0.08)' } },
        y: { ticks: { color: '#9c958a', callback: (v) => (v >= 1000 ? (v / 1000).toFixed(0) + 'k' : v) }, grid: { color: 'rgba(197,160,89,0.08)' } },
      },
    };
  }

  function renderStructureChart() {
    destroyCharts();
    const pnl = DATA.pnl || {};
    const m = pnl.moyka || {};
    const a = pnl.akkuartova || {};
    const t = pnl.total || {};
    const defs = DATASETS[structureView];
    const datasets = [];

    if (structureView === 'branches') {
      const labels = ['Мойка', 'Аккуратова', 'Всего'];
      const locs = [m, a, t];
      if (enabledDatasets.revenue) {
        datasets.push({ label: 'Выручка', data: locs.map((l) => l.revenue?.total || 0), backgroundColor: '#C5A059' });
      }
      if (enabledDatasets.profit) {
        datasets.push({ label: 'Чистая прибыль', data: locs.map((l) => l.profit?.net || 0), backgroundColor: '#81c784' });
      }
      if (enabledDatasets.ttk) {
        datasets.push({ label: 'Фудкост ТТК', data: locs.map((l) => l.foodcost?.ttk?.total || 0), backgroundColor: '#5a7a9a' });
      }
      if (enabledDatasets.actual) {
        datasets.push({ label: 'Фудкост факт', data: locs.map((l) => l.foodcost?.actual?.total || 0), backgroundColor: '#a05252' });
      }
      const canvas = $('#chartStructure');
      charts.push(new Chart(canvas, { type: 'bar', data: { labels, datasets }, options: chartOpts() }));
      return;
    }

    const labels = ['Бар', 'Кухня', 'Кальяны'];
    const revM = [m.revenue?.bar, m.revenue?.kitchen, m.revenue?.shisha];
    const revA = [a.revenue?.bar, a.revenue?.kitchen, a.revenue?.shisha];
    const ttk = [(m.foodcost?.ttk?.bar || 0) + (a.foodcost?.ttk?.bar || 0),
      (m.foodcost?.ttk?.kitchen || 0) + (a.foodcost?.ttk?.kitchen || 0),
      (m.foodcost?.ttk?.shisha || 0) + (a.foodcost?.ttk?.shisha || 0)];
    const act = [(m.foodcost?.actual?.bar || 0) + (a.foodcost?.actual?.bar || 0),
      (m.foodcost?.actual?.kitchen || 0) + (a.foodcost?.actual?.kitchen || 0),
      (m.foodcost?.actual?.shisha || 0) + (a.foodcost?.actual?.shisha || 0)];
    if (enabledDatasets.rev_m) datasets.push({ label: 'Выручка Мойка', data: revM, backgroundColor: '#C5A059' });
    if (enabledDatasets.rev_a) datasets.push({ label: 'Выручка Аккуратова', data: revA, backgroundColor: '#8e7037' });
    if (enabledDatasets.ttk) datasets.push({ label: 'ТТК', data: ttk, backgroundColor: '#5a7a9a' });
    if (enabledDatasets.actual) datasets.push({ label: 'Факт', data: act, backgroundColor: '#a05252' });
    charts.push(new Chart($('#chartStructure'), { type: 'bar', data: { labels, datasets }, options: chartOpts() }));
  }

  function renderFlowBlocks() {
    renderFlowColumn('#incomeBlocks', incomeLoc, 'income');
    renderFlowColumn('#expenseBlocks', expenseLoc, 'expense');
  }

  function renderFlowColumn(sel, locKey, type) {
    const loc = locData(locKey);
    if (!loc) return;
    const items = type === 'income'
      ? (loc.revenue?.items || [])
      : (loc.expenses?.items || loc.overheads?.items || []);
    const total = type === 'income' ? loc.revenue?.total : items.reduce((s, i) => s + (i.amount || 0), 0);
    const maxPct = Math.max(...items.map((i) => (i.pct || 0) * 100), 1);
    const el = $(sel);
    el.innerHTML = items.map((it) => {
      const w = ((it.pct || 0) * 100 / maxPct * 100).toFixed(1);
      return `<div class="flow-block ${type}">
        <div class="flow-block-head"><span class="name">${it.label}</span><span class="pct">${pct(it.pct)}</span></div>
        <div class="flow-bar-track"><div class="flow-bar-fill" style="width:${w}%"></div></div>
        <div class="flow-amount">${fmt(it.amount)}</div>
      </div>`;
    }).join('') + `<div class="flow-total"><span>Итого</span><span>${fmt(total)}</span></div>`;
  }

  document.addEventListener('DOMContentLoaded', () => {
    loadData();
    $('#runAnalysisBtn').addEventListener('click', runAnalysis);

    $('#structureViewTabs').addEventListener('click', (e) => {
      const btn = e.target.closest('.view-tab');
      if (!btn) return;
      $$('#structureViewTabs .view-tab').forEach((b) => b.classList.remove('active'));
      btn.classList.add('active');
      structureView = btn.dataset.view;
      initDatasetToggles();
      renderStructureChart();
    });

    ['incomeViewTabs', 'expenseViewTabs'].forEach((id) => {
      $(`#${id}`).addEventListener('click', (e) => {
        const btn = e.target.closest('.view-tab');
        if (!btn) return;
        $$(`#${id} .view-tab`).forEach((b) => b.classList.remove('active'));
        btn.classList.add('active');
        if (id === 'incomeViewTabs') incomeLoc = btn.dataset.loc;
        else expenseLoc = btn.dataset.loc;
        renderFlowBlocks();
      });
    });
  });
})();
