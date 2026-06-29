"""Inventory, writeoffs, transfers."""
from __future__ import annotations

from typing import Any


def _num(v: Any) -> float | None:
    if v is None:
        return None
    try:
        return float(v)
    except (TypeError, ValueError):
        return None


def parse_writeoffs(ws) -> dict:
    rows = []
    for r in range(3, ws.max_row + 1):
        reason = ws.cell(r, 1).value
        if not reason or 'итого' in str(reason).lower():
            continue
        m = {'bar': _num(ws.cell(r, 5).value), 'kitchen': _num(ws.cell(r, 6).value), 'shisha': _num(ws.cell(r, 7).value)}
        a = {'bar': _num(ws.cell(r, 2).value), 'kitchen': _num(ws.cell(r, 3).value), 'shisha': _num(ws.cell(r, 4).value)}
        s = lambda o: sum(x or 0 for x in o.values())
        rows.append({'reason': str(reason).strip(), 'moyka': s(m), 'akku': s(a), 'total': s(m) + s(a)})
    total = sum(x['total'] for x in rows)
    return {'rows': rows, 'total': total, 'byReason': sorted(rows, key=lambda x: x['total'], reverse=True)}


def parse_stock(ws) -> dict:
    warnings = []
    for r in range(2, 12):
        name = ws.cell(r, 1).value
        if not name or 'алкоголь' not in str(name).lower():
            continue
        vals = [_num(ws.cell(r, c).value) for c in range(2, 10)]
        if vals[4] and all(not v for v in vals[5:]):
            warnings.append(f'Алкоголь «{name}»: нулевые остатки после марта.')
    return {'warnings': warnings}


def parse_transfers(ws) -> dict:
    items = []
    for r in range(2, ws.max_row + 1):
        fr, to, amt = ws.cell(r, 1).value, ws.cell(r, 2).value, _num(ws.cell(r, 3).value)
        if fr and to and amt is not None:
            items.append({'from': str(fr).strip(), 'to': str(to).strip(), 'amount': amt})
    return {'items': items, 'total': sum(i['amount'] for i in items)}


def parse_inventory(ws, location: str, category: str) -> dict:
    items = []
    totals = {}
    for r in range(9, ws.max_row + 1):
        name = ws.cell(r, 2).value
        if not name:
            continue
        if str(name).lower() == 'итого':
            totals = {'surplusSum': _num(ws.cell(r, 12).value), 'shortageSum': _num(ws.cell(r, 14).value)}
            continue
        sur, sho = _num(ws.cell(r, 12).value) or 0, _num(ws.cell(r, 14).value) or 0
        if sur or sho:
            items.append({'name': str(name).strip(), 'surplus': sur, 'shortage': sho, 'absVariance': abs(sur) + abs(sho)})
    items.sort(key=lambda x: x['absVariance'], reverse=True)
    return {
        'location': location, 'category': category, 'sheet': ws.title,
        'totals': totals, 'absoluteVariance': sum(i['absVariance'] for i in items),
        'topAbs': items[:20],
    }
