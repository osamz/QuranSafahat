<?php
chdir(__DIR__);
require_once "../vendor/autoload.php";
require_once 'AyaForTest.php';

use PHPUnit\Framework\TestCase;
use QuranSafahat\Helper;
use QuranSafahat\JsonDecoder;
use QuranSafahat\QuranJsonData;

class HelperTest extends TestCase
{
  use AyaForTest;

  private Helper $helper;

  public function __construct(string $name)
  {
    parent::__construct($name);
    $qDataFile = "data/quran.json";
    $quranData = new QuranJsonData(new JsonDecoder($qDataFile));

    $suraDataFile = "data/sura_data.json";
    $this->helper = new Helper(new JsonDecoder($suraDataFile), $quranData);

  }

  public function test__construct()
  {
    $qDataFile = "data/quran.json";
    $quranData = new QuranJsonData(new JsonDecoder($qDataFile));

    $suraDataFile = "data/sura_data.json";

    $helper = $this->helper = new Helper(new JsonDecoder($suraDataFile), $quranData);
    $this->assertInstanceOf(Helper::class, $helper);
  }

  public function testGetSuraNoByAltNameNotFound()
  {
    $altName = "incorrect";
    $this->expectExceptionObject(new \QuranSafahat\Exceptions\SuraNameNotFound($altName));
    $this->helper->getSuraNoByAltName($altName);
  }

  public function testGetSuraNoByAltName()
  {
    $altName = "الفاتحة";
    $suraNo = $this->helper->getSuraNoByAltName($altName);
    $this->assertEquals(1, $suraNo);
  }

  public function testGetCheckedAyaPointAyaNotFound()
  {
    $ayaNo = 10;
    $this->expectException(\QuranSafahat\Exceptions\AyaNotFound::class);
    $altName = "الفاتحة";
    $this->helper->getCheckedAyaPoint($altName, $ayaNo);
  }

  public function testGetCheckedAyaPoint()
  {
    $ayaNo = 1;
    $altName = "الفاتحة";
    $ayaPoint = $this->helper->getCheckedAyaPoint($altName, $ayaNo);
    $this->assertEquals($ayaPoint, self::firstAya());
  }

  // TODO: write test for getting last aya of sura by surData.json
}
