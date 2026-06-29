(function (root) {
  'use strict';
  const A = root.OdrAnalyzer;
  if (!A) return;

  function readLoc(ws, row, loc) {
    const col = loc === 'Мойка' ? 4 : 5;
    return A.toNum(A.cellVal(ws, row, col));
  }

  A.parsePnl = function (ws) {
    function buildLoc(loc) {
      const revenueTotal = readLoc(ws, 5, loc);
      const bar = readLoc(ws, 7, loc);
      const kitchen = readLoc(ws, 8, loc);
      const shisha = readLoc(ws, 9, loc);
      const cogsTotal = readLoc(ws, 14, loc);
      const cogsBar = readLoc(ws, 15, loc);
      const cogsKitchen = readLoc(ws, 16, loc);
      const cogsShisha = readLoc(ws, 17, loc);
      const cogsPct = readLoc(ws, 38, loc);
      const overhead = readLoc(ws, 42, loc);
      const payroll = readLoc(ws, 43, loc);
      const rent = readLoc(ws, 53, loc);
      const marketing = readLoc(ws, 65, loc);
      const maintenance = readLoc(ws, 69, loc);
      const taxes = readLoc(ws, 73, loc) ?? readLoc(ws, 75, loc);
      const netProfit = readLoc(ws, 77, loc);
      const netMargin = readLoc(ws, 78, loc);
      const days = readLoc(ws, 93, loc);
      const guests = readLoc(ws, 96, loc);
      const avgCheck = readLoc(ws, 98, loc);
      const revShare = (part) => (revenueTotal && part != null ? part / revenueTotal : null);
      const grossProfit = revenueTotal != null && cogsTotal != null ? revenueTotal - cogsTotal : null;

      return {
        revenue: {
          total: revenueTotal,
          bar, kitchen, shisha,
          shares: { bar: revShare(bar), kitchen: revShare(kitchen), shisha: revShare(shisha) },
        },
        cogs: {
          total: cogsTotal,
          bar: cogsBar, kitchen: cogsKitchen, shisha: cogsShisha,
          pct: cogsPct,
          pctOfRevenue: revenueTotal && cogsTotal != null ? cogsTotal / revenueTotal : null,
        },
        overheads: {
          total: overhead,
          payroll,
          payrollPct: revenueTotal && payroll != null ? payroll / revenueTotal : null,
          rent,
          rentPct: revenueTotal && rent != null ? rent / revenueTotal : null,
          marketing,
          maintenance,
          taxes,
        },
        profit: {
          gross: grossProfit,
          net: netProfit,
          margin: netMargin,
          netPerGuest: guests && netProfit != null ? netProfit / guests : null,
        },
        analytics: { guests, avgCheck, days },
      };
    }

    const moyka = buildLoc('Мойка');
    const akku = buildLoc('Аккуратова');
    const revTotal = (moyka.revenue.total || 0) + (akku.revenue.total || 0);
    const netTotal = (moyka.profit.net || 0) + (akku.profit.net || 0);
    return {
      moyka,
      akkuartova: akku,
      total: {
        revenue: { total: revTotal },
        profit: { net: netTotal, margin: revTotal ? netTotal / revTotal : null },
        analytics: { guests: (moyka.analytics.guests || 0) + (akku.analytics.guests || 0) },
      },
    };
  };
})(typeof window !== 'undefined' ? window : globalThis);
