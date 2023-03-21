<?php
chdir(__DIR__);

use PHPUnit\Framework\TestCase;
use QuranSafahat\Editions\QuranEdition;
use QuranSafahat\Interfaces\QuranDataRetriever;
use QuranSafahat\JsonDecoder;
use QuranSafahat\QuranJsonData;

class QuranEditionTest extends TestCase
{
  public function testData()
  {
    $edition = new class extends QuranEdition {

      public function data(): QuranDataRetriever
      {
        return new QuranJsonData(new JsonDecoder("data/quran.json"));
      }
    };
    $this->assertInstanceOf(QuranEdition::class, $edition);
    $this->assertInstanceOf(QuranDataRetriever::class, $edition->data());
  }

  public function testConstants()
  {
    $this->assertIsInt(QuranEdition::PAGES);
    $this->assertIsInt(QuranEdition::PAGES);
    $this->assertIsInt(QuranEdition::SURA_DECORATION_LINES);
    $this->assertIsInt(QuranEdition::SLIM_SURA_DECORATION_LINES);
    $this->assertIsArray(QuranEdition::SLIM_DECORATED_SURA);
    foreach (QuranEdition::SLIM_DECORATED_SURA as $sds) {
      $this->assertIsInt($sds);
    }
    $this->assertIsInt(QuranEdition::MAX_PAGE_LINES);
    $this->assertIsInt(QuranEdition::MIN_PAGE_LINES);
    $this->assertLessThanOrEqual(QuranEdition::MAX_PAGE_LINES, QuranEdition::MIN_PAGE_LINES);
    $this->assertIsArray(QuranEdition::SMALL_PAGES);
    foreach (QuranEdition::SMALL_PAGES as $PAGE) {
      $this->assertIsInt($PAGE);
    }
  }
}
