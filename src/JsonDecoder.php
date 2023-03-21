<?php

namespace QuranSafahat;

use Exception;
use JsonException;
use QuranSafahat\Interfaces\Decoder;

class JsonDecoder implements Decoder
{
  private string $jsonFile;

  /**
   * @throws Exception
   */
  public function __construct(string $jsonFile)
  {
    $this->jsonFile = $jsonFile;

    if (!$this->checkFile()) {
      throw new Exception("Json file not exist.");
    }
  }

  private function checkFile(): bool
  {
    return file_exists($this->jsonFile);
  }

  /**
   * @throws JsonException
   * @throws Exception
   */
  public function decode(): array
  {
    if ($content = file_get_contents($this->jsonFile)) {
      return json_decode(json: $content, associative: true, flags: JSON_THROW_ON_ERROR);
    }
    throw new Exception("No content in file");
  }

}