<?php

namespace app\repositories;

use app\models\Story;
use yii\db\Exception;
use yii\web\NotFoundHttpException;
use Yii;

class StoryRepository
{
    /**
     * @throws Exception
     */
    public function save(Story $story): bool
    {
        try {
            return $story->save();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @throws NotFoundHttpException
     */
    public function findOneForManagement(int $id, string $token): Story
    {
        $story = Story::find()
            ->where(['id' => $id, 'auth_token' => $token])
            ->andWhere(['deleted_at' => null]) // Исключаем "мягко" удаленные
            ->one();

        if ($story === null) {
            Yii::$app->response->setStatusCode(404);
            Yii::$app->session->setFlash('error', 'Story not found');
            throw new NotFoundHttpException();
        }

        return $story;
    }

    public function getLastPostTimeByIp(string $ip): int
    {
        return (int) Story::find()
            ->select('created_at')
            ->where(['author_ip' => $ip])
            ->andWhere(['deleted_at' => null])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(1)
            ->scalar();
    }

    public function countPostsByIp(string $ip): int
    {
        return (int) Story::find()
            ->where(['author_ip' => $ip])
            ->andWhere(['deleted_at' => null])
            ->count();
    }

    public function findAllActiveStories(): array
    {
        return Story::find()
            ->where(['deleted_at' => null])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
    }

    /**
     * @throws Exception
     */
    public function softDelete(Story $story): bool
    {
        $story->deleted_at = time();
        return $story->save(false, ['deleted_at']);
    }
}