<?php
include __DIR__ . '/../Game.php';
include __DIR__ . '/../RefactoredGame.php';


use PHPUnit\Framework\TestCase;
use Refactored\RefactoredGame;

class GoldenMasterGameTest extends TestCase
{
    const SIMULATED_GAMES = 3000;
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
        return array_slice($playersPool, 0, rand(1, count($playersPool) - 1));
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
        $legacyGame = $this->createLegacyGame($players);
        return $this->playGame($legacyGame, $rolledDices, $answers);
    }

    private function playRefactoredGame(array $players, array $rolledDices, array $answers): bool
    {
        $refactoredGame = $this->createRefactoredGame($players);
        return $this->playGame($refactoredGame, $rolledDices, $answers);
    }

    private function createLegacyGame(array $players): Game
    {
        $game = new Game();

        foreach ($players as $player) {
            $game->add($player);
        }

        return $game;
    }

    private function createRefactoredGame(array $players): RefactoredGame
    {
        $game = new RefactoredGame();

        foreach ($players as $player) {
            $game->addPlayer($player);
        }

        return $game;
    }

    // CANNOT USE A TYPE HINT (INTERFACE) YET AS THAT WOULD BE REFACTORING BEFORE WRITING THE TESTS
    /** @var Game | RefactoredGame $game */
    private function playGame($game, array $rolledDices, array $answers): bool
    {
        $i = 0;

        do {
            $game->roll($rolledDices[$i]);

            if ($answers[$i] === false) {
                $notAWinner = $game->wrongAnswer();
            } else {
                $notAWinner = $game->wasCorrectlyAnswered();
            }
            $i++;
        } while ($notAWinner && $i < count($rolledDices));

        return $notAWinner;
    }
}
