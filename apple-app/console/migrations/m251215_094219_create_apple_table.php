<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%apple}}`.
 */
class m251215_094219_create_apple_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%apples}}', [
            'id' => $this->primaryKey(),
            'color' => $this->string(20)->notNull(),
            'created_at' => $this->integer()->notNull(), // дата появления
            'fell_at' => $this->integer(),               // дата падения
            'eaten_percent' => $this->decimal(5,2)->notNull()->defaultValue(0.00), // сколько съели (%)
            'status' => $this->smallInteger()->notNull()->defaultValue(1), // 1 - на дереве, 2 - упало, 3 - гнилое
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%apples}}');
    }
}
