<?php

namespace Refactored;

class QuestionDeck
{
    private $questions = [];

    private function __construct()
    {
        // no need for a public constructor yet
    }

    public static function generateDeck(string $type, int $numberOfCards): self
    {
        $instance = new self();

        for ($i = 0; $i < $numberOfCards; $i++) {
            $instance->questions[] = sprintf('%s Question %s', $type, $i);
        }

        return $instance;
    }

    public function readNextCard(): string
    {
        return array_shift($this->questions);
    }
}
