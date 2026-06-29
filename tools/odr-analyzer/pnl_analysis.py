"""P&L (ОДР sheet) parser."""
from __future__ import annotations

from typing import Any


def _num(v: Any) -> float | None:
    if v is None or isinstance(v, str) and v.startswith('#'):
        return None
    try:
        return float(v)
    except (TypeError, ValueError):
        return None


def _read(ws, row: int, loc: str) -> float | None:
    col = 4 if loc == 'Мойка' else 5
    return _num(ws.cell(row, col).value)


def parse_pnl(ws) -> dict:
    def build(loc: str) -> dict:
        revenue_total = _read(ws, 5, loc)
        bar, kitchen, shisha = _read(ws, 7, loc), _read(ws, 8, loc), _read(ws, 9, loc)
        cogs_total = _read(ws, 14, loc)
        cogs_pct = _read(ws, 38, loc)
        payroll, rent = _read(ws, 43, loc), _read(ws, 53, loc)
        net_profit, net_margin = _read(ws, 77, loc), _read(ws, 78, loc)
        guests, avg_check, days = _read(ws, 96, loc), _read(ws, 98, loc), _read(ws, 93, loc)
        share = lambda p: (p / revenue_total if revenue_total and p is not None else None)
        return {
            'revenue': {'total': revenue_total, 'bar': bar, 'kitchen': kitchen, 'shisha': shisha,
                        'shares': {'bar': share(bar), 'kitchen': share(kitchen), 'shisha': share(shisha)}},
            'cogs': {'total': cogs_total, 'pctOfRevenue': (cogs_total / revenue_total if revenue_total and cogs_total else None), 'pct': cogs_pct},
            'overheads': {'payroll': payroll, 'payrollPct': (payroll / revenue_total if revenue_total and payroll else None), 'rent': rent},
            'profit': {'net': net_profit, 'margin': net_margin, 'netPerGuest': (net_profit / guests if guests and net_profit else None)},
            'analytics': {'guests': guests, 'avgCheck': avg_check, 'days': days},
        }

    moyka, akku = build('Мойка'), build('Аккуратова')
    rev = (moyka['revenue']['total'] or 0) + (akku['revenue']['total'] or 0)
    net = (moyka['profit']['net'] or 0) + (akku['profit']['net'] or 0)
    return {'moyka': moyka, 'akkuartova': akku, 'total': {'revenue': {'total': rev}, 'profit': {'net': net, 'margin': net / rev if rev else None}}}
