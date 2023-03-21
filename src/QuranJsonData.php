<?php

namespace QuranSafahat;

use Exception;
use JsonException;
use QuranSafahat\Editions\QuranNaskhEdition;
use QuranSafahat\Entities\RichAya;
use QuranSafahat\Exceptions\AyaNotFound;
use QuranSafahat\Exceptions\PageNotFound;
use QuranSafahat\Exceptions\SuraNameNotFound;
use QuranSafahat\Exceptions\SuraNotFound;
use QuranSafahat\Interfaces\Decoder;
use QuranSafahat\Interfaces\QuranDataRetriever;

class QuranJsonData implements QuranDataRetriever
{

  /**
   * @var array{ array{
   *   id: int,
   *   jozz: int,
   *   page: int,
   *   sura_no: int,
   *   sura_name_en: string,
   *   sura_name_ar: string,
   *   line_start: int,
   *   line_end: int,
   *   aya_no: int,
   *   aya_text_emlaey: string
   *  }
   * }
   */
  private readonly array $ayat;

  /**
   * @throws JsonException
   */
  public function __construct(Decoder|JsonDecoder $decoder)
  {
    $this->ayat = $decoder->decode();
  }

  /**
   * @throws SuraNameNotFound
   */
  public function getSuraNoByName($suraName, $lang = 'ar'): int
  {
    $lang = ($lang == 'ar') ? 'ar' : 'en';
    $lastNotMatched = "";
    $suraNameKey = "sura_name_$lang";

    foreach ($this->ayat as $aya) {
      if ($lastNotMatched == $aya[$suraNameKey]) {
        continue;
      }

      if (strcmp($aya[$suraNameKey], $suraName) === 0) {
        return $aya['sura_no'];
      } else {
        $lastNotMatched = $aya[$suraNameKey];
      }
    }
    throw new SuraNameNotFound($suraName);
  }

  /**
   * @throws AyaNotFound
   */
  public function getAya(int $suraNo, int $ayaNo): RichAya
  {
    foreach ($this->ayat as $aya) {
      if ($aya['sura_no'] == $suraNo and $aya['aya_no'] == $ayaNo) {
        return $this->makeAya($aya);
      }
    }

    throw new AyaNotFound($suraNo, $ayaNo);
  }

  /**
   * @param array{
   *   id: int,
   *   jozz: int,
   *   page: int,
   *   sura_no: int,
   *   sura_name_en: string,
   *   sura_name_ar: string,
   *   line_start: int,
   *   line_end: int,
   *   aya_no: int,
   *   aya_text_emlaey: string
   *  } $aya
   * @return RichAya
   */
  private function makeAya(array $aya): RichAya
  {
    return new RichAya(
      $aya['id'],
      $aya['jozz'],
      $aya['page'],
      $aya['sura_no'],
      $aya['sura_name_ar'],
      $aya['sura_name_en'],
      $aya['line_start'],
      $aya['line_end'],
      $aya['aya_no'],
      $aya['aya_text_emlaey']
    );
  }

  /**
   * @throws SuraNotFound
   */
  public function getFirstAyaOfSura(int $suraNo): RichAya
  {
    foreach ($this->ayat as $aya) {
      if ($aya['sura_no'] == $suraNo) {
        return $this->makeAya($aya);
      }
    }
    throw new SuraNotFound($suraNo);
  }

  /**
   * @throws SuraNotFound
   */
  public function getLastAyaOfSura(int $suraNo): RichAya
  {
    $lastAya = null;
    foreach ($this->ayat as $aya) {
      if ($aya['sura_no'] == $suraNo) {
        $lastAya = $aya;
      } else if ($aya['sura_no'] > $suraNo) {
        break;
      }
    }
    if ($lastAya != null) {
      return $this->makeAya($lastAya);
    } else {
      throw new SuraNotFound($suraNo);
    }
  }

  /**
   * @throws Exception
   */
  public function getFirstLineOfPage(int $page): int
  {
    return $this->getFirstAyaOfPage($page)->lineStart;
  }

  /**
   * @throws PageNotFound
   */
  public function getFirstAyaOfPage($page): RichAya
  {
    foreach ($this->ayat as $aya) {
      if ($aya['page'] == $page) {
        return $this->makeAya($aya);
      }
    }
    throw new PageNotFound($page, QuranNaskhEdition::PAGES);
  }

  public function getLastLineOfPage($page): int
  {
    return $this->getLastAyaOfPage($page)->lineEnd;
  }

  /**
   * @throws PageNotFound
   */
  public function getLastAyaOfPage($page): RichAya
  {
    $foundAya = null;
    foreach ($this->ayat as $aya) {
      if ($aya['page'] == $page) {
        $foundAya = $aya;
      } elseif ($aya['page'] > $page) {
        break;
      }
    }
    if ($foundAya) {
      return $this->makeAya($foundAya);
    }

    throw new PageNotFound($page, QuranNaskhEdition::PAGES);
  }

  /**
   * @throws PageNotFound
   */
  public function getFirstAyaOfNextPage($page): RichAya
  {
    return $this->getFirstAyaOfPage($page + 1);
  }

  public function getLastAyaOfPrevPage($page): RichAya
  {
    return $this->getLastAyaOfPage($page - 1);
  }
}