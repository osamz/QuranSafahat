<?php

namespace QuranSafahat\Editions;

use QuranSafahat\Interfaces\QuranDataRetriever;

abstract class QuranEdition
{
  const PAGES = 604;

  const SURA_DECORATION_LINES = 2;
  const SLIM_SURA_DECORATION_LINES = 1;
  const SLIM_DECORATED_SURA = [1, 9];

  const MAX_PAGE_LINES = 15;
  const MIN_PAGE_LINES = self::MAX_PAGE_LINES - (2 * self::SURA_DECORATION_LINES);

  /**
   * pages that have exceptional size.
   */
  const SMALL_PAGES = [1, 2];

  public abstract function data(): QuranDataRetriever;
}