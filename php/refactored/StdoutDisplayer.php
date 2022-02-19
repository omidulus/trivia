<?php

namespace Refactored;

class StdoutDisplayer
{

    public function displayPlayerAnsweredWrongly(string $playerName): void
    {
        $this->echoln("Question was incorrectly answered");
        $this->echoln($playerName . " was sent to the penalty box");
    }

    public function displayPlayerAnswersCorrectly(string $playerName, int $coinsAfterAnswer): void
    {
        $this->echoln("Answer was correct!!!!");
        $this->echoln(
            $playerName
            . " now has "
            . $coinsAfterAnswer
            . " Gold Coins."
        );
    }

    public function displayPlayerAdded(string $playerName, int $playerNumber): void
    {
        $this->echoln($playerName . " was added");
        $this->echoln("They are player number " . $playerNumber);
    }

    public function displayPlayerCategory(string $currentPlayerCategory): void
    {
        $this->echoln("The category is " . $currentPlayerCategory);
    }

    public function displayCurrentPlayerChanged(string $playerName): void
    {
        $this->echoln($playerName . " is the current player");
    }

    public function displayPlayerLeftThePenaltyBox(string $playerName): void
    {
        $this->echoln($playerName . " is getting out of the penalty box");
    }

    public function displayPlayerCouldNotLeavePenaltyBox(string $playerName): void
    {
        $this->echoln($playerName . " is not getting out of the penalty box");
    }

    public function displayQuestion(string $question): void
    {
        $this->echoln($question);
    }

    public function displayRolledDie(int $roll): void
    {
        $this->echoln("They have rolled a " . $roll);
    }

    public function displayPlayerNewStatus(Player $currentPlayer): void
    {
        $this->echoln(
            sprintf("%s's new location is %d", $currentPlayer->name(), $currentPlayer->boardPlace())
        );
    }

    private function echoln(string $string) {
        echo $string."\n";
    }
}
