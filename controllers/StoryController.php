<?php


namespace app\controllers;

use app\models\forms\StoryForm;
use app\repositories\StoryRepository;
use app\services\StoryService;
use Yii;
use yii\db\Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\web\ServerErrorHttpException;


class StoryController extends Controller
{
    private StoryService $storyService;
    public StoryRepository $storyRepository;
    public function __construct($id, $module, StoryRepository $repository, $config = [])
    {
        $this->storyRepository = $repository;
        $this->storyService = new StoryService($repository);
        parent::__construct($id, $module, $config);
    }

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST', 'GET'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
            ]
        ];
    }

    /**
     * @throws Exception
     * @throws \yii\base\Exception
     * @throws ServerErrorHttpException
     */
    public function actionIndex(): \yii\web\Response|string
    {
        $form = new StoryForm();

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            if ($this->storyService->createStory($form)) {
                Yii::$app->session->setFlash('success', 'Ваш пост успешно опубликован! Проверьте почту для ссылок управления.');
                return $this->refresh(); // Предотвращаем повторную отправку
            }
        }

        $stories = $this->storyRepository->findAllActiveStories();

        return $this->render('index', [
            'stories' => $stories,
            'model' => $form,
        ]);
    }

    /**
     * @param int $id ID поста
     * @param string $token Уникальный токен
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionEdit(int $id, string $token)
    {
        try {
            $story = $this->storyRepository->findOneForManagement($id, $token);
        } catch (NotFoundHttpException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['index']);
        }

        $form = new StoryForm(['message' => $story->message]);

        if ($form->load(Yii::$app->request->post())) {
            try {
                if ($this->storyService->editStory($id, $token, $form->message)) {
                    Yii::$app->session->setFlash('success', 'Пост успешно обновлен.');
                    return $this->redirect(['index']);
                }
            } catch (Exception $e) {

            } catch (NotFoundHttpException $e) {

            } catch (ServerErrorHttpException $e) {

            }
        }

        return $this->render('edit', [
            'model' => $form,
            'story' => $story,
        ]);
    }

    /**
     * Страница подтверждения удаления.
     * @param int $id ID поста
     * @param string $token Уникальный токен
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function actionDelete(int $id, string $token)
    {
        try {
            $story = $this->storyRepository->findOneForManagement($id, $token);
        } catch (NotFoundHttpException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['index']);
        }

        if (Yii::$app->request->isPost) {
            if ($this->storyService->softDeleteStory($id, $token)) {
                Yii::$app->session->setFlash('success', 'Пост успешно удален (мягкое удаление).');
            }
            return $this->redirect(['index']);
        }

        return $this->render('delete', [
            'story' => $story,
            'token' => $token,
        ]);
    }
}