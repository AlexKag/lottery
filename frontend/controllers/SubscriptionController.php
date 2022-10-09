<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
//use yii\web\NotFoundHttpException;
use frontend\modules\user\models\NotifyEmailForm;
use common\models\SubscriptionModel;
use yii\filters\AccessControl;

class SubscriptionController extends Controller
{

    public $defaultAction = 'subscribe';

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['subscribe', 'unsubscribe'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                ]
            ]
        ];
    }

    /**
     * @return 
     */
    public function actionSubscribe()
    {
        $model = new NotifyEmailForm();

        if (($model->load(Yii::$app->request->post()) && $model->validate()) || ($model->load(Yii::$app->request->get()) && $model->validate())) {
            $item = SubscriptionModel::findOne(['email' => $model->email]);
            $type = 'type_' . $model->type;
            if ($item instanceof SubscriptionModel) {
                $item->$type = true;
            } else {
                $item = new SubscriptionModel();
                $params = $model->attributes + [$type => true];
                $item->load($params, '');
            }
            $item->user_id = isset(Yii::$app->user->id) ? getMyId() : null;
            if ($item->save()) {
                $message = sprintf('Адрес %s подписан на уведомления!', $model->email);
                Yii::$app->session->addFlash('info', $message);
                Yii::info($message . ", тип [$type]", 'subscription');
            } else {
                $message = sprintf('Извините! Не удалось оформить подписку на уведомления адреса %s!', $model->email);
                Yii::$app->session->addFlash('warning', $message);
                Yii::warning($message . ", тип [$type]", 'subscription');
            }
        }

        return $this->goBack(Yii::$app->request->referrer);
    }

    /**
     * @return 
     */
    public function actionUnsubscribe()
    {
        $model = new NotifyEmailForm();

        if (($model->load(Yii::$app->request->post()) && $model->validate()) || ($model->load(Yii::$app->request->get()) && $model->validate())) {
            $item = SubscriptionModel::findOne(['email' => $model->email]);
            $type = 'type_' . $model->type;
            if ($item instanceof SubscriptionModel) {
                $item->user_id = isset(Yii::$app->user->id) ? getMyId() : null;
                $item->$type = false;
                if ($item->save()) {
                    $message = sprintf('Подписка уведомлений на адрес %s отменена!', $model->email);
                    Yii::$app->session->addFlash('info', $message);
                    Yii::info($message . ", тип [$type]", 'subscription');
                }
            }
            return $this->goBack();
        }
        $message = sprintf('Извините! Не удалось отменить подписку на адрес %s!', $model->email);
        Yii::$app->session->addFlash('warning', $message);
        Yii::warning($message . ", тип [$type]", 'subscription');

        return $this->goBack(Yii::$app->request->referrer);
    }

}
