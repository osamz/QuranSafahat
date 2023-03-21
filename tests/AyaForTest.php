<?php
chdir(__DIR__);

use QuranSafahat\Entities\Aya;
use QuranSafahat\Entities\RichAya;

trait AyaForTest
{
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

  static function firstAyaLite(): Aya
  {
    return new Aya(
      1,
      1,
      1,
      2,
      2,
      1,
    );
  }

  static function lastAyaLite(): Aya
  {
    return new Aya(
      7,
      1,
      1,
      6,
      8,
      7,
    );

  }

}