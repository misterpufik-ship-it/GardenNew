#!/usr/bin/env python3
"""Scrape full Taplink visual menu via paginated API and save WebP images."""

from __future__ import annotations

import io
import json
import re
from pathlib import Path

import requests
from PIL import Image
from unidecode import unidecode

ROOT = Path(__file__).resolve().parents[1]
OUT_DIR = ROOT / "images-menu"
API_URL = "https://taplink.cc/loungegarden/api/market/products/list.json"
IMAGE_BASE = "https://i.taplink.st/p/"
MAX_BYTES = 80 * 1024

PAGES = [
    (None, "https://taplink.cc/loungegarden/m/"),
    ("199da0", "https://taplink.cc/loungegarden/m/199da0/"),
    ("199dcd", "https://taplink.cc/loungegarden/m/199dcd/"),
    ("199d8d", "https://taplink.cc/loungegarden/m/199d8d/"),
]

SESSION = requests.Session()
SESSION.headers.update(
    {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36",
    }
)


def slugify_english(title: str) -> str:
    text = unidecode(title.strip())
    text = text.lower()
    text = re.sub(r"[^a-z0-9]+", "-", text)
    text = re.sub(r"-+", "-", text).strip("-")
    return text or "item"


def parse_initial_products(html: str) -> list[dict]:
    match = re.search(r"window\.data = (\{.*?\});\s*</script>", html, re.S)
    if not match:
        return []
    data = json.loads(match.group(1))["data"]
    return data.get("products") or []


def fetch_collection_products(page_url: str, collection_hex: str | None) -> list[dict]:
    response = SESSION.get(page_url, headers={"Referer": page_url}, timeout=60)
    response.raise_for_status()
    initial = parse_initial_products(response.text)
    items: dict[int, dict] = {p["product_id"]: p for p in initial}
    next_cursor = initial[-1]["column_id"] if initial else 0
    collection_id = int(collection_hex, 16) if collection_hex else None

    while True:
        params: dict = {"next": next_cursor, "filters": {"query": ""}}
        if collection_id is not None:
            params["collection_id"] = collection_id
        api_response = SESSION.get(
            API_URL,
            params=params,
            headers={"Referer": page_url},
            timeout=60,
        )
        if api_response.status_code != 200 or not api_response.text.strip():
            break
        body = api_response.json()
        if body.get("result") != "success":
            break
        batch = (body.get("response") or {}).get("products") or []
        if not batch:
            break
        for product in batch:
            items[product["product_id"]] = product
        next_cursor = batch[-1]["column_id"]

    return list(items.values())


def fetch_all_products() -> list[dict]:
    merged: dict[int, dict] = {}
    for collection_hex, page_url in PAGES:
        for product in fetch_collection_products(page_url, collection_hex):
            merged[product["product_id"]] = product
    return list(merged.values())


def download_image(picture_path: str) -> Image.Image:
    url = IMAGE_BASE + picture_path.lstrip("/")
    response = SESSION.get(url, timeout=60)
    response.raise_for_status()
    return Image.open(io.BytesIO(response.content)).convert("RGB")


def save_webp_under_limit(image: Image.Image, dest: Path, max_bytes: int = MAX_BYTES) -> int:
    width, height = image.size
    quality = 82
    scale = 1.0
    best = None

    for _ in range(32):
        resized = image
        if scale < 1.0:
            new_size = (max(1, int(width * scale)), max(1, int(height * scale)))
            resized = image.resize(new_size, Image.Resampling.LANCZOS)

        buffer = io.BytesIO()
        resized.save(buffer, format="WEBP", quality=quality, method=6)
        data = buffer.getvalue()
        if best is None or len(data) < len(best):
            best = data

        if len(data) <= max_bytes:
            dest.write_bytes(data)
            return len(data)

        if quality > 35:
            quality -= 5
        elif scale > 0.35:
            scale *= 0.85
            quality = 82
        else:
            break

    if best is None:
        raise RuntimeError(f"Failed to compress {dest.name}")

    dest.write_bytes(best)
    return len(best)


def unique_filename(base: str, used: set[str]) -> str:
    candidate = base
    index = 2
    while candidate in used:
        candidate = f"{base}-{index}"
        index += 1
    used.add(candidate)
    return candidate


def main() -> None:
    OUT_DIR.mkdir(parents=True, exist_ok=True)
    products = fetch_all_products()
    products.sort(key=lambda p: (p.get("title") or "").lower())

    used_names: set[str] = set()
    catalog_lines = ["filename\tdish_title\tprice_rub"]
    downloaded = 0
    skipped_no_photo = 0

    for product in products:
        title = (product.get("title") or "").strip()
        price = product.get("price")
        picture = product.get("picture")

        if not picture:
            skipped_no_photo += 1
            catalog_lines.append(f"\t{title}\t{price}")
            continue

        slug = unique_filename(slugify_english(title), used_names)
        filename = f"{slug}.webp"
        dest = OUT_DIR / filename

        if dest.exists():
            print(f"Skip existing: {filename}")
        else:
            print(f"Downloading: {title} -> {filename}")
            image = download_image(picture)
            size = save_webp_under_limit(image, dest)
            print(f"  saved {size // 1024} KB")
            downloaded += 1

        catalog_lines.append(f"{filename}\t{title}\t{price}")

    catalog_path = OUT_DIR / "menu-catalog.tsv"
    catalog_path.write_text("\n".join(catalog_lines) + "\n", encoding="utf-8")
    print(f"\nProducts total: {len(products)}")
    print(f"New downloads: {downloaded}")
    print(f"Without photo: {skipped_no_photo}")
    print(f"Catalog: {catalog_path}")


if __name__ == "__main__":
    main()
