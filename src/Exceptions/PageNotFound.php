<?php

namespace QuranSafahat\Exceptions;

use Throwable;

class PageNotFound extends \Exception
{
  public function __construct(int $page, int $lastPage, string $message = "Page %d Not Found, must be within 1 - %d", int $code = 0, ?Throwable $previous = null)
  {
    parent::__construct(sprintf($message, $page, $lastPage), $code, $previous);
  }
}