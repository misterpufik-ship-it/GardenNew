/* Garden Lounge ODR Analyzer — core utilities */
(function (root) {
  'use strict';

  const OdrAnalyzer = root.OdrAnalyzer || {};
  OdrAnalyzer.VERSION = '1.0.0';

  OdrAnalyzer.LOCATION_MAP = {
    АДМИРАЛ: 'Мойка',
    АДМИР: 'Мойка',
    Адмирал: 'Мойка',
    Мойка: 'Мойка',
    УДЕЛЬНАЯ: 'Аккуратова',
    УДЕЛКА: 'Аккуратова',
    Аккуратова: 'Аккуратова',
  };

  OdrAnalyzer.SHEET_PATTERNS = {
    odr: /^одр$/i,
    dailyMoyka: /^адмир$/i,
    dailyAkku: /^уделка$/i,
    shiftMoyka1: /^а_.*\(1\s*смен/i,
    shiftMoyka2: /^а_.*\(2\s*смен/i,
    shiftAkku1: /^у_.*\(1\s*смен/i,
    shiftAkku2: /^у_.*\(2\s*смен/i,
    stock: /^остатки\s+на\s+склад/i,
    writeoffs: /^списания/i,
    transfers: /^перемещения/i,
    invBarMoyka: /^адмирал\s/i,
    invBarAkku: /^аккуратова\s/i,
    invKmMoyka: /^км\s+мойка/i,
    invKmAkku: /^км\s+аккуратова/i,
    invKitchenMoyka: /^кухня\s+мойка/i,
    invKitchenAkku: /^кухня\s+аккуратова/i,
  };

  OdrAnalyzer.normalizeLoc = function (name) {
    if (!name) return name;
    const t = String(name).trim();
    return OdrAnalyzer.LOCATION_MAP[t] || t;
  };

  OdrAnalyzer.toNum = function (v) {
    if (v == null || v === '') return null;
    if (typeof v === 'object' && v.error) return null;
    if (typeof v === 'number' && !Number.isNaN(v)) return Math.round(v * 100) / 100;
    const s = String(v).replace(/\s/g, '').replace(',', '.');
    const n = parseFloat(s);
    return Number.isNaN(n) ? null : Math.round(n * 100) / 100;
  };

  OdrAnalyzer.isErrorCell = function (v) {
    if (v == null) return false;
    if (typeof v === 'object' && v.error) return true;
    const s = String(v).trim();
    return /^#(REF!|DIV\/0!|NAME\?|VALUE!|N\/A|null)$/i.test(s);
  };

  OdrAnalyzer.fmtMoney = function (n) {
    if (n == null || Number.isNaN(n)) return '—';
    return n.toLocaleString('ru-RU', { maximumFractionDigits: 0 }) + ' ₽';
  };

  OdrAnalyzer.fmtPct = function (n, digits) {
    if (n == null || Number.isNaN(n)) return '—';
    return (n * 100).toFixed(digits == null ? 1 : digits) + '%';
  };

  OdrAnalyzer.monthLabel = function (ym) {
    if (!ym) return '—';
    const [y, m] = ym.split('-');
    const names = ['', 'январь', 'февраль', 'март', 'апрель', 'май', 'июнь', 'июль', 'август', 'сентябрь', 'октябрь', 'ноябрь', 'декабрь'];
    return `${names[parseInt(m, 10)] || m} ${y}`;
  };

  OdrAnalyzer.isValidDate = function (d) {
    return d instanceof Date && !Number.isNaN(d.getTime());
  };

  OdrAnalyzer.formatDateISO = function (d) {
    if (!OdrAnalyzer.isValidDate(d)) return null;
    const y = d.getFullYear();
    const m = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${y}-${m}-${day}`;
  };

  OdrAnalyzer.parseDate = function (v) {
    if (v == null || v === '') return null;

    if (v instanceof Date) {
      return OdrAnalyzer.isValidDate(v) ? v : null;
    }

    if (typeof v === 'object') {
      if (v.error) return null;
      if (v.result != null) return OdrAnalyzer.parseDate(v.result);
      if (v.year != null && v.month != null && v.day != null) {
        const d = new Date(v.year, v.month - 1, v.day);
        return OdrAnalyzer.isValidDate(d) ? d : null;
      }
      return null;
    }

    if (typeof v === 'number') {
      const epoch = Date.UTC(1899, 11, 30);
      const d = new Date(epoch + v * 86400000);
      return OdrAnalyzer.isValidDate(d) ? d : null;
    }

    const s = String(v).trim();
    if (!s || /^итого$/i.test(s)) return null;

    const dmY = s.match(/^(\d{1,2})\.(\d{1,2})\.(\d{4})$/);
    if (dmY) {
      const d = new Date(+dmY[3], +dmY[2] - 1, +dmY[1]);
      return OdrAnalyzer.isValidDate(d) ? d : null;
    }

    const d = new Date(s);
    return OdrAnalyzer.isValidDate(d) ? d : null;
  };

  OdrAnalyzer.unwrapCell = function (v) {
    if (v == null) return null;
    if (v instanceof Date) return OdrAnalyzer.isValidDate(v) ? v : null;
    if (typeof v === 'object' && v.error) return null;
    if (typeof v === 'object' && v.result != null) return OdrAnalyzer.unwrapCell(v.result);
    if (typeof v === 'object' && v.richText) return v.richText.map((t) => t.text).join('');
    if (typeof v === 'object' && v.text) return v.text;
    return v;
  };

  OdrAnalyzer.cellVal = function (ws, r, c) {
    try {
      const cell = ws.getCell(r, c);
      if (!cell || cell.value == null) return null;
      return OdrAnalyzer.unwrapCell(cell.value);
    } catch (_) {
      return null;
    }
  };

  OdrAnalyzer.findSheets = function (wb) {
    const out = {};
    wb.eachSheet((ws) => {
      const name = ws.name.trim();
      const lower = name.toLowerCase();
      for (const [key, re] of Object.entries(OdrAnalyzer.SHEET_PATTERNS)) {
        if (re.test(lower) || re.test(name)) {
          out[key] = ws;
        }
      }
    });
    return out;
  };

  OdrAnalyzer.scanErrors = function (wb) {
    const errors = [];
    wb.eachSheet((ws) => {
      ws.eachRow({ includeEmpty: false }, (row, rowNumber) => {
        row.eachCell({ includeEmpty: false }, (cell, colNumber) => {
          const v = cell.value;
          if (OdrAnalyzer.isErrorCell(v)) {
            errors.push({
              sheet: ws.name,
              cell: cell.address,
              error: typeof v === 'object' ? v.error : String(v),
            });
          }
        });
      });
    });
    return errors;
  };

  OdrAnalyzer.labelMapFromSheet = function (ws, labelCol, valCols) {
    const map = {};
    const max = ws.rowCount || 200;
    for (let r = 1; r <= max; r++) {
      const label = OdrAnalyzer.cellVal(ws, r, labelCol);
      if (!label || typeof label === 'object') continue;
      const key = String(label).trim();
      if (!key) continue;
      const entry = {};
      for (const [loc, col] of Object.entries(valCols)) {
        entry[loc] = OdrAnalyzer.toNum(OdrAnalyzer.cellVal(ws, r, col));
      }
      map[key.toLowerCase()] = { label: key, row: r, values: entry };
    }
    return map;
  };

  OdrAnalyzer.getPnlValue = function (map, labels, loc) {
    for (const lbl of labels) {
      const item = map[lbl.toLowerCase()];
      if (item && item.values[loc] != null) return item.values[loc];
    }
    return null;
  };

  root.OdrAnalyzer = OdrAnalyzer;
})(typeof window !== 'undefined' ? window : globalThis);
