[![License](https://img.shields.io/badge/license-GPL--3.0--only-blue)](https://spdx.org/licenses/GPL-3.0-only.html)
![Tests Coverage](https://img.shields.io/badge/coverage-100%25-brightgreen)
![GitHub tag (latest by date)](https://img.shields.io/github/v/tag/osamz/QuranSafahat)

# صفحاتُ القرآن الكريم

هذه المكتبة تحسِب طولَ المقطعِ القرآني بالصفحات والأسطر.

## التثبيت

```
composer require osamz/quran-safahat
```

## الاستعمال

```PHP
$startAya = $helper->getCheckedAyaPoint("النازعات", 17);
$endAya =   $helper->getCheckedAyaPoint("المرسلات", 50);

$clip =   new QuranClip($startAya, $endAya);

$quran =  new QuranNaskhEdition($quranData);

$quranCalculator = new QuranCalculator($quran);

$clipSize = $quranCalculator->calculate($clip);
echo "الصفحات: $clipSize->pages ، الأسطر: $clipSize->lines.";
// الصفحات: 4 ، الأسطر: 1
```

انظر مجلد الأمثلة: لتجد مثالًا مفصلًا

## الرخصة

[GPL-3.0-only](https://spdx.org/licenses/GPL-3.0-only.html)

## المُنشئ

- [أسامة](https://www.github.com/osamz)

