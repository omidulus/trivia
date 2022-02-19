<?php

namespace Refactored;

class StdoutDisplayer
{

    public function displayPlayerAnsweredWrongly(string $playerName): void
    {
        echoln("Question was incorrectly answered");
        echoln($playerName . " was sent to the penalty box");
    }

    public function displayPlayerAnswersCorrectly(string $playerName, int $coinsAfterAnswer): void
    {
        echoln("Answer was correct!!!!");
        echoln(
            $playerName
            . " now has "
            . $coinsAfterAnswer
            . " Gold Coins."
        );
    }

    public function displayPlayerAdded(string $playerName, int $playerNumber): void
    {
        echoln($playerName . " was added");
        echoln("They are player number " . $playerNumber);
    }

    public function displayPlayerCategory(string $currentPlayerCategory): void
    {
        echoln("The category is " . $currentPlayerCategory);
    }

    public function displayCurrentPlayerChanged(string $playerName): void
    {
        echoln($playerName . " is the current player");
    }

    public function displayPlayerLeftThePenaltyBox(string $playerName): void
    {
        echoln($playerName . " is getting out of the penalty box");
    }

    public function displayPlayerCouldNotLeavePenaltyBox(string $playerName): void
    {
        echoln($playerName . " is not getting out of the penalty box");
    }

    public function displayQuestion(string $question): void
    {
        echoln($question);
    }

    public function displayRolledDie(int $roll): void
    {
        echoln("They have rolled a " . $roll);
    }

    public function displayPlayerNewStatus(Player $currentPlayer): void
    {
        echoln(
            sprintf("%s's new location is %d", $currentPlayer->name(), $currentPlayer->boardPlace())
        );
    }
}
