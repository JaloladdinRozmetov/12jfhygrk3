<?php

namespace migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%story}}`.
 */
class m251106_185835_create_story_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->createTable('{{%story}}', [
            'id' => $this->primaryKey(),
            'author_name' => $this->string(15)->notNull(),
            'message' => $this->text()->notNull(),
            'author_email' => $this->string(50)->notNull(),
            'author_ip' => $this->string(45)->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted_at' => $this->dateTime()->null(),
            'auth_token' => $this->string(32)->null()->unique(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropTable('{{%story}}');
    }
}
