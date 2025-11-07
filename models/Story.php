<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;

/**
 * @property-read $id
 * @property $deleted_at
 * @property $author_name
 * @property $author_email
 * @property $message
 * @property $author_ip
 * @property $created_at
 * @property $updated_at
 * @property $auth_token
 *
 **/
class Story extends \yii\db\ActiveRecord
{
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public static function tableName(): string
    {
        return 'story';
    }
}