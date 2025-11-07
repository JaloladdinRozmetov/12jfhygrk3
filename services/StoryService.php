<?php

namespace app\services;

use app\models\Story;
use app\helpers\IpHelper;
use app\models\forms\StoryForm;
use app\repositories\StoryRepository;
use Yii;
use yii\db\Exception;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

class StoryService
{
    private const int POST_LIMIT_SECONDS = 180; // 3 minutes
    private const int|float EDIT_LIMIT_SECONDS = 12 * 3600; // 12 hours
    private const int|float DELETE_LIMIT_SECONDS = 14 * 24 * 3600; // 14 days

    private StoryRepository $repository;

    public function __construct(StoryRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @throws Exception
     * @throws \yii\base\Exception
     * @throws ServerErrorHttpException
     */
    public function createStory(StoryForm $form): bool
    {
        $clientIp = IpHelper::getIp();

        if (!$this->canPost($clientIp)) {
            $lastTime = $this->repository->getLastPostTimeByIp($clientIp);
            $nextAllowedTime = $lastTime + self::POST_LIMIT_SECONDS;

            $retryAfter = date('H:i:s', $nextAllowedTime);
            Yii::$app->session->setFlash('error', "Слишком много постов. Следующий пост можно опубликовать после $retryAfter (UTC).");
            return false;
        }

        $story = new Story();
        $story->author_name = $form->author_name;
        $story->author_email = $form->author_email;

        $story->message = $this->cleanMessage($form->message);

        $story->author_ip = $clientIp;
        $story->auth_token = Yii::$app->security->generateRandomString();

        if ($this->repository->save($story)) {
            $this->sendManagementEmail($story);
            return true;
        }

        throw new ServerErrorHttpException('Ошибка сохранения поста. Попробуйте позже.');
    }

    /**
     * @throws Exception
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function editStory(int $id, string $token, string $newMessage): bool
    {
        $story = $this->repository->findOneForManagement($id, $token);

        if (time() > $story->created_at + self::EDIT_LIMIT_SECONDS) {
            Yii::$app->session->setFlash('error', 'Редактирование доступно только в течение 12 часов после публикации.');
            return false;
        }

        if (strlen(trim($newMessage)) < 5 || strlen($newMessage) > 1000) {
            Yii::$app->session->setFlash('error', 'Сообщение должно быть от 5 до 1000 символов и не может состоять только из пробелов.');
            return false;
        }

        $story->message = $this->cleanMessage($newMessage);

        if ($this->repository->save($story)) {
            return true;
        }

        throw new ServerErrorHttpException('Ошибка сохранения изменений.');
    }

    /**
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function softDeleteStory(int $id, string $token): bool
    {
        try {
            $story = $this->repository->findOneForManagement($id, $token);
        } catch (NotFoundHttpException $e) {
            throw new NotFoundHttpException();
        }

        if (time() > $story->created_at + self::DELETE_LIMIT_SECONDS) {
            Yii::$app->session->setFlash('error', 'Удаление поста доступно только в течение 14 дней после публикации.');
            return false;
        }

        try {
            return $this->repository->softDelete($story);
        } catch (Exception $e) {
            throw new ServerErrorHttpException();
        }
    }

    private function canPost(string $ip): bool
    {
        $lastPostTime = $this->repository->getLastPostTimeByIp($ip);
        if ($lastPostTime === 0) {
            return true;
        }

        return (time() - $lastPostTime) >= self::POST_LIMIT_SECONDS;
    }

    private function cleanMessage(string $message): string
    {
        $config = \HTMLPurifier_Config::createDefault();

        $config->set('HTML.Allowed', 'b,i,s');

        $purifier = new \HTMLPurifier($config);
        return $purifier->purify($message);
    }

    private function sendManagementEmail(Story $story): void
    {
        $editLink = Yii::$app->urlManager->createAbsoluteUrl([
            'story/edit',
            'id' => $story->id,
            'token' => $story->auth_token
        ]);
        $deleteLink = Yii::$app->urlManager->createAbsoluteUrl([
            'story/delete',
            'id' => $story->id,
            'token' => $story->auth_token
        ]);

        Yii::$app->mailer->compose()
            ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
            ->setTo($story->author_email)
            ->setSubject('Ссылки для управления вашим постом на StoryValut')
            ->setTextBody("Ваш пост успешно опубликован. \n\n"
                . "Ссылка для редактирования (12 часов): $editLink\n"
                . "Ссылка для удаления (14 дней): $deleteLink\n")
            ->send();
    }
}