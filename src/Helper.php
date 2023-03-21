<?php

namespace QuranSafahat;

use Exception;
use QuranSafahat\Entities\Aya;
use QuranSafahat\Exceptions\AyaNotFound;
use QuranSafahat\Exceptions\SuraNameNotFound;
use QuranSafahat\Interfaces\Decoder;
use QuranSafahat\Interfaces\QuranDataRetriever;

class Helper
{
  private array $suraData;
  private QuranDataRetriever $quranData;

  /**
   * @throws Exception
   */
  public function __construct(Decoder $suraData, QuranDataRetriever $quranData)
  {
    $this->suraData = $suraData->decode();
    $this->quranData = $quranData;
  }

  /**
   * @throws SuraNameNotFound
   * @throws AyaNotFound
   */
  function getCheckedAyaPoint(string $suraName, int $ayaNo): Aya
  {
    $suraNo = $this->getSuraNoByAltName($suraName);
    return $this->quranData->getAya($suraNo, $ayaNo);
  }

  /**
   * @throws SuraNameNotFound
   */
  public function getSuraNoByAltName(string $name): int
  {
    foreach ($this->suraData as $key => $data) {
      if (in_array($name, $data["names"])) {
        return $key + 1;
      }
    }
    throw new SuraNameNotFound($name);
  }

  public function getLastAyaNo(int $suraNo): int
  {
    $key = $suraNo - 1;
    return $this->suraData[$key]["last_aya_no"];
  }
}