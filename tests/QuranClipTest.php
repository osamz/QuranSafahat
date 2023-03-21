<?php
chdir(__DIR__);

require_once "../vendor/autoload.php";

use PHPUnit\Framework\TestCase;
use QuranSafahat\Entities\Aya;
use QuranSafahat\Entities\RichAya;

class QuranClipTest extends TestCase
{
  private Aya $firstAya, $lastAya;

  public function __construct(string $name)
  {
    parent::__construct($name);

    $this->firstAya = self::firstAya();
    $this->lastAya = self::lastAya();
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

  public function testInvalidEnd()
  {
    $this->expectExceptionObject(new Exception("End Aya cannot be before start aya in same sura: {$this->lastAya->ayaNo} > {$this->firstAya->ayaNo}"));
    new \QuranSafahat\Entities\QuranClip($this->lastAya, $this->firstAya);
  }

  public function testValidOneAya()
  {
    $clip = new \QuranSafahat\Entities\QuranClip($this->firstAya, $this->lastAya);
    $this->assertEquals($clip->start, $this->firstAya);
    $this->assertEquals($clip->end, $this->lastAya);
  }
}
