[![License](https://img.shields.io/badge/license-GPL--3.0--only-blue)](https://spdx.org/licenses/GPL-3.0-only.html)
![Tests Coverage](https://img.shields.io/badge/coverage-100%25-brightgreen)
![GitHub tag (latest by date)](https://img.shields.io/github/v/tag/osamz/QuranSafahat)

[للنسخة العربية انقر هنا](README.ar.md)

# Quran Safahat

This library calculates length of Quran Clip (Makta') (مقطع) the pages from point (ayah, surah) to another.

## Installation

```
composer require osamz/quran-safahat
```

## Usage

```PHP
$startAya = $helper->getCheckedAyaPoint("النازعات", 17);
$endAya =   $helper->getCheckedAyaPoint("المرسلات", 50);

$clip =   new QuranClip($startAya, $endAya);

$quran =  new QuranNaskhEdition($quranData);

$quranCalculator = new QuranCalculator($quran);

$clipSize = $quranCalculator->calculate($clip);
echo "Pages: $clipSize->pages, and $clipSize->lines lines.";
// Pages: 4, and 1 lines.
```

see: examples folder for detailed example.

## License

[GPL-3.0-only](https://spdx.org/licenses/GPL-3.0-only.html)

## Authors

- [@osamz](https://www.github.com/osamz)

