<?php
namespace Refactored;

use function echoln;

require_once __DIR__ . '/RefactoredPlayer.php';

class RefactoredGame
{
    const CATEGORY_POP = 'Pop';
    const CATEGORY_SCIENCE = 'Science';
    const CATEGORY_SPORTS = 'Sports';
    const CATEGORY_ROCK = 'Rock';
    const BOARD_PLACES = [
        0 => self::CATEGORY_POP,
        1 => self::CATEGORY_SCIENCE,
        2 => self::CATEGORY_SPORTS,
        3 => self::CATEGORY_ROCK,
        4 => self::CATEGORY_POP,
        5 => self::CATEGORY_SCIENCE,
        6 => self::CATEGORY_SPORTS,
        7 => self::CATEGORY_ROCK,
        8 => self::CATEGORY_POP,
        9 => self::CATEGORY_SCIENCE,
        10 => self::CATEGORY_SPORTS,
        11 => self::CATEGORY_ROCK
    ];
    const DECK_SIZE = 50;

    private $players;

    private $popQuestions;
    private $scienceQuestions;
    private $sportsQuestions;
    private $rockQuestions;

    private $currentPlayer = 0;
    private $isGettingOutOfPenaltyBox;

    function __construct()
    {
        $this->players = [];

        $this->createNewQuestionDecks();
    }

    private function createNewQuestionDecks(): void
    {
        $this->popQuestions = [];
        $this->scienceQuestions = [];
        $this->sportsQuestions = [];
        $this->rockQuestions = [];

        for ($i = 0; $i < self::DECK_SIZE; $i++) {
            $this->popQuestions[] = self::createPopQuestion($i);
            $this->scienceQuestions[] = self::createScienceQuestion($i);
            $this->sportsQuestions[] = self::createSportsQuestion($i);
            $this->rockQuestions[] = self::createRockQuestion($i);
        }
    }

    private static function createRockQuestion($index): string
    {
        return "Rock Question " . $index;
    }

    private static function createSportsQuestion(int $index): string
    {
        return ('Sports Question ' . $index);
    }

    private static function createScienceQuestion(int $index): string
    {
        return ('Science Question ' . $index);
    }

    private static function createPopQuestion(int $i): string
    {
        return 'Pop Question ' . $i;
    }

    public function addPlayer($playerName): void
    {
        $this->players[] = new RefactoredPlayer($playerName);

        echoln($playerName . " was added");
        echoln("They are player number " . count($this->players));
    }

    private function currentPlayer(): RefactoredPlayer
    {
        return $this->players[$this->currentPlayer];
    }

    public function roll($roll)
    {
        echoln($this->currentPlayer()->name() . " is the current player");
        echoln("They have rolled a " . $roll);

        if (!$this->currentPlayer()->isInPenaltyBox()) {
            $this->movePlayerOnBoardAndAskNextQuestion($roll);
        } else {
            $oddNumberWasRolled = $roll % 2 != 0;
            if ($oddNumberWasRolled) {
                $this->isGettingOutOfPenaltyBox = true;

                echoln($this->currentPlayer()->name() . " is getting out of the penalty box");
                $this->movePlayerOnBoardAndAskNextQuestion($roll);
            } else {
                echoln($this->currentPlayer()->name() . " is not getting out of the penalty box");
                $this->isGettingOutOfPenaltyBox = false;
            }
        }
    }

    private function askNextQuestion()
    {
        if ($this->currentPlayerCategory() == self::CATEGORY_POP) {
            echoln(array_shift($this->popQuestions));
        }
        if ($this->currentPlayerCategory() == self::CATEGORY_SCIENCE) {
            echoln(array_shift($this->scienceQuestions));
        }
        if ($this->currentPlayerCategory() == self::CATEGORY_SPORTS) {
            echoln(array_shift($this->sportsQuestions));
        }
        if ($this->currentPlayerCategory() == self::CATEGORY_ROCK) {
            echoln(array_shift($this->rockQuestions));
        }
    }

    private function currentPlayerCategory(): string
    {
        return self::BOARD_PLACES[$this->currentPlayer()->boardPlace()];
    }

    public function wasCorrectlyAnswered(): bool// true = notAWinner
    {
        if ($this->currentPlayer()->isInPenaltyBox() && !$this->isGettingOutOfPenaltyBox) {
            $this->nextPlayer();
            return true;
        }

        echoln("Answer was correct!!!!");
        $this->currentPlayer()->addOneCoin();
        echoln(
            $this->currentPlayer()->name()
            . " now has "
            . $this->currentPlayer()->coins()
            . " Gold Coins."
        );

        if ($this->didCurrentPlayerWin()) {
            return false;
        }

        $this->nextPlayer();

        return true;
    }

    public function wrongAnswer(): bool
    {
        echoln("Question was incorrectly answered");
        echoln($this->currentPlayer()->name() . " was sent to the penalty box");
        $this->currentPlayer()->moveInPenaltyBox();

        $this->nextPlayer();

        return true;
    }

    private function didCurrentPlayerWin(): bool
    {
        return $this->currentPlayer()->coins() >= 6;
    }

    private function nextPlayer(): void
    {
        $this->currentPlayer++;
        if ($this->currentPlayer == count($this->players)) {
            $this->currentPlayer = 0;
        }
    }

    private function movePlayerOnBoardAndAskNextQuestion(int $roll): void
    {
        $this->currentPlayer()->moveOnBoard($roll);

        echoln(
            sprintf("%s's new location is %d", $this->currentPlayer()->name(), $this->currentPlayer()->boardPlace())
        );
        echoln("The category is " . $this->currentPlayerCategory());
        $this->askNextQuestion();
    }
}
