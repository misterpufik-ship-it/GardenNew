/* global OdrAnalyzer, Chart, ExcelJS */
(function () {
  'use strict';

  const $ = (s) => document.querySelector(s);
  const A = OdrAnalyzer;
  let REPORTS = [];
  let ANALYSES = {};
  let CURRENT = null;
  let PREVIOUS = null;
  let charts = [];

  function fmt(n) { return A.fmtMoney(n); }
  function pct(n) { return A.fmtPct(n); }

  async function loadData() {
    const [repRes, anaRes] = await Promise.all([
      fetch('api/manifest.php').then((r) => r.json()),
      fetch('api/analyze-list.php').then((r) => r.json()),
    ]);
    REPORTS = repRes.reports || [];
    const anaMap = {};
    (anaRes.items || []).forEach((i) => { anaMap[i.month] = i; });
    ANALYSES = anaMap;
    renderTabs();
    const params = new URLSearchParams(location.search);
    const month = params.get('month') || (REPORTS[0]?.month);
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

  function unwrapAnalysis(payload) {
    if (!payload) return null;
    if (payload.analysis && (payload.analysis.pnl || payload.analysis.executive)) {
      return payload.analysis;
    }
    if (payload.pnl || payload.executive) return payload;
    return payload.analysis || null;
  }

  async function fetchCachedAnalysis(month) {
    try {
      const res = await fetch(`api/analyze-get.php?month=${encodeURIComponent(month)}`);
      if (!res.ok) return null;
      const json = await res.json();
      if (!json.ok) return null;
      return unwrapAnalysis(json.data);
    } catch (_) {
      return null;
    }
  }

  function reportForMonth(month) {
    return REPORTS.find((r) => r.month === month);
  }

  async function selectMonth(month) {
    CURRENT = month;
    $$('.month-tab').forEach((b) => b.classList.toggle('active', b.dataset.month === month));
    const rep = reportForMonth(month);
    $('#runAnalysisBtn').disabled = !rep;
    $('#pageSubtitle').textContent = rep
      ? `${A.monthLabel(month)} · ${rep.originalName}`
      : A.monthLabel(month);

    const cached = await fetchCachedAnalysis(month);

    if (cached) {
      await renderDashboard(cached);
      setStatus('Анализ загружен с сервера', 'ok');
    } else if (ANALYSES[month]) {
      setStatus('Кэш найден, но не читается — нажмите «Анализировать» ещё раз', 'err');
      $('#dashboard').classList.add('hidden');
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
      const safeAnalysis = JSON.parse(JSON.stringify(analysis));
      const saveRes = await fetch('api/analyze-save.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ month: rep.month, reportId: rep.id, analysis: safeAnalysis }),
      });
      const saveOut = await saveRes.json().catch(() => ({}));
      if (!saveRes.ok || !saveOut.ok) {
        throw new Error(saveOut.error || 'Не удалось сохранить анализ на сервере');
      }
      ANALYSES[rep.month] = { month: rep.month, generatedAt: new Date().toISOString() };
      renderTabs();
      $$('.month-tab').forEach((b) => b.classList.toggle('active', b.dataset.month === rep.month));
      await renderDashboard(safeAnalysis);
      setStatus('Анализ сохранён на сервере', 'ok');
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
    const json = await res.json();
    return unwrapAnalysis(json.data);
  }

  async function renderDashboard(data) {
    if (!data) return;
    destroyCharts();
    let comparison = null;
    try {
      if ($('#compareMode').checked) {
        PREVIOUS = await loadPrevious(data.meta?.month || CURRENT);
        if (PREVIOUS) comparison = A.compareMonths(data, PREVIOUS);
      }

      const ex = data.executive || {};
      const rec = data.recommendations || {};
    const html = `
      <section class="panel exec-summary">
        <h2>Executive Summary</h2>
        <p class="owner-summary">${rec.ownerSummary || ''}</p>
        <div class="kpi-grid">
          ${kpiCard('Выручка', fmt(ex.revenue), comparison?.revenue)}
          ${kpiCard('Чистая прибыль', fmt(ex.profit), comparison?.profit)}
          ${kpiCard('Маржа', pct(ex.margin), comparison?.margin, true)}
          ${kpiCard('Гости', (ex.guests || 0).toLocaleString('ru-RU'), comparison?.guests)}
          ${kpiCard('Средний чек', fmt(ex.avgCheck))}
          ${kpiCard('Списания', fmt(ex.writeoffs), comparison?.writeoffs)}
          ${kpiCard('Инв. расхожд.', fmt(ex.inventoryAbsVariance))}
          ${kpiCard('Сильнее', ex.strongerLocation || '—')}
        </div>
      </section>

      <section class="panel charts-row">
        <div class="chart-box"><h3>Выручка по точкам</h3><canvas id="chartRevenue"></canvas></div>
        <div class="chart-box"><h3>Структура выручки</h3><canvas id="chartMix"></canvas></div>
        <div class="chart-box wide"><h3>Дневная выручка (чистая)</h3><canvas id="chartDaily"></canvas></div>
        <div class="chart-box"><h3>Выручка по дням недели</h3><canvas id="chartWeekday"></canvas></div>
        <div class="chart-box"><h3>Списания по причинам</h3><canvas id="chartWriteoffs"></canvas></div>
      </section>

      ${comparison ? renderComparison(comparison) : ''}

      <section class="panel two-col">
        <div><h2>Что хорошо</h2><ul class="bullet-list good">${(data.good || []).map((t) => `<li>${t}</li>`).join('') || '<li>—</li>'}</ul></div>
        <div><h2>Главные проблемы</h2><ul class="bullet-list bad">${(data.problems || []).map((t) => `<li>${t}</li>`).join('') || '<li>—</li>'}</ul></div>
      </section>

      <section class="panel">
        <h2>P&amp;L — Мойка vs Аккуратова</h2>
        ${renderPnlTable(data.pnl)}
      </section>

      <section class="panel">
        <h2>Качество данных</h2>
        ${renderDataQuality(data.dataQuality)}
      </section>

      <section class="panel">
        <h2>Рекомендации</h2>
        ${renderRecommendations(rec)}
      </section>

      <section class="panel">
        <h2>Топ-5 дней по выручке — Мойка</h2>
        ${renderDayTable(data.daily?.moyka?.topBest)}
        <h2 class="section-gap">Топ-5 слабых дней — Мойка</h2>
        ${renderDayTable(data.daily?.moyka?.topWorst)}
      </section>

      <section class="panel">
        <h2>Инвентаризации — топ отклонений</h2>
        ${renderInventory(data.inventories)}
      </section>
    `;

    $('#dashboard').innerHTML = html;
    $('#dashboard').classList.remove('hidden');
      renderCharts(data);
    } catch (err) {
      console.error('renderDashboard', err);
      $('#dashboard').innerHTML = `<section class="panel"><h2>Ошибка отображения</h2><p class="msg err">${err.message || err}</p></section>`;
      $('#dashboard').classList.remove('hidden');
    }
  }

  function kpiCard(label, value, cmp, isPct) {
    let delta = '';
    if (cmp?.delta != null) {
      const sign = cmp.delta >= 0 ? '+' : '';
      const d = isPct ? (cmp.delta * 100).toFixed(1) + ' п.п.' : sign + Math.round(cmp.delta).toLocaleString('ru-RU') + ' ₽';
      const cls = cmp.delta >= 0 ? 'up' : 'down';
      delta = `<span class="kpi-delta ${cls}">${d}</span>`;
    }
    return `<div class="kpi-card"><div class="kpi-label">${label}</div><div class="kpi-value">${value}${delta}</div></div>`;
  }

  function renderComparison(cmp) {
    return `<section class="panel"><h2>Месяц к месяцу</h2><table class="data-table"><thead><tr><th>Показатель</th><th>Текущий</th><th>Прошлый</th><th>Δ</th><th>Δ%</th></tr></thead><tbody>
      ${rowCmp('Выручка', cmp.revenue)}
      ${rowCmp('Прибыль', cmp.profit)}
      ${rowCmp('Гости', cmp.guests)}
    </tbody></table></section>`;
  }

  function rowCmp(label, o) {
    if (!o) return '';
    return `<tr><td>${label}</td><td>${typeof o.current === 'number' && label === 'Гости' ? o.current.toLocaleString('ru-RU') : fmt(o.current)}</td><td>${typeof o.previous === 'number' && label === 'Гости' ? (o.previous || 0).toLocaleString('ru-RU') : fmt(o.previous)}</td><td>${o.delta != null ? Math.round(o.delta).toLocaleString('ru-RU') : '—'}</td><td>${o.pct != null ? pct(o.pct) : '—'}</td></tr>`;
  }

  function renderPnlTable(pnl) {
    if (!pnl) return '<p class="msg">Нет данных ОДР</p>';
    const rows = [
      ['Выручка', pnl.moyka?.revenue?.total, pnl.akkuartova?.revenue?.total],
      ['Бар', pnl.moyka?.revenue?.bar, pnl.akkuartova?.revenue?.bar],
      ['Кухня', pnl.moyka?.revenue?.kitchen, pnl.akkuartova?.revenue?.kitchen],
      ['Кальяны', pnl.moyka?.revenue?.shisha, pnl.akkuartova?.revenue?.shisha],
      ['Себестоимость', pnl.moyka?.cogs?.total, pnl.akkuartova?.cogs?.total],
      ['ФОТ', pnl.moyka?.overheads?.payroll, pnl.akkuartova?.overheads?.payroll],
      ['Аренда+КУ', pnl.moyka?.overheads?.rent, pnl.akkuartova?.overheads?.rent],
      ['Чистая прибыль', pnl.moyka?.profit?.net, pnl.akkuartova?.profit?.net],
      ['Маржа', pnl.moyka?.profit?.margin, pnl.akkuartova?.profit?.margin],
      ['Гости', pnl.moyka?.analytics?.guests, pnl.akkuartova?.analytics?.guests],
    ];
    return `<table class="data-table"><thead><tr><th>Статья</th><th>Мойка</th><th>Аккуратова</th></tr></thead><tbody>
      ${rows.map(([l, a, b]) => `<tr><td>${l}</td><td>${l.includes('Маржа') ? pct(a) : (l === 'Гости' ? (a || 0).toLocaleString('ru-RU') : fmt(a))}</td><td>${l.includes('Маржа') ? pct(b) : (l === 'Гости' ? (b || 0).toLocaleString('ru-RU') : fmt(b))}</td></tr>`).join('')}
    </tbody></table>`;
  }

  function renderDataQuality(dq) {
    if (!dq) return '';
    const errs = (dq.errors || []).slice(0, 20);
    const rec = (dq.reconciliations || []).concat(dq.warnings || []);
    return `
      <p>Ошибок формул: <strong>${errs.length}</strong>${dq.missingSheets?.length ? ` · Не найдены листы: ${dq.missingSheets.join(', ')}` : ''}</p>
      ${errs.length ? `<table class="data-table compact"><thead><tr><th>Лист</th><th>Ячейка</th><th>Ошибка</th></tr></thead><tbody>${errs.map((e) => `<tr><td>${e.sheet}</td><td>${e.cell}</td><td class="bad">${e.error}</td></tr>`).join('')}</tbody></table>` : ''}
      ${rec.length ? `<ul class="bullet-list">${rec.map((r) => `<li>${typeof r === 'string' ? r : r.message}</li>`).join('')}</ul>` : ''}`;
  }

  function renderRecommendations(rec) {
    return `
      <h3>Предупреждения</h3><ul class="bullet-list bad">${(rec.warnings || []).map((w) => `<li>${w.text}</li>`).join('') || '<li>—</li>'}</ul>
      <h3 class="section-gap">Возможности</h3><ul class="bullet-list good">${(rec.opportunities || []).map((o) => `<li>${o.text}</li>`).join('') || '<li>—</li>'}</ul>
      <h3 class="section-gap">План на 7 дней</h3><ul class="bullet-list">${(rec.action7 || []).map((t) => `<li>${t}</li>`).join('') || '<li>—</li>'}</ul>
      <h3 class="section-gap">План на 30 дней</h3><ul class="bullet-list">${(rec.action30 || []).map((t) => `<li>${t}</li>`).join('') || '<li>—</li>'}</ul>`;
  }

  function renderDayTable(days) {
    if (!days?.length) return '<p class="msg">—</p>';
    return `<table class="data-table compact"><thead><tr><th>Дата</th><th>День</th><th>Выручка</th><th>Гости</th><th>Кухня %</th></tr></thead><tbody>
      ${days.map((d) => `<tr><td>${d.date}</td><td>${d.weekday || ''}</td><td>${fmt(d.netRevenue)}</td><td>${d.guests || '—'}</td><td>${pct(d.kitchenShare)}</td></tr>`).join('')}
    </tbody></table>`;
  }

  function renderInventory(invs) {
    if (!invs?.length) return '<p class="msg">—</p>';
    return invs.map((inv) => `
      <h3>${inv.sheet} · ${inv.location}</h3>
      <p>Излишки: ${fmt(inv.totals?.surplusSum)} · Недостача: ${fmt(inv.totals?.shortageSum)} · Абс. расхождение: ${fmt(inv.absoluteVariance)}</p>
      <table class="data-table compact"><thead><tr><th>Позиция</th><th>Излишек</th><th>Недостача</th></tr></thead><tbody>
        ${inv.topAbs.slice(0, 10).map((i) => `<tr><td>${i.name}</td><td>${fmt(i.surplus)}</td><td>${fmt(i.shortage)}</td></tr>`).join('')}
      </tbody></table>`).join('');
  }

  function chartOpts() {
    return {
      responsive: true,
      plugins: { legend: { labels: { color: '#c5a059' } } },
      scales: {
        x: { ticks: { color: '#9c958a' }, grid: { color: 'rgba(197,160,89,0.1)' } },
        y: { ticks: { color: '#9c958a' }, grid: { color: 'rgba(197,160,89,0.1)' } },
      },
    };
  }

  function renderCharts(data) {
    const gold = '#C5A059';
    const pnl = data.pnl || {};
    const c1 = document.getElementById('chartRevenue');
    if (c1) {
      charts.push(new Chart(c1, {
        type: 'bar',
        data: {
          labels: ['Мойка', 'Аккуратова'],
          datasets: [{ label: 'Выручка', data: [pnl.moyka?.revenue?.total, pnl.akkuartova?.revenue?.total], backgroundColor: [gold, '#8e7037'] }],
        },
        options: chartOpts(),
      }));
    }

    const c2 = document.getElementById('chartMix');
    if (c2 && pnl.moyka) {
      charts.push(new Chart(c2, {
        type: 'doughnut',
        data: {
          labels: ['Бар', 'Кухня', 'Кальяны'],
          datasets: [{
            data: [pnl.moyka.revenue?.bar, pnl.moyka.revenue?.kitchen, pnl.moyka.revenue?.shisha],
            backgroundColor: ['#C5A059', '#6b8e4e', '#4a6fa5'],
          }],
        },
        options: { plugins: { legend: { labels: { color: '#c5a059' } } } },
      }));
    }

    const days = data.daily?.moyka?.days || [];
    const c3 = document.getElementById('chartDaily');
    if (c3 && days.length) {
      charts.push(new Chart(c3, {
        type: 'line',
        data: {
          labels: days.map((d) => d.date.slice(5)),
          datasets: [
            { label: 'Мойка', data: days.map((d) => d.netRevenue), borderColor: gold, tension: 0.2 },
            { label: 'Аккуратова', data: (data.daily?.akkuartova?.days || []).map((d) => d.netRevenue), borderColor: '#6b8e4e', tension: 0.2 },
          ],
        },
        options: chartOpts(),
      }));
    }

    const wd = data.daily?.moyka?.byWeekday || {};
    const order = ['пн', 'вт', 'ср', 'чт', 'пт', 'сб', 'вс'];
    const c4 = document.getElementById('chartWeekday');
    if (c4) {
      charts.push(new Chart(c4, {
        type: 'bar',
        data: {
          labels: order,
          datasets: [{ label: 'Ср. выручка', data: order.map((w) => wd[w]?.avgRevenue || 0), backgroundColor: gold }],
        },
        options: chartOpts(),
      }));
    }

    const wo = data.writeoffs?.byReason?.slice(0, 8) || [];
    const c5 = document.getElementById('chartWriteoffs');
    if (c5 && wo.length) {
      charts.push(new Chart(c5, {
        type: 'bar',
        data: {
          labels: wo.map((w) => w.reason.slice(0, 24)),
          datasets: [{ label: 'Списания', data: wo.map((w) => w.total), backgroundColor: '#a05252' }],
        },
        options: { ...chartOpts(), indexAxis: 'y' },
      }));
    }
  }

  function $$(sel) { return document.querySelectorAll(sel); }

  document.addEventListener('DOMContentLoaded', () => {
    loadData();
    $('#runAnalysisBtn').addEventListener('click', runAnalysis);
    $('#compareMode').addEventListener('change', async () => {
      if ($('#dashboard').classList.contains('hidden') || !CURRENT) return;
      const cached = await fetchCachedAnalysis(CURRENT);
      if (cached) await renderDashboard(cached);
    });
  });
})();
