<?php

namespace QuranSafahat\Exceptions;

use Throwable;

class AyaNotFound extends \Exception
{
  public function __construct(int $suraNo, int $ayaNo, string $message = "Aya %d Not Found in Sura %d", int $code = 0, ?Throwable $previous = null)
  {
    parent::__construct(sprintf($message, $ayaNo, $suraNo), $code, $previous);
  }
}