<?php

namespace QuranSafahat\Entities;

readonly class ClipSize
{
  public function __construct(public int $pages, public int $lines)
  {
  }
}