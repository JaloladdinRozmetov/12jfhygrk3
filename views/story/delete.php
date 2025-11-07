<?php
/** @var yii\web\View $this */
/** @var app\models\Story $story */

use yii\helpers\Html;

$this->title = 'Подтверждение удаления';
?>

<div class="story-delete">
    <h1>Удаление поста #<?= $story->id ?></h1>

    <div class="alert alert-warning">
        <p>Вы действительно хотите удалить этот пост? Это действие нельзя отменить.</p>
        <p>У вас есть <?= Yii::$app->formatter->asDuration($story->created_at + 14 * 24 * 3600 - time()) ?> до истечения срока удаления.</p>

        <blockquote class="blockquote">
            <p class="mb-0">"<?= $story->message ?>"</p>
            <footer class="blockquote-footer"><?= $story->author_name ?></footer>
        </blockquote>
    </div>

    <?= Html::beginForm(['delete', 'id' => $story->id, 'token' => $story->auth_token], 'post') ?>
    <?= Html::submitButton('Подтвердить удаление', ['class' => 'btn btn-danger', 'data-confirm' => 'Вы уверены?']) ?>
    <?= Html::a('Отменить', ['index'], ['class' => 'btn btn-default']) ?>
    <?= Html::endForm() ?>

</div>