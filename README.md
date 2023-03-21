[![GPLv3 License](https://img.shields.io/badge/License-GPL%20v3-yellow.svg)](https://opensource.org/licenses/)

# Quran Safahat
This library calculates length of Quran Clip (Makta') the pages from point (ayah, surah) to another.



## Usage/Examples

```PHP
$startAya = $helper->getCheckedAyaPoint("النازعات", 17);
$endAya = $helper->getCheckedAyaPoint("المرسلات", 50);

$clip = new QuranClip($startAya, $endAya);

$quran = new QuranNaskhEdition($quranData);

$quranCalculator = new QuranCalculator($quran);

$clipSize = $quranCalculator->calculate($clip);
echo "Pages: $clipSize->pages, and $clipSize->lines lines.";
// Pages: 4, and 1 lines.
```

