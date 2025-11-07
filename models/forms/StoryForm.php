<?php
namespace app\models\forms;

use yii\base\Model;

/**
 * @property $author_name
 * @property $author_email
 * @property $message
 */
class StoryForm extends Model
{
    public $author_name;
    public $author_email;
    public $message;
    public $verificationCode;
    public function rules(): array
    {
        return [
            [['author_name','author_email','message'], 'required'],
            ['author_name', 'string','min'=>2,'max'=>15],
            ['author_email', 'email'],
            ['message', 'string','min'=>5,'max'=>1000],
            ['verificationCode', 'captcha','captchaAction'=>'story/captcha'],
        ];
    }
}