#!/usr/bin/env python3
"""Build categorized menu import JSON from Taplink API + local images."""

import json
import re
from pathlib import Path

import requests

ROOT = Path(__file__).resolve().parents[1]
CATALOG = ROOT / "images-menu" / "menu-catalog.tsv"
IMAGES_SRC = ROOT / "images-menu"
IMAGES_DST = ROOT / "public_html" / "admiralteyskaya" / "couch" / "uploads" / "image" / "menu-visual"
OUT_JSON = ROOT / "public_html" / "admiralteyskaya" / "menu" / "visual" / "taplink-import-data.json"

API = "https://taplink.cc/loungegarden/api/market/products/list.json"
HEADERS = {
    "User-Agent": "Mozilla/5.0",
    "Referer": "https://taplink.cc/loungegarden/m/199da0/",
}

COLLECTIONS = [
    ("bar", "199da0", "https://taplink.cc/loungegarden/m/199da0/"),
    ("desserts", "199dcd", "https://taplink.cc/loungegarden/m/199dcd/"),
    ("kitchen", "199d8d", "https://taplink.cc/loungegarden/m/199d8d/"),
    ("kitchen_extra", None, "https://taplink.cc/loungegarden/m/"),
]

# teas/lemonades from "all" page that belong to bar
BAR_TITLES = {
    "Green Bro", "Summer Breeze", "Tropical Extaz",
    "Лесные ягоды", "Молочный улун & белый шоколад", "Молочный чай с пряностями",
    "Облепиха&имбирь", "Пуэр на вишневом соке", "Травяной чай",
}


def parse_initial(html: str) -> list[dict]:
    m = re.search(r"window\.data = (\{.*?\});\s*</script>", html, re.S)
    if not m:
        return []
    return json.loads(m.group(1))["data"].get("products") or []


def fetch_collection(session: requests.Session, collection_hex: str | None, page_url: str) -> list[dict]:
    r = session.get(page_url, headers={"Referer": page_url}, timeout=60)
    r.raise_for_status()
    items = {p["product_id"]: p for p in parse_initial(r.text)}
    next_cursor = list(items.values())[-1]["column_id"] if items else 0
    collection_id = int(collection_hex, 16) if collection_hex else None
    while True:
        params = {"next": next_cursor, "filters": {"query": ""}}
        if collection_id is not None:
            params["collection_id"] = collection_id
        resp = session.get(API, params=params, headers={"Referer": page_url}, timeout=60)
        if resp.status_code != 200 or not resp.text.strip():
            break
        body = resp.json()
        if body.get("result") != "success":
            break
        batch = (body.get("response") or {}).get("products") or []
        if not batch:
            break
        for p in batch:
            items[p["product_id"]] = p
        next_cursor = batch[-1]["column_id"]
    return list(items.values())


def slugify(title: str) -> str:
    from unidecode import unidecode

    text = unidecode(title.strip()).lower()
    text = re.sub(r"[^a-z0-9]+", "-", text).strip("-")
    return text or "item"


def load_catalog() -> dict[str, dict]:
    rows = {}
    for line in CATALOG.read_text(encoding="utf-8").splitlines()[1:]:
        parts = line.split("\t")
        if len(parts) < 3:
            continue
        filename, title, price = parts[0], parts[1], parts[2]
        rows[title.strip()] = {"filename": filename.strip(), "price": price.strip(), "title": title.strip()}
    return rows


def main() -> None:
    session = requests.Session()
    session.headers.update(HEADERS)

    assigned: set[int] = set()
    by_cat: dict[str, dict[int, dict]] = {"bar": {}, "kitchen": {}, "desserts": {}}

    for cat, coll_hex, url in COLLECTIONS:
        if cat == "kitchen_extra":
            continue
        for p in fetch_collection(session, coll_hex, url):
            by_cat[cat][p["product_id"]] = p
            assigned.add(p["product_id"])

    # Items only on the "all" page: snacks -> kitchen, teas/lemonades -> bar
    for p in fetch_collection(session, None, COLLECTIONS[-1][2]):
        pid = p["product_id"]
        if pid in assigned:
            continue
        title = p["title"].strip()
        if title in BAR_TITLES:
            by_cat["bar"][pid] = p
        else:
            by_cat["kitchen"][pid] = p
        assigned.add(pid)

    catalog = load_catalog()
    IMAGES_DST.mkdir(parents=True, exist_ok=True)

    payload = {"bar": [], "kitchen": [], "desserts": []}
    missing_images = []

    for cat in ("bar", "kitchen", "desserts"):
        products = sorted(by_cat[cat].values(), key=lambda x: x["title"].lower())
        for p in products:
            title = p["title"].strip()
            cat_row = catalog.get(title)
            if not cat_row:
                # fuzzy: normalize spaces
                for k, v in catalog.items():
                    if k.replace("  ", " ") == title.replace("  ", " "):
                        cat_row = v
                        title = k
                        break
            price = str(p.get("price") if p.get("price") is not None else (cat_row or {}).get("price", ""))
            filename = (cat_row or {}).get("filename", "")
            if filename:
                src = IMAGES_SRC / filename
                dst = IMAGES_DST / filename
                if src.exists():
                    dst.write_bytes(src.read_bytes())
                else:
                    missing_images.append(filename)
                img = f"/admiralteyskaya/couch/uploads/image/menu-visual/{filename}"
            else:
                img = ""
                missing_images.append(title)

            payload[cat].append(
                {
                    "item_title": title,
                    "item_price": price,
                    "item_weight": "",
                    "item_img": img,
                    "item_desc": "",
                    "item_tag": "-",
                }
            )

    OUT_JSON.write_text(json.dumps(payload, ensure_ascii=False, indent=2), encoding="utf-8")
    print(json.dumps({k: len(v) for k, v in payload.items()}, ensure_ascii=False))
    if missing_images:
        print("missing:", missing_images[:10])


if __name__ == "__main__":
    main()
