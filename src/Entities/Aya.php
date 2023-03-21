<?php

namespace QuranSafahat\Entities;

readonly class Aya
{
  /**
   * @param int $id
   * @param int $suraNo
   * @param int $page
   * @param int $lineStart
   * @param int $lineEnd
   * @param int $ayaNo
   */
  public function __construct(
    public int $id,
    public int $suraNo,
    public int $page,
    public int $lineStart,
    public int $lineEnd,
    public int $ayaNo,
  )
  {
  }
}