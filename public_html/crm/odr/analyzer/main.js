(function (root) {
  'use strict';
  const A = root.OdrAnalyzer;
  if (!A) return;

  A.runAnalysis = async function (arrayBuffer, meta) {
    const wb = new ExcelJS.Workbook();
    await wb.xlsx.load(arrayBuffer);

    const sheets = A.findSheets(wb);
    const sheetNames = [];
    wb.eachSheet((ws) => sheetNames.push({ name: ws.name, rows: ws.rowCount, cols: ws.columnCount }));

    const errors = A.scanErrors(wb);
    const pnl = sheets.odr ? A.parsePnl(sheets.odr) : null;

    const daily = {};
    if (sheets.dailyMoyka) daily.moyka = A.parseDailySheet(sheets.dailyMoyka, 'Мойка');
    if (sheets.dailyAkku) daily.akkuartova = A.parseDailySheet(sheets.dailyAkku, 'Аккуратова');

    const writeoffs = sheets.writeoffs ? A.parseWriteoffs(sheets.writeoffs) : null;
    const stock = sheets.stock ? A.parseStock(sheets.stock) : null;
    const transfers = sheets.transfers ? A.parseTransfers(sheets.transfers) : null;

    const inventories = [];
    const invMap = [
      ['invBarMoyka', 'Мойка', 'Бар б/а'],
      ['invBarAkku', 'Аккуратова', 'Бар б/а'],
      ['invKmMoyka', 'Мойка', 'Кальяны'],
      ['invKmAkku', 'Аккуратова', 'Кальяны'],
      ['invKitchenMoyka', 'Мойка', 'Кухня'],
      ['invKitchenAkku', 'Аккуратова', 'Кухня'],
    ];
    invMap.forEach(([key, loc, cat]) => {
      if (sheets[key]) inventories.push(A.parseInventorySheet(sheets[key], loc, cat));
    });

    const analysis = {
      meta: {
        fileName: meta.fileName || '',
        month: meta.month || '',
        periodLabel: A.monthLabel(meta.month),
        analyzedAt: new Date().toISOString(),
        sheetNames,
      },
      dataQuality: { errors },
      pnl,
      daily,
      writeoffs,
      stock,
      transfers,
      inventories,
    };

    const validation = A.validateData(analysis, sheets);
    analysis.dataQuality.reconciliations = validation.issues;
    analysis.dataQuality.missingSheets = validation.missingSheets;
    if (stock?.warnings?.length) {
      analysis.dataQuality.warnings = stock.warnings;
    }

    analysis.executive = A.buildExecutive(analysis);
    analysis.recommendations = A.buildRecommendations(analysis);
    analysis.problems = A.buildProblems(analysis);
    analysis.good = A.buildGoodPoints(analysis);

    return analysis;
  };

  A.buildExecutive = function (analysis) {
    const pnl = analysis.pnl || {};
    const m = pnl.moyka || {};
    const a = pnl.akkuartova || {};
    const rev = (m.revenue?.total || 0) + (a.revenue?.total || 0);
    const profit = (m.profit?.net || 0) + (a.profit?.net || 0);
    const guests = (m.analytics?.guests || 0) + (a.analytics?.guests || 0);
    const invAbs = (analysis.inventories || []).reduce((s, i) => s + (i.absoluteVariance || 0), 0);
    const wo = analysis.writeoffs?.total || 0;

    return {
      revenue: rev,
      profit,
      margin: rev ? profit / rev : null,
      guests,
      avgCheck: guests ? rev / guests : null,
      payrollPctMoyka: m.overheads?.payrollPct,
      payrollPctAkku: a.overheads?.payrollPct,
      cogsPctMoyka: m.cogs?.pctOfRevenue,
      cogsPctAkku: a.cogs?.pctOfRevenue,
      writeoffs: wo,
      writeoffsPct: rev ? wo / rev : null,
      inventoryAbsVariance: invAbs,
      planAchievementMoyka: analysis.daily?.moyka?.stats?.planAchievement,
      planAchievementAkku: analysis.daily?.akkuartova?.stats?.planAchievement,
      strongerLocation: (m.profit?.net || 0) >= (a.profit?.net || 0) ? 'Мойка' : 'Аккуратова',
      weakerLocation: (m.profit?.net || 0) >= (a.profit?.net || 0) ? 'Аккуратова' : 'Мойка',
    };
  };

  A.buildProblems = function (analysis) {
    const out = [];
    (analysis.recommendations?.warnings || []).slice(0, 5).forEach((w) => out.push(w.text));
    (analysis.dataQuality?.errors || []).slice(0, 3).forEach((e) => out.push(`Ошибка ${e.error} на ${e.sheet}!${e.cell}`));
    return out.slice(0, 5);
  };

  A.buildGoodPoints = function (analysis) {
    const out = [];
    const ex = analysis.executive || {};
    if (ex.planAchievementMoyka != null && ex.planAchievementMoyka >= 0.95) {
      out.push(`Мойка выполнила план на ${A.fmtPct(ex.planAchievementMoyka)}`);
    }
    if (ex.margin != null && ex.margin >= 0.1) {
      out.push(`Совокупная маржа ${A.fmtPct(ex.margin)} — здоровый уровень`);
    }
    if ((analysis.dataQuality?.errors || []).length === 0) {
      out.push('Критических ошибок формул в файле не найдено');
    }
    return out;
  };

  A.compareMonths = function (current, previous) {
    if (!current || !previous) return null;
    const c = current.executive || {};
    const p = previous.executive || {};
    const delta = (a, b) => (a != null && b != null ? a - b : null);
    const pct = (a, b) => (a != null && b && b !== 0 ? (a - b) / Math.abs(b) : null);
    return {
      revenue: { current: c.revenue, previous: p.revenue, delta: delta(c.revenue, p.revenue), pct: pct(c.revenue, p.revenue) },
      profit: { current: c.profit, previous: p.profit, delta: delta(c.profit, p.profit), pct: pct(c.profit, p.profit) },
      margin: { current: c.margin, previous: p.margin, delta: delta(c.margin, p.margin) },
      guests: { current: c.guests, previous: p.guests, delta: delta(c.guests, p.guests), pct: pct(c.guests, p.guests) },
      writeoffs: { current: c.writeoffs, previous: p.writeoffs, delta: delta(c.writeoffs, p.writeoffs) },
    };
  };
})(typeof window !== 'undefined' ? window : globalThis);
