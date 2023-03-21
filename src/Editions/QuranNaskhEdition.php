<?php

namespace QuranSafahat\Editions;

use QuranSafahat\Interfaces\QuranDataRetriever;

class QuranNaskhEdition extends QuranEdition
{

  public function __construct(private readonly QuranDataRetriever $data)
  {
  }

  public function data(): QuranDataRetriever
  {
    return $this->data;
  }
}