<?php

/**
 * Helper class to generate numbers for the Set For Life game.
 */

declare(strict_types=1);

namespace App\Services\Lottery;

/**
 * Helper class to generate numbers for the Set For Life game.
 *
 * @package App\Services\Lottery
 * @since 1.0.0
 */
class SetForLifeGenerate
{
    /**
     * The name of the Set For Life game.
     *
     * @return string Name of the Set For Life game.
     */
    protected static function getNameOfGame(): string
    {
        return 'Set For Life';
    }

    /**
     * The number of main balls to return in the results.
     *
     * @return int Number of main balls in results.
     */
    protected static function getNumOfMainBalls(): int
    {
        return 5;
    }

    /**
     * Generate Set For Life numbers.
     *
     * @since 1.0.0
     *
     * @return array Array of lines containing generated numbers.
     */
    public static function generate(): array
    {
        $allDraws = SetForLifeDownload::readSetForLifeDrawHistory();

        // Build the results array header
        $gameName = static::getNameOfGame();
        $latestDrawDate = Utils::getLatestDrawDate($allDraws);

        // Build generated lines
        $linesFrequentTogether = self::generateMostFrequentTogether($allDraws);
        $linesFullIteration = self::generateFullIteration($allDraws);

        // Find the most popular Life Ball number across generated lines
        $mostPopularLifeBall = self::findMostPopularLifeBall($linesFrequentTogether, $linesFullIteration);

        // Find first line from full-iteration with the most popular Life Ball and its index
        $firstLineIndex = self::findFirstLineIndexWithLifeBall($linesFullIteration, $mostPopularLifeBall);
        $firstLineWithPopularLifeBall = ($firstLineIndex !== null) ? $linesFullIteration[$firstLineIndex] : $linesFullIteration[0];

        // Remove this line from full-iteration to avoid duplication
        $remainingFullIterationLines = $linesFullIteration;
        if ($firstLineIndex !== null) {
            array_splice($remainingFullIterationLines, $firstLineIndex, 1);
        }

        $lines = [
            'most-popular-life-ball' => [$firstLineWithPopularLifeBall],
            'most-freq-together' => $linesFrequentTogether,
            'full-iteration' => $remainingFullIterationLines,
        ];

        $lineBalls = [
            'mainNumbers' => static::getNumOfMainBalls(),
            'lifeBall' => 1,
        ];

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
     * Find the most popular Life Ball number across all generated lines.
     *
     * @param array $lines1 First set of lines to analyze.
     * @param array $lines2 Second set of lines to analyze.
     * @return int The most popular Life Ball number, or 1 if no valid data exists.
     */
    protected static function findMostPopularLifeBall(array $lines1, array $lines2): int
    {
        $lifeBallCounts = [];

        $allLines = array_merge($lines1, $lines2);
        foreach ($allLines as $line) {
            if (isset($line['lifeBall']) && is_array($line['lifeBall']) && count($line['lifeBall']) > 0) {
                $lifeBall = $line['lifeBall'][0];
                if (!isset($lifeBallCounts[$lifeBall])) {
                    $lifeBallCounts[$lifeBall] = 0;
                }
                $lifeBallCounts[$lifeBall]++;
            }
        }

        if (empty($lifeBallCounts)) {
            return 1;
        }

        arsort($lifeBallCounts);
        reset($lifeBallCounts);
        return (int) key($lifeBallCounts);
    }

    /**
     * Find the index of the first line with the specified Life Ball number.
     *
     * @param array $lines Lines to search through.
     * @param int $lifeBall The Life Ball number to find.
     * @return int|null The index of the first line with the specified Life Ball, or null if not found.
     */
    protected static function findFirstLineIndexWithLifeBall(array $lines, int $lifeBall): ?int
    {
        foreach ($lines as $index => $line) {
            if (isset($line['lifeBall']) && is_array($line['lifeBall']) && count($line['lifeBall']) > 0) {
                if ($line['lifeBall'][0] === $lifeBall) {
                    return $index;
                }
            }
        }

        return null;
    }

    /**
     * Generate a line by finding balls that occur most frequently together.
     *
     * @param array $draws The draws array to use.
     * @return array Array of lines generated.
     */
    protected static function generateMostFrequentTogether(array $draws): array
    {
        $lines = [];
        $lines[] = self::getFrequentlyOccurringBalls($draws, true);
        return $lines;
    }

    /**
     * Returns array of balls that frequently occur for the specified draws array.
     *
     * @param array $draws The draws array to use.
     * @param bool $together Balls that occur together?
     * @return array
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

        $lifeBall = Utils::getFrequentlyOccurringBalls(
            $draws,
            ['lifeBall'],
            1,
            $together
        );
        $results['lifeBall'] = $lifeBall;

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
     * Generate lines by iterating through most frequent machine, ball set and balls within that set.
     *
     * @param array $draws The draws array to use.
     * @return array Array of lines generated.
     */
    protected static function generateFullIteration(array $draws): array
    {
        $lines = [];
        $machines = self::getMachineNames($draws);
        foreach ($machines as $machine) {
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
     * @param array $draws Array of draws.
     * @param int|string $ballSet Ball set to filter by.
     * @return array Filtered array of draws.
     */
    private static function filterDrawsByBallSet(array $draws, int|string $ballSet): array
    {
        $filteredDraws = Utils::filterDrawsBy(['ballSet'], $draws, $ballSet);
        return $filteredDraws;
    }
}
