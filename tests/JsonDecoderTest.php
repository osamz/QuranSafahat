<?php
chdir(__DIR__);

require_once '../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use QuranSafahat\JsonDecoder;

class JsonDecoderTest extends TestCase
{
  public function test__constructFileNotExistExecution()
  {
    $this->expectExceptionObject(new Exception("Json file not exist."));
    $file = "";
    new JsonDecoder($file);
  }

  public function testDecodeInvalidJson()
  {
    $this->expectException(JsonException::class);
    $file = "data/invalid.json";
    (new JsonDecoder($file))->decode();
  }


  public function testDecodeNullFile()
  {
    $this->expectExceptionObject(new Exception("No content in file"));
    $file = "data/null.json";
    (new JsonDecoder($file))->decode();
  }

  public function testDecodeFileExistValidJson()
  {
    $file = "data/quran.json";
    $this->assertIsArray((new JsonDecoder($file))->decode());
  }
}
