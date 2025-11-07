<?php
/** @var yii\web\View $this */
/** @var models\forms\StoryForm $model */
/** @var app\models\Story $story */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Редактировать пост';
?>

<div class="story-edit">
    <h1>Редактирование поста #<?= $story->id ?></h1>
    <p class="text-danger">У вас осталось <?= Yii::$app->formatter->asDuration($story->created_at + 12 * 3600 - time()) ?> на редактирование.</p>

    <div class="row">
        <div class="col-lg-6">
            <?php $form = ActiveForm::begin(['id' => 'story-edit-form']); ?>

            <?= $form->field($model, 'message')->textarea(['rows' => 6]) ?>

            <div class="form-group">
                <?= Html::submitButton('Сохранить изменения', ['class' => 'btn btn-success']) ?>
                <?= Html::a('Отмена', ['index'], ['class' => 'btn btn-default']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>