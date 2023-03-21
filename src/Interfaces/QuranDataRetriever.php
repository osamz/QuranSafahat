<?php

namespace QuranSafahat\Interfaces;

use QuranSafahat\Entities\Aya;
use QuranSafahat\Exceptions\AyaNotFound;

interface QuranDataRetriever
{
  public function getSuraNoByName(string $suraName, string $lang = 'ar'): int;

  /**
   * @throws AyaNotFound
   */
  public function getAya(int $suraNo, int $ayaNo): Aya;

  public function getFirstAyaOfSura(int $suraNo): Aya;

  public function getLastAyaOfSura(int $suraNo): Aya;

  public function getFirstAyaOfPage(int $page): Aya;

  public function getLastAyaOfPage(int $page): Aya;

  public function getFirstLineOfPage(int $page): int;

  public function getLastLineOfPage(int $page): int;

  public function getFirstAyaOfNextPage(int $page): Aya;

  public function getLastAyaOfPrevPage(int $page): Aya;
}