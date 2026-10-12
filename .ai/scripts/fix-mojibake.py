#!/usr/bin/env python3
"""
Mojibake (Karakter Bozulması) Düzeltme Scripti
=================================================
UTF-8 karakterlerin Windows-1252/Latin-1 olarak yorumlanıp tekrar kodlanmasını düzeltir.

Kullanım:
    python .ai/scripts/fix-mojibake.py [--dry-run] [--verbose]

Korunacak dosyalar:
    - .ai/**/*.md (247+ dosya)
    - .claude/**/*.md
    - .opencode/**/*.md
"""

import os
import re
import sys
import io
import argparse
from pathlib import Path
from typing import Dict, Tuple

# Windows konsol encoding sorunlarını çöz
if sys.platform == 'win32':
    sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')
    sys.stderr = io.TextIOWrapper(sys.stderr.buffer, encoding='utf-8', errors='replace')
    os.environ['PYTHONIOENCODING'] = 'utf-8'

# ─── Mojibake Düzeltme Haritası ──────────────────────────────────────────────
# Her satır: (bozulmuş_karakter, doğru_karakter, açıklama)
MOJIBAKE_FIXES: Dict[str, str] = {
    # ── Em Dash & Tire ──
    'â€"': '—',       # Em dash (en yaygın)
    'â€"': '—',       # Em dash (alternatif encoding)
    'â€"': '--',      # Em dash olarak kullanılmış

    # ── Matematik Sembolleri ──
    'Ã—': '×',         # Multiplication sign
    'â‰¥': '≥',        # Greater than or equal
    'â‰¤': '≤',        # Less than or equal
    'Ã·': '÷',         # Division sign
    'Â·': '·',         # Middle dot (interpunct)

    # ── Teknik Semboller ──
    'Â§': '§',         # Section sign
    'Â±': '±',         # Plus-minus
    'Î©': 'Ω',         # Ohm sign
    'â‚€': 'Ω',        # Ohm (alternatif)

    # ── Emoji ──
    'âœ…': '✅',       # Check mark

    # ── Ok İşaretleri ──
    'â†\'': '→',       # Right arrow (with apostrophe)
    'â†': '→',         # Right arrow (truncated)
    'â†œ': '←',        # Left arrow

    # ── Türkçe Karakterler (tekil mojibake) ──
    'Ã¶': 'ö',         # o-umlaut
    'Ã¼': 'ü',         # u-umlaut
    'Ã§': 'ç',         # c-cedilla
    'Ä±': 'ı',         # dotless-i
    'ÅŸ': 'ş',         # s-cedilla
    'ÄŸ': 'ğ',         # g-breve
    'Ã¤': 'ä',         # a-umlaut

    # ── Büyük Türkçe Karakterler ──
    'Ã–': 'Ö',         # O-umlaut (büyük)
    'Ãœ': 'Ü',         # U-umlaut (büyük)
    'Ã‡': 'Ç',         # C-cedilla (büyük)
    'Å': 'Ş',          # S-cedilla (büyük) - tek karakter kalıbı

    # ── Sayısal ──
    'Ã': 'İ',          # Turkish I (context-dependent, dikkatli kullanılmalı)

    # ── Diğer ──
    'Â»': '»',         # Right guillemet
    'Â«': '«',         # Left guillemet
    'â€¦': '…',        # Horizontal ellipsis
    'â€˜': "'",        # Single quote
    'â€ž': '"',        # Double quote (low)
    'â€œ': '"',        # Double quote (left)
    'â€™': "'",        # Right single quote
}

# ── Üçlü Kodlama Bozulmaları (triple-encoded) ──
# Bunlar daha önce iki kez bozulmuş karakterler
TRIPLE_ENCODED: Dict[str, str] = {
    'Ã„Â±': 'ı',       # triple-encoded dotless-i
    'Ã…Â¸': 'ş',       # triple-encoded s-cedilla
    'Ã„ÂŸ': 'ğ',       # triple-encoded g-breve
    'ÃƒÂ§': 'ç',       # triple-encoded c-cedilla
    'ÃƒÂ¶': 'ö',       # triple-encoded o-umlaut
    'ÃƒÂ¼': 'ü',       # triple-encoded u-umlaut
    'â\u009dŒ': '✅',   # check mark variant 1
    'â\u009cš \u008f': '✅',  # check mark variant 2
}

# ─── Dosya İşleme ────────────────────────────────────────────────────────────

def fix_mojibake_in_text(text: str) -> Tuple[str, int]:
    """
    Metin içindeki mojibake kalıplarını düzelt.
    Döndürür: (düzeltilmiş_metin, düzeltme_sayısı)
    """
    fix_count = 0

    # Önce üçlü kodlama bozulmalarını düzelt (en derin olanlardan başla)
    for broken, correct in TRIPLE_ENCODED.items():
        if broken in text:
            count = text.count(broken)
            text = text.replace(broken, correct)
            fix_count += count

    # Sonra tekil mojibake bozulmalarını düzelt
    for broken, correct in MOJIBAKE_FIXES.items():
        if broken in text:
            count = text.count(broken)
            text = text.replace(broken, correct)
            fix_count += count

    return text, fix_count


def process_file(filepath: Path, dry_run: bool = False, verbose: bool = False) -> Tuple[int, int]:
    """
    Tek bir dosyayı işle.
    Döndürür: (dosya_boyutu_değişikliği, düzeltme_sayısı)
    """
    try:
        # Dosyayı UTF-8 olarak oku
        original = filepath.read_text(encoding='utf-8')
    except UnicodeDecodeError:
        try:
            # UTF-8 okunamazsa, Latin-1 olarak oku ve UTF-8'a çevir
            original = filepath.read_text(encoding='latin-1')
            if verbose:
                print(f"  ⚠️  {filepath}: Latin-1 olarak okundu, UTF-8'a çevriliyor")
        except Exception as e:
            print(f"  ❌ {filepath}: Okuma hatası - {e}")
            return 0, 0

    # Mojibake düzeltmesi uygula
    fixed, fix_count = fix_mojibake_in_text(original)

    if fix_count == 0:
        return 0, 0

    if verbose:
        print(f"  🔧 {filepath}: {fix_count} düzeltme")

    if not dry_run:
        try:
            filepath.write_text(fixed, encoding='utf-8')
        except Exception as e:
            print(f"  ❌ {filepath}: Yazma hatası - {e}")
            return 0, 0

    return len(fixed) - len(original), fix_count


def scan_directory(base_dir: Path, patterns: list[str], dry_run: bool = False, verbose: bool = False) -> dict:
    """
    Belirtilen dizinlerdeki tüm dosyaları tara ve düzelt.
    """
    stats = {
        'files_scanned': 0,
        'files_modified': 0,
        'total_fixes': 0,
        'total_size_change': 0,
        'modified_files': [],
    }

    for pattern in patterns:
        for filepath in base_dir.glob(pattern):
            if not filepath.is_file():
                continue

            stats['files_scanned'] += 1
            size_change, fix_count = process_file(filepath, dry_run, verbose)

            if fix_count > 0:
                stats['files_modified'] += 1
                stats['total_fixes'] += fix_count
                stats['total_size_change'] += size_change
                stats['modified_files'].append(str(filepath.relative_to(base_dir)))

    return stats


# ─── Ana İşlev ───────────────────────────────────────────────────────────────

def main():
    parser = argparse.ArgumentParser(
        description='Mojibake (karakter bozulması) düzeltme scripti',
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
Örnekler:
  python fix-mojibake.py --dry-run --verbose   # Sadece tara, düzeltme yapma
  python fix-mojibake.py --verbose              # Düzelt ve raporla
  python fix-mojibake.py                        # Sessizce düzelt
        """
    )
    parser.add_argument('--dry-run', action='store_true',
                        help='Sadece tara, dosyaları değiştirme')
    parser.add_argument('--verbose', action='store_true',
                        help='Ayrıntılı çıktı göster')

    args = parser.parse_args()

    # Proje kök dizini
    base_dir = Path(__file__).parent.parent.parent  # .ai/scripts/ -> proje kökü

    print("=" * 60)
    print("🔧 Mojibake Düzeltme Scripti")
    print("=" * 60)

    if args.dry_run:
        print("⚠️  DRY-RUN modu: Dosyalar değiştirilmeyecek")
    print()

    # Tara
    patterns = [
        '.ai/**/*.md',
        '.claude/**/*.md',
        '.opencode/**/*.md',
    ]

    print("📂 Dizinler taranıyor...")
    stats = scan_directory(base_dir, patterns, args.dry_run, args.verbose)

    # Rapor
    print()
    print("=" * 60)
    print("📊 RAPOR")
    print("=" * 60)
    print(f"  Taranan dosya:     {stats['files_scanned']}")
    print(f"  Düzeltilen dosya:  {stats['files_modified']}")
    print(f"  Toplam düzeltme:   {stats['total_fixes']}")
    print(f"  Boyut değişikliği: {stats['total_size_change']:+} byte")
    print()

    if stats['modified_files']:
        print("📝 Düzeltilen dosyalar:")
        for f in stats['modified_files']:
            print(f"    • {f}")
    else:
        print("✅ Mojibake tespit edilmedi!")

    print()
    if args.dry_run and stats['total_fixes'] > 0:
        print("💡 Gerçek düzeltme için --dry-run bayrağını kaldırın")

    return 0 if stats['total_fixes'] == 0 else 1


if __name__ == '__main__':
    sys.exit(main())
