/* global ExcelJS */
/**
 * Стилизованный экспорт Excel для AI-CRM Garden Lounge
 */
const CrmExport = (() => {
  const C = {
    headerBg: 'FF0A0A0A',
    headerText: 'FFC5A059',
    titleText: 'FFE4C04A',
    bodyText: 'FFF3EFE4',
    bodyBg: 'FF111111',
    altBg: 'FF0D0D0D',
    border: 'FF2E2618',
    greenBg: 'FF1E3A24',
    greenText: 'FF81C784',
    yellowBg: 'FF3A321E',
    yellowText: 'FFFFD54F',
    orangeBg: 'FF3A2818',
    orangeText: 'FFFFB74D',
    redBg: 'FF3A1E1E',
    redText: 'FFEF9A9A',
    blueBg: 'FF1A2030',
    blueText: 'FF90CAF9',
  };

  const STATUS = {
    exact: { bg: C.greenBg, fg: C.greenText },
    group: { bg: C.yellowBg, fg: C.yellowText },
    unmatched: { bg: C.orangeBg, fg: C.orangeText },
    unmatched_stmt: { bg: C.orangeBg, fg: C.orangeText },
    match: { bg: C.greenBg, fg: C.greenText },
    partial: { bg: C.yellowBg, fg: C.yellowText },
    mismatch: { bg: C.redBg, fg: C.redText },
    no_admir: { bg: C.redBg, fg: C.redText },
    todo: { bg: C.yellowBg, fg: C.yellowText },
    progress: { bg: C.blueBg, fg: C.blueText },
    done: { bg: C.greenBg, fg: C.greenText },
  };

  function thinBorder() {
    return {
      top: { style: 'thin', color: { argb: C.border } },
      left: { style: 'thin', color: { argb: C.border } },
      bottom: { style: 'thin', color: { argb: C.border } },
      right: { style: 'thin', color: { argb: C.border } },
    };
  }

  function styleCell(cell, { bg, fg, bold, align, wrap, numFmt } = {}) {
    cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: bg || C.bodyBg } };
    cell.font = {
      name: 'Calibri',
      size: 10,
      bold: !!bold,
      color: { argb: fg || C.bodyText },
    };
    cell.alignment = {
      wrapText: wrap !== false,
      vertical: 'top',
      horizontal: align || 'left',
    };
    cell.border = thinBorder();
    if (numFmt) cell.numFmt = numFmt;
  }

  function addSheet(wb, spec) {
    const {
      name,
      title,
      subtitle,
      headers,
      rows,
      rowStatus,
      colWidths,
      numberCols = [],
      wrapCols,
    } = spec;

    const ws = wb.addWorksheet(name, {
      properties: { defaultRowHeight: 18 },
      views: [{ state: 'frozen', ySplit: title ? 3 : 1 }],
    });

    let startRow = 1;
    const colCount = headers.length;

    if (title) {
      ws.mergeCells(1, 1, 1, colCount);
      const tCell = ws.getCell(1, 1);
      tCell.value = title;
      styleCell(tCell, { bg: C.headerBg, fg: C.titleText, bold: true, align: 'center', wrap: true });
      tCell.font = { ...tCell.font, size: 14 };

      ws.mergeCells(2, 1, 2, colCount);
      const sCell = ws.getCell(2, 1);
      sCell.value = subtitle || `AI-CRM Garden Lounge · ${new Date().toLocaleString('ru-RU')}`;
      styleCell(sCell, { bg: C.headerBg, fg: C.headerText, align: 'center', wrap: true });
      sCell.font = { name: 'Calibri', size: 9, color: { argb: C.headerText } };
      startRow = 3;
    }

    const headerRow = ws.getRow(startRow);
    headers.forEach((h, i) => {
      const cell = headerRow.getCell(i + 1);
      cell.value = h;
      styleCell(cell, { bg: C.headerBg, fg: C.headerText, bold: true, align: 'center', wrap: true });
    });
    headerRow.height = 22;

    rows.forEach((row, ri) => {
      const excelRow = ws.getRow(startRow + 1 + ri);
      const status = rowStatus ? rowStatus(row, ri) : null;
      const palette = STATUS[status] || null;
      const zebra = ri % 2 === 1 && !palette;

      row.forEach((val, ci) => {
        const cell = excelRow.getCell(ci + 1);
        cell.value = val ?? '';
        const isNum = typeof val === 'number';
        const bg = palette ? palette.bg : (zebra ? C.altBg : C.bodyBg);
        const fg = palette ? palette.fg : C.bodyText;
        const numFmt = numberCols.includes(ci) ? '#,##0.00' : undefined;
        const wrap = wrapCols ? wrapCols.includes(ci) : true;
        styleCell(cell, { bg, fg, numFmt, wrap });
        if (isNum) cell.numFmt = '#,##0.00';
      });
      excelRow.height = null;
    });

    const widths = colWidths || headers.map((h, i) => {
      const maxLen = Math.max(
        String(h).length,
        ...rows.map((r) => String(r[i] ?? '').length)
      );
      return Math.min(Math.max(maxLen + 3, 12), 48);
    });

    widths.forEach((w, i) => {
      ws.getColumn(i + 1).width = w;
    });

    ws.autoFilter = {
      from: { row: startRow, column: 1 },
      to: { row: startRow + rows.length, column: colCount },
    };

    return ws;
  }

  async function download(filename, sheets) {
    const wb = new ExcelJS.Workbook();
    wb.creator = 'AI-CRM Garden Lounge';
    wb.created = new Date();

    sheets.forEach((spec) => addSheet(wb, spec));

    const buffer = await wb.xlsx.writeBuffer();
    const blob = new Blob([buffer], {
      type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    a.remove();
    URL.revokeObjectURL(url);
  }

  return { download, C, STATUS };
})();
