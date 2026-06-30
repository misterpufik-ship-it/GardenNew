(function (root) {
  'use strict';
  const A = root.OdrAnalyzer;
  if (!A) return;

  function sumObj(obj) {
    return (obj.bar || 0) + (obj.kitchen || 0) + (obj.shisha || 0);
  }

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
    const moykaTotal = rows.reduce((s, r) => s + sumObj(r.moyka), 0);
    const akkuTotal = rows.reduce((s, r) => s + sumObj(r.akku), 0);
    const byReason = rows.map((r) => ({
      reason: r.reason,
      moyka: sumObj(r.moyka),
      akku: sumObj(r.akku),
      total: sumObj(r.moyka) + sumObj(r.akku),
      moykaByCat: { ...r.moyka },
      akkuByCat: { ...r.akku },
    })).sort((a, b) => b.total - a.total);

    return { rows, moykaTotal, akkuTotal, total: moykaTotal + akkuTotal, byReason };
  };

  A.parseStockRowMeta = function (name) {
    const n = String(name).toLowerCase();
    let branch = null;
    let division = null;
    if (n.includes('мойка')) branch = 'moyka';
    if (n.includes('аккуратова')) branch = 'akku';
    if (n.includes('кухня')) division = 'kitchen';
    else if (n.includes('кальян')) division = 'shisha';
    else if (n.includes('б/а') || n.includes('алкоголь')) division = 'bar';
    return { branch, division };
  };

  A.parseStockDateLabel = function (label) {
    const m = String(label).match(/(\d{2})\.(\d{2})\.(\d{4})/);
    if (!m) return null;
    return `${m[3]}-${m[2]}-${m[1]}`;
  };

  A.parseStock = function (ws, reportMonth) {
    const columns = [];
    for (let c = 2; c <= 12; c++) {
      const h = A.cellVal(ws, 1, c);
      if (!h) continue;
      const label = String(h).trim();
      columns.push({ col: c, label, date: A.parseStockDateLabel(label) });
    }

    const rows = [];
    for (let r = 2; r <= 20; r++) {
      const name = A.cellVal(ws, r, 1);
      if (!name || /итого/i.test(String(name))) continue;
      const meta = A.parseStockRowMeta(name);
      const balances = columns.map((col) => ({
        col: col.col,
        label: col.label,
        date: col.date,
        value: A.toNum(A.cellVal(ws, r, col.col)),
      }));
      rows.push({ name: String(name).trim(), ...meta, balances });
    }

    const warnings = [];
    rows.forEach((row) => {
      if (/алкоголь/i.test(row.name)) {
        const mar = row.balances.find((b) => b.label.includes('03.2026') || b.label.includes('01.03'));
        const later = row.balances.filter((b) => {
          if (!mar?.date || !b.date) return false;
          return b.date > mar.date;
        });
        if (mar?.value > 0 && later.length && later.every((b) => !b.value)) {
          warnings.push(`Алкоголь «${row.name}»: остатки обнулились после марта — проверить учёт.`);
        }
      }
    });

    const flow = A.buildStockFlow(rows, columns, reportMonth);
    return { months: columns.map((c) => c.label), columns, rows, warnings, flow };
  };

  A.buildStockFlow = function (rows, columns, reportMonth) {
    if (!reportMonth || !rows.length) return null;

    const [y, m] = reportMonth.split('-').map(Number);
    const openDate = `${y}-${String(m).padStart(2, '0')}-01`;
    const nextM = m === 12 ? 1 : m + 1;
    const nextY = m === 12 ? y + 1 : y;
    const closeDate = `${nextY}-${String(nextM).padStart(2, '0')}-01`;

    function balanceOn(row, date) {
      const hit = row.balances.find((b) => b.date === date);
      return hit?.value ?? null;
    }

    function aggregate(branch, division) {
      const matched = rows.filter((r) => r.branch === branch && r.division === division);
      const opening = matched.reduce((s, r) => s + (balanceOn(r, openDate) || 0), 0) || null;
      const closing = matched.reduce((s, r) => s + (balanceOn(r, closeDate) || 0), 0) || null;
      return { opening, closing, rows: matched.map((r) => r.name) };
    }

    const branches = [
      { key: 'moyka', label: 'Адмиралтейская · Мойка', pnlKey: 'moyka' },
      { key: 'akku', label: 'Удельная · Аккуратова', pnlKey: 'akkuartova' },
    ];
    const divisions = [
      { key: 'bar', label: 'Бар' },
      { key: 'kitchen', label: 'Кухня' },
      { key: 'shisha', label: 'Кальяны' },
    ];

    return {
      period: { openDate, closeDate, openLabel: columns.find((c) => c.date === openDate)?.label, closeLabel: columns.find((c) => c.date === closeDate)?.label },
      lines: branches.flatMap((br) => divisions.map((div) => {
        const stock = aggregate(br.key, div.key);
        return { branch: br.key, branchLabel: br.label, division: div.key, divisionLabel: div.label, pnlKey: br.pnlKey, ...stock };
      })),
    };
  };

  A.enrichStockFlow = function (flow, pnl) {
    if (!flow?.lines) return flow;
    flow.lines = flow.lines.map((line) => {
      const loc = pnl?.[line.pnlKey];
      const ttkKey = line.division === 'bar' ? 'bar' : line.division === 'kitchen' ? 'kitchen' : 'shisha';
      const ttk = loc?.foodcost?.ttk?.[ttkKey] ?? null;
      const fact = loc?.cogs?.[ttkKey] ?? null;
      const opening = line.opening;
      const closing = line.closing;
      const purchases = (opening != null && closing != null && ttk != null)
        ? closing - opening + ttk
        : null;
      const calcClosing = (opening != null && purchases != null && ttk != null)
        ? opening + purchases - ttk
        : null;
      const drift = (calcClosing != null && closing != null) ? closing - calcClosing : null;
      const delta = (opening != null && closing != null) ? closing - opening : null;

      let insight = '';
      if (opening == null || closing == null) {
        insight = 'Нет данных остатков на складах за выбранный период.';
      } else if (ttk == null) {
        insight = `Остаток ${A.fmtMoney(opening)} → ${A.fmtMoney(closing)} (${delta >= 0 ? '+' : ''}${A.fmtMoney(delta)}). ТТК в ОДР не найден.`;
      } else {
        const trend = delta > 0 ? 'запасы выросли' : delta < 0 ? 'запасы снизились' : 'запасы без изменений';
        insight = `${trend}: было ${A.fmtMoney(opening)}, по ТТК списано ${A.fmtMoney(ttk)}, оценка закупок ~${A.fmtMoney(purchases)}, стало ${A.fmtMoney(closing)}.`;
        if (drift != null && Math.abs(drift) > Math.max(closing, 1) * 0.05) {
          insight += ` Расхождение модели ${A.fmtMoney(drift)} — проверить инвенту и закупки.`;
        } else if (fact != null && ttk != null && fact > ttk * 1.08) {
          insight += ` Факт COGS (${A.fmtMoney(fact)}) выше ТТК — перерасход.`;
        } else if (fact != null && ttk != null && fact < ttk * 0.92) {
          insight += ` Факт COGS ниже ТТК — возможна недоучётка списаний.`;
        }
      }

      return { ...line, ttk, fact, purchases, calcClosing, drift, delta, insight };
    });
    return flow;
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

  function detectFormat(ws) {
    const maxScan = Math.min(ws.rowCount || 15, 15);
    for (let r = 1; r <= maxScan; r++) {
      for (let c = 1; c <= Math.min(ws.columnCount || 8, 10); c++) {
        const v = String(A.cellVal(ws, r, c) || '').toLowerCase();
        if (v.includes('разница') && v.includes('сумма')) return 'kitchen';
        if (v === 'товар' || v.startsWith('товар')) return 'kitchen';
      }
    }
    const name = ws.name.toLowerCase();
    if (/^км\s/.test(name) || name.includes('кальян')) return 'km';
    return 'bar';
  }

  function parseBarFormat(ws, location, category, division) {
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
      const surplus = A.toNum(A.cellVal(ws, r, 12)) || 0;
      const shortage = A.toNum(A.cellVal(ws, r, 14)) || 0;
      if (!surplus && !shortage) continue;
      items.push({
        name: String(name).trim(),
        factSum: A.toNum(A.cellVal(ws, r, 8)),
        calcSum: A.toNum(A.cellVal(ws, r, 10)),
        surplus, shortage,
        absVariance: Math.abs(surplus) + Math.abs(shortage),
      });
    }
    return finalizeInventory(ws, location, category, division, items, totals);
  }

  function parseKmFormat(ws, location, category, division) {
    const items = [];
    let totals = { surplusSum: 0, shortageSum: 0, factSum: 0, calcSum: 0 };
    for (let r = 12; r <= (ws.rowCount || 300); r++) {
      const name = A.cellVal(ws, r, 2);
      if (!name || String(name).toLowerCase() === 'итого') {
        if (String(name).toLowerCase() === 'итого') {
          totals = {
            factSum: A.toNum(A.cellVal(ws, r, 6)),
            calcSum: A.toNum(A.cellVal(ws, r, 8)),
            surplusSum: A.toNum(A.cellVal(ws, r, 10)),
            shortageSum: A.toNum(A.cellVal(ws, r, 12)),
          };
        }
        continue;
      }
      const surplus = A.toNum(A.cellVal(ws, r, 10)) || 0;
      const shortage = A.toNum(A.cellVal(ws, r, 12)) || 0;
      if (!surplus && !shortage) continue;
      items.push({
        name: String(name).trim(),
        factSum: A.toNum(A.cellVal(ws, r, 6)),
        calcSum: A.toNum(A.cellVal(ws, r, 8)),
        surplus, shortage,
        absVariance: Math.abs(surplus) + Math.abs(shortage),
      });
    }
    if (!totals.surplusSum && !totals.shortageSum) {
      totals.surplusSum = items.reduce((s, i) => s + i.surplus, 0);
      totals.shortageSum = items.reduce((s, i) => s + i.shortage, 0);
    }
    return finalizeInventory(ws, location, category, division, items, totals);
  }

  function parseKitchenFormat(ws, location, category, division) {
    const surplusTotal = A.toNum(A.cellVal(ws, 5, 2));
    const shortageTotal = A.toNum(A.cellVal(ws, 6, 2));
    const items = [];
    let dataStart = 11;
    for (let r = 1; r <= 12; r++) {
      const h = String(A.cellVal(ws, r, 1) || '').toLowerCase();
      if (h.includes('товар')) { dataStart = r + 2; break; }
    }
    for (let r = dataStart; r <= (ws.rowCount || 400); r++) {
      const name = A.cellVal(ws, r, 1);
      if (!name) continue;
      const factSum = A.toNum(A.cellVal(ws, r, 5));
      const diffSum = A.toNum(A.cellVal(ws, r, 8));
      if (factSum == null && diffSum == null) continue;
      const surplus = diffSum > 0 ? diffSum : 0;
      const shortage = diffSum < 0 ? Math.abs(diffSum) : 0;
      if (!surplus && !shortage && factSum == null) continue;
      items.push({
        name: String(name).trim(),
        factSum,
        calcSum: factSum != null && diffSum != null ? factSum - diffSum : null,
        surplus, shortage,
        absVariance: Math.abs(surplus) + Math.abs(shortage),
      });
    }
    items.sort((a, b) => b.absVariance - a.absVariance);
    const totals = {
      surplusSum: surplusTotal ?? items.reduce((s, i) => s + i.surplus, 0),
      shortageSum: shortageTotal ?? items.reduce((s, i) => s + i.shortage, 0),
      factSum: items.reduce((s, i) => s + (i.factSum || 0), 0),
    };
    return finalizeInventory(ws, location, category, division, items, totals);
  }

  function finalizeInventory(ws, location, category, division, items, totals) {
    items.sort((a, b) => b.absVariance - a.absVariance);
    const absoluteVariance = items.reduce((s, i) => s + i.absVariance, 0);
    return {
      location,
      category,
      division: division || category,
      sheet: ws.name,
      totals: totals || {},
      absoluteVariance,
      netVariance: (totals?.surplusSum || 0) - (totals?.shortageSum || 0),
      topShortage: items.filter((i) => i.shortage > 0).slice(0, 20),
      topSurplus: items.filter((i) => i.surplus > 0).slice(0, 20),
      topAbs: items.slice(0, 20),
      itemCount: items.length,
    };
  }

  A.parseInventorySheet = function (ws, location, category, division) {
    const fmt = detectFormat(ws);
    if (fmt === 'kitchen') return parseKitchenFormat(ws, location, category, division || 'kitchen');
    if (fmt === 'km') return parseKmFormat(ws, location, category, division || 'shisha');
    return parseBarFormat(ws, location, category, division || 'bar');
  };
})(typeof window !== 'undefined' ? window : globalThis);
