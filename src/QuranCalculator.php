<?php

namespace QuranSafahat;

use Exception;
use QuranSafahat\Editions\QuranEdition;
use QuranSafahat\Entities\Aya;
use QuranSafahat\Entities\ClipSize;
use QuranSafahat\Entities\QuranClip;

class QuranCalculator
{
  private QuranEdition $quran;


  public function __construct(QuranEdition $quranEdition)
  {
    $this->quran = $quranEdition;
  }

  /**
   * @throws Exception
   */
  public function calculate(QuranClip $clip, bool $convertLinesToPages = true): ClipSize
  {
    if ($this->startPrecedesEnd($clip)) {
      return $this->calculateStraightClip($clip, $convertLinesToPages);
    } else {
      return $this->calculateInvertedClip($clip, $convertLinesToPages);
    }
  }

  private function startPrecedesEnd(QuranClip $clip): bool
  {
    $startAya = $clip->start;
    $endAya = $clip->end;

    return ($startAya->id <= $endAya->id);

    /*    if ($startAya->page == $endAya->page) {
          if ($startAya->suraNo == $endAya->suraNo) {
            return true;
          } elseif ($startAya->suraNo < $endAya->suraNo) {
            return true;
          } else {
            return false;
          }
        } elseif ($startAya->page < $endAya->page) {
          return true;
        } else {
          return false;
        }*/
  }

  private function calculateStraightClip(QuranClip $clip, bool $convertLinesToPages = true): ClipSize
  {
    $appendDecorations = $convertLinesToPages;

    $lines = $pages = 0;

    if ($this->clipWithinPage($clip)) {
      $linesInClip = $this->countLinesWithinClip($clip, $appendDecorations, $this->ayaEndsPage($clip->end));
      if ($this->checkClipIsPage($clip, $linesInClip)) {
        $pages += 1;
      } else {
        $lines += $linesInClip;
      }
    } else {

      if ($this->ayaBeginsPage($clip->start)) {
        $actualStartPageAya = $clip->start;
      } else {
        $lastAyaInStartPage = $this->quran->data()->getLastAyaOfPage($clip->end->page);
        $linesTillPageEnd = $this->countLines($clip->start, $lastAyaInStartPage, $appendDecorations, true);
        $lines += $linesTillPageEnd;
        $nextPageFirstAya = $this->quran->data()->getFirstAyaOfNextPage($clip->start->page);
        $actualStartPageAya = $nextPageFirstAya;
      }

      if ($this->ayaEndsPage($clip->end)) {
        $actualEndPageAya = $clip->end;
      } else {
        $firstAyaInEndPage = $this->quran->data()->getFirstAyaOfPage($clip->end->page);
        $linesTillEndAyaOfEndPage = $this->countLines($firstAyaInEndPage, $clip->end, $appendDecorations);
        $lines += $linesTillEndAyaOfEndPage;
        $actualEndPageAya = $this->quran->data()->getLastAyaOfPrevPage($clip->end->page);
      }
      $actualPagesCount = $this->countPages($actualStartPageAya, $actualEndPageAya);
      $pages += $actualPagesCount;
    }

    if ($convertLinesToPages) {
      $this->convertLinesToPages($pages, $lines);
    }
    return new ClipSize($pages, $lines);
  }

  private function clipWithinPage(QuranClip $clip): bool
  {
    return ($clip->start->page == $clip->end->page);
  }

  private function countLinesWithinClip(QuranClip $clip, bool $appendDecorations, bool $endEndsPage = false): int
  {
    return $this->countLines($clip->start, $clip->end, $appendDecorations, $endEndsPage);
  }

  private function countLines(Aya $start, Aya $end, bool $appendDecorationLines = false, bool $endEndsPage = false): int
  {
    $suraTitleDecorationLines = 0;
    if ($appendDecorationLines) {
      $suraTitleDecorationLines = $this->countDecorationLines($start, $end, $endEndsPage);
    }
    return ($end->lineEnd - $start->lineStart) + 1 + $suraTitleDecorationLines;
  }

  private function countDecorationLines(Aya $start, Aya $end, bool $endEndsPage = false): int
  {
    $suraTitleDecorationLines = 0;
    if ($start->ayaNo == 1 && $start->lineStart > 1/*($start->lineStart == 3 || $start->lineStart == 2)*/) {
      // decoration prefix, add decoration lines.
      $suraTitleDecorationLines += $this->decorationLinesOfSura($start->suraNo);
    }
    if ($endEndsPage && $end->lineEnd < 15 && !in_array($end->suraNo, $this->quran::SMALL_PAGES)) {
      // NOTE: In new Quran Pages design the Sura title decorations has been moved to upper of every sura
      // some other editions may have old design, therefore we handle should handle it.
      $suraTitleDecorationLines += $this->decorationLinesOfSura($end->suraNo + 1); // @codeCoverageIgnore
    }

    return $suraTitleDecorationLines;
  }

  private function decorationLinesOfSura(int $suraNo): int
  {
    return (in_array($suraNo, $this->quran::SLIM_DECORATED_SURA))
      ? $this->quran::SLIM_SURA_DECORATION_LINES
      : $this->quran::SURA_DECORATION_LINES;
  }


  private function ayaEndsPage(Aya $aya): bool
  {
    return ($aya->lineEnd == $this->quran::MAX_PAGE_LINES
      || $aya->lineEnd == $this->quran->data()->getLastLineOfPage($aya->page));
  }

  private function checkClipIsPage(QuranClip $clip, int $linesInClip): bool
  {
    $linesCanBePage = ($linesInClip >= $this->quran::MIN_PAGE_LINES);

    if ($linesCanBePage || in_array($clip->start->page, $this->quran::SMALL_PAGES)) {
      // get first, last line for every page then consider it is a page
//      $lineStartsPage = $this->quran->data()->getFirstLineOfPage($clip->start->page);
      if (!$this->ayaBeginsPage($clip->start)) {
        // Aya not starts in first line of page.
        return false;
      }

//      $lineEndsPage = $this->quran->data()->getLastLineOfPage($clip->end->page);
      // first line is evaluated, last line evaluation result is final result.
      return ($this->ayaEndsPage($clip->end));
    }
    // lines less than min possible lines in page, and
    return false;
  }

  private function ayaBeginsPage(Aya $aya): bool
  {
    return ($aya->lineStart == 1 || $aya->lineStart == $this->quran->data()->getFirstLineOfPage($aya->page));
  }

  private function countPages(Aya $start, Aya $end): int
  {
    return ($end->page - $start->page) + 1;
  }

  private function convertLinesToPages(int &$pages, int &$lines): void
  {
    if ($lines >= $this->quran::MAX_PAGE_LINES) {
      [$additionalPages, $remainderLines] = $this->divideWithRemainder($lines, $this->quran::MAX_PAGE_LINES);

      $pages += intval($additionalPages);
      $lines = $remainderLines;
    }
  }

  /**
   * @param int $dividend
   * @param int $divisor
   * @return array{int, int}
   */
  private function divideWithRemainder(int $dividend, int $divisor): array
  {
    $result = floor($dividend / $divisor);
    $remainder = $dividend % $divisor;
    return [intval($result), intval($remainder)];
  }

  /**
   * @throws Exception
   */
  private function calculateInvertedClip(QuranClip $clip, bool $convertLinesToPages): ClipSize
  {
    $splitClips = $this->splitInvertedClips($clip);
    $mergedClips = $this->mergeMultiClips($splitClips);

    $lines = $pages = 0;

    foreach ($mergedClips as $mergedClip) {
      $size = $this->calculateStraightClip($mergedClip, $convertLinesToPages);
      $lines += $size->lines;
      $pages += $size->pages;
    }

    if ($convertLinesToPages) {
      $this->convertLinesToPages($pages, $lines);
    }
    return new ClipSize($pages, $lines);
  }

  /**
   * @param QuranClip $clip
   * @return QuranClip[]
   * @throws Exception
   */
  private function splitInvertedClips(QuranClip $clip): array
  {
    $clips = [];

    $lastClipStart = $clip->start;
    $lastClipEnd = $this->quran->data()->getLastAyaOfSura($clip->start->suraNo);
    $lastClip = new QuranClip($lastClipStart, $lastClipEnd);

    $firstClipStart = $this->quran->data()->getFirstAyaOfSura($clip->end->suraNo);
    $firstClipEnd = $clip->end;
    $firstClip = new QuranClip($firstClipStart, $firstClipEnd);
    $clips[] = $firstClip;

    if (($lastClip->end->suraNo - $firstClip->end->suraNo) > 1) {
      $suraAfterFirst = $firstClip->start->suraNo + 1;
      $suraBeforeThird = $lastClip->end->suraNo - 1;
      $middleClipStart = $this->quran->data()->getFirstAyaOfSura($suraAfterFirst);
      $middleClipEnd = $this->quran->data()->getLastAyaOfSura($suraBeforeThird);
      $middleClip = new QuranClip($middleClipStart, $middleClipEnd);
      $clips[] = $middleClip;
    }
    $clips[] = $lastClip;

    return array_filter($clips);
  }

  /**
   * @param QuranClip[] $clips
   * @return QuranClip[]
   * @throws Exception
   */
  private function mergeMultiClips(array $clips): array
  {
    $merged = [];
    $current = $clips[0];
    for ($i = 1; $i < count($clips); $i++) {
      if ($this->canMerged($current, $clips[$i])) {
        $current = $this->mergeTwoClips($current, $clips[$i]);
      } else {
        $merged[] = $current;
        $current = $clips[$i];
      }
    }
    $merged[] = $current;
    return $merged;
  }

  private function canMerged(QuranClip $first, QuranClip $second): bool
  {
    return (($first->end->id + 1) === $second->start->id);
  }

  /**
   * @throws Exception
   */
  private function mergeTwoClips(QuranClip $first, QuranClip $second): QuranClip
  {
    return new QuranClip($first->start, $second->end);
  }
}