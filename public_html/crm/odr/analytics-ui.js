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
  let structureMetric = 'revenue';
  let incomeLoc = 'total';
  let expenseLoc = 'total';

  const DIVISIONS = [
    { key: 'bar', label: 'Бар', color: '#C5A059' },
    { key: 'kitchen', label: 'Кухня', color: '#c49a6a' },
    { key: 'shisha', label: 'Кальяны', color: '#8e7037' },
  ];

  const STRUCTURE_METRICS = [
    { id: 'revenue', label: 'Выручка' },
    { id: 'ttk', label: 'Расход по ТТК' },
    { id: 'actual', label: 'Факт COGS' },
  ];

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

  function subSection(title, body, open) {
    return `<details class="collapsible nested"${open ? ' open' : ''}>
      <summary>${title}</summary>
      <div class="collapsible-body">${body}</div>
    </details>`;
  }

  function ensureStockFlow(data) {
    if (!data?.stock?.rows?.length || !data.pnl || !data.meta?.month) return;
    if (!data.stock.columns && data.stock.months) {
      data.stock.columns = data.stock.months.map((label, i) => ({
        col: i + 2,
        label,
        date: A.parseStockDateLabel(label),
      }));
    }
    if (data.stock.rows) {
      data.stock.rows.forEach((row) => {
        if (!row.balances && row.values && data.stock.columns) {
          row.balances = data.stock.columns.map((c, i) => ({ ...c, value: row.values[i] ?? null }));
        }
        if (!row.branch || !row.division) {
          const meta = A.parseStockRowMeta(row.name);
          row.branch = row.branch || meta.branch;
          row.division = row.division || meta.division;
        }
      });
    }
    if (!data.stock.flow && data.stock.columns) {
      const flow = A.buildStockFlow(data.stock.rows, data.stock.columns, data.meta.month);
      data.stock.flow = A.enrichStockFlow(flow, data.pnl);
    } else if (data.stock.flow && !data.stock.flow.lines?.[0]?.ttk) {
      data.stock.flow = A.enrichStockFlow(data.stock.flow, data.pnl);
    }
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

  function buildPnlSections(pnl) {
    const m = pnl.moyka || {};
    const a = pnl.akkuartova || {};
    const mk = (title, open) => ({ title, open: !!open, rows: [] });
    const add = (sec, label, vm, va, opts = {}) => {
      if (!opts.force && vm == null && va == null) return;
      sec.rows.push({ label, vm, va, ...opts });
    };

    const income = mk('Доходы', true);
    add(income, 'ДОХОДЫ ВСЕГО', m.revenue?.total, a.revenue?.total, { header: true, force: true });
    add(income, 'Бар', m.revenue?.bar, a.revenue?.bar);
    add(income, 'Кухня', m.revenue?.kitchen, a.revenue?.kitchen);
    add(income, 'Кальяны', m.revenue?.shisha, a.revenue?.shisha);
    add(income, 'Банкеты', m.revenue?.banquets, a.revenue?.banquets);
    add(income, 'Прочее', m.revenue?.other, a.revenue?.other);

    const cogs = mk('Себестоимость и ТТК');
    add(cogs, 'СЕБЕСТОИМОСТЬ (факт)', m.cogs?.total, a.cogs?.total, { header: true, force: true });
    add(cogs, '  Себест. бар', m.cogs?.bar, a.cogs?.bar);
    add(cogs, '  Себест. кухня', m.cogs?.kitchen, a.cogs?.kitchen);
    add(cogs, '  Себест. кальяны', m.cogs?.shisha, a.cogs?.shisha);
    add(cogs, 'Факт себест., %', m.cogs?.pct, a.cogs?.pct, { isMargin: true });
    add(cogs, 'ПО ТТК', m.foodcost?.ttk?.total, a.foodcost?.ttk?.total, { header: true });
    add(cogs, '  ТТК бар', m.foodcost?.ttk?.bar, a.foodcost?.ttk?.bar);
    add(cogs, '  ТТК кухня', m.foodcost?.ttk?.kitchen, a.foodcost?.ttk?.kitchen);
    add(cogs, '  ТТК кальяны', m.foodcost?.ttk?.shisha, a.foodcost?.ttk?.shisha);
    add(cogs, 'Отклонение факт − ТТК', m.foodcost?.variance, a.foodcost?.variance);

    const overhead = mk('Накладные расходы');
    add(overhead, 'НАКЛАДНЫЕ ЗАТРАТЫ', m.overheads?.total, a.overheads?.total, { header: true, force: true });
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
      add(overhead, lm?.label || la?.label || key, lm?.amount, la?.amount);
    });

    const profit = mk('Прибыль и гости');
    add(profit, 'Валовая прибыль', m.profit?.gross, a.profit?.gross, { header: true });
    add(profit, 'Чистая прибыль', m.profit?.net, a.profit?.net, { header: true, force: true });
    add(profit, 'Маржа', m.profit?.margin, a.profit?.margin, { isMargin: true, force: true });
    add(profit, 'Гости', m.analytics?.guests, a.analytics?.guests, { isGuests: true, force: true });
    add(profit, 'Средний чек', m.analytics?.avgCheck, a.analytics?.avgCheck);

    return [income, cogs, overhead, profit];
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

  function renderPnlRowsTable(rows, pnl) {
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

  function renderPnlTable(pnl) {
    if (!pnl) return '<p class="msg">Нет данных ОДР</p>';
    return buildPnlSections(pnl).map((sec) =>
      subSection(sec.title, renderPnlRowsTable(sec.rows, pnl), sec.open)
    ).join('');
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

  function renderInvSheet(inv) {
    const top = inv.topAbs || [];
    if (!top.length && !inv.totals?.surplusSum && !inv.totals?.shortageSum) {
      return `<p class="msg">Нет расхождений по позициям · лист «${inv.sheet}»</p>`;
    }
    return `<p class="inv-totals">Позиций: <strong>${inv.itemCount || top.length}</strong> · Излишки: ${fmt(inv.totals?.surplusSum)} · Недостача: ${fmt(inv.totals?.shortageSum)} · Абс.: ${fmt(inv.absoluteVariance)}</p>
      <table class="data-table compact"><thead><tr><th>Позиция</th><th>Факт</th><th>Излишек</th><th>Недостача</th></tr></thead><tbody>
        ${top.slice(0, 15).map((i) => `<tr><td>${i.name}</td><td>${fmt(i.factSum)}</td><td>${fmt(i.surplus)}</td><td>${fmt(i.shortage)}</td></tr>`).join('')}
      </tbody></table>`;
  }

  function renderInventory(invs) {
    if (!invs?.length) return '<p class="msg">Нет листов инвентаризации — нажмите «Анализировать»</p>';

    const branches = [
      { key: 'moyka', label: 'Адмиралтейская · Мойка', loc: 'Мойка' },
      { key: 'akku', label: 'Удельная · Аккуратова', loc: 'Аккуратова' },
    ];
    const divOrder = [
      { key: 'bar', label: 'Бар' },
      { key: 'kitchen', label: 'Кухня' },
      { key: 'shisha', label: 'Кальяны (КМ)' },
    ];

    return branches.map((br) => {
      const sheets = divOrder.map((div) => {
        const inv = invs.find((i) => i.location === br.loc && (i.division === div.key || i.category === div.label || (div.key === 'shisha' && /кальян|км/i.test(i.sheet))));
        if (!inv) return subSection(div.label, '<p class="msg">Лист не найден в файле</p>');
        return subSection(`${div.label} · ${inv.sheet}`, renderInvSheet(inv));
      }).join('');
      return subSection(br.label, sheets, true);
    }).join('');
  }

  function renderWriteoffsSection() {
    return [
      subSection('Общие списания', '<div class="chart-box"><canvas id="chartWriteoffsTotal"></canvas></div>', true),
      subSection('Мойка · Адмиралтейская', '<div class="chart-box"><canvas id="chartWriteoffsMoyka"></canvas></div>'),
      subSection('Удельная · Аккуратова', '<div class="chart-box"><canvas id="chartWriteoffsAkku"></canvas></div>'),
    ].join('');
  }

  function renderStockBalances(stock) {
    if (!stock?.rows?.length) return '<p class="msg">Лист «Остатки на складах» не найден</p>';
    const period = stock.flow?.period;
    const header = period?.openLabel && period?.closeLabel
      ? `<p class="stock-period">Период: ${period.openLabel} → ${period.closeLabel}</p>` : '';
    return `${header}<div class="wrap"><table class="data-table compact">
      <thead><tr><th>Склад</th>${(stock.columns || []).map((c) => `<th>${c.label.replace('На ', '').replace(', руб', '')}</th>`).join('')}</tr></thead>
      <tbody>${stock.rows.map((r) => `<tr>
        <td>${r.name}</td>
        ${(stock.columns || []).map((c) => {
          const b = r.balances?.find((x) => x.col === c.col);
          return `<td>${fmt(b?.value)}</td>`;
        }).join('')}
      </tr>`).join('')}
      </tbody></table></div>`;
  }

  function renderStockFlow(stock) {
    const flow = stock?.flow;
    if (!flow?.lines?.length) return '<p class="msg">Нет данных для расчёта движения запасов</p>';

    const branches = [
      { key: 'moyka', label: 'Адмиралтейская · Мойка' },
      { key: 'akku', label: 'Удельная · Аккуратова' },
    ];
    const divOrder = ['bar', 'kitchen', 'shisha'];
    const divLabel = { bar: 'Бар', kitchen: 'Кухня', shisha: 'Кальяны' };

    return branches.map((br) => {
      const blocks = divOrder.map((div) => {
        const line = flow.lines.find((l) => l.branch === br.key && l.division === div);
        if (!line) return subSection(divLabel[div], '<p class="msg">—</p>');
        const body = `<table class="data-table compact stock-flow-table">
          <tbody>
            <tr><td>Остаток на начало месяца</td><td>${fmt(line.opening)}</td></tr>
            <tr><td>Расход по ТТК</td><td>${fmt(line.ttk)}</td></tr>
            <tr><td>Оценка закупок (конец − начало + ТТК)</td><td>${fmt(line.purchases)}</td></tr>
            <tr><td>Остаток на конец периода</td><td>${fmt(line.closing)}</td></tr>
            <tr><td>Факт COGS (ОДР)</td><td>${fmt(line.fact)}</td></tr>
            <tr><td>Изменение запаса</td><td>${fmt(line.delta)}</td></tr>
          </tbody>
        </table>
        <p class="stock-insight">${line.insight || ''}</p>`;
        return subSection(divLabel[div], body);
      }).join('');
      return subSection(br.label, blocks);
    }).join('');
  }

  function renderStockDashboard(stock) {
    return [
      subSection('Остатки по месяцам (из листа)', renderStockBalances(stock)),
      subSection('Движение запасов: начало → ТТК → закупки → конец', renderStockFlow(stock), true),
    ].join('');
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

      ensureStockFlow(data);

      const html = [
        section('Сводка и KPI', renderKpiGrid(ex, comparison, details), true),
        section('Филиалы — выручка и чистая прибыль', renderBranchHero(data.pnl || {})),
        section('Структура выручки и фудкост (бар / кухня / кальяны)', `
          <div class="chart-toolbar">
            <div class="view-tabs" id="structureMetricTabs">
              ${STRUCTURE_METRICS.map((m) => `<button type="button" class="view-tab${structureMetric === m.id ? ' active' : ''}" data-metric="${m.id}">${m.label}</button>`).join('')}
            </div>
          </div>
          <p class="chart-hint">Столбец = филиал, внутри — три подразделения: бар, кухня, кальяны</p>
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
        section('Списания', renderWriteoffsSection()),
        section('Инвентаризации', renderInventory(data.inventories)),
        section('Остатки на складах и движение запасов', renderStockDashboard(data.stock)),
        section('Качество данных', renderDataQuality(data.dataQuality)),
        section('Рекомендации', renderRecommendations(rec)),
      ].join('');

      $('#dashboard').innerHTML = html;
      $('#dashboard').classList.remove('hidden');
      bindDashboardEvents(details);
      renderStructureChart();
      renderWriteoffsCharts(data);
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

    $('#structureMetricTabs')?.addEventListener('click', (e) => {
      const btn = e.target.closest('.view-tab');
      if (!btn) return;
      $$('#structureMetricTabs .view-tab').forEach((b) => b.classList.remove('active'));
      btn.classList.add('active');
      structureMetric = btn.dataset.metric;
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

  function chartOpts(stacked) {
    return {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: stacked, position: 'bottom', labels: { color: '#9c958a', boxWidth: 12 } },
        tooltip: { callbacks: { label: (ctx) => `${ctx.dataset.label}: ${fmt(ctx.raw)}` } },
      },
      scales: {
        x: { stacked, ticks: { color: '#9c958a' }, grid: { color: 'rgba(197,160,89,0.08)' } },
        y: { stacked, ticks: { color: '#9c958a', callback: (v) => (Math.abs(v) >= 1000 ? (v / 1000).toFixed(0) + 'k' : v) }, grid: { color: 'rgba(197,160,89,0.08)' } },
      },
    };
  }

  function metricValue(loc, metric, divKey) {
    if (metric === 'revenue') return loc.revenue?.[divKey] || 0;
    if (metric === 'ttk') return loc.foodcost?.ttk?.[divKey] || 0;
    return loc.cogs?.[divKey] || 0;
  }

  function renderStructureChart() {
    const old = charts.find((c) => c.canvas?.id === 'chartStructure');
    if (old) { old.destroy(); charts = charts.filter((c) => c !== old); }
    const pnl = DATA?.pnl || {};
    const m = pnl.moyka || {};
    const a = pnl.akkuartova || {};
    const labels = ['Мойка', 'Аккуратова'];
    const locs = [m, a];
    const datasets = DIVISIONS.map((div) => ({
      label: div.label,
      data: locs.map((loc) => metricValue(loc, structureMetric, div.key)),
      backgroundColor: div.color,
      stack: 'divisions',
    }));
    const canvas = document.getElementById('chartStructure');
    if (canvas) charts.push(new Chart(canvas, { type: 'bar', data: { labels, datasets }, options: chartOpts(true) }));
  }

  function makeWriteoffChart(canvasId, items, valueKey) {
    const canvas = document.getElementById(canvasId);
    if (!canvas || !items.length) return;
    charts.push(new Chart(canvas, {
      type: 'bar',
      data: {
        labels: items.map((w) => w.reason.slice(0, 32)),
        datasets: [{ label: 'Списания', data: items.map((w) => w[valueKey]), backgroundColor: '#a05252' }],
      },
      options: { ...chartOpts(false), indexAxis: 'y' },
    }));
  }

  function renderWriteoffsCharts(data) {
    const wo = data.writeoffs?.byReason || [];
    makeWriteoffChart('chartWriteoffsTotal', wo.slice(0, 10), 'total');
    makeWriteoffChart('chartWriteoffsMoyka', wo.filter((w) => w.moyka > 0).slice(0, 10), 'moyka');
    makeWriteoffChart('chartWriteoffsAkku', wo.filter((w) => w.akku > 0).slice(0, 10), 'akku');
  }

  document.addEventListener('DOMContentLoaded', () => {
    loadData();
    $('#runAnalysisBtn').addEventListener('click', runAnalysis);
    $('#compareMode').addEventListener('change', async () => {
      if (!$('#dashboard').classList.contains('hidden') && DATA) await renderDashboard(DATA);
    });
  });
})();
