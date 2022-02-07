<?php

namespace Refactored;

class RefactoredPlayer
{
    private $name;
    private $boardPlace;
    private $coins;
    private $isInPenaltyBox;

    public function __construct(
        string $name
    ) {
        $this->name = $name;
        $this->boardPlace = 0;
        $this->coins = 0;
        $this->isInPenaltyBox = false;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function boardPlace(): int
    {
        return $this->boardPlace;
    }

    public function coins(): int
    {
        return $this->coins;
    }

    public function isInPenaltyBox(): bool
    {
        return $this->isInPenaltyBox;
    }

    public function moveInPenaltyBox()
    {
        $this->isInPenaltyBox = true;
    }

    public function moveOnBoard(int $roll)
    {
        $this->boardPlace += $roll;
        if ($this->boardPlace >= count(RefactoredGame::BOARD_PLACES)) {
            $this->boardPlace -= count(RefactoredGame::BOARD_PLACES);
        }
    }

    public function addOneCoin()
    {
        $this->coins++;
    }
}

