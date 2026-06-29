#!/usr/bin/env python3
"""Garden Lounge ODR Excel analyzer — CLI entry point."""
from __future__ import annotations

import argparse
import json
import sys
from pathlib import Path

from workbook_loader import load_odr_workbook
from pnl_analysis import parse_pnl
from daily_sales_analysis import parse_daily
from inventory_analysis import parse_writeoffs, parse_stock, parse_transfers, parse_inventory
from validators import scan_errors, validate_data
from recommendations import build_recommendations, build_executive


def main() -> int:
    parser = argparse.ArgumentParser(description='Analyze Garden Lounge ODR Excel report')
    parser.add_argument('xlsx', type=Path, help='Path to ODR xlsx file')
    parser.add_argument('-o', '--output', type=Path, help='Output JSON path')
    parser.add_argument('--month', default='', help='Report month YYYY-MM')
    args = parser.parse_args()

    wb = load_odr_workbook(args.xlsx)
    sheets = wb['sheets']
    sheet_names = wb['sheet_names']

    pnl = parse_pnl(sheets.get('odr')) if sheets.get('odr') else None
    daily = {}
    if sheets.get('daily_moyka'):
        daily['moyka'] = parse_daily(sheets['daily_moyka'], 'Мойка')
    if sheets.get('daily_akku'):
        daily['akkuartova'] = parse_daily(sheets['daily_akku'], 'Аккуратова')

    writeoffs = parse_writeoffs(sheets['writeoffs']) if sheets.get('writeoffs') else None
    stock = parse_stock(sheets['stock']) if sheets.get('stock') else None
    transfers = parse_transfers(sheets['transfers']) if sheets.get('transfers') else None

    inventories = []
    for key, loc, cat in [
        ('inv_bar_moyka', 'Мойка', 'Бар б/а'),
        ('inv_bar_akku', 'Аккуратова', 'Бар б/а'),
        ('inv_km_moyka', 'Мойка', 'Кальяны'),
        ('inv_km_akku', 'Аккуратова', 'Кальяны'),
        ('inv_kitchen_moyka', 'Мойка', 'Кухня'),
        ('inv_kitchen_akku', 'Аккуратова', 'Кухня'),
    ]:
        if sheets.get(key):
            inventories.append(parse_inventory(sheets[key], loc, cat))

    analysis = {
        'meta': {
            'fileName': args.xlsx.name,
            'month': args.month,
            'sheetNames': sheet_names,
        },
        'dataQuality': {
            'errors': scan_errors(wb['workbook']),
        },
        'pnl': pnl,
        'daily': daily,
        'writeoffs': writeoffs,
        'stock': stock,
        'transfers': transfers,
        'inventories': inventories,
    }
    validation = validate_data(analysis, sheets)
    analysis['dataQuality']['reconciliations'] = validation['issues']
    analysis['dataQuality']['missingSheets'] = validation['missing']
    analysis['executive'] = build_executive(analysis)
    analysis['recommendations'] = build_recommendations(analysis)

    out = json.dumps(analysis, ensure_ascii=False, indent=2)
    if args.output:
        args.output.write_text(out, encoding='utf-8')
        print(f'Written: {args.output}')
    else:
        print(out)
    return 0


if __name__ == '__main__':
    sys.exit(main())
