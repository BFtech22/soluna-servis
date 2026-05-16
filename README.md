# soluna-servis.cz

Jednostránkový web pro Soluna Servis — pozáruční servis baterií Soluna pod skupinou BF technology s.r.o.

Postaveno stejnou stavebnicí jako [automatizovany-dum.cz](../automatizovany-dum) — stejná logika top baru, hlavičky, hero, sekcí, kontaktního formuláře i patičky. Hlavní vizuální odlišení: oranžová Soluna barva (`#f39200`) místo Loxone zelené.

## Co je v balíku

```
.
├── index.html                  # celý web (one-page)
├── send.php                    # zpracování kontaktního formuláře (mail() → info@bftechnology.cz)
├── README.md                   # tento soubor
├── assets/
│   ├── bf-technology-logo_final_bile.svg   # logo mateřské firmy (top bar, mobilní menu, footer)
│   └── favicon.svg                          # favicon — stylizovaná baterie
├── dokumenty/                  # PDF datasheety ke stažení (linkované ze sekce „Dokumentace")
│   ├── 1.) Soluna Brochure_HV Pack 10K 15K L-E _ V.202303.pdf
│   ├── HV-series.pdf
│   ├── HV-Parallel-Box-v10.pdf
│   ├── SOLUNA HV Active Balancing Instruction.pdf
│   └── Soluna Products 20231010.pdf
└── foto/
    └── indikator-led.jpg       # foto LED indikátoru Soluna (rezerva, zatím nevyužito)
```

Cesty v HTML jsou root-relativní (`assets/...`, `dokumenty/...`). Web tedy potřebuje webserver, ale **funguje i přes `file://`** (testováno).

## Sekce webu

1. **Hero** — claim „Pozáruční servis baterií Soluna" + vizuál stylizovaného HV stacku s parallel boxem
2. **Stats** — 8+ let, 500+ modulů, 24/7 diagnostika, CZ + SK
3. **Služby** — 3 karty: Výměna BMS / Oživení baterie / Upgrade firmware
4. **Modely** — 6 karet: HV Pack 10K, HV Pack 15K, S12 EU-G2 LV, 4K/8K Pack LV, HV Active Balancer, HV Parallel Box
5. **Parallel Box detail** — schéma zapojení 4 packů na 1 měnič + tabulka technických parametrů (převzato z `HV-Parallel-Box-v10.pdf`)
6. **O nás** — „bývalý technický support Nanosun s.r.o., nyní pod BF technology s.r.o."
7. **Dokumentace** — 5× PDF ke stažení
8. **Kontakt** — formulář s polem „Model baterie" jako select + kontaktní karta
9. **Footer** — odkazy na sourozenecké brandy ve skupině BF technology

## Lokální test

S PHP (formulář bude fungovat, pokud máš lokálně `mail()`):
```bash
cd /Users/bj_air/Downloads/Claude/soluna-servis
php -S localhost:8000
```

Jen statika (formulář nepošle e-mail):
```bash
python3 -m http.server 8000
# nebo
ruby -run -e httpd . -p 8000
```

Pak otevři <http://localhost:8000>.

## Nasazení na produkci

### Wedos / Active24 / Forpsi (PHP hosting) — doporučeno

1. FTP klientem (FileZilla, Cyberduck) nahraj **celý obsah** složky do `www/` nebo `public_html/`.
2. Nastav doménu `soluna-servis.cz` na hosting.
3. Otestuj formulář — vyplň, odešli, zkontroluj schránku `info@bftechnology.cz`.

### Netlify / Cloudflare Pages (jen statika, bez PHP)

PHP formulář pak nepoužiješ — přepiš `<form action="send.php">` na některý ze služebních endpointů:

- **Formspree** — `https://formspree.io/f/TVOJ_ID` (free 50 zpráv / měsíc)
- **Web3Forms** — `https://api.web3forms.com/submit` (free, bez limitu, vyžaduje skryté pole `access_key`)
- **Getform** — `https://getform.io/...`

Pak `send.php` smaž a deploy:
1. `git init && git add . && git commit -m "init"`
2. Push na GitHub / GitLab.
3. V Netlify / CF Pages → Import Git repo → Deploy.

## Co upravit před spuštěním

- [ ] **Statistiky** (`<section class="stats">`) — čísla „8+ let", „500+ modulů" doupřesnit nebo upravit.
- [ ] **Telefon** — momentálně `+420 776 111 100` (BF technology). Pokud má servis vlastní číslo, přepiš v `topbar`, `kontakt` sekci a footeru.
- [ ] **E-mail** — momentálně `info@bftechnology.cz`. Pokud chceš samostatnou adresu (`servis@soluna-servis.cz`), uprav v `index.html`, `send.php` a hlavně si schránku/forward založ u poskytovatele e-mailu.
- [ ] **Adresa** — momentálně Obchodní 455/12, Děčín. Změnit, pokud servisní dílna sídlí jinde.
- [ ] **Modely** — text u modelů (HV Pack 10K/15K, S12, 4K/8K…) je sepsaný z brožur. Doplň, pokud servisujete ještě jiné modely (např. Soluna 3K Pack, novější G3 řady…).
- [ ] **Specifikace LV modelů** (S12 EU-G2, 4K/8K Pack) — hodnoty kapacity/napětí jsou orientační z paměti. Před spuštěním porovnat s aktuálními datasheety.
- [ ] **Foto produktů** — momentálně jen stylizovaný HV stack v hero. Pokud máš fotky reálných instalací nebo servisních prací, doplň do hero/about sekce (`foto/` složka už existuje).

## Známé poznámky

- **Web není oficiální** stránkou Soluny ani Nanosun s.r.o. — to je explicitně v patičce („Soluna™ je ochranná známka výrobce. Tento web není oficiálním webem Soluny ani Nanosun s.r.o."). Než to spustíš, zvaž, jestli text není potřeba ještě jemněji formulovat z hlediska práva na ochrannou známku — slovo „Soluna" v doméně i názvu může vyvolat dotazy od výrobce / původního distributora.
- **GDPR** — formulář obsahuje GDPR doložku. Stránku „Ochrana osobních údajů" zatím nemá (na rozdíl od automatizovany-dum). Pokud chceš, doplníme `ochrana-osobnich-udaju/index.html` ve stejném stylu.
- **GTM / analytika** — záměrně neobsahuje žádný tracker. Pokud chceš měřit, dolep GA4 / Plausible / GTM kód.
