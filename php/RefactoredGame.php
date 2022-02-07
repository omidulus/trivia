<?php
namespace Refactored;

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
    private $playersPlaces;
    private $purses;
    private $inPenaltyBox;

    private $popQuestions;
    private $scienceQuestions;
    private $sportsQuestions;
    private $rockQuestions;

    private $currentPlayer = 0;
    private $isGettingOutOfPenaltyBox;

    function __construct()
    {
        //TODO pare ca jocul se initializeaza fara primul jucator desi sunt atribuite niste proprietati pentru el
        $this->players = [];
        $this->playersPlaces = [];
        $this->purses = [];
        $this->inPenaltyBox = [];

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
        $this->players[] = $playerName;
        $this->playersPlaces[$this->howManyPlayers() - 1] = 0;
        $this->purses[$this->howManyPlayers() - 1] = 0;
        $this->inPenaltyBox[$this->howManyPlayers() - 1] = false;

        echoln($playerName . " was added");
        echoln("They are player number " . count($this->players));
    }

    private function howManyPlayers(): int
    {
        return count($this->players);
    }

    public function roll($roll)
    {
        echoln($this->players[$this->currentPlayer] . " is the current player");
        echoln("They have rolled a " . $roll);

        if (!$this->inPenaltyBox[$this->currentPlayer]) {

            $this->movePlayerOnBoardAndAskNextQuestion($roll);
        } else {
            $oddNumberWasRolled = $roll % 2 != 0;
            if ($oddNumberWasRolled) {
                $this->isGettingOutOfPenaltyBox = true;

                echoln($this->players[$this->currentPlayer] . " is getting out of the penalty box");
                $this->movePlayerOnBoardAndAskNextQuestion($roll);
            } else {
                echoln($this->players[$this->currentPlayer] . " is not getting out of the penalty box");
                $this->isGettingOutOfPenaltyBox = false;
            }

        }

    }

    private function askQuestion()
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
        return self::BOARD_PLACES[$this->playersPlaces[$this->currentPlayer]];
    }

    public function wasCorrectlyAnswered(): bool
    {
        if ($this->inPenaltyBox[$this->currentPlayer]) {
            if ($this->isGettingOutOfPenaltyBox) {
                $playerWonTheGame = $this->afterCorrectAnswer();

                return $playerWonTheGame;
            } else {
                $this->nextPlayer();
                return true;
            }


        } else {
            $playerWonTheGame = $this->afterCorrectAnswer();

            return $playerWonTheGame;
        }
    }

    public function wrongAnswer(): bool
    {
        echoln("Question was incorrectly answered");
        echoln($this->players[$this->currentPlayer] . " was sent to the penalty box");
        // TODO verifica daca nu cumva e un bug faptul ca jucatorii nu par sa iasa din cutia pedepsei
        $this->inPenaltyBox[$this->currentPlayer] = true;

        $this->nextPlayer();

        return true;
    }

    private function didPlayerWin(): bool
    {
        //TODO ce e cu 6 asta ?
        return $this->purses[$this->currentPlayer] != 6;
    }

    private function nextPlayer(): void
    {
        $this->currentPlayer++;
        if ($this->currentPlayer == count($this->players)) {
            $this->currentPlayer = 0;
        }
    }

    private function afterCorrectAnswer(): bool
    {
        echoln("Answer was correct!!!!");
        $this->purses[$this->currentPlayer]++;
        echoln(
            $this->players[$this->currentPlayer]
            . " now has "
            . $this->purses[$this->currentPlayer]
            . " Gold Coins."
        );

        $winner = $this->didPlayerWin();
        $this->nextPlayer();
        return $winner;
    }

    private function movePlayerOnBoardAndAskNextQuestion(int $roll): void
    {
        $this->playersPlaces[$this->currentPlayer] = $this->playersPlaces[$this->currentPlayer] + $roll;
        if ($this->playersPlaces[$this->currentPlayer] >= count(self::BOARD_PLACES)) {
            $this->playersPlaces[$this->currentPlayer] -= count(self::BOARD_PLACES);
        }

        echoln(
            sprintf("%s's new location is %d", $this->players[$this->currentPlayer], $this->playersPlaces[$this->currentPlayer])
        );
        echoln("The category is " . $this->currentPlayerCategory());
        $this->askQuestion();
    }
}
