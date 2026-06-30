/* global OdrAnalyzer, Chart, ExcelJS */
(function () {
  'use strict';

  const $ = (s) => document.querySelector(s);
  const $$ = (s) => document.querySelectorAll(s);
  const A = OdrAnalyzer;

  let REPORTS = [];
  let ANALYSES = {};
  let CURRENT = null;
  let PREVIOUS = null;
  let DATA = null;
  let charts = [];
  let activeKpi = null;
  let structureView = 'branches';
  let incomeLoc = 'total';
  let expenseLoc = 'total';
  let enabledDatasets = {};

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

  function section(title, body, open) {
    return `<details class="panel collapsible"${open ? ' open' : ''}>
      <summary>${title}</summary>
      <div class="collapsible-body">${body}</div>
    </details>`;
  }

  function locData(loc) {
    const pnl = DATA?.pnl;
    if (!pnl) return null;
    if (loc === 'moyka') return pnl.moyka;
    if (loc === 'akku') return pnl.akkuartova;
    return pnl.total;
  }

  function reportForMonth(month) {
    return REPORTS.find((r) => r.month === month);
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
    if (month) selectMonth(month);
  }

  function renderTabs() {
    const months = [...new Set(REPORTS.map((r) => r.month))].sort().reverse();
    const el = $('#monthTabs');
    if (!months.length) {
      el.innerHTML = '<p class="msg">Сначала загрузите отчёт ОДР в реестре.</p>';
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
    const rep = reportForMonth(month);
    $('#runAnalysisBtn').disabled = !rep;
    $('#pageSubtitle').textContent = rep
      ? `${A.monthLabel(month)} · ${rep.originalName}`
      : A.monthLabel(month);

    DATA = await fetchCachedAnalysis(month);
    if (DATA) {
      await renderDashboard(DATA);
      setStatus(DATA.pnl?.moyka?.foodcost ? 'Анализ загружен' : 'Нажмите «Анализировать» для полных данных', DATA.pnl?.moyka?.foodcost ? 'ok' : 'err');
    } else if (ANALYSES[month]) {
      $('#dashboard').classList.add('hidden');
      setStatus('Кэш не читается — нажмите «Анализировать»', 'err');
    } else {
      $('#dashboard').classList.add('hidden');
      setStatus('Анализ ещё не выполнен — нажмите «Анализировать»', '');
    }
  }

  function setStatus(text, cls) {
    const el = $('#statusMsg');
    el.textContent = text;
    el.className = 'msg' + (cls ? ` ${cls}` : '');
  }

  async function runAnalysis() {
    const rep = reportForMonth(CURRENT);
    if (!rep) return;
    setStatus('Читаем Excel и считаем KPI…', '');
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
      const saveOut = await saveRes.json().catch(() => ({}));
      if (!saveRes.ok || !saveOut.ok) throw new Error(saveOut.error || 'Ошибка сохранения');
      ANALYSES[rep.month] = { month: rep.month };
      renderTabs();
      DATA = safe;
      await renderDashboard(safe);
      setStatus('Анализ сохранён', 'ok');
    } catch (e) {
      setStatus(e.message || 'Ошибка анализа', 'err');
    } finally {
      $('#runAnalysisBtn').disabled = false;
    }
  }

  function destroyCharts() {
    charts.forEach((c) => c.destroy());
    charts = [];
  }

  async function loadPrevious(month) {
    const d = new Date(month + '-01');
    d.setMonth(d.getMonth() - 1);
    const prev = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`;
    const res = await fetch(`api/analyze-get.php?month=${encodeURIComponent(prev)}`);
    if (!res.ok) return null;
    return unwrapAnalysis((await res.json()).data);
  }

  function kpiCard(label, value, cmp, isPct) {
    let delta = '';
    if (cmp?.delta != null) {
      const sign = cmp.delta >= 0 ? '+' : '';
      const d = isPct ? (cmp.delta * 100).toFixed(1) + ' п.п.' : sign + Math.round(cmp.delta).toLocaleString('ru-RU') + ' ₽';
      delta = `<span class="kpi-delta ${cmp.delta >= 0 ? 'up' : 'down'}">${d}</span>`;
    }
    return `<div class="kpi-card"><div class="kpi-label">${label}</div><div class="kpi-value">${value}${delta}</div></div>`;
  }

  function renderBranchHero(pnl) {
    const m = pnl.moyka || {};
    const a = pnl.akkuartova || {};
    const t = pnl.total || {};
    const card = (title, loc, extra) => `
      <div class="branch-card ${extra || ''}">
        <h3>${title}</h3>
        <div class="branch-metric"><span class="lbl">Выручка</span><span class="val">${fmt(loc.revenue?.total)}</span></div>
        <div class="branch-metric"><span class="lbl">Чистая прибыль</span><span class="val profit">${fmt(loc.profit?.net)}</span></div>
        <div class="branch-metric"><span class="lbl">Маржа</span><span class="val pct">${pct(loc.profit?.margin)}</span></div>
        <div class="branch-metric"><span class="lbl">Фудкост факт / ТТК</span><span class="val pct">${pct(loc.foodcost?.actualPct)} / ${pct(loc.foodcost?.ttkPct)}</span></div>
        <div class="branch-metric"><span class="lbl">Гости</span><span class="val">${(loc.analytics?.guests || 0).toLocaleString('ru-RU')}</span></div>
      </div>`;
    return `<div class="branch-cards">
      ${card('Мойка · Адмиралтейская', m)}
      ${card('Аккуратова · Удельная', a)}
      ${card('Всего', t, 'total')}
    </div>`;
  }

  function renderKpiGrid(ex, comparison, details) {
    const kpis = [
      { id: 'revenue', label: 'Выручка', value: fmt(ex.revenue) },
      { id: 'profit', label: 'Чистая прибыль', value: fmt(ex.profit) },
      { id: 'margin', label: 'Маржа', value: pct(ex.margin) },
      { id: 'guests', label: 'Гости', value: (ex.guests || 0).toLocaleString('ru-RU') },
      { id: 'avgCheck', label: 'Средний чек', value: fmt(ex.avgCheck) },
      { id: 'writeoffs', label: 'Списания', value: fmt(ex.writeoffs) },
      { id: 'inventory', label: 'Инв. расхожд.', value: fmt(ex.inventoryAbsVariance) },
    ];
    return `<p class="owner-summary">${DATA.recommendations?.ownerSummary || ''}</p>
      <div class="kpi-grid kpi-grid--clickable" id="kpiGrid">
        ${kpis.map((k) => {
          const cmp = comparison?.[k.id];
          let card = kpiCard(k.label, k.value, cmp, k.id === 'margin');
          return `<div class="kpi-card" data-kpi="${k.id}" role="button" tabindex="0">${card.replace(/^<div class="kpi-card">|<\/div>$/g, '')}</div>`;
        }).join('')}
      </div>
      <div class="kpi-detail hidden" id="kpiDetail"></div>`;
  }

  function buildPnlRows(pnl) {
    const m = pnl.moyka || {};
    const a = pnl.akkuartova || {};
    const rows = [];
    const add = (label, vm, va, opts = {}) => {
      if (!opts.force && vm == null && va == null) return;
      rows.push({ label, vm, va, ...opts });
    };

    add('ДОХОДЫ ВСЕГО', m.revenue?.total, a.revenue?.total, { header: true, force: true });
    add('Бар', m.revenue?.bar, a.revenue?.bar);
    add('Кухня', m.revenue?.kitchen, a.revenue?.kitchen);
    add('Кальяны', m.revenue?.shisha, a.revenue?.shisha);
    add('Банкеты', m.revenue?.banquets, a.revenue?.banquets);
    add('Прочее', m.revenue?.other, a.revenue?.other);

    add('СЕБЕСТОИМОСТЬ (факт)', m.cogs?.total, a.cogs?.total, { header: true, force: true });
    add('  Себест. бар', m.cogs?.bar, a.cogs?.bar);
    add('  Себест. кухня', m.cogs?.kitchen, a.cogs?.kitchen);
    add('  Себест. кальяны', m.cogs?.shisha, a.cogs?.shisha);
    add('Факт себест., %', m.cogs?.pct, a.cogs?.pct, { isMargin: true });
    add('ПО ТТК', m.foodcost?.ttk?.total, a.foodcost?.ttk?.total, { header: true });
    add('  ТТК бар', m.foodcost?.ttk?.bar, a.foodcost?.ttk?.bar);
    add('  ТТК кухня', m.foodcost?.ttk?.kitchen, a.foodcost?.ttk?.kitchen);
    add('  ТТК кальяны', m.foodcost?.ttk?.shisha, a.foodcost?.ttk?.shisha);
    add('Отклонение факт − ТТК', m.foodcost?.variance, a.foodcost?.variance);

    add('НАКЛАДНЫЕ ЗАТРАТЫ', m.overheads?.total, a.overheads?.total, { header: true, force: true });

    const expenseKeys = [];
    const seen = new Set();
    [...(m.expenses?.items || []), ...(a.expenses?.items || [])].forEach((it) => {
      if (it.key === 'cogs' || seen.has(it.key)) return;
      seen.add(it.key);
      expenseKeys.push(it.key);
    });
    expenseKeys.forEach((key) => {
      const lm = m.expenses?.items?.find((i) => i.key === key);
      const la = a.expenses?.items?.find((i) => i.key === key);
      add(lm?.label || la?.label || key, lm?.amount, la?.amount);
    });

    add('Валовая прибыль', m.profit?.gross, a.profit?.gross, { header: true });
    add('Чистая прибыль', m.profit?.net, a.profit?.net, { header: true, force: true });
    add('Маржа', m.profit?.margin, a.profit?.margin, { isMargin: true, force: true });
    add('Гости', m.analytics?.guests, a.analytics?.guests, { isGuests: true, force: true });
    add('Средний чек', m.analytics?.avgCheck, a.analytics?.avgCheck);

    return rows;
  }

  function pnlCell(val, row) {
    if (row.isMargin) return pct(val);
    if (row.isGuests) return val != null ? val.toLocaleString('ru-RU') : '—';
    return fmt(val);
  }

  function pnlPctCell(revenue, val, row) {
    if (row.isGuests || row.isMargin) return row.isMargin ? '—' : '';
    if (revenue && val != null) return pct(val / revenue);
    return '—';
  }

  function renderPnlTable(pnl) {
    if (!pnl) return '<p class="msg">Нет данных ОДР</p>';
    const rows = buildPnlRows(pnl);
    const revM = pnl.moyka?.revenue?.total;
    const revA = pnl.akkuartova?.revenue?.total;
    return `<div class="wrap"><table class="data-table pnl-table">
      <thead><tr>
        <th>Статья</th>
        <th>Мойка</th><th>%</th>
        <th>Аккуратова</th><th>%</th>
      </tr></thead>
      <tbody>${rows.map((r) => `
        <tr class="${r.header ? 'pnl-row-header' : ''}">
          <td>${r.label}</td>
          <td>${pnlCell(r.vm, r)}</td><td class="pnl-pct">${pnlPctCell(revM, r.vm, r)}</td>
          <td>${pnlCell(r.va, r)}</td><td class="pnl-pct">${pnlPctCell(revA, r.va, r)}</td>
        </tr>`).join('')}
      </tbody></table></div>`;
  }

  function renderFlowColumn(type, locKey) {
    const loc = locData(locKey);
    if (!loc) return '<p class="msg">—</p>';
    const items = type === 'income'
      ? (loc.revenue?.items || [])
      : (loc.expenses?.items || []);
    const total = type === 'income' ? loc.revenue?.total : items.reduce((s, i) => s + (i.amount || 0), 0);
    const maxPct = Math.max(...items.map((i) => (i.pct || 0) * 100), 0.01);
    return items.map((it) => {
      const w = ((it.pct || 0) * 100 / maxPct * 100).toFixed(1);
      return `<div class="flow-block ${type}">
        <div class="flow-block-head"><span class="name">${it.label}</span><span class="pct">${pct(it.pct)}</span></div>
        <div class="flow-bar-track"><div class="flow-bar-fill" style="width:${w}%"></div></div>
        <div class="flow-amount">${fmt(it.amount)}</div>
      </div>`;
    }).join('') + `<div class="flow-total"><span>Итого</span><span>${fmt(total)}</span></div>`;
  }

  function flowTabs(id, active) {
    return `<div class="flow-view-tabs" data-flow="${id}">
      <button type="button" class="view-tab${active === 'total' ? ' active' : ''}" data-loc="total">Всего</button>
      <button type="button" class="view-tab${active === 'moyka' ? ' active' : ''}" data-loc="moyka">Мойка</button>
      <button type="button" class="view-tab${active === 'akku' ? ' active' : ''}" data-loc="akku">Аккуратова</button>
    </div>`;
  }

  function renderComparison(cmp) {
    if (!cmp) return '';
    return `<table class="data-table"><thead><tr><th>Показатель</th><th>Текущий</th><th>Прошлый</th><th>Δ</th><th>Δ%</th></tr></thead><tbody>
      ${['revenue', 'profit', 'guests'].map((k) => {
        const o = cmp[k];
        if (!o) return '';
        const fmtVal = (v) => (k === 'guests' ? (v || 0).toLocaleString('ru-RU') : fmt(v));
        return `<tr><td>${k === 'revenue' ? 'Выручка' : k === 'profit' ? 'Прибыль' : 'Гости'}</td>
          <td>${fmtVal(o.current)}</td><td>${fmtVal(o.previous)}</td>
          <td>${o.delta != null ? Math.round(o.delta).toLocaleString('ru-RU') : '—'}</td>
          <td>${o.pct != null ? pct(o.pct) : '—'}</td></tr>`;
      }).join('')}
    </tbody></table>`;
  }

  function renderDataQuality(dq) {
    if (!dq) return '';
    const errs = (dq.errors || []).slice(0, 20);
    const rec = (dq.reconciliations || []).concat(dq.warnings || []);
    return `<p>Ошибок формул: <strong>${errs.length}</strong>${dq.missingSheets?.length ? ` · Листы: ${dq.missingSheets.join(', ')}` : ''}</p>
      ${errs.length ? `<table class="data-table compact"><thead><tr><th>Лист</th><th>Ячейка</th><th>Ошибка</th></tr></thead><tbody>
        ${errs.map((e) => `<tr><td>${e.sheet}</td><td>${e.cell}</td><td class="bad">${e.error}</td></tr>`).join('')}
      </tbody></table>` : ''}
      ${rec.length ? `<ul class="bullet-list">${rec.map((r) => `<li>${typeof r === 'string' ? r : r.message}</li>`).join('')}</ul>` : ''}`;
  }

  function renderRecommendations(rec) {
    return `
      <h3>Предупреждения</h3><ul class="bullet-list bad">${(rec.warnings || []).map((w) => `<li>${w.text}</li>`).join('') || '<li>—</li>'}</ul>
      <h3 class="section-gap">Возможности</h3><ul class="bullet-list good">${(rec.opportunities || []).map((o) => `<li>${o.text}</li>`).join('') || '<li>—</li>'}</ul>
      <h3 class="section-gap">План на 7 дней</h3><ul class="bullet-list">${(rec.action7 || []).map((t) => `<li>${t}</li>`).join('') || '<li>—</li>'}</ul>
      <h3 class="section-gap">План на 30 дней</h3><ul class="bullet-list">${(rec.action30 || []).map((t) => `<li>${t}</li>`).join('') || '<li>—</li>'}</ul>`;
  }

  function renderInventory(invs) {
    if (!invs?.length) return '<p class="msg">—</p>';
    return invs.map((inv) => `
      <h3>${inv.sheet} · ${inv.location}</h3>
      <p>Излишки: ${fmt(inv.totals?.surplusSum)} · Недостача: ${fmt(inv.totals?.shortageSum)} · Абс.: ${fmt(inv.absoluteVariance)}</p>
      <table class="data-table compact"><thead><tr><th>Позиция</th><th>Излишек</th><th>Недостача</th></tr></thead><tbody>
        ${inv.topAbs.slice(0, 10).map((i) => `<tr><td>${i.name}</td><td>${fmt(i.surplus)}</td><td>${fmt(i.shortage)}</td></tr>`).join('')}
      </tbody></table>`).join('');
  }

  async function renderDashboard(data) {
    if (!data) return;
    DATA = data;
    destroyCharts();
    activeKpi = null;
    let comparison = null;
    try {
      if ($('#compareMode').checked) {
        PREVIOUS = await loadPrevious(data.meta?.month || CURRENT);
        if (PREVIOUS) comparison = A.compareMonths(data, PREVIOUS);
      }

      const ex = data.executive || {};
      const rec = data.recommendations || {};
      const details = A.buildKpiDetails(data.pnl, ex, data.writeoffs, data.inventories);

      const html = [
        section('Сводка и KPI', renderKpiGrid(ex, comparison, details), true),
        section('Филиалы — выручка и чистая прибыль', renderBranchHero(data.pnl || {})),
        section('Структура выручки и фудкост', `
          <div class="chart-toolbar">
            <div class="view-tabs" id="structureViewTabs">
              <button type="button" class="view-tab active" data-view="branches">По филиалам</button>
              <button type="button" class="view-tab" data-view="categories">По категориям</button>
            </div>
          </div>
          <div class="dataset-toggles" id="datasetToggles"></div>
          <div class="chart-box chart-box--tall"><canvas id="chartStructure"></canvas></div>
        `, true),
        section('Доходы и расходы (% от выручки)', `
          <div class="flow-columns">
            <div><h3>Доходы</h3>${flowTabs('income', incomeLoc)}<div id="incomeBlocks">${renderFlowColumn('income', incomeLoc)}</div></div>
            <div><h3>Расходы</h3>${flowTabs('expense', expenseLoc)}<div id="expenseBlocks">${renderFlowColumn('expense', expenseLoc)}</div></div>
          </div>
        `),
        section('P&L — все статьи ОДР', renderPnlTable(data.pnl), true),
        comparison ? section('Месяц к месяцу', renderComparison(comparison)) : '',
        section('Выводы', `
          <div class="two-col">
            <div><h3>Что хорошо</h3><ul class="bullet-list good">${(data.good || []).map((t) => `<li>${t}</li>`).join('') || '<li>—</li>'}</ul></div>
            <div><h3>Главные проблемы</h3><ul class="bullet-list bad">${(data.problems || []).map((t) => `<li>${t}</li>`).join('') || '<li>—</li>'}</ul></div>
          </div>`),
        section('Списания', '<div class="chart-box"><canvas id="chartWriteoffs"></canvas></div>'),
        section('Качество данных', renderDataQuality(data.dataQuality)),
        section('Рекомендации', renderRecommendations(rec)),
        section('Инвентаризации', renderInventory(data.inventories)),
      ].join('');

      $('#dashboard').innerHTML = html;
      $('#dashboard').classList.remove('hidden');
      bindDashboardEvents(details);
      initDatasetToggles();
      renderStructureChart();
      renderWriteoffsChart(data);
    } catch (err) {
      console.error(err);
      $('#dashboard').innerHTML = section('Ошибка', `<p class="msg err">${err.message || err}</p>`, true);
      $('#dashboard').classList.remove('hidden');
    }
  }

  function bindDashboardEvents(details) {
    $('#kpiGrid')?.querySelectorAll('.kpi-card').forEach((card) => {
      const open = () => toggleKpi(card.dataset.kpi, details, card);
      card.addEventListener('click', open);
      card.addEventListener('keydown', (e) => { if (e.key === 'Enter') open(); });
    });

    $('#structureViewTabs')?.addEventListener('click', (e) => {
      const btn = e.target.closest('.view-tab');
      if (!btn) return;
      $$('#structureViewTabs .view-tab').forEach((b) => b.classList.remove('active'));
      btn.classList.add('active');
      structureView = btn.dataset.view;
      initDatasetToggles();
      renderStructureChart();
    });

    $$('[data-flow]').forEach((el) => {
      el.addEventListener('click', (e) => {
        const btn = e.target.closest('.view-tab');
        if (!btn) return;
        el.querySelectorAll('.view-tab').forEach((b) => b.classList.remove('active'));
        btn.classList.add('active');
        if (el.dataset.flow === 'income') {
          incomeLoc = btn.dataset.loc;
          $('#incomeBlocks').innerHTML = renderFlowColumn('income', incomeLoc);
        } else {
          expenseLoc = btn.dataset.loc;
          $('#expenseBlocks').innerHTML = renderFlowColumn('expense', expenseLoc);
        }
      });
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
      return `<h3>${d.title}</h3><div class="kpi-detail-grid">
        <div class="kpi-detail-card"><div class="title">Всего</div><div class="big">${fmt(d.total)}</div></div>
        <div class="kpi-detail-card"><div class="title">Мойка</div><div class="big">${fmt(d.moyka)}</div></div>
        <div class="kpi-detail-card"><div class="title">Аккуратова</div><div class="big">${fmt(d.akku)}</div></div>
      </div><div class="kpi-detail-list">${(d.top || []).map((r) => `<div><span>${r.reason}</span><span>${fmt(r.total)}</span></div>`).join('')}</div>`;
    }
    if (id === 'inventory') {
      return `<h3>${d.title}</h3><div class="kpi-detail-list">${(d.sheets || []).map((s) =>
        `<div><span>${s.name}</span><span>${fmt(s.abs)}</span></div>`).join('')}</div>`;
    }
    const branches = (d.branches || []).map((b) => {
      let extra = '';
      if (b.items) extra = `<div class="kpi-detail-list">${b.items.map((i) =>
        `<div><span>${i.label}</span><span>${fmt(i.amount)} (${pct(i.pct)})</span></div>`).join('')}</div>`;
      else if (b.count != null) extra = `<div class="kpi-detail-list"><div><span>Гостей</span><span>${b.count}</span></div><div><span>Ср. чек</span><span>${fmt(b.avgCheck)}</span></div></div>`;
      else if (b.pct != null) extra = `<div class="kpi-detail-list"><div><span>Выручка</span><span>${fmt(b.revenue)}</span></div><div><span>ЧП</span><span>${fmt(b.profit)}</span></div></div>`;
      const main = b.amount != null ? fmt(b.amount) : (b.pct != null ? pct(b.pct) : (b.count != null ? b.count.toLocaleString('ru-RU') : '—'));
      return `<div class="kpi-detail-card"><div class="title">${b.name}</div><div class="big">${main}</div>${extra}</div>`;
    }).join('');
    const totalVal = d.total != null ? (typeof d.total === 'number' && Math.abs(d.total) < 2 && id === 'margin' ? pct(d.total) : (typeof d.total === 'number' ? fmt(d.total) : d.total)) : '';
    return `<h3>${d.title}</h3><div class="kpi-detail-grid">${branches}
      <div class="kpi-detail-card"><div class="title">Итого</div><div class="big">${totalVal}</div></div></div>`;
  }

  function initDatasetToggles() {
    const defs = DATASETS[structureView];
    enabledDatasets = {};
    defs.forEach((d) => { enabledDatasets[d.id] = d.default; });
    const el = $('#datasetToggles');
    if (!el) return;
    el.innerHTML = defs.map((d) => `
      <label class="dataset-toggle"><input type="checkbox" data-ds="${d.id}" ${d.default ? 'checked' : ''}>
      <span style="color:${d.color}">■</span> ${d.label}</label>`).join('');
    el.querySelectorAll('input').forEach((inp) => {
      inp.addEventListener('change', () => {
        enabledDatasets[inp.dataset.ds] = inp.checked;
        renderStructureChart();
      });
    });
  }

  function chartOpts() {
    return {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: { callbacks: { label: (ctx) => `${ctx.dataset.label}: ${fmt(ctx.raw)}` } },
      },
      scales: {
        x: { ticks: { color: '#9c958a' }, grid: { color: 'rgba(197,160,89,0.08)' } },
        y: { ticks: { color: '#9c958a', callback: (v) => (Math.abs(v) >= 1000 ? (v / 1000).toFixed(0) + 'k' : v) }, grid: { color: 'rgba(197,160,89,0.08)' } },
      },
    };
  }

  function renderStructureChart() {
    const old = charts.find((c) => c.canvas?.id === 'chartStructure');
    if (old) { old.destroy(); charts = charts.filter((c) => c !== old); }
    const pnl = DATA?.pnl || {};
    const m = pnl.moyka || {};
    const a = pnl.akkuartova || {};
    const t = pnl.total || {};
    const datasets = [];

    if (structureView === 'branches') {
      const labels = ['Мойка', 'Аккуратова', 'Всего'];
      const locs = [m, a, t];
      if (enabledDatasets.revenue) datasets.push({ label: 'Выручка', data: locs.map((l) => l.revenue?.total || 0), backgroundColor: '#C5A059' });
      if (enabledDatasets.profit) datasets.push({ label: 'Чистая прибыль', data: locs.map((l) => l.profit?.net || 0), backgroundColor: '#81c784' });
      if (enabledDatasets.ttk) datasets.push({ label: 'Фудкост ТТК', data: locs.map((l) => l.foodcost?.ttk?.total || 0), backgroundColor: '#5a7a9a' });
      if (enabledDatasets.actual) datasets.push({ label: 'Фудкост факт', data: locs.map((l) => l.foodcost?.actual?.total || l.cogs?.total || 0), backgroundColor: '#a05252' });
      const canvas = document.getElementById('chartStructure');
      if (canvas) charts.push(new Chart(canvas, { type: 'bar', data: { labels, datasets }, options: chartOpts() }));
      return;
    }

    const labels = ['Бар', 'Кухня', 'Кальяны'];
    if (enabledDatasets.rev_m) datasets.push({ label: 'Выручка Мойка', data: [m.revenue?.bar, m.revenue?.kitchen, m.revenue?.shisha], backgroundColor: '#C5A059' });
    if (enabledDatasets.rev_a) datasets.push({ label: 'Выручка Аккуратова', data: [a.revenue?.bar, a.revenue?.kitchen, a.revenue?.shisha], backgroundColor: '#8e7037' });
    if (enabledDatasets.ttk) datasets.push({ label: 'ТТК', data: [m.foodcost?.ttk?.bar, m.foodcost?.ttk?.kitchen, m.foodcost?.ttk?.shisha].map((v, i) => (v || 0) + [a.foodcost?.ttk?.bar, a.foodcost?.ttk?.kitchen, a.foodcost?.ttk?.shisha][i] || 0), backgroundColor: '#5a7a9a' });
    if (enabledDatasets.actual) datasets.push({ label: 'Факт', data: [m.cogs?.bar, m.cogs?.kitchen, m.cogs?.shisha].map((v, i) => (v || 0) + [a.cogs?.bar, a.cogs?.kitchen, a.cogs?.shisha][i] || 0), backgroundColor: '#a05252' });
    const canvas = document.getElementById('chartStructure');
    if (canvas) charts.push(new Chart(canvas, { type: 'bar', data: { labels, datasets }, options: chartOpts() }));
  }

  function renderWriteoffsChart(data) {
    const wo = data.writeoffs?.byReason?.slice(0, 8) || [];
    const canvas = document.getElementById('chartWriteoffs');
    if (!canvas || !wo.length) return;
    charts.push(new Chart(canvas, {
      type: 'bar',
      data: {
        labels: wo.map((w) => w.reason.slice(0, 28)),
        datasets: [{ label: 'Списания', data: wo.map((w) => w.total), backgroundColor: '#a05252' }],
      },
      options: { ...chartOpts(), indexAxis: 'y' },
    }));
  }

  document.addEventListener('DOMContentLoaded', () => {
    loadData();
    $('#runAnalysisBtn').addEventListener('click', runAnalysis);
    $('#compareMode').addEventListener('change', async () => {
      if (!$('#dashboard').classList.contains('hidden') && DATA) await renderDashboard(DATA);
    });
  });
})();
