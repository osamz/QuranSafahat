<?php
chdir(__DIR__);

require_once "../vendor/autoload.php";
require_once 'AyaForTest.php';

use PHPUnit\Framework\TestCase;

class QuranClipTest extends TestCase
{

  use AyaForTest;

  private \QuranSafahat\Entities\Aya $firstAya, $lastAya;

  public function __construct(string $name)
  {
    parent::__construct($name);

    $this->firstAya = self::firstAya();
    $this->lastAya = self::lastAya();
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
