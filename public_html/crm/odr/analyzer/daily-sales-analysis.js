(function (root) {
  'use strict';
  const A = root.OdrAnalyzer;
  if (!A) return;

  const WEEKDAYS = ['вс', 'пн', 'вт', 'ср', 'чт', 'пт', 'сб'];

  A.parseDailySheet = function (ws, location) {
    const days = [];
    const max = ws.rowCount || 400;
    for (let r = 3; r <= max; r++) {
      const dateRaw = A.cellVal(ws, r, 1);
      const d = A.parseDate(dateRaw);
      if (!d) continue;
      const fact = A.toNum(A.cellVal(ws, r, 4));
      if (fact == null) continue;
      const bonus1 = A.toNum(A.cellVal(ws, r, 10)) || 0;
      const bonus2 = A.toNum(A.cellVal(ws, r, 11)) || 0;
      const bonuses = bonus1 + bonus2;
      const guests = A.toNum(A.cellVal(ws, r, 14));
      const bar = A.toNum(A.cellVal(ws, r, 17));
      const kitchen = A.toNum(A.cellVal(ws, r, 18));
      const shisha = A.toNum(A.cellVal(ws, r, 20));
      const cash = (A.toNum(A.cellVal(ws, r, 6)) || 0) + (A.toNum(A.cellVal(ws, r, 7)) || 0);
      const card = (A.toNum(A.cellVal(ws, r, 8)) || 0) + (A.toNum(A.cellVal(ws, r, 9)) || 0);
      const netRevenue = fact - bonuses;
      days.push({
        date: d.toISOString().slice(0, 10),
        weekday: A.cellVal(ws, r, 2) || WEEKDAYS[d.getDay()],
        plan: A.toNum(A.cellVal(ws, r, 3)),
        fact,
        planPct: A.toNum(A.cellVal(ws, r, 5)),
        cash, card, bonuses,
        netRevenue,
        guests,
        avgCheck: A.toNum(A.cellVal(ws, r, 15)),
        avgCheckNet: guests ? netRevenue / guests : null,
        bar, kitchen, shisha,
        barShare: fact && bar != null ? bar / fact : null,
        kitchenShare: fact && kitchen != null ? kitchen / fact : null,
        shishaShare: fact && shisha != null ? shisha / fact : null,
      });
    }

    const byWeekday = {};
    WEEKDAYS.forEach((w) => { byWeekday[w] = { revenue: 0, guests: 0, count: 0, avgCheck: 0 }; });
    days.forEach((day) => {
      const w = day.weekday || WEEKDAYS[new Date(day.date).getDay()];
      if (!byWeekday[w]) byWeekday[w] = { revenue: 0, guests: 0, count: 0 };
      byWeekday[w].revenue += day.netRevenue || 0;
      byWeekday[w].guests += day.guests || 0;
      byWeekday[w].count += 1;
    });
    Object.keys(byWeekday).forEach((w) => {
      const b = byWeekday[w];
      b.avgRevenue = b.count ? b.revenue / b.count : 0;
      b.avgGuests = b.count ? b.guests / b.count : 0;
      b.avgCheck = b.guests ? b.revenue / b.guests : 0;
    });

    const sorted = [...days].sort((a, b) => (b.netRevenue || 0) - (a.netRevenue || 0));
    const totalNet = days.reduce((s, d) => s + (d.netRevenue || 0), 0);
    const totalPlan = days.reduce((s, d) => s + (d.plan || 0), 0);
    const totalGuests = days.reduce((s, d) => s + (d.guests || 0), 0);

    const avgDaily = days.length ? totalNet / days.length : 0;
    const avgGuestsDay = days.length ? totalGuests / days.length : 0;

    return {
      location,
      days,
      stats: {
        daysCount: days.length,
        totalNet,
        totalPlan,
        planAchievement: totalPlan ? totalNet / totalPlan : null,
        totalGuests,
        avgDailyRevenue: avgDaily,
        avgDailyGuests: avgGuestsDay,
        avgCheck: totalGuests ? totalNet / totalGuests : null,
        avgKitchenShare: days.length ? days.reduce((s, d) => s + (d.kitchenShare || 0), 0) / days.length : null,
      },
      byWeekday,
      topBest: sorted.slice(0, 5),
      topWorst: sorted.slice(-5).reverse(),
      weakDays: days.filter((d) =>
        (d.netRevenue != null && d.netRevenue < avgDaily * 0.7)
        || (d.guests != null && d.guests < avgGuestsDay * 0.7)
        || (d.kitchenShare != null && d.kitchenShare < 0.18)
      ).slice(0, 10),
    };
  };
})(typeof window !== 'undefined' ? window : globalThis);
