<?php
chdir(__DIR__);
ini_set("memory_limit", "1024M");

require_once '../vendor/autoload.php';
require_once 'AyaForTest.php';

use PHPUnit\Framework\TestCase;
use QuranSafahat\Editions\QuranNaskhEdition;
use QuranSafahat\Entities\Aya;
use QuranSafahat\Entities\RichAya;
use QuranSafahat\Interfaces\QuranDataRetriever;

class QuranJsonDataTest extends TestCase
{
  use AyaForTest;

  private QuranDataRetriever $retriever;

  private Aya $firstAya;
  private Aya $lastAya;

  public function __construct(?string $name = null, array $data = [], $dataName = '')
  {
    parent::__construct($name, $data, $dataName);
    $this->firstAya = self::firstAya();
    $this->lastAya = self::lastAya();

    $file = "data/quran.json";
    $decoder = new \QuranSafahat\JsonDecoder($file);
    $data = new \QuranSafahat\QuranJsonData($decoder);
    $this->retriever = $data;
    $this->assertInstanceOf(\QuranSafahat\QuranJsonData::class, $this->retriever);
  }

  public function test__construct()
  {
    $file = "data/quran.json";
    $decoder = new \QuranSafahat\JsonDecoder($file);
    $this->assertInstanceOf(\QuranSafahat\JsonDecoder::class, $decoder);
    $this->assertIsArray($decoder->decode());

    $data = new \QuranSafahat\QuranJsonData($decoder);
    $this->assertInstanceOf(\QuranSafahat\QuranJsonData::class, $this->retriever);
  }

  public function testGetSuraNoByNameIncorrect()
  {
    $name = "incorrect name";
    $this->expectExceptionObject(new \QuranSafahat\Exceptions\SuraNameNotFound($name));
    $this->retriever->getSuraNoByName($name);
  }

  public function testGetSuraNoByName()
  {
    $name = "الفَاتِحة";
    $soraNo = $this->retriever->getSuraNoByName($name);
    $this->assertIsInt($soraNo);
    $this->assertEquals(1, $soraNo);
  }

  public function testGetAyaNotFound()
  {
    $this->expectExceptionObject(new \QuranSafahat\Exceptions\AyaNotFound(0, 0));
    $this->retriever->getAya(0, 0);
  }

  public function testGetAya()
  {
    $actAya = $this->retriever->getAya(1, 1);
    $this->assertInstanceOf(RichAya::class, $actAya);
    $this->assertEquals($this->firstAya, $actAya);
  }

  public function testGetAyaAndCompareToLite()
  {
    $actAya = $this->retriever->getAya(1, 1);
    $this->assertTrue(is_a($actAya, Aya::class));
    $this->assertInstanceOf(RichAya::class, $actAya);
    $this->assertEquals($this->firstAya, $actAya);
    $this->assertEquals(Aya::class, get_parent_class($actAya));
    $parentVars = get_object_vars(self::firstAyaLite());
    $childVars = get_object_vars($actAya);

    foreach ($parentVars as $parentKey => $parentVar) {
      $this->assertEquals($parentVar, $childVars[$parentKey]);
    }
  }

  public function testGetFirstAyaOfSuraNotFound()
  {
    $suraNo = 0;
    $this->expectExceptionObject(new \QuranSafahat\Exceptions\SuraNotFound($suraNo));
    $this->retriever->getFirstAyaOfSura($suraNo);
  }

  public function testGetFirstAyaOfSura()
  {
    $soraNo = 1;
    $actAya = $this->retriever->getFirstAyaOfSura($soraNo);
    $this->assertInstanceOf(RichAya::class, $actAya);
    $this->assertEquals($this->firstAya, $actAya);
  }

  public function testGetLastAyaOfSuraNotFound()
  {
    $suraNo = 115;
    $this->expectExceptionObject(new \QuranSafahat\Exceptions\SuraNotFound($suraNo));
    $this->retriever->getLastAyaOfSura($suraNo);
  }

  public function testGetLastAyaOfSura()
  {
    $suraNo = 1;
    $actAya = $this->retriever->getLastAyaOfSura($suraNo);
    $this->assertInstanceOf(RichAya::class, $actAya);
    $this->assertEquals($this->lastAya, $actAya);
  }

  public function testGetFirstAyaOfPageNotFound()
  {
    $page = 0;
    $lastPage = QuranNaskhEdition::PAGES;
    $this->expectExceptionObject(new \QuranSafahat\Exceptions\PageNotFound($page, $lastPage));
    $this->retriever->getFirstAyaOfPage($page);
  }

  public function testGetFirstAyaOfPage()
  {
    $page = 1;
    $actAya = $this->retriever->getFirstAyaOfPage($page);
    $this->assertEquals($this->firstAya, $actAya);
  }

  public function testGetFirstLineOfPage()
  {
    $page = 1;
    $actLine = $this->retriever->getFirstLineOfPage($page);
    $this->assertEquals($this->firstAya->lineStart, $actLine);
  }

  public function testGetLastAyaOfPageNotFound()
  {
    $page = 0;
    $lastPage = QuranNaskhEdition::PAGES;
    $this->expectExceptionObject(new \QuranSafahat\Exceptions\PageNotFound($page, $lastPage));
    $this->retriever->getLastAyaOfPage($page);
  }

  public function testGetLastAyaOfPage()
  {
    $page = 1;
    $actAya = $this->retriever->getLastAyaOfPage($page);
    $this->assertEquals($this->lastAya, $actAya);
  }

  public function testGetLastLineOfPage()
  {
    $page = 1;
    $actLine = $this->retriever->getLastLineOfPage($page);
    $this->assertEquals($this->lastAya->lineEnd, $actLine);
  }


  public function testGetFirstAyaOfNextPage()
  {
    $page = 0;
    $actAya = $this->retriever->getFirstAyaOfNextPage($page);
    $this->assertEquals($this->firstAya, $actAya);
  }

  public function testGetLastAyaOfPrevPage()
  {
    $page = 2;
    $actAya = $this->retriever->getLastAyaOfPrevPage($page);
    $this->assertEquals($this->lastAya, $actAya);
  }
}
