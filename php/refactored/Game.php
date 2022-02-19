<?php
namespace Refactored;

use Exception;
use function echoln;

require_once __DIR__ . '/Player.php';

class Game
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

    private $currentPlayer = -1;
    private $currentPlayerMustAnswer = false;

    function __construct(array $players)
    {
        foreach ($players as $player) {
            $this->addPlayer($player);
        }

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
        return 'Rock Question ' . $index;
    }

    private static function createSportsQuestion(int $index): string
    {
        return 'Sports Question ' . $index;
    }

    private static function createScienceQuestion(int $index): string
    {
        return 'Science Question ' . $index;
    }

    private static function createPopQuestion(int $index): string
    {
        return 'Pop Question ' . $index;
    }

    private function addPlayer($playerName): void
    {
        $this->players[] = new Player($playerName);

        echoln($playerName . " was added");
        echoln("They are player number " . count($this->players));
    }

    private function activateNextPlayer(): void
    {
        if ($this->currentPlayer < 0) {
            $this->currentPlayer = 0;
        } else {
            $this->currentPlayer++;
        }
        if ($this->currentPlayer == count($this->players)) {
            $this->currentPlayer = 0;
        }

        echoln($this->currentPlayer()->name() . " is the current player");
        $this->currentPlayerMustAnswer = false;
    }

    private function currentPlayer(): Player
    {
        return $this->players[$this->currentPlayer];
    }

    public function nextPlayerRoll(int $roll)
    {
        if ($this->currentPlayerMustAnswer) {
            throw new \RuntimeException('You cannot move to the next player yet. Answer the question first.');
        }

        $this->activateNextPlayer();
        echoln("They have rolled a " . $roll);

        $this->playGamePhaseForGettingOutOfThePenaltyBox($roll);
        if (!$this->currentPlayer()->isInPenaltyBox()) {
            $this->movePlayerOnBoard($roll);
            $this->askNextQuestion();
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

        $this->currentPlayerMustAnswer = true;
    }

    private function currentPlayerCategory(): string
    {
        return self::BOARD_PLACES[$this->currentPlayer()->boardPlace()];
    }

    public function hasEnded(): bool
    {
        return $this->currentPlayer()->coins() >= 6;
    }

    private function movePlayerOnBoard(int $roll): void
    {
        $this->currentPlayer()->moveOnBoard($roll);

        echoln(
            sprintf("%s's new location is %d", $this->currentPlayer()->name(), $this->currentPlayer()->boardPlace())
        );
        echoln("The category is " . $this->currentPlayerCategory());
    }

    private function playGamePhaseForGettingOutOfThePenaltyBox(int $roll): void
    {
        $oddNumberWasRolled = $roll % 2 != 0;

        if ($this->currentPlayer()->isInPenaltyBox() && $oddNumberWasRolled) {
            echoln($this->currentPlayer()->name() . " is getting out of the penalty box");
            $this->currentPlayer()->exitPenaltyBox();
        }

        if ($this->currentPlayer()->isInPenaltyBox() && !$oddNumberWasRolled) {
            echoln($this->currentPlayer()->name() . " is not getting out of the penalty box");
        }
    }

    public function currentPlayerMustAnswer(): bool
    {
        return $this->currentPlayerMustAnswer;
    }

    public function currentPlayerAnswersCorrectly(): void
    {
        echoln("Answer was correct!!!!");
        $this->currentPlayer()->addOneCoin();
        echoln(
            $this->currentPlayer()->name()
            . " now has "
            . $this->currentPlayer()->coins()
            . " Gold Coins."
        );
        $this->currentPlayerMustAnswer = false;
    }

    public function currentPlayerAnswersWrongly(): void
    {
        echoln("Question was incorrectly answered");
        echoln($this->currentPlayer()->name() . " was sent to the penalty box");
        $this->currentPlayer()->moveInPenaltyBox();
        $this->currentPlayerMustAnswer = false;
    }
}
