(function (root) {
  'use strict';
  const A = root.OdrAnalyzer;
  if (!A) return;

  function readLoc(ws, row, loc) {
    const col = loc === 'Мойка' ? 4 : 5;
    return A.toNum(A.cellVal(ws, row, col));
  }

  function pct(revenue, val) {
    return revenue && val != null ? val / revenue : null;
  }

  function item(label, amount, revenue, group, key) {
    if (amount == null || amount === 0) return null;
    return { key: key || label, label, amount, pct: pct(revenue, amount), group };
  }

  const FLOW_SKIP_KEYS = new Set(['fot_uk', 'fot_acc', 'fot_shift', 'fot_var', 'rent_room']);

  function buildExpenseFlowItems(cogsTotal, overheadTotal, revenueTotal) {
    const items = [];
    if (cogsTotal != null) {
      items.push({
        key: 'cogs',
        label: 'Себестоимость (факт)',
        amount: cogsTotal,
        pct: pct(revenueTotal, cogsTotal),
        group: 'cogs',
      });
    }
    if (overheadTotal != null) {
      items.push({
        key: 'overheads',
        label: 'Накладные затраты',
        amount: overheadTotal,
        pct: pct(revenueTotal, overheadTotal),
        group: 'overhead',
      });
    }
    return items;
  }

  function buildLoc(ws, loc) {
    const revenueTotal = readLoc(ws, 5, loc);
    const totalExpenses = readLoc(ws, 13, loc);
    const bar = readLoc(ws, 7, loc);
    const kitchen = readLoc(ws, 8, loc);
    const shisha = readLoc(ws, 9, loc);
    const banquets = readLoc(ws, 10, loc);
    const otherRev = readLoc(ws, 11, loc);

    const cogsTotal = readLoc(ws, 14, loc);
    const cogsBar = readLoc(ws, 15, loc);
    const cogsKitchen = readLoc(ws, 16, loc);
    const cogsShisha = readLoc(ws, 17, loc);
    const cogsPctPlan = readLoc(ws, 20, loc);

    const ttkBar = readLoc(ws, 35, loc);
    const ttkKitchen = readLoc(ws, 36, loc);
    const ttkShisha = readLoc(ws, 37, loc);
    const ttkTotal = (ttkBar || 0) + (ttkKitchen || 0) + (ttkShisha || 0);
    const factCogsPct = readLoc(ws, 38, loc);

    const overhead = readLoc(ws, 42, loc);
    const payroll = readLoc(ws, 43, loc);
    const fotUk = readLoc(ws, 44, loc);
    const fotAccounting = readLoc(ws, 45, loc);
    const fotShift = readLoc(ws, 46, loc);
    const fotVariable = readLoc(ws, 47, loc);
    const staffFood = readLoc(ws, 50, loc);
    const taxi = readLoc(ws, 51, loc);
    const rentTotal = readLoc(ws, 53, loc);
    const rent = readLoc(ws, 54, loc);
    const garbage = readLoc(ws, 55, loc);
    const otherAiku = readLoc(ws, 57, loc);
    const aur = readLoc(ws, 59, loc);
    const kkm = readLoc(ws, 60, loc);
    const bankFee = readLoc(ws, 62, loc);
    const acquiring = readLoc(ws, 63, loc);
    const marketing = readLoc(ws, 65, loc);
    const marketingPrint = readLoc(ws, 66, loc);
    const maintenance = readLoc(ws, 69, loc);
    const equipment = readLoc(ws, 70, loc);
    const household = readLoc(ws, 71, loc);
    const taxes = readLoc(ws, 73, loc) ?? readLoc(ws, 75, loc);

    const netProfit = readLoc(ws, 77, loc);
    const netMargin = readLoc(ws, 78, loc);
    const days = readLoc(ws, 93, loc);
    const guests = readLoc(ws, 96, loc);
    const avgCheck = readLoc(ws, 98, loc);

    const grossProfit = revenueTotal != null && cogsTotal != null ? revenueTotal - cogsTotal : null;
    const revShare = (part) => pct(revenueTotal, part);

    const incomeItems = [
      item('Бар', bar, revenueTotal, 'revenue', 'bar'),
      item('Кухня', kitchen, revenueTotal, 'revenue', 'kitchen'),
      item('Кальяны', shisha, revenueTotal, 'revenue', 'shisha'),
      item('Банкеты', banquets, revenueTotal, 'revenue', 'banquets'),
      item('Прочее', otherRev, revenueTotal, 'revenue', 'other'),
    ].filter(Boolean);

    const expenseItems = [
      item('Себестоимость (факт)', cogsTotal, revenueTotal, 'cogs', 'cogs'),
      item('ФОТ всего', payroll, revenueTotal, 'payroll', 'payroll'),
      item('ФОТ УК', fotUk, revenueTotal, 'payroll', 'fot_uk'),
      item('Бухгалтерия + калькулятор', fotAccounting, revenueTotal, 'payroll', 'fot_acc'),
      item('ФОТ сменный + %', fotShift, revenueTotal, 'payroll', 'fot_shift'),
      item('ФОТ переменный', fotVariable, revenueTotal, 'payroll', 'fot_var'),
      item('Служебное питание', staffFood, revenueTotal, 'overhead', 'staff_food'),
      item('Такси', taxi, revenueTotal, 'overhead', 'taxi'),
      item('Аренда и КУ', rentTotal, revenueTotal, 'overhead', 'rent'),
      item('Аренда помещений', rent, revenueTotal, 'overhead', 'rent_room'),
      item('Вывоз мусора', garbage, revenueTotal, 'overhead', 'garbage'),
      item('Прочие АиКУ', otherAiku, revenueTotal, 'overhead', 'aiku'),
      item('АУР', aur, revenueTotal, 'overhead', 'aur'),
      item('ККМ, ПО, 1С', kkm, revenueTotal, 'overhead', 'kkm'),
      item('Комиссия банка', bankFee, revenueTotal, 'overhead', 'bank'),
      item('Эквайринг', acquiring, revenueTotal, 'overhead', 'acquiring'),
      item('Реклама и продвижение', marketing, revenueTotal, 'overhead', 'marketing'),
      item('Реклама и полиграфия', marketingPrint, revenueTotal, 'overhead', 'marketing_print'),
      item('Содержание проекта', maintenance, revenueTotal, 'overhead', 'maintenance'),
      item('Техника, ремонт, форма', equipment, revenueTotal, 'overhead', 'equipment'),
      item('Хозрасходы', household, revenueTotal, 'overhead', 'household'),
      item('Налоги и сборы', taxes, revenueTotal, 'tax', 'taxes'),
    ].filter(Boolean);

    return {
      location: loc,
      revenue: {
        total: revenueTotal,
        bar, kitchen, shisha, banquets, other: otherRev,
        shares: { bar: revShare(bar), kitchen: revShare(kitchen), shisha: revShare(shisha) },
        items: incomeItems,
      },
      cogs: {
        total: cogsTotal,
        bar: cogsBar, kitchen: cogsKitchen, shisha: cogsShisha,
        pct: factCogsPct,
        pctPlan: cogsPctPlan,
        pctOfRevenue: pct(revenueTotal, cogsTotal),
      },
      foodcost: {
        ttk: { bar: ttkBar, kitchen: ttkKitchen, shisha: ttkShisha, total: ttkTotal },
        actual: { bar: cogsBar, kitchen: cogsKitchen, shisha: cogsShisha, total: cogsTotal },
        ttkPct: pct(revenueTotal, ttkTotal),
        actualPct: factCogsPct ?? pct(revenueTotal, cogsTotal),
        variance: cogsTotal != null && ttkTotal != null ? cogsTotal - ttkTotal : null,
        variancePct: factCogsPct != null && cogsPctPlan != null ? factCogsPct - cogsPctPlan : null,
      },
      overheads: {
        total: overhead,
        payroll,
        payrollPct: pct(revenueTotal, payroll),
        rent: rentTotal,
        rentPct: pct(revenueTotal, rentTotal),
        marketing,
        maintenance,
        taxes,
        items: expenseItems.filter((e) => e.group !== 'cogs' && !FLOW_SKIP_KEYS.has(e.key)),
      },
      expenses: {
        total: totalExpenses,
        cogsTotal,
        overheadTotal: overhead,
        items: expenseItems,
        flowItems: buildExpenseFlowItems(cogsTotal, overhead, revenueTotal),
        detailItems: expenseItems.filter((e) => !FLOW_SKIP_KEYS.has(e.key)),
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

  function mergeLoc(a, b) {
    const rev = (a.revenue?.total || 0) + (b.revenue?.total || 0);
    const net = (a.profit?.net || 0) + (b.profit?.net || 0);
    const guests = (a.analytics?.guests || 0) + (b.analytics?.guests || 0);
    const sumItems = (itemsA, itemsB) => {
      const map = {};
      [...(itemsA || []), ...(itemsB || [])].forEach((it) => {
        if (!map[it.key]) map[it.key] = { ...it, amount: 0 };
        map[it.key].amount += it.amount || 0;
      });
      return Object.values(map).map((it) => ({
        ...it,
        pct: pct(rev, it.amount),
      })).sort((x, y) => (y.amount || 0) - (x.amount || 0));
    };
    return {
      location: 'Всего',
      revenue: {
        total: rev,
        bar: (a.revenue?.bar || 0) + (b.revenue?.bar || 0),
        kitchen: (a.revenue?.kitchen || 0) + (b.revenue?.kitchen || 0),
        shisha: (a.revenue?.shisha || 0) + (b.revenue?.shisha || 0),
        items: sumItems(a.revenue?.items, b.revenue?.items),
      },
      foodcost: {
        ttk: {
          bar: (a.foodcost?.ttk?.bar || 0) + (b.foodcost?.ttk?.bar || 0),
          kitchen: (a.foodcost?.ttk?.kitchen || 0) + (b.foodcost?.ttk?.kitchen || 0),
          shisha: (a.foodcost?.ttk?.shisha || 0) + (b.foodcost?.ttk?.shisha || 0),
          total: (a.foodcost?.ttk?.total || 0) + (b.foodcost?.ttk?.total || 0),
        },
        actual: {
          bar: (a.foodcost?.actual?.bar || 0) + (b.foodcost?.actual?.bar || 0),
          kitchen: (a.foodcost?.actual?.kitchen || 0) + (b.foodcost?.actual?.kitchen || 0),
          shisha: (a.foodcost?.actual?.shisha || 0) + (b.foodcost?.actual?.shisha || 0),
          total: (a.foodcost?.actual?.total || 0) + (b.foodcost?.actual?.total || 0),
        },
        ttkPct: pct(rev, (a.foodcost?.ttk?.total || 0) + (b.foodcost?.ttk?.total || 0)),
        actualPct: pct(rev, (a.foodcost?.actual?.total || 0) + (b.foodcost?.actual?.total || 0)),
      },
      profit: { net, margin: rev ? net / rev : null },
      analytics: { guests, avgCheck: guests ? rev / guests : null },
      cogs: {
        total: (a.cogs?.total || 0) + (b.cogs?.total || 0),
      },
      overheads: {
        total: (a.overheads?.total || 0) + (b.overheads?.total || 0),
      },
      expenses: {
        total: (a.expenses?.total || 0) + (b.expenses?.total || 0),
        cogsTotal: (a.cogs?.total || 0) + (b.cogs?.total || 0),
        overheadTotal: (a.overheads?.total || 0) + (b.overheads?.total || 0),
        flowItems: buildExpenseFlowItems(
          (a.cogs?.total || 0) + (b.cogs?.total || 0),
          (a.overheads?.total || 0) + (b.overheads?.total || 0),
          rev,
        ),
        detailItems: sumItems(a.expenses?.detailItems || a.expenses?.items, b.expenses?.detailItems || b.expenses?.items),
        items: sumItems(a.expenses?.items, b.expenses?.items),
      },
    };
  }

  A.parsePnl = function (ws) {
    const moyka = buildLoc(ws, 'Мойка');
    const akku = buildLoc(ws, 'Аккуратова');
    const total = mergeLoc(moyka, akku);
    return { moyka, akkuartova: akku, total };
  };

  A.buildKpiDetails = function (pnl, executive, writeoffs, inventories) {
    const invAbs = (inventories || []).reduce((s, i) => s + (i.absoluteVariance || 0), 0);
    const m = pnl?.moyka || {};
    const a = pnl?.akkuartova || {};
    return {
      revenue: {
        title: 'Выручка',
        branches: [
          { name: 'Мойка (Адмиралтейская)', amount: m.revenue?.total, margin: m.profit?.margin, items: m.revenue?.items },
          { name: 'Аккуратова (Удельная)', amount: a.revenue?.total, margin: a.profit?.margin, items: a.revenue?.items },
        ],
        total: pnl?.total?.revenue?.total,
      },
      profit: {
        title: 'Чистая прибыль',
        branches: [
          { name: 'Мойка', amount: m.profit?.net, margin: m.profit?.margin, guests: m.analytics?.guests },
          { name: 'Аккуратова', amount: a.profit?.net, margin: a.profit?.margin, guests: a.analytics?.guests },
        ],
        total: (m.profit?.net || 0) + (a.profit?.net || 0),
      },
      margin: {
        title: 'Маржа',
        branches: [
          { name: 'Мойка', pct: m.profit?.margin, revenue: m.revenue?.total, profit: m.profit?.net },
          { name: 'Аккуратова', pct: a.profit?.margin, revenue: a.revenue?.total, profit: a.profit?.net },
        ],
        total: executive?.margin,
      },
      guests: {
        title: 'Гости',
        branches: [
          { name: 'Мойка', count: m.analytics?.guests, avgCheck: m.analytics?.avgCheck, days: m.analytics?.days },
          { name: 'Аккуратова', count: a.analytics?.guests, avgCheck: a.analytics?.avgCheck, days: a.analytics?.days },
        ],
        total: executive?.guests,
      },
      avgCheck: {
        title: 'Средний чек',
        branches: [
          { name: 'Мойка', amount: m.analytics?.avgCheck, guests: m.analytics?.guests },
          { name: 'Аккуратова', amount: a.analytics?.avgCheck, guests: a.analytics?.guests },
        ],
        total: executive?.avgCheck,
      },
      writeoffs: {
        title: 'Списания',
        total: writeoffs?.total,
        moyka: writeoffs?.moykaTotal,
        akku: writeoffs?.akkuTotal,
        top: (writeoffs?.byReason || []).slice(0, 8),
      },
      inventory: {
        title: 'Инвентаризационные расхождения',
        total: invAbs,
        sheets: (inventories || []).map((i) => ({
          name: i.sheet,
          location: i.location,
          abs: i.absoluteVariance,
          surplus: i.totals?.surplusSum,
          shortage: i.totals?.shortageSum,
        })),
      },
    };
  };
})(typeof window !== 'undefined' ? window : globalThis);
