<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use common\models\Apple;
use yii\web\BadRequestHttpException;
use yii\db\Exception;

class AppleController extends Controller
{
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        // Простая защита по паролю
        $password = Yii::$app->request->get('password');
        if ($password !== 'secret123') {
            return $this->render('auth');
        }

        return true;
    }

    public function actionIndex()
    {
        // Проверим и обновим статус у всех яблок (гнилые)
        $apples = Apple::find()->all();
        foreach ($apples as $apple) {
            $apple->isRotten(); // вызов обновит статус, если нужно
        }

        return $this->render('index', [
            'apples' => Apple::find()->all(),
        ]);
    }

    public function actionGenerate()
    {
        $count = rand(3, 10);
        for ($i = 0; $i < $count; $i++) {
            $apple = new Apple();
            $apple->save();
        }
        return $this->redirect(['index', 'password' => 'secret123']);
    }

    public function actionFall($id)
    {
        $apple = Apple::findOne($id);
        if (!$apple) throw new BadRequestHttpException('Яблоко не найдено');

        try {
            $apple->fallToGround();
        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }

        return $this->redirect(['index', 'password' => 'secret123']);
    }

    public function actionEat($id)
    {
        $apple = Apple::findOne($id);
        if (!$apple) throw new BadRequestHttpException('Яблоко не найдено');

        $percent = (float)Yii::$app->request->post('percent', 0);

        try {
            $apple->eat($percent);
        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }

        return $this->redirect(['index', 'password' => 'secret123']);
    }
}