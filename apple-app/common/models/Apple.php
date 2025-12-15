<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use common\interfaces\AppleInterface;
use Exception;

/**
 * @property int $id
 * @property string $color
 * @property int $created_at
 * @property int|null $fell_at
 * @property float $eaten_percent // сколько процетов съедено
 * @property int $status
 */

class Apple extends ActiveRecord implements AppleInterface
{
    const STATUS_ON_TREE = 1; // на дереве
    const STATUS_FALLEN  = 2; // упало
    const STATUS_ROTTEN  = 3; // гнилое

    const ROT_TIME_HOURS = 5; // портится после 5 часов

    public static function tableName()
    {
        return '{{%apples}}';
    }

    /**
     * @return void
     */
    public function init()
    {
        parent::init();
        if (!$this->isNewRecord) return;

        // При создании — цвет случайный согласно ТЗ
        $colors = ['green', 'red', 'yellow'];
        $this->color = $colors[array_rand($colors)];

        // Дата появления — текущая
        $this->created_at = time();

        // По умолчанию на дереве
        $this->status = self::STATUS_ON_TREE;
    }

    /**
     * @return void
     * @throws \yii\db\Exception
     */
    public function fallToGround(): void
    {
        if ($this->status !== self::STATUS_ON_TREE) {
            throw new Exception('Яблоко уже не на дереве');
        }
        $this->status = self::STATUS_FALLEN;
        $this->fell_at = time();
        $this->save(false);
    }

    /**
     * @param float $percent
     * @return void
     * @throws \yii\db\Exception
     */
    public function eat(float $percent): void
    {
        if ($this->isRotten()) {
            throw new Exception('Нельзя есть гнилое яблоко');
        }

        if ($this->isOnTree()) {
            throw new Exception('Съесть нельзя, яблоко на дереве');
        }

        if ($percent <= 0 || $percent > 100) {
            throw new Exception('Процент должен быть от 0 до 100');
        }

        $newEaten = $this->eaten_percent + $percent;

        if ($newEaten > 100) {
            throw new Exception('Нельзя съесть больше 100%');
        }

        $this->eaten_percent = $newEaten;
        $this->save(false);

        if ($this->isEaten()) { // если откусано больше 100% то считаем что яблоко съели, удаляем его
            $this->deleteIfEaten();
        }
    }

    /**
     * @return bool
     * @throws \yii\db\Exception
     */
    public function isRotten(): bool
    {
        if ($this->status === self::STATUS_ON_TREE) {
            return false;
        }

        if ($this->status === self::STATUS_ROTTEN) {
            return true;
        }

        if ($this->fell_at === null) {
            return false;
        }

        $hoursOnGround = (time() - $this->fell_at) / 3600;
        if ($hoursOnGround >= self::ROT_TIME_HOURS) {
            $this->status = self::STATUS_ROTTEN;
            $this->save(false);
            return true;
        }

        return false;
    }

    public function isOnTree(): bool
    {
        return $this->status === self::STATUS_ON_TREE;
    }

    public function isEaten(): bool
    {
        return $this->eaten_percent >= 100;
    }

    public function deleteIfEaten(): void
    {
        if ($this->isEaten()) {
            $this->delete();
        }
    }

    // Вспомогательный метод для отображения состояния яблока
    public function getStatusLabel(): string
    {
        if ($this->isRotten()) {
            return 'Гнилое';
        }
        if ($this->isOnTree()) {
            return 'На дереве';
        }
        return 'Упало';
    }

    // Для view — сколько осталось
    public function getRemainingPercent(): float
    {
        return 100 - $this->eaten_percent;
    }
}