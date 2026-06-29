(function (root) {
  'use strict';
  const A = root.OdrAnalyzer;
  if (!A) return;

  A.parseWriteoffs = function (ws) {
    const rows = [];
    const max = ws.rowCount || 30;
    for (let r = 3; r <= max; r++) {
      const reason = A.cellVal(ws, r, 1);
      if (!reason || String(reason).toLowerCase().includes('итого')) continue;
      rows.push({
        reason: String(reason).trim(),
        akku: { bar: A.toNum(A.cellVal(ws, r, 2)), kitchen: A.toNum(A.cellVal(ws, r, 3)), shisha: A.toNum(A.cellVal(ws, r, 4)) },
        moyka: { bar: A.toNum(A.cellVal(ws, r, 5)), kitchen: A.toNum(A.cellVal(ws, r, 6)), shisha: A.toNum(A.cellVal(ws, r, 7)) },
      });
    }
    const sum = (obj) => (obj.bar || 0) + (obj.kitchen || 0) + (obj.shisha || 0);
    const moykaTotal = rows.reduce((s, r) => s + sum(r.moyka), 0);
    const akkuTotal = rows.reduce((s, r) => s + sum(r.akku), 0);
    const byReason = rows.map((r) => ({
      reason: r.reason,
      moyka: sum(r.moyka),
      akku: sum(r.akku),
      total: sum(r.moyka) + sum(r.akku),
    })).sort((a, b) => b.total - a.total);

    return { rows, moykaTotal, akkuTotal, total: moykaTotal + akkuTotal, byReason };
  };

  A.parseStock = function (ws) {
    const rows = [];
    const months = [];
    for (let c = 2; c <= 9; c++) {
      const h = A.cellVal(ws, 1, c);
      if (h) months.push(String(h));
    }
    for (let r = 2; r <= 20; r++) {
      const name = A.cellVal(ws, r, 1);
      if (!name || String(name).toLowerCase().includes('общее')) continue;
      const vals = [];
      for (let c = 2; c <= 9; c++) vals.push(A.toNum(A.cellVal(ws, r, c)));
      rows.push({ name: String(name).trim(), values: vals });
    }
    const warnings = [];
    rows.forEach((row) => {
      if (/алкоголь/i.test(row.name)) {
        const mar = row.values[4];
        const later = row.values.slice(5);
        if (mar > 0 && later.every((v) => !v)) {
          warnings.push(`Алкоголь «${row.name}»: остатки обнулились после марта — проверить учёт.`);
        }
      }
    });
    return { months, rows, warnings };
  };

  A.parseTransfers = function (ws) {
    const items = [];
    for (let r = 2; r <= (ws.rowCount || 15); r++) {
      const from = A.cellVal(ws, r, 1);
      const to = A.cellVal(ws, r, 2);
      const amount = A.toNum(A.cellVal(ws, r, 3));
      if (!from || !to || amount == null) continue;
      items.push({ from: String(from).trim(), to: String(to).trim(), amount });
    }
    const total = items.reduce((s, i) => s + i.amount, 0);
    return { items, total };
  };

  A.parseInventorySheet = function (ws, location, category) {
    const items = [];
    let totals = null;
    for (let r = 9; r <= (ws.rowCount || 300); r++) {
      const name = A.cellVal(ws, r, 2);
      if (!name) continue;
      if (String(name).toLowerCase() === 'итого') {
        totals = {
          factSum: A.toNum(A.cellVal(ws, r, 8)),
          calcSum: A.toNum(A.cellVal(ws, r, 10)),
          surplusSum: A.toNum(A.cellVal(ws, r, 12)),
          shortageSum: A.toNum(A.cellVal(ws, r, 14)),
        };
        continue;
      }
      const surplus = A.toNum(A.cellVal(ws, r, 12));
      const shortage = A.toNum(A.cellVal(ws, r, 14));
      if (surplus == null && shortage == null) continue;
      items.push({
        name: String(name).trim(),
        factSum: A.toNum(A.cellVal(ws, r, 8)),
        calcSum: A.toNum(A.cellVal(ws, r, 10)),
        surplus: surplus || 0,
        shortage: shortage || 0,
        absVariance: Math.abs(surplus || 0) + Math.abs(shortage || 0),
      });
    }
    items.sort((a, b) => b.absVariance - a.absVariance);
    const absoluteVariance = items.reduce((s, i) => s + i.absVariance, 0);
    return {
      location, category, sheet: ws.name,
      totals: totals || {},
      absoluteVariance,
      netVariance: (totals?.surplusSum || 0) - (totals?.shortageSum || 0),
      topShortage: items.filter((i) => i.shortage > 0).slice(0, 20),
      topSurplus: items.filter((i) => i.surplus > 0).slice(0, 20),
      topAbs: items.slice(0, 20),
    };
  };
})(typeof window !== 'undefined' ? window : globalThis);
