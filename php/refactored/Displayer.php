<?php

namespace Refactored;

interface Displayer
{
    public function displayPlayerAnsweredWrongly(string $playerName): void;

    public function displayPlayerAnswersCorrectly(string $playerName, int $coinsAfterAnswer): void;

    public function displayPlayerAdded(string $playerName, int $playerNumber): void;

    public function displayPlayerCategory(string $currentPlayerCategory): void;

    public function displayCurrentPlayerChanged(string $playerName): void;

    public function displayPlayerLeftThePenaltyBox(string $playerName): void;

    public function displayPlayerCouldNotLeavePenaltyBox(string $playerName): void;

    public function displayQuestion(string $question): void;

    public function displayRolledDie(int $roll): void;

    public function displayPlayerNewStatus(Player $currentPlayer): void;
}
