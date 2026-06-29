"""Daily revenue sheets АДМИР / УДЕЛКА."""
from __future__ import annotations

from datetime import datetime, date
from typing import Any


def _num(v: Any) -> float | None:
    if v is None:
        return None
    try:
        return float(v)
    except (TypeError, ValueError):
        return None


def parse_daily(ws, location: str) -> dict:
    days = []
    for r in range(3, ws.max_row + 1):
        raw = ws.cell(r, 1).value
        if not isinstance(raw, (datetime, date)):
            continue
        fact = _num(ws.cell(r, 4).value)
        if fact is None:
            continue
        bonus = (_num(ws.cell(r, 10).value) or 0) + (_num(ws.cell(r, 11).value) or 0)
        net = fact - bonus
        guests = _num(ws.cell(r, 14).value)
        days.append({
            'date': raw.strftime('%Y-%m-%d') if hasattr(raw, 'strftime') else str(raw)[:10],
            'weekday': ws.cell(r, 2).value,
            'fact': fact, 'netRevenue': net, 'guests': guests,
            'bar': _num(ws.cell(r, 17).value), 'kitchen': _num(ws.cell(r, 18).value),
            'shisha': _num(ws.cell(r, 20).value),
            'kitchenShare': (_num(ws.cell(r, 18).value) / fact if fact and _num(ws.cell(r, 18).value) else None),
        })
    total_net = sum(d['netRevenue'] for d in days)
    sorted_days = sorted(days, key=lambda d: d['netRevenue'], reverse=True)
    return {
        'location': location, 'days': days,
        'stats': {'daysCount': len(days), 'totalNet': total_net, 'totalGuests': sum(d['guests'] or 0 for d in days)},
        'topBest': sorted_days[:5], 'topWorst': list(reversed(sorted_days[-5:])),
    }
