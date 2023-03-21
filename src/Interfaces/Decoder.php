<?php

namespace QuranSafahat\Interfaces;

use Exception;

interface Decoder
{

  /**
   * @throws Exception
   */
  public function decode(): array;

}