"""Data quality checks."""
from __future__ import annotations

ERROR_PREFIX = ('#REF!', '#DIV/0!', '#NAME?', '#VALUE!', '#N/A')


def scan_errors(wb) -> list:
    out = []
    for ws in wb.worksheets:
        for row in ws.iter_rows():
            for cell in row:
                v = cell.value
                if isinstance(v, str) and v.startswith('#'):
                    out.append({'sheet': ws.title, 'cell': cell.coordinate, 'error': v})
    return out


def validate_data(analysis: dict, sheets: dict) -> dict:
    issues = []
    missing = [k for k, v in [('ОДР', sheets.get('odr')), ('АДМИР', sheets.get('daily_moyka')), ('УДЕЛКА', sheets.get('daily_akku'))] if not v]
    return {'issues': issues, 'missing': missing}
