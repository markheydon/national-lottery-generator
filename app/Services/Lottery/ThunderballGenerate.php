<?php

/**
 * Helper class to generate numbers for the Thunderball game.
 */

declare(strict_types=1);

namespace App\Services\Lottery;

/**
 * Helper class to generate numbers for the Thunderball game.
 *
 * @package App\Services\Lottery
 * @since 1.0.0
 */
class ThunderballGenerate
{
    /**
     * The name of the Lotto game.
     *
     * @return string Name of the Lotto game.
     */
    protected static function getNameOfGame(): string
    {
        return 'Thunderball';
    }

    /**
     * Should results include Lucky Stars?
     *
     * @return bool True if results should include Lucky Stars.
     */
    private static function hasThunderball(): bool
    {
        return (static::getNameOfGame() === self::getNameOfGame());
    }

    /**
     * The number of Lotto balls to return in the results.
     *
     * @return int Num of Lotto balls in results.
     */
    protected static function getNumOfMainBalls(): int
    {
        return 5;
    }

    /**
     * Generate 'random' Lotto numbers.
     *
     * @since 1.0.0
     *
     * @return array Array of lines containing generated numbers.
     */
    public static function generate(): array
    {
        $allDraws = ThunderballDownload::readThunderballDrawHistory();

        // Build the results array header
        $gameName = static::getNameOfGame();
        $latestDrawDate = Utils::getLatestDrawDate($allDraws);

        // Build some generated lines of 'random' numbers and return
        $linesMethod1 = self::generateMostFrequentTogether($allDraws);
        $linesMethod3 = self::generateFullIteration($allDraws);

        // Find the most popular Thunderball number across all generated lines
        $mostPopularThunderball = self::findMostPopularThunderball($linesMethod1, $linesMethod3);

        // Find first line from full-iteration with the most popular Thunderball and its index
        $firstLineIndex = self::findFirstLineIndexWithThunderball($linesMethod3, $mostPopularThunderball);
        $firstLineWithPopularThunderball = $linesMethod3[$firstLineIndex] ?? $linesMethod3[0];

        // Remove this line from full-iteration to avoid duplication
        $remainingFullIterationLines = $linesMethod3;
        if ($firstLineIndex !== null) {
            array_splice($remainingFullIterationLines, $firstLineIndex, 1);
        }

        // Reorder lines: new line 1 (popular thunderball), line 2 (most-freq-together), rest (full-iteration)
        $lines = [
            'most-popular-thunderball' => [$firstLineWithPopularThunderball],
            'most-freq-together' => $linesMethod1,
            'full-iteration' => $remainingFullIterationLines,
        ];
        $lineBalls = [
            'mainNumbers' => static::getNumOfMainBalls(),
        ];
        if (static::hasThunderball()) {
            $lineBalls['thunderball'] = 1;
        }


        // Build the results array and return
        $results = [
            'gameName' => $gameName,
            'latestDrawDate' => $latestDrawDate,
            'numOfMethods' => count($lines),
            'lineBalls' => $lineBalls,
            'lines' => $lines,
        ];
        return $results;
    }

    /**
     * Find the most popular Thunderball number across all generated lines.
     *
     * @param array $lines1 First set of lines to analyze.
     * @param array $lines2 Second set of lines to analyze.
     * @return int The most popular Thunderball number.
     */
    private static function findMostPopularThunderball(array $lines1, array $lines2): int
    {
        $thunderballCounts = [];

        // Count occurrences from all lines
        $allLines = array_merge($lines1, $lines2);
        foreach ($allLines as $line) {
            if (isset($line['thunderball']) && is_array($line['thunderball']) && count($line['thunderball']) > 0) {
                $thunderball = $line['thunderball'][0];
                if (!isset($thunderballCounts[$thunderball])) {
                    $thunderballCounts[$thunderball] = 0;
                }
                $thunderballCounts[$thunderball]++;
            }
        }

        // Find the most popular one
        if (empty($thunderballCounts)) {
            return 1; // Default fallback
        }

        arsort($thunderballCounts);
        reset($thunderballCounts);
        return key($thunderballCounts);
    }

    /**
     * Find the index of the first line with the specified Thunderball number.
     *
     * @param array $lines Lines to search through.
     * @param int $thunderball The Thunderball number to find.
     * @return int|null The index of the first line with the specified Thunderball, or null if not found.
     */
    private static function findFirstLineIndexWithThunderball(array $lines, int $thunderball): ?int
    {
        foreach ($lines as $index => $line) {
            if (isset($line['thunderball']) && is_array($line['thunderball']) && count($line['thunderball']) > 0) {
                if ($line['thunderball'][0] === $thunderball) {
                    return $index;
                }
            }
        }

        // Return 0 (first line index) if not found
        return 0;
    }

    /**
     * Generate a line by finding balls that occurs most frequently across all data together.
     *
     * I.e. looks for numbers that occur within the same lines together, not across the whole data set.
     *
     * @since 1.0.0
     *
     * @param array $draws The draws array to use.
     * @return array Array of lines generated.
     */
    protected static function generateMostFrequentTogether(array $draws): array
    {
        // return as array to keep consistence with other generate method(s)
        $lines = [];
        $lines[] = self::getFrequentlyOccurringBalls($draws, true);
        return $lines;
    }

    /**
     * Returns array of balls that frequently occur for the specified draws array.
     *
     * @param array $draws The draws array to use.
     * @param bool $together Balls that occur together?
     * @return array Array of balls 'normal' => (5), 'luckyStars' => (2).
     */
    private static function getFrequentlyOccurringBalls(array $draws, bool $together): array
    {
        $results = [];
        $normalBalls = Utils::getFrequentlyOccurringBalls(
            $draws,
            self::getNormalBallNames(),
            static::getNumOfMainBalls(),
            $together
        );
        $results['mainNumbers'] = $normalBalls;
        if (static::hasThunderball()) {
            $thunderball = Utils::getFrequentlyOccurringBalls(
                $draws,
                ['thunderball'],
                1,
                $together
            );
            $results['thunderball'] = $thunderball;
        }

        // Return results array
        return $results;
    }

    /**
     * Array of normal ball names.
     *
     * @return array Array of normal ball names.
     */
    private static function getNormalBallNames(): array
    {
        $ballNames = [];
        for ($b = 1; $b <= static::getNumOfMainBalls(); $b++) {
            $ballNumber = 'ball' . $b;
            $ballNames[] = $ballNumber;
        }
        return $ballNames;
    }

    /**
     * Generate lotto lines by iterating through most frequent machine, ball set and balls within that set.
     *
     * Will run through however many history draws there are available and generate as many lines as possible
     * depending on the size of the data.
     *
     * @since 1.0.0
     *
     * @param array $draws The draws array to use.
     * @return array Array of lines generated.
     */
    protected static function generateFullIteration(array $draws): array
    {
        $lines = [];
        $machines = self::getMachineNames($draws);
        foreach ($machines as $machine) {
            // Loop through ball sets (for single machine).
            $machineDraws = self::filterDrawsByMachine($draws, $machine);
            $ballSets = self::getBallSets($machineDraws);
            foreach ($ballSets as $ballSet) {
                $filteredDraws = self::filterDrawsByBallSet($machineDraws, $ballSet);
                $lines[] = self::getFrequentlyOccurringBalls($filteredDraws, true);
            }
        }

        return $lines;
    }

    /**
     * Returns a list of machine names sorted by most frequent first.
     *
     * @since 1.0.0
     *
     * @param array $draws Array of draws.
     * @return array Array of machine names with most frequent first.
     */
    private static function getMachineNames(array $draws): array
    {
        $machineCount = Utils::getCount($draws, ['machine']);
        arsort($machineCount);
        reset($machineCount);
        return array_keys($machineCount);
    }

    /**
     * Returns a list of ball sets sorted by most frequent first.
     *
     * @since 1.0.0
     *
     * @param array $draws Array of draws.
     * @return array Array of ball sets with most frequent first.
     */
    private static function getBallSets(array $draws): array
    {
        $ballSetCount = Utils::getCount($draws, ['ballSet']);
        arsort($ballSetCount);
        reset($ballSetCount);
        return array_keys($ballSetCount);
    }

    /**
     * Filter the specified array by the specified machine name.
     *
     * @since 1.0.0
     *
     * @param array $draws Array of draws.
     * @param string $machine Machine name to filter by.
     * @return array Filtered array of draws.
     */
    private static function filterDrawsByMachine(array $draws, string $machine): array
    {
        $filteredDraws = Utils::filterDrawsBy(['machine'], $draws, $machine);
        return $filteredDraws;
    }

    /**
     * Filter the specified array by the specified ball set.
     *
     * @since 1.0.0
     *
     * @param array $draws Array of draws.
     * @param string $ballSet Ball set to filter by.
     * @return array Filtered array of draws.
     */
    private static function filterDrawsByBallSet(array $draws, string $ballSet): array
    {
        $filteredDraws = Utils::filterDrawsBy(['ballSet'], $draws, $ballSet);
        return $filteredDraws;
    }
}
