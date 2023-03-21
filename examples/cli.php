<?php

use QuranSafahat\Editions\QuranNaskhEdition;
use QuranSafahat\Entities\QuranClip;
use QuranSafahat\Exceptions\AyaNotFound;
use QuranSafahat\Exceptions\SuraNameNotFound;
use QuranSafahat\Helper;
use QuranSafahat\JsonDecoder;
use QuranSafahat\QuranCalculator;
use QuranSafahat\QuranJsonData;

require_once '../vendor/autoload.php';

// Configuration of data sources
$quranDataFile = "../data/quran.json";
$suraDataFile = "../data/sura_data.json";

try {
  $quranData = new QuranJsonData(new JsonDecoder($quranDataFile));
} catch (JsonException|Exception $e) {
  exit($e->getMessage());
}


try {
  $helper = new Helper(new JsonDecoder($suraDataFile), $quranData);
} catch (Exception $e) {
  exit($e->getMessage());
}

try {
  $startAya = $helper->getCheckedAyaPoint("النازعات", 17);
  $endAya = $helper->getCheckedAyaPoint("المرسلات", 50);
} catch (AyaNotFound|SuraNameNotFound $e) {
  exit($e->getMessage());
}

try {
  $clip = new QuranClip($startAya, $endAya);
} catch (Exception $e) {
  exit($e->getMessage());
}

$quran = new QuranNaskhEdition($quranData);

$quranCalculator = new QuranCalculator($quran);

try {
  $clipSize = $quranCalculator->calculate($clip);
  echo "Pages: $clipSize->pages, and $clipSize->lines lines.";
} catch (Exception $e) {
  exit($e->getMessage());
}

