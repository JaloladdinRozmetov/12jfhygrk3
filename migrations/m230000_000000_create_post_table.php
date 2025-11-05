<?php
use yii\db\Migration;

class m230000_000000_create_post_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%post}}', [
            'id' => $this->primaryKey(),
            'author' => $this->string(15)->notNull(),
            'email' => $this->string(255)->notNull(),
            'message' => $this->text()->notNull(),
            'ip' => $this->string(45)->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->null(),
            'edit_token' => $this->string(64)->notNull(),
            'delete_token' => $this->string(64)->notNull(),
            'deleted_at' => $this->integer()->null(),
        ]);

        $this->createIndex('idx-post-ip', '{{%post}}', 'ip');
        $this->createIndex('idx-post-created_at', '{{%post}}', 'created_at');
    }

    public function safeDown()
    {
        $this->dropTable('{{%post}}');
    }
}
