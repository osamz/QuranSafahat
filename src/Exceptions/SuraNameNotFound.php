<?php

namespace QuranSafahat\Exceptions;

use Throwable;

class SuraNameNotFound extends \Exception
{
  public function __construct(string $suraName, string $message = "Sura %s Not Found, must be correct", int $code = 0, ?Throwable $previous = null)
  {
    parent::__construct(sprintf($message, $suraName), $code, $previous);
  }
}