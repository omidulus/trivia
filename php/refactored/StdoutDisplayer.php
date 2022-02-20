<?php

namespace Refactored;

require_once __DIR__ . '/Displayer.php';

class StdoutDisplayer implements Displayer
{

    public function displayPlayerAnsweredWrongly(string $playerName): void
    {
        $this->echoLine("Question was incorrectly answered");
        $this->echoLine($playerName . " was sent to the penalty box");
    }

    public function displayPlayerAnswersCorrectly(string $playerName, int $coinsAfterAnswer): void
    {
        $this->echoLine("Answer was correct!!!!");
        $this->echoLine(
            $playerName
            . " now has "
            . $coinsAfterAnswer
            . " Gold Coins."
        );
    }

    public function displayPlayerAdded(string $playerName, int $playerNumber): void
    {
        $this->echoLine($playerName . " was added");
        $this->echoLine("They are player number " . $playerNumber);
    }

    public function displayPlayerCategory(string $currentPlayerCategory): void
    {
        $this->echoLine("The category is " . $currentPlayerCategory);
    }

    public function displayCurrentPlayerChanged(string $playerName): void
    {
        $this->echoLine($playerName . " is the current player");
    }

    public function displayPlayerLeftThePenaltyBox(string $playerName): void
    {
        $this->echoLine($playerName . " is getting out of the penalty box");
    }

    public function displayPlayerCouldNotLeavePenaltyBox(string $playerName): void
    {
        $this->echoLine($playerName . " is not getting out of the penalty box");
    }

    public function displayQuestion(string $question): void
    {
        $this->echoLine($question);
    }

    public function displayRolledDie(int $roll): void
    {
        $this->echoLine("They have rolled a " . $roll);
    }

    public function displayPlayerNewStatus(Player $currentPlayer): void
    {
        $this->echoLine(
            sprintf("%s's new location is %d", $currentPlayer->name(), $currentPlayer->boardPlace())
        );
    }

    private function echoLine(string $string) {
        echo $string."\n";
    }
}
