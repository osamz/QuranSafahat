<?php
chdir(__DIR__);
ini_set("memory_limit", "1024M");

require_once '../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use QuranSafahat\Editions\QuranEdition;
use QuranSafahat\Editions\QuranNaskhEdition;
use QuranSafahat\Entities\Aya;
use QuranSafahat\Entities\QuranClip;
use QuranSafahat\Entities\RichAya;
use QuranSafahat\Helper;
use QuranSafahat\Interfaces\QuranDataRetriever;
use QuranSafahat\JsonDecoder;
use QuranSafahat\QuranCalculator;
use QuranSafahat\QuranJsonData;

class QuranCalculatorTest extends TestCase
{
  private QuranCalculator $calculator;
  private Helper $helper;
  private QuranEdition $quran;
  private QuranDataRetriever $retriever;

  private Aya $firstAya, $lastAya;

  public function __construct(string $name)
  {
    parent::__construct($name);
    $this->firstAya = self::firstAya();
    $this->lastAya = self::lastAya();

    $file = "data/quran.json";

    $quranData = new QuranJsonData(new JsonDecoder($file));

    $this->retriever = $quranData;

    $suraDataFile = "data/sura_data.json";

    $helper = new Helper(new JsonDecoder($suraDataFile), $quranData);

    $this->helper = $helper;

    $edition = new QuranNaskhEdition($quranData);
    $this->quran = $edition;

    $this->calculator = new QuranCalculator($edition);
  }

  public function test__construct()
  {
    $file = "data/quran.json";
    $this->assertFileExists($file);
    $quranData = new QuranJsonData(new JsonDecoder($file));
    $this->assertInstanceOf(QuranJsonData::class, $quranData);

    $suraDataFile = "data/sura_data.json";
    $this->assertFileExists($suraDataFile);

    $helper = new Helper(new JsonDecoder($suraDataFile), $quranData);
    $this->assertInstanceOf(Helper::class, $helper);

    $startAya = $helper->getCheckedAyaPoint("آل عمران", 1);
    $this->assertInstanceOf(\QuranSafahat\Entities\RichAya::class, $startAya);

    $endAya = $helper->getCheckedAyaPoint("آل عمران", 8);
    $this->assertInstanceOf(\QuranSafahat\Entities\RichAya::class, $endAya);

    $clip = new QuranClip($startAya, $endAya);
    $this->assertInstanceOf(QuranClip::class, $clip);

    $edition = new QuranNaskhEdition($quranData);
    $this->assertInstanceOf(QuranNaskhEdition::class, $edition);

    $calculator = new QuranCalculator($edition);
    $this->assertInstanceOf(QuranCalculator::class, $calculator);
  }

  public function testCalculateOneAya()
  {
    $clip = new QuranClip($this->firstAya, $this->firstAya);

    $size = $this->calculator->calculate($clip, false);
    $this->assertInstanceOf(\QuranSafahat\Entities\ClipSize::class, $size);

    $size = $this->calculator->calculate($clip, true);
    $this->assertEquals(2, $size->lines);
    $this->assertEquals(0, $size->pages);

    $size = $this->calculator->calculate($clip, false);
    $this->assertEquals(1, $size->lines);
    $this->assertEquals(0, $size->pages);
  }

  public function testCalculateLinesFromPageHeader()
  {
    $endAya = $this->helper->getCheckedAyaPoint("الفاتحة", $this->lastAya->ayaNo - 1);
    $clip = new QuranClip($this->firstAya, $endAya);
    $size = $this->calculator->calculate($clip, false);
    $this->assertInstanceOf(\QuranSafahat\Entities\ClipSize::class, $size);

    $this->assertEquals(5, $size->lines);
    $this->assertEquals(0, $size->pages);

    $size = $this->calculator->calculate($clip, true);
    $this->assertEquals(6, $size->lines);
    $this->assertEquals(0, $size->pages);
  }

  public function testCalculateLinesFromHalfToEndPage()
  {
    $startAya = $this->helper->getCheckedAyaPoint("الفاتحة", 3);
    $clip = new QuranClip($startAya, $this->lastAya);
    $size = $this->calculator->calculate($clip, false);
    $this->assertInstanceOf(\QuranSafahat\Entities\ClipSize::class, $size);

    $this->assertEquals(5, $size->lines);
    $this->assertEquals(0, $size->pages);

    $size = $this->calculator->calculate($clip, true);
    $this->assertEquals(5, $size->lines);
    $this->assertEquals(0, $size->pages);
  }

  public function testCalculateSpecificOnePageClip()
  {
    $clip = new QuranClip($this->firstAya, $this->lastAya);
    $size = $this->calculator->calculate($clip, false);
    $this->assertInstanceOf(\QuranSafahat\Entities\ClipSize::class, $size);

    $this->assertEquals(0, $size->lines);
    $this->assertEquals(1, $size->pages);

    $size = $this->calculator->calculate($clip, true);
    $this->assertEquals(0, $size->lines);
    $this->assertEquals(1, $size->pages);
  }

  public function testCalculateSpecificPageAndLine()
  {
    $endAya = $this->helper->getCheckedAyaPoint("البقرة", 1);
    $clip = new QuranClip($this->firstAya, $endAya);
    $size = $this->calculator->calculate($clip, false);
    $this->assertInstanceOf(\QuranSafahat\Entities\ClipSize::class, $size);
    $this->assertEquals(1, $size->lines);
    $this->assertEquals(1, $size->pages);

    $size = $this->calculator->calculate($clip, true);
    $this->assertEquals(3, $size->lines);
    $this->assertEquals(1, $size->pages);
  }

  public function testCalculateSpecificPageAndManyLines()
  {
    $endAya = $this->helper->getCheckedAyaPoint("البقرة", 4);
    $clip = new QuranClip($this->firstAya, $endAya);
    $size = $this->calculator->calculate($clip, false);
    $this->assertInstanceOf(\QuranSafahat\Entities\ClipSize::class, $size);
    $this->assertEquals(4, $size->lines);
    $this->assertEquals(1, $size->pages);

    $size = $this->calculator->calculate($clip, true);
    $this->assertEquals(6, $size->lines);
    $this->assertEquals(1, $size->pages);
  }

  public function testCalculateSpecificTowPages()
  {
    $endAya = $this->helper->getCheckedAyaPoint("البقرة", 5);
    $clip = new QuranClip($this->firstAya, $endAya);
    $size = $this->calculator->calculate($clip, false);
    $this->assertInstanceOf(\QuranSafahat\Entities\ClipSize::class, $size);
    $this->assertEquals(0, $size->lines);
    $this->assertEquals(2, $size->pages);

    $size = $this->calculator->calculate($clip, true);
    $this->assertEquals(0, $size->lines);
    $this->assertEquals(2, $size->pages);
  }

  public function testCalculateSpecificTowPagesWithExtraLines()
  {
    $endAya = $this->helper->getCheckedAyaPoint("البقرة", 6);
    $clip = new QuranClip($this->firstAya, $endAya);
    $size = $this->calculator->calculate($clip, false);
    $this->assertInstanceOf(\QuranSafahat\Entities\ClipSize::class, $size);
    $this->assertEquals(2, $size->lines);
    $this->assertEquals(2, $size->pages);

    $size = $this->calculator->calculate($clip, true);
    $this->assertEquals(2, $size->lines);
    $this->assertEquals(2, $size->pages);
  }

  public function testCalculateSpecificTowPagesWithExtraHalf()
  {
    $endAya = $this->helper->getCheckedAyaPoint("البقرة", 11);
    $clip = new QuranClip($this->firstAya, $endAya);
    $size = $this->calculator->calculate($clip, false);
    $this->assertInstanceOf(\QuranSafahat\Entities\ClipSize::class, $size);
    $this->assertEquals(8, $size->lines);
    $this->assertEquals(2, $size->pages);

    $size = $this->calculator->calculate($clip, true);
    $this->assertEquals(8, $size->lines);
    $this->assertEquals(2, $size->pages);
  }

  public function testCalculateSpecificTowPagesWithFullPage()
  {
    $endAya = $this->helper->getCheckedAyaPoint("البقرة", 16);
    $clip = new QuranClip($this->firstAya, $endAya);
    $size = $this->calculator->calculate($clip, false);
    $this->assertInstanceOf(\QuranSafahat\Entities\ClipSize::class, $size);
    $this->assertEquals(0, $size->lines);
    $this->assertEquals(3, $size->pages);

    $size = $this->calculator->calculate($clip, true);
    $this->assertEquals(0, $size->lines);
    $this->assertEquals(3, $size->pages);
  }

  public function testCalculateMultiLineAya()
  {
    $startAya = $this->helper->getCheckedAyaPoint("البقرة", 22);
    $endAya = $this->helper->getCheckedAyaPoint("البقرة", 22);
    $clip = new QuranClip($startAya, $endAya);
    $size = $this->calculator->calculate($clip, false);
    $this->assertInstanceOf(\QuranSafahat\Entities\ClipSize::class, $size);
    $this->assertEquals(4, $size->lines);
    $this->assertEquals(0, $size->pages);

    $size = $this->calculator->calculate($clip, true);
    $this->assertEquals(4, $size->lines);
    $this->assertEquals(0, $size->pages);
  }

  public function testCalculateMultiAyat()
  {
    $startAya = $this->helper->getCheckedAyaPoint("البقرة", 20);
    $endAya = $this->helper->getCheckedAyaPoint("البقرة", 23);
    $clip = new QuranClip($startAya, $endAya);
    $size = $this->calculator->calculate($clip, false);
    $this->assertInstanceOf(\QuranSafahat\Entities\ClipSize::class, $size);
    $this->assertEquals(10, $size->lines);
    $this->assertEquals(0, $size->pages);

    $size = $this->calculator->calculate($clip, true);
    $this->assertEquals(10, $size->lines);
    $this->assertEquals(0, $size->pages);
  }

  public function testCalculateHalfFromStart()
  {
    $startAya = $this->helper->getCheckedAyaPoint("البقرة", 30);
    $endAya = $this->helper->getCheckedAyaPoint("البقرة", 34);
    $clip = new QuranClip($startAya, $endAya);
    $size = $this->calculator->calculate($clip, false);
    $this->assertInstanceOf(\QuranSafahat\Entities\ClipSize::class, $size);
    $this->assertEquals(10, $size->lines);
    $this->assertEquals(0, $size->pages);

    $size = $this->calculator->calculate($clip, true);
    $this->assertEquals(10, $size->lines);
    $this->assertEquals(0, $size->pages);
  }

  public function testCalculateHalfTillEnd()
  {
    $startAya = $this->helper->getCheckedAyaPoint("البقرة", 34);
    $endAya = $this->helper->getCheckedAyaPoint("البقرة", 37);
    $clip = new QuranClip($startAya, $endAya);
    $size = $this->calculator->calculate($clip, false);
    $this->assertInstanceOf(\QuranSafahat\Entities\ClipSize::class, $size);
    $this->assertEquals(7, $size->lines);
    $this->assertEquals(0, $size->pages);

    $size = $this->calculator->calculate($clip, true);
    $this->assertEquals(7, $size->lines);
    $this->assertEquals(0, $size->pages);
  }

  public function testCalculateFullPageSection()
  {
    $startAya = $this->helper->getCheckedAyaPoint("البقرة", 30);
    $endAya = $this->helper->getCheckedAyaPoint("البقرة", 37);
    $clip = new QuranClip($startAya, $endAya);
    $size = $this->calculator->calculate($clip, false);
    $this->assertInstanceOf(\QuranSafahat\Entities\ClipSize::class, $size);
    $this->assertEquals(0, $size->lines);
    $this->assertEquals(1, $size->pages);

    $size = $this->calculator->calculate($clip, true);
    $this->assertEquals(0, $size->lines);
    $this->assertEquals(1, $size->pages);
  }

  public function testCalculatePageAndLineCrossSura()
  {
    $startAya = $this->helper->getCheckedAyaPoint("البقرة", 283);
    $endAya = $this->helper->getCheckedAyaPoint("آل عمران", 2);
    $clip = new QuranClip($startAya, $endAya);
    $size = $this->calculator->calculate($clip, false);
    $this->assertInstanceOf(\QuranSafahat\Entities\ClipSize::class, $size);
    $this->assertEquals(1, $size->lines);
    $this->assertEquals(1, $size->pages);

    $size = $this->calculator->calculate($clip, true);
    $this->assertEquals(3, $size->lines);
    $this->assertEquals(1, $size->pages);
  }

  public function testCalculateHalfAndHalfCrossSura()
  {
    $startAya = $this->helper->getCheckedAyaPoint("البقرة", 285);
    $endAya = $this->helper->getCheckedAyaPoint("آل عمران", 6);
    $clip = new QuranClip($startAya, $endAya);
    $size = $this->calculator->calculate($clip, false);
    $this->assertInstanceOf(\QuranSafahat\Entities\ClipSize::class, $size);
    $this->assertEquals(15, $size->lines);
    $this->assertEquals(0, $size->pages);

    $size = $this->calculator->calculate($clip, true);
    $this->assertEquals(2, $size->lines);
    $this->assertEquals(1, $size->pages);
  }

  public function testCalculateHalfAndPageCrossSura()
  {
    $startAya = $this->helper->getCheckedAyaPoint("البقرة", 285);
    $endAya = $this->helper->getCheckedAyaPoint("آل عمران", 9);
    $clip = new QuranClip($startAya, $endAya);
    $size = $this->calculator->calculate($clip, false);
    $this->assertInstanceOf(\QuranSafahat\Entities\ClipSize::class, $size);
    $this->assertEquals(9, $size->lines);
    $this->assertEquals(1, $size->pages);

    $size = $this->calculator->calculate($clip, true);
    $this->assertEquals(9, $size->lines);
    $this->assertEquals(1, $size->pages);
  }

  public function testCalculateTowPagesCrossSura()
  {
    $startAya = $this->helper->getCheckedAyaPoint("البقرة", 283);
    $endAya = $this->helper->getCheckedAyaPoint("آل عمران", 9);
    $clip = new QuranClip($startAya, $endAya);
    $size = $this->calculator->calculate($clip, false);
    $this->assertInstanceOf(\QuranSafahat\Entities\ClipSize::class, $size);
    $this->assertEquals(0, $size->lines);
    $this->assertEquals(2, $size->pages);

    $size = $this->calculator->calculate($clip, true);
    $this->assertEquals(0, $size->lines);
    $this->assertEquals(2, $size->pages);
  }

  public function testCalculateManyPagesCrossManySura()
  {
    $startAya = $this->helper->getCheckedAyaPoint("الطارق", 1);
    $endAya = $this->helper->getCheckedAyaPoint("الفجر", 22);
    $clip = new QuranClip($startAya, $endAya);
    $size = $this->calculator->calculate($clip, false);
    $this->assertInstanceOf(\QuranSafahat\Entities\ClipSize::class, $size);
    $this->assertEquals(0, $size->lines);
    $this->assertEquals(3, $size->pages);

    $size = $this->calculator->calculate($clip, true);
    $this->assertEquals(0, $size->lines);
    $this->assertEquals(3, $size->pages);
  }

  public function testCalculateManyPagesLinesCrossManySura()
  {
    $startAya = $this->helper->getCheckedAyaPoint("الطارق", 1);
    $endAya = $this->helper->getCheckedAyaPoint("الضحى", 11);
    $clip = new QuranClip($startAya, $endAya);
    $size = $this->calculator->calculate($clip, false);
    $this->assertInstanceOf(\QuranSafahat\Entities\ClipSize::class, $size);
    $this->assertEquals(12, $size->lines);
    $this->assertEquals(5, $size->pages);

    $size = $this->calculator->calculate($clip, true);
    $this->assertEquals(12, $size->lines);
    $this->assertEquals(5, $size->pages);
  }

  public function testCalculateAllPagesThroughQuran()
  {
    $startAya = $this->helper->getCheckedAyaPoint("الفاتحة", 1);
    $endAya = $this->helper->getCheckedAyaPoint("الناس", 6);
    $clip = new QuranClip($startAya, $endAya);
    $size = $this->calculator->calculate($clip, false);
    $this->assertInstanceOf(\QuranSafahat\Entities\ClipSize::class, $size);
    $this->assertEquals(0, $size->lines);
    $this->assertEquals($this->quran::PAGES, $size->pages);

    $size = $this->calculator->calculate($clip, true);
    $this->assertEquals(0, $size->lines);
    $this->assertEquals($this->quran::PAGES, $size->pages);
  }

  public function testCalculateHalfAndMoreInNextSura()
  {
    $startAya = $this->helper->getCheckedAyaPoint("آل عمران", 199);
    $endAya = $this->helper->getCheckedAyaPoint("النساء", 5);
    $clip = new QuranClip($startAya, $endAya);
    $size = $this->calculator->calculate($clip, false);
    $this->assertInstanceOf(\QuranSafahat\Entities\ClipSize::class, $size);
    $this->assertEquals(16, $size->lines);
    $this->assertEquals(0, $size->pages);

    $size = $this->calculator->calculate($clip, true);
    $this->assertEquals(3, $size->lines);
    $this->assertEquals(1, $size->pages);
  }

  public function testCalculateHalfAndMoreInNextSuraWithSlim()
  {
    $startAya = $this->helper->getCheckedAyaPoint("الأنفال", 71);
    $endAya = $this->helper->getCheckedAyaPoint("التوبة", 1);
    $clip = new QuranClip($startAya, $endAya);
    $size = $this->calculator->calculate($clip, false);
    $this->assertInstanceOf(\QuranSafahat\Entities\ClipSize::class, $size);
    $this->assertEquals(14, $size->lines);
    $this->assertEquals(0, $size->pages);

    $size = $this->calculator->calculate($clip, true);
    $this->assertEquals(0, $size->lines);
    $this->assertEquals(1, $size->pages);
  }

  public function testCalculateHalfAndMoreInManySura()
  {
    $startAya = $this->helper->getCheckedAyaPoint("الماعون", 1);
    $endAya = $this->helper->getCheckedAyaPoint("المسد", 2);
    $clip = new QuranClip($startAya, $endAya);
    $size = $this->calculator->calculate($clip, false);
    $this->assertInstanceOf(\QuranSafahat\Entities\ClipSize::class, $size);
    $this->assertEquals(19, $size->lines);
    $this->assertEquals(0, $size->pages);

    $size = $this->calculator->calculate($clip, true);
    $this->assertEquals(8, $size->lines);
    $this->assertEquals(1, $size->pages);
  }

  public function testCalculateSmallSura()
  {
    $startAya = $this->helper->getCheckedAyaPoint("الماعون", 1);
    $endAya = $this->helper->getCheckedAyaPoint("الماعون", 7);
    $clip = new QuranClip($startAya, $endAya);
    $size = $this->calculator->calculate($clip, false);
    $this->assertInstanceOf(\QuranSafahat\Entities\ClipSize::class, $size);
    $this->assertEquals(4, $size->lines);
    $this->assertEquals(0, $size->pages);

    $size = $this->calculator->calculate($clip, true);
    $this->assertEquals(6, $size->lines);
    $this->assertEquals(0, $size->pages);
  }

  // test merging in inverted Clips, clips may be 2 or 3: merging possibilities
  // 2 = may merge 2, or not.
  // 3 = may not merge, merge 1&2, merge 2&3, all merge
  public function testCalculateInvertedTowClipsNotMerge()
  {
    $startAya = $this->helper->getCheckedAyaPoint("التغابن", 7);
    $endAya = $this->helper->getCheckedAyaPoint("المنافقون", 8);
    $clip = new QuranClip($startAya, $endAya);
    $size = $this->calculator->calculate($clip, false);
    // 1p, 6l, 18l
    // المنافقون 16
    // التغابن6س + 1ص
    $this->assertEquals(22, $size->lines);
    $this->assertEquals(1, $size->pages);

    // المنافقون 18
    // التغابن6س + 1ص
    $size = $this->calculator->calculate($clip, true);
    $this->assertEquals(9, $size->lines);
    $this->assertEquals(2, $size->pages);
  }

  public function testCalculateInvertedTowClipsCanMerge()
  {
    $startAya = $this->helper->getCheckedAyaPoint("النازعات", 1);
    $endAya = $this->helper->getCheckedAyaPoint("النبإ", 40);
    $clip = new QuranClip($startAya, $endAya);
    $size = $this->calculator->calculate($clip, false);

    $this->assertEquals(0, $size->lines);
    $this->assertEquals(3, $size->pages);


    $size = $this->calculator->calculate($clip, true);
    $this->assertEquals(0, $size->lines);
    $this->assertEquals(3, $size->pages);

  }

  public function testCalculateInvertedThreeClipsNotMerge()
  {
    $startAya = $this->helper->getCheckedAyaPoint("النازعات", 17);
    $endAya = $this->helper->getCheckedAyaPoint("المرسلات", 19);
    $clip = new QuranClip($startAya, $endAya);
    $size = $this->calculator->calculate($clip, false);

    $this->assertEquals(14, $size->lines);
    $this->assertEquals(2, $size->pages);

    $size = $this->calculator->calculate($clip, true);
    $this->assertEquals(1, $size->lines);
    $this->assertEquals(3, $size->pages);

  }

  public function testCalculateInvertedThreeClipsFirstTowMerge()
  {
    $startAya = $this->helper->getCheckedAyaPoint("النازعات", 17);
    $endAya = $this->helper->getCheckedAyaPoint("المرسلات", 50);
    $clip = new QuranClip($startAya, $endAya);
    $size = $this->calculator->calculate($clip, false);

    $this->assertEquals(14, $size->lines);
    $this->assertEquals(3, $size->pages);

    $size = $this->calculator->calculate($clip, true);
    $this->assertEquals(1, $size->lines);
    $this->assertEquals(4, $size->pages);
  }

  public function testCalculateInvertedThreeClipsLastTowMerge()
  {
    $startAya = $this->helper->getCheckedAyaPoint("النازعات", 1);
    $endAya = $this->helper->getCheckedAyaPoint("المرسلات", 40);
    $clip = new QuranClip($startAya, $endAya);
    $size = $this->calculator->calculate($clip, false);

    $this->assertEquals(17, $size->lines);
    $this->assertEquals(3, $size->pages);

    $size = $this->calculator->calculate($clip, true);
    $this->assertEquals(4, $size->lines);
    $this->assertEquals(4, $size->pages);
  }

  public function testCalculateInvertedThreeClipsAllMerge()
  {
    $startAya = $this->helper->getCheckedAyaPoint("النازعات", 1);
    $endAya = $this->helper->getCheckedAyaPoint("المرسلات", 50);
    $clip = new QuranClip($startAya, $endAya);
    $size = $this->calculator->calculate($clip, false);

    $this->assertEquals(7, $size->lines);
    $this->assertEquals(4, $size->pages);

    $size = $this->calculator->calculate($clip, true);
    $this->assertEquals(9, $size->lines);
    $this->assertEquals(4, $size->pages);
  }


  static function firstAya(): RichAya
  {
    return new RichAya(
      1,
      1,
      1,
      1,
      'الفَاتِحة',
      'Al-Fātiḥah',
      2,
      2,
      1,
      'بسم الله الرحمن الرحيم'
    );
  }

  static function lastAya(): RichAya
  {
    return new RichAya(
      7,
      1,
      1,
      1,
      'الفَاتِحة',
      'Al-Fātiḥah',
      6,
      8,
      7,
      'صراط الذين أنعمت عليهم غير المغضوب عليهم ولا الضالين',
    );
  }
}
