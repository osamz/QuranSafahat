<?php

namespace QuranSafahat\Entities;

readonly class RichAya extends Aya
{
  /**
   * @param int    $id
   * @param int    $jozz
   * @param int    $page
   * @param int    $suraNo
   * @param string $suraNameAr
   * @param string $suraNameEn
   * @param int    $lineStart
   * @param int    $lineEnd
   * @param int    $ayaNo
   * @param string $ayaTextEmlaey
   */
  public function __construct(
    public int    $id,
    public int    $jozz,
    public int    $page,
    public int    $suraNo,
    public string $suraNameAr,
    public string $suraNameEn,
    public int    $lineStart,
    public int    $lineEnd,
    public int    $ayaNo,
    public string $ayaTextEmlaey)
  {
//    parent::__construct($id, $suraNo, $page, $lineStart, $lineEnd, $ayaNo);
  }
}