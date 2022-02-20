<?php
namespace Refactored;

require_once __DIR__ . '/Player.php';
require_once __DIR__ . '/QuestionDeck.php';
require_once __DIR__ . '/Displayer.php';

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
    private $currentPlayer = -1;
    private $currentPlayerMustAnswer = false;
    /** @var QuestionDeck[] */
    private $questionDecks = [];
    private $displayer;

    function __construct(array $players, Displayer $displayer)
    {
        $this->displayer = $displayer;

        foreach ($players as $player) {
            $this->addPlayer($player);
        }

        $this->generateQuestionDecks();
    }

    private function addPlayer($playerName): void
    {
        $this->players[] = new Player($playerName);

        $this->displayer->displayPlayerAdded($playerName, count($this->players));
    }

    private function generateQuestionDecks(): void
    {
        $this->questionDecks[self::CATEGORY_POP] = QuestionDeck::generateDeck(self::CATEGORY_POP, self::DECK_SIZE);
        $this->questionDecks[self::CATEGORY_ROCK] = QuestionDeck::generateDeck(self::CATEGORY_ROCK, self::DECK_SIZE);
        $this->questionDecks[self::CATEGORY_SCIENCE] = QuestionDeck::generateDeck(self::CATEGORY_SCIENCE, self::DECK_SIZE);
        $this->questionDecks[self::CATEGORY_SPORTS] = QuestionDeck::generateDeck(self::CATEGORY_SPORTS, self::DECK_SIZE);
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

        $this->displayer->displayCurrentPlayerChanged($this->currentPlayer()->name());
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
        $this->displayer->displayRolledDie($roll);

        $this->playGamePhaseForGettingOutOfThePenaltyBox($roll);
        if (!$this->currentPlayer()->isInPenaltyBox()) {
            $this->currentPlayer()->moveOnBoard($roll);

            $this->displayer->displayPlayerNewStatus($this->currentPlayer());
            $this->displayer->displayPlayerCategory($this->currentPlayerCategory());
            $this->askNextQuestion();
        }
    }

    private function playGamePhaseForGettingOutOfThePenaltyBox(int $roll): void
    {
        $oddNumberWasRolled = $roll % 2 != 0;

        if ($this->currentPlayer()->isInPenaltyBox() && $oddNumberWasRolled) {
            $this->displayer->displayPlayerLeftThePenaltyBox($this->currentPlayer()->name());
            $this->currentPlayer()->exitPenaltyBox();
        }

        if ($this->currentPlayer()->isInPenaltyBox() && !$oddNumberWasRolled) {
            $this->displayer->displayPlayerCouldNotLeavePenaltyBox($this->currentPlayer()->name());
        }
    }

    private function askNextQuestion()
    {
        $this->displayer->displayQuestion($this->questionDecks[$this->currentPlayerCategory()]->readNextCard());

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

    public function currentPlayerMustAnswer(): bool
    {
        return $this->currentPlayerMustAnswer;
    }

    public function currentPlayerAnswersCorrectly(): void
    {
        $this->currentPlayer()->addOneCoin();
        $this->displayer->displayPlayerAnswersCorrectly($this->currentPlayer()->name(), $this->currentPlayer()->coins());
        $this->currentPlayerMustAnswer = false;
    }

    public function currentPlayerAnswersWrongly(): void
    {
        $this->displayer->displayPlayerAnsweredWrongly($this->currentPlayer()->name());
        $this->currentPlayer()->moveInPenaltyBox();
        $this->currentPlayerMustAnswer = false;
    }
}
