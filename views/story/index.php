<?php
/** @var yii\web\View $this */
/** @var app\models\forms\StoryForm $model */
/** @var app\models\Story[] $stories */

use app\helpers\IpHelper;
use yii\captcha\Captcha;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'StoryValut: Оставьте своё сообщение';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Добро пожаловать в StoryValut!</h1>
        <p class="lead">Поделитесь своей историей (до 1000 символов).</p>
    </div>

    <div class="row">
        <div class="col-lg-6 offset-lg-3">
            <h3>Оставить сообщение</h3>

            <?php $form = ActiveForm::begin([
                'id' => 'story-form',
                'enableClientValidation' => true,
            ]); ?>

            <?= $form->field($model, 'author_name')->textInput(['autofocus' => true]) ?>
            <?= $form->field($model, 'author_email') ?>
            <?= $form->field($model, 'message')->textarea(['rows' => 6]) ?>

            <?= $form->field($model,'verificationCode')->widget(Captcha::class,[
                'captchaAction' => 'story/captcha',
            ]) ?>
            <div class="form-group">
                <?= Html::submitButton('Опубликовать', ['class' => 'btn btn-primary', 'name' => 'story-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <hr>

    <h3>Опубликованные истории (<?= count($stories) ?>)</h3>
    <div class="row">
        <?php foreach ($stories as $story): ?>
            <div class="col-md-6 mb-3">
                <div class="card card-default">
                    <div class="card-body">
                        <h5 class="card-title"><?= Html::encode($story->author_name) ?></h5>

                        <p><?= $story->message ?></p>

                        <p>
                            <small class="text-muted">
                                <?= Yii::$app->formatter->asRelativeTime($story->created_at) ?> |

                                <?= IpHelper::maskIp($story->author_ip) ?> |

                                <?= Yii::t('app', '{postsCount, plural, =0{нет постов} one{# пост} few{# поста} many{# постов} other{# поста}}', [
                                    'postsCount' => $this->context->storyRepository->countPostsByIp($story->author_ip)
                                ]) ?>
                            </small>
                        </p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>