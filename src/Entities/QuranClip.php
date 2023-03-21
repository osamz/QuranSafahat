<?php

namespace QuranSafahat\Entities;

use Exception;

readonly class QuranClip
{
  /**
   * @throws Exception
   */
  public function __construct(public Aya $start, public Aya $end)
  {
    $this->validateSectionEnd();
  }

  /**
   * @throws Exception
   */
  private function validateSectionEnd(): void
  {
    if ($this->start->suraNo == $this->end->suraNo) {
      if ($this->start->ayaNo > $this->end->ayaNo) {
        throw new Exception("End Aya cannot be before start aya in same sura: {$this->start->ayaNo} > {$this->end->ayaNo}");
      }
    }
  }
}