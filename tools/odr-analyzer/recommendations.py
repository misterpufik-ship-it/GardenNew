"""Recommendations and executive summary."""
from __future__ import annotations


def build_executive(analysis: dict) -> dict:
    pnl = analysis.get('pnl') or {}
    m, a = pnl.get('moyka') or {}, pnl.get('akkuartova') or {}
    rev = (m.get('revenue') or {}).get('total') or 0 + (a.get('revenue') or {}).get('total') or 0
    rev = (m.get('revenue') or {}).get('total') or 0
    rev += (a.get('revenue') or {}).get('total') or 0
    profit = (m.get('profit') or {}).get('net') or 0
    profit += (a.get('profit') or {}).get('net') or 0
    guests = (m.get('analytics') or {}).get('guests') or 0
    guests += (a.get('analytics') or {}).get('guests') or 0
    inv_abs = sum((i.get('absoluteVariance') or 0) for i in analysis.get('inventories') or [])
    wo = (analysis.get('writeoffs') or {}).get('total') or 0
    return {
        'revenue': rev, 'profit': profit, 'margin': profit / rev if rev else None,
        'guests': guests, 'avgCheck': rev / guests if guests else None,
        'writeoffs': wo, 'inventoryAbsVariance': inv_abs,
        'strongerLocation': 'Мойка' if (m.get('profit') or {}).get('net', 0) >= (a.get('profit') or {}).get('net', 0) else 'Аккуратова',
    }


def build_recommendations(analysis: dict) -> dict:
    warnings, action7, action30, opportunities = [], [], [], []
    pnl = analysis.get('pnl') or {}
    for key, name in [('moyka', 'Мойка'), ('akkuartova', 'Аккуратова')]:
        loc = pnl.get(key) or {}
        oh = loc.get('overheads') or {}
        if oh.get('payrollPct') and oh['payrollPct'] > 0.45:
            warnings.append({'text': f'{name}: ФОТ критически высокий ({oh["payrollPct"]:.1%}).', 'priority': 'high'})
        margin = (loc.get('profit') or {}).get('margin')
        if margin is not None and margin < 0.05:
            warnings.append({'text': f'{name}: маржа {margin:.1%} — точка почти не зарабатывает.', 'priority': 'high'})
        kshare = ((loc.get('revenue') or {}).get('shares') or {}).get('kitchen')
        if kshare is not None and kshare < 0.18:
            warnings.append({'text': f'{name}: доля кухни {kshare:.1%} — ниже нормы.', 'priority': 'medium'})
    ex = build_executive(analysis)
    summary = f"Выручка {ex['revenue']:,.0f} ₽, прибыль {ex['profit']:,.0f} ₽, маржа {(ex['margin'] or 0):.1%}."
    return {'warnings': warnings, 'opportunities': opportunities, 'action7': action7, 'action30': action30, 'ownerSummary': summary}
