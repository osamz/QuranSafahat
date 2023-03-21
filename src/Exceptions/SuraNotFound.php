<?php

namespace QuranSafahat\Exceptions;

use Throwable;

class SuraNotFound extends \Exception
{
  public function __construct(int $suraNo, string $message = "Sura %d Not Found, must be within 1 - 114", int $code = 0, ?Throwable $previous = null)
  {
    parent::__construct(sprintf($message, $suraNo), $code, $previous);
  }
}