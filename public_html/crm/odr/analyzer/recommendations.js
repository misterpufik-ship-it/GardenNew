(function (root) {
  'use strict';
  const A = root.OdrAnalyzer;
  if (!A) return;

  A.buildRecommendations = function (analysis) {
    const warnings = [];
    const opportunities = [];
    const action7 = [];
    const action30 = [];
    const addWarning = (text, priority) => warnings.push({ text, priority: priority || 'medium' });
    const addOpp = (text) => opportunities.push({ text });

    const pnl = analysis.pnl || {};
    ['moyka', 'akkuartova'].forEach((key) => {
      const loc = pnl[key];
      if (!loc) return;
      const locName = key === 'moyka' ? 'Мойка' : 'Аккуратова';
      const rev = loc.revenue?.total;
      const payrollPct = loc.overheads?.payrollPct;
      const margin = loc.profit?.margin;
      const kitchenShare = loc.revenue?.shares?.kitchen;

      if (payrollPct != null && payrollPct > 0.45) {
        addWarning(`${locName}: ФОТ ${A.fmtPct(payrollPct)} — критически высокий. Пересмотреть расписание и переменную мотивацию.`, 'high');
        action30.push(`${locName}: аудит смен и ФОТ-план на следующий месяц`);
      }
      if (margin != null && margin < 0.05) {
        addWarning(`${locName}: маржа ${A.fmtPct(margin)} — точка почти не зарабатывает.`, 'high');
        action7.push(`${locName}: план роста трафика и снижения постоянных расходов`);
      }
      if (kitchenShare != null && kitchenShare < 0.18) {
        addWarning(`${locName}: доля кухни ${A.fmtPct(kitchenShare)} — ниже нормы 18–20%. Усилить attach rate.`, 'medium');
        action30.push(`${locName}: upsell-кухни в сменах и акции на блюда`);
      }
      if (loc.cogs?.pctOfRevenue != null && loc.cogs.pctOfRevenue > 0.22) {
        addWarning(`${locName}: себестоимость ${A.fmtPct(loc.cogs.pctOfRevenue)} — проверить ТТК и списания.`, 'medium');
      }
    });

    const wo = analysis.writeoffs || {};
    const woPctM = pnl.moyka?.revenue?.total ? wo.moykaTotal / pnl.moyka.revenue.total : null;
    const woPctA = pnl.akkuartova?.revenue?.total ? wo.akkuTotal / pnl.akkuartova.revenue.total : null;
    if ((woPctM != null && woPctM > 0.02) || (woPctA != null && woPctA > 0.02)) {
      addWarning('Списания выше 2% от выручки — проверить порчу, стафф-питание и удаления блюд.', 'high');
      action7.push('Разбор топ-причин списаний с управляющими смен');
    }

    const inv = analysis.inventories || [];
    inv.forEach((sheet) => {
      const fact = sheet.totals?.factSum;
      if (sheet.absoluteVariance && fact && sheet.absoluteVariance > fact * 0.05) {
        addWarning(`${sheet.sheet}: абсолютное расхождение ${A.fmtMoney(sheet.absoluteVariance)} (>5% от остатка) — нужен SKU-контроль.`, 'high');
      }
    });

    if (pnl.moyka?.profit?.net != null && pnl.akkuartova?.profit?.net != null) {
      if (pnl.moyka.profit.net > pnl.akkuartova.profit.net * 3) {
        opportunities.push({ text: 'Мойка существенно прибыльнее — масштабировать практики сильной точки на Аккуратову.' });
      }
      if (pnl.akkuartova.profit.margin > pnl.moyka.profit.margin) {
        opportunities.push({ text: 'Аккуратова эффективнее по марже — изучить cost-structure для тиражирования.' });
      }
    }

    const daily = analysis.daily || {};
    if (daily.moyka?.stats?.avgKitchenShare != null && daily.moyka.stats.avgKitchenShare < 0.18) {
      action7.push('Мойка: усилить продажи кухни в слабые дни недели');
    }

    if ((analysis.dataQuality?.errors || []).length) {
      action7.push('Исправить ошибки формул (#REF!) в отчёте до следующего закрытия месяца');
    }

    return {
      warnings,
      opportunities,
      action7: [...new Set(action7)],
      action30: [...new Set(action30)],
      ownerSummary: A.buildOwnerSummary(analysis, warnings, opportunities),
    };
  };

  A.buildOwnerSummary = function (analysis, warnings, opportunities) {
    const pnl = analysis.pnl || {};
    const rev = (pnl.moyka?.revenue?.total || 0) + (pnl.akkuartova?.revenue?.total || 0);
    const profit = (pnl.moyka?.profit?.net || 0) + (pnl.akkuartova?.profit?.net || 0);
    const margin = rev ? profit / rev : null;
    const guests = (pnl.moyka?.analytics?.guests || 0) + (pnl.akkuartova?.analytics?.guests || 0);
    const high = warnings.filter((w) => w.priority === 'high').length;

    let text = `За ${analysis.meta?.periodLabel || 'период'} выручка ${A.fmtMoney(rev)}, чистая прибыль ${A.fmtMoney(profit)}`;
    if (margin != null) text += ` (маржа ${A.fmtPct(margin)}).`;
    text += ` Гостей: ${guests.toLocaleString('ru-RU')}.`;
    if (high) text += ` Выявлено ${high} критических зон внимания.`;
    else text += ' Критических отклонений по ключевым KPI не зафиксировано.';
    return text;
  };

  A.validateData = function (analysis, sheets) {
    const issues = [];
    const pnl = analysis.pnl;
    const dailyM = analysis.daily?.moyka;
    const dailyA = analysis.daily?.akkuartova;

    if (pnl?.moyka?.revenue?.total != null && dailyM?.stats?.totalNet != null) {
      const diff = Math.abs(pnl.moyka.revenue.total - dailyM.stats.totalNet - (dailyM.stats.totalBonuses || 0));
      const bonusEst = dailyM.days.reduce((s, d) => s + (d.bonuses || 0), 0);
      const diff2 = Math.abs(pnl.moyka.revenue.total - dailyM.stats.totalNet - bonusEst);
      if (diff2 > pnl.moyka.revenue.total * 0.02) {
        issues.push({
          type: 'reconciliation',
          message: `Мойка: ОДР (${A.fmtMoney(pnl.moyka.revenue.total)}) vs дневная выручка (${A.fmtMoney(dailyM.stats.totalNet)}) — возможное расхождение с бонусами.`,
          diff: diff2,
        });
      }
    }

    if (pnl?.akkuartova?.revenue?.total != null && dailyA?.stats?.totalNet != null) {
      const bonusEst = dailyA.days.reduce((s, d) => s + (d.bonuses || 0), 0);
      const diff2 = Math.abs(pnl.akkuartova.revenue.total - dailyA.stats.totalNet - bonusEst);
      if (diff2 > pnl.akkuartova.revenue.total * 0.02) {
        issues.push({
          type: 'reconciliation',
          message: `Аккуратова: расхождение ОДР и листа УДЕЛКА.`,
          diff: diff2,
        });
      }
    }

    return { issues, missingSheets: Object.entries({
      ОДР: sheets.odr,
      АДМИР: sheets.dailyMoyka,
      УДЕЛКА: sheets.dailyAkku,
    }).filter(([, v]) => !v).map(([k]) => k) };
  };
})(typeof window !== 'undefined' ? window : globalThis);
