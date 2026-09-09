#!/usr/bin/env node
// vault-cmd.mjs — CoreMusic Türkçe komut arayüzü (yazım-hatası toleranslı)
//
// Amaç: Vault Updater'ın terminal ihtiyaçlarını PowerShell'e danışmadan karşılar.
// Salt-okunur komutlar serbest; YAZIMA yol açan işlemler vault-utf8-writer.mjs'ye
// yönlendirilir (PowerShell dosya cmdlet'leri yasaktır — UTF-8 bozulması).
//
// Kullanım:
//   node vault-cmd.mjs ls <klasor>              (takma adlar: dir, liste)
//   node vault-cmd.mjs type <dosya>             (takma adlar: oku, cat)
//   node vault-cmd.mjs kg <metin> <klasor> [--ext .md]   (takma adlar: ara, grep, bul)
//   node vault-cmd.mjs chk <dosya>              (takma adlar: dogrula, verify)
//   node vault-cmd.mjs ekle --file <hedef> --entry-file <girdi>   (-> append)
//   node vault-cmd.mjs yaz --file <hedef> --content-file <icerik> (-> write)
//   node vault-cmd.mjs onar --file <hedef>      (-> repair)
//   node vault-cmd.mjs tara --dir <klasor> [--idle-min 10]        (-> scan)
//   node vault-cmd.mjs yardim

import fs from 'node:fs';
import path from 'node:path';
import { spawnSync } from 'node:child_process';

const WRITER = path.join(import.meta.dirname, 'vault-utf8-writer.mjs');

// --- Türkçe yardımcılar -----------------------------------------------------

function levenshtein(a, b) {
  const m = a.length;
  const n = b.length;
  if (!m) return n;
  if (!n) return m;
  let prev = Array.from({ length: n + 1 }, (_, j) => j);
  for (let i = 1; i <= m; i++) {
    const cur = [i];
    for (let j = 1; j <= n; j++) {
      cur[j] = Math.min(
        prev[j] + 1,
        cur[j - 1] + 1,
        prev[j - 1] + (a[i - 1] === b[j - 1] ? 0 : 1),
      );
    }
    prev = cur;
  }
  return prev[n];
}

// --- Komut kaydı ------------------------------------------------------------

const KOMUTLAR = [
  { ad: 'ls', takma: ['ls', 'dir', 'liste'], aciklama: 'Klasör içeriğini listeler', tur: 'oku' },
  { ad: 'type', takma: ['type', 'oku', 'cat'], aciklama: 'Dosya içeriğini yazdırır (UTF-8)', tur: 'oku' },
  { ad: 'kg', takma: ['kg', 'ara', 'grep', 'bul'], aciklama: 'Klasörde metin arar [--ext .md]', tur: 'oku' },
  { ad: 'chk', takma: ['chk', 'dogrula', 'verify', 'kontrol'], aciklama: 'UTF-8/BOM denetimi yapar', tur: 'oku' },
  { ad: 'ekle', takma: ['ekle', 'append', 'log'], aciklama: 'Hedef dosyaya kayıt ekler (append)', tur: 'yazim' },
  { ad: 'yaz', takma: ['yaz', 'write'], aciklama: 'Dosyayı UTF-8 (BOM\u2019suz) yazar', tur: 'yazim' },
  { ad: 'onar', takma: ['onar', 'repair', 'duzelt'], aciklama: 'CP1254 bozuk baytları UTF-8\u2019e çevirir', tur: 'yazim' },
  { ad: 'tara', takma: ['tara', 'scan'], aciklama: 'Dizin çapında UTF-8/BOM taraması', tur: 'oku' },
  { ad: 'yardim', takma: ['yardim', 'help', '?'], aciklama: 'Komut listesini gösterir', tur: 'oku' },
];

function komutBul(girdi) {
  for (const k of KOMUTLAR) {
    if (k.takma.includes(girdi)) return { komut: k, duzeltildi: null };
  }
  // yazım kontrolü: en yakın takma ad (mesafe <= 2)
  let enIyi = null;
  for (const k of KOMUTLAR) {
    for (const t of k.takma) {
      const d = levenshtein(girdi.toLowerCase(), t);
      if (!enIyi || d < enIyi.d) enIyi = { d, t, komut: k };
    }
  }
  if (enIyi && enIyi.d <= 2) return { komut: enIyi.komut, duzeltildi: enIyi.t };
  return null;
}

function yardimGoster() {
  console.log('CoreMusic Vault Komut Arayüzü — Türkçe');
  console.log('Kullanım: node vault-cmd.mjs <komut> [argümanlar]');
  console.log('');
  for (const k of KOMUTLAR) {
    const yazi = k.tur === 'yazim' ? ' [yazım → utf8-writer]' : '';
    console.log(`  ${k.takma.join(', ').padEnd(22)} ${k.aciklama}${yazi}`);
  }
  console.log('');
  console.log('Yazım hatası toleransı: bilinmeyen komut, en yakın komuta (mesafe ≤ 2) otomatik düzeltilir.');
}

function writerCalistir(args) {
  const sonuc = spawnSync(process.execPath, [WRITER, ...args], { stdio: 'inherit' });
  process.exit(sonuc.status ?? 1);
}

// --- Ana --------------------------------------------------------------------

const [, , girdi, ...args] = process.argv;
if (!girdi || girdi === 'yardim') { yardimGoster(); process.exit(0); }

const eslesme = komutBul(girdi);
if (!eslesme) {
  console.error(`HATA: "${girdi}" bilinmeyen komut. "node vault-cmd.mjs yardim" ile listeye bakın.`);
  process.exit(1);
}
if (eslesme.duzeltildi) {
  console.log(`Not: "${girdi}" bulunamadı — Türkçe yazım kontrolüyle "${eslesme.duzeltildi}" olarak yorumlandı.`);
}
const komut = eslesme.komut;

switch (komut.ad) {
  case 'ls': {
    const hedef = path.resolve(args[0] || '.');
    if (!fs.existsSync(hedef)) { console.error(`HATA: Klasör bulunamadı: ${hedef}`); process.exit(1); }
    const ogeler = fs.readdirSync(hedef, { withFileTypes: true });
    const klasorler = ogeler.filter((e) => e.isDirectory()).map((e) => e.name);
    const dosyalar = ogeler
      .filter((e) => e.isFile())
      .map((e) => {
        const st = fs.statSync(path.join(hedef, e.name));
        return { ad: e.name, boyut: st.size };
      })
      .sort((a, b) => a.ad.localeCompare(b.ad, 'tr'));
    console.log(`Klasör: ${hedef}`);
    console.log(`Toplam: ${klasorler.length} klasör, ${dosyalar.length} dosya`);
    console.log('');
    for (const k of klasorler.sort((a, b) => a.localeCompare(b, 'tr'))) console.log(`  [KLASÖR]  ${k}`);
    for (const d of dosyalar) console.log(`  ${String(d.boyut).padStart(9)} B  ${d.ad}`);
    break;
  }

  case 'type': {
    if (!args[0]) { console.error('HATA: Dosya yolu gerekli. Kullanım: type <dosya>'); process.exit(1); }
    const hedef = path.resolve(args[0]);
    if (!fs.existsSync(hedef)) { console.error(`HATA: Dosya bulunamadı: ${hedef}`); process.exit(1); }
    const icerik = fs.readFileSync(hedef, 'utf8').replace(/^\uFEFF/, '');
    process.stdout.write(icerik);
    break;
  }

  case 'kg': {
    const metin = args[0];
    const klasor = args[1] || '.';
    if (!metin) { console.error('HATA: Aranacak metin gerekli. Kullanım: kg <metin> <klasor> [--ext .md]'); process.exit(1); }
    const extIdx = args.indexOf('--ext');
    const ext = extIdx >= 0 ? args[extIdx + 1] : null;
    const kok = path.resolve(klasor);
    const eslesenler = [];
    (function walk(d) {
      for (const e of fs.readdirSync(d, { withFileTypes: true })) {
        const p = path.join(d, e.name);
        if (e.isDirectory()) { walk(p); continue; }
        if (ext && path.extname(e.name).toLowerCase() !== ext) continue;
        let icerik;
        try { icerik = fs.readFileSync(p, 'utf8'); } catch { continue; }
        const satirlar = icerik.split(/\r?\n/);
        for (let i = 0; i < satirlar.length; i++) {
          if (satirlar[i].includes(metin)) {
            eslesenler.push({ dosya: path.relative(kok, p), satir: i + 1, metin: satirlar[i].trim().slice(0, 120) });
            if (eslesenler.length >= 50) return;
          }
        }
      }
    })(kok);
    if (!eslesenler.length) { console.log(`Sonuç bulunamadı: "${metin}"`); break; }
    console.log(`Arama: "${metin}" | ${eslesenler.length} eşleşme (ilk 50):`);
    for (const e of eslesenler) console.log(`  ${e.dosya}:${e.satir}: ${e.metin}`);
    break;
  }

  case 'chk': {
    if (!args[0]) { console.error('HATA: Dosya yolu gerekli. Kullanım: chk <dosya>'); process.exit(1); }
    writerCalistir(['verify', '--file', args[0]]);
    break;
  }

  case 'ekle':
  case 'yaz':
  case 'onar':
  case 'tara': {
    const modu = { ekle: 'append', yaz: 'write', onar: 'repair', tara: 'scan' }[komut.ad];
    writerCalistir([modu, ...args]);
    break;
  }

  case 'yardim':
    yardimGoster();
    break;
}
