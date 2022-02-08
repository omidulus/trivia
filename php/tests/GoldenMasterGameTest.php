<?php
include __DIR__ . '/../Game.php';
include __DIR__ . '/../RefactoredGame.php';


use PHPUnit\Framework\TestCase;
use Refactored\RefactoredGame;

class GoldenMasterGameTest extends TestCase
{
    const SIMULATED_GAMES = 1000;
    const PLAYERS_POOL = ["Chet", "Pat", "Sue", "Victor", "Tomy", "Tarzan", "Unchiu Bobitza"];

    /** @test */
    public function compareOutputAndWinners()
    {
        for ($i = 1; $i <= self::SIMULATED_GAMES; $i++) {
            $playedTurnsInCurrentGame = rand(3, 10);
            $players = self::simulatePlayers();
            $rolledDices = self::simulateRolledDices($playedTurnsInCurrentGame);
            $answers = self::simulatePlayersAnswers($playedTurnsInCurrentGame);

            ob_start();
            $notAWinnerInLegacy = $this->playLegacyGame($players, $rolledDices, $answers);
            $legacyOutput = ob_get_contents();

            ob_clean();
            $notAWinnerInRefactored = $this->playRefactoredGame($players, $rolledDices, $answers);
            $refactoredOutput = ob_get_contents();
            ob_end_clean();

            self::assertEquals($notAWinnerInLegacy, $notAWinnerInRefactored);
            self::assertEquals($legacyOutput, $refactoredOutput);
        }
    }

    private static function simulatePlayers(): array
    {
        $playersPool = self::PLAYERS_POOL;

        shuffle($playersPool);
        return array_slice($playersPool, 0, rand(2, count($playersPool) - 1));
    }

    private static function simulatePlayersAnswers(int $playedTurns): array
    {
        return array_map(
            function () {
                return GoldenMasterGameTest::tenPercentProbability();
            },
            array_fill(0, $playedTurns, null)
        );
    }

    private static function simulateRolledDices(int $playedTurns): array
    {
        return array_map(
            function () {
                return GoldenMasterGameTest::rollDie();
            },
            array_fill(0, $playedTurns, null)
        );
    }

    private static function rollDie(): int
    {
        return rand(0, 5) + 1;
    }

    private static function tenPercentProbability(): bool
    {
        return rand(0, 9) == 7;
    }

    private function playLegacyGame(array $players, array $rolledDices, array $answers): bool
    {
        $legacyGame = new Game();

        foreach ($players as $player) {
            $legacyGame->add($player);
        }

        $i = 0;
        do {
            $legacyGame->roll($rolledDices[$i]);

            if ($answers[$i] === false) {
                $notAWinner = $legacyGame->wrongAnswer();
            } else {
                $notAWinner = $legacyGame->wasCorrectlyAnswered();
            }
            $i++;
        } while ($notAWinner && $i < count($rolledDices));

        return $notAWinner;
    }

    private function playRefactoredGame(array $players, array $rolledDices, array $answers): bool
    {
        $refactoredGame = new RefactoredGame($players);

        $i = 0;
        do {

            if ($answers[$i] === false) {
                $refactoredGame->nextPlayerRoll($rolledDices[$i], false);
            } else {
                $refactoredGame->nextPlayerRoll($rolledDices[$i], true);
            }
            $i++;
            $simulationOver = $i >= count($rolledDices);
        } while (!$refactoredGame->hasEnded() && !$simulationOver);

        return !$refactoredGame->hasEnded();

    }
}
