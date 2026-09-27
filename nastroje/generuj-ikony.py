#!/usr/bin/env python3
"""Rastrove ikony z assets/favicon.svg.

Google i Apple potrebuji PNG: Google chce ctverec v nasobku 48 px, Apple
u apple-touch-icon SVG vubec neumi. SVG zustava zdrojem pravdy — po jeho
zmene spustit znovu:

    python3 nastroje/generuj-ikony.py
"""

from pathlib import Path

from PIL import Image, ImageDraw

KOREN = Path(__file__).resolve().parent.parent
ASSETS = KOREN / "assets"

POZADI = (14, 16, 21, 255)     # #0e1015
ZLUTA = (248, 181, 0)          # #f8b500
KRESLI_NA = 768                # kreslime 12x vetsi a zmensujeme, at jsou hrany hladke


def znacka(strana: int) -> Image.Image:
    """Baterie ze tri clanku — stejne tvary jako v assets/favicon.svg."""
    k = KRESLI_NA / 64          # prevod ze souradnic SVG (viewBox 0 0 64 64)
    img = Image.new("RGBA", (KRESLI_NA, KRESLI_NA), (0, 0, 0, 0))
    d = ImageDraw.Draw(img)
    d.rounded_rectangle([0, 0, KRESLI_NA - 1, KRESLI_NA - 1], radius=12 * k, fill=POZADI)

    # obrys baterie — SVG kresli tah na stred cary, proto ramecek o pulku rozsirime
    d.rounded_rectangle([(8 - 1.5) * k, (20 - 1.5) * k, (52 + 1.5) * k, (48 + 1.5) * k],
                        radius=4 * k, outline=ZLUTA + (255,), width=round(3 * k))
    # kontakt
    d.rounded_rectangle([52 * k, 28 * k, 57 * k, 40 * k], radius=1.5 * k, fill=ZLUTA + (255,))

    # tri clanky s klesajici sytosti
    for x, kryti in ((13, 1.0), (25, 0.6), (37, 0.3)):
        vrstva = Image.new("RGBA", img.size, (0, 0, 0, 0))
        ImageDraw.Draw(vrstva).rounded_rectangle(
            [x * k, 25 * k, (x + 9) * k, 43 * k], radius=1.5 * k,
            fill=ZLUTA + (round(255 * kryti),))
        img.alpha_composite(vrstva)

    return img.resize((strana, strana), Image.LANCZOS)


if __name__ == "__main__":
    for strana, jmeno in ((192, ASSETS / "favicon-192.png"),
                          (180, ASSETS / "apple-touch-icon.png")):
        znacka(strana).save(jmeno, optimize=True)
        print(f"  {jmeno.relative_to(KOREN)}  {strana}x{strana}")

    ico = KOREN / "favicon.ico"
    znacka(48).save(ico, "ICO", sizes=[(16, 16), (32, 32), (48, 48)])
    print(f"  {ico.name}  16/32/48")
