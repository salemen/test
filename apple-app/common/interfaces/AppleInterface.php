<?php

namespace common\interfaces;

interface AppleInterface {
    public function fallToGround(): void;
    public function eat(float $percent): void;
    public function isRotten(): bool;
    public function isOnTree(): bool;
    public function isEaten(): bool;
    public function deleteIfEaten(): void;
}
