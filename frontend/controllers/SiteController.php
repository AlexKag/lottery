<?php

namespace frontend\controllers;

use Yii;
use frontend\models\ContactForm;
use yii\web\Controller;
use common\components\lottery\models\L6x45;
use yii\helpers\Url;
use frontend\models\InviteFriendForm;
use common\models\Page;

//use yii\bootstrap\ActiveForm;
//use yii\base\Model;
//use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends Controller
{

    public $layout = 'container';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error'      => [
                'class' => 'yii\web\ErrorAction'
            ],
            'set-locale' => [
                'class'   => 'common\actions\SetLocaleAction',
                'locales' => array_keys(Yii::$app->params['availableLocales'])
            ]
        ];
    }

    public function actionIndex()
    {

        $this->layout    = 'simple.php';
        $jackpot_summ    = 0;
        $jackpot_draw_at = null;

        $items   = [];
        $items[] = [
            'name'                => '«1 из 3»',
            'logo_url'            => '/img/lotto_4.png',
            'countdown_timestamp' => 'Мгновенная игра!',
            'superprize'          => 'Испытай удачу здесь и сейчас!',
            'game_url'            => '/instant1x3/index',
        ];
        $l6x45   = L6x45::findCurrent();
        if ($l6x45 instanceof L6x45) {
            $items[]         = [
                'name'                => '«6 из 45»',
                'logo_url'            => '/img/lotto_2.png',
                'countdown_timestamp' => $l6x45->draw_at,
                'superprize'          => $l6x45->superprizeReadable,
                'game_url'            => '/lottery6x45/index',
            ];
            $jackpot_summ    = $l6x45->superprize;
            $jackpot_draw_at = $l6x45->draw_at;
        }

        $items[] = [
            'name'                => '«3 из 9»',
            'logo_url'            => '/img/lotto_5.png',
            'countdown_timestamp' => 'Мгновенная игра!',
            'superprize'          => 'Испытай удачу здесь и сейчас!',
            'game_url'            => '/instant3x9/index',
        ];
        $items[] = [
            'name'       => 'Случайная ставка',
            'logo_url'   => '/img/topical-4.png',
//                'countdown_timestamp' => $l6x45->draw_at,
            'superprize' => 'Купи билет на&nbsp;случайную игру и&nbsp;выиграй!',
            'game_url'   => '/site/random-game',
        ];
        $items[] = [
            'name'                => '«1 из 3»',
            'logo_url'            => '/img/lotto_4.png',
            'countdown_timestamp' => 'Мгновенная игра!',
            'superprize'          => 'Испытай удачу здесь и сейчас!',
            'game_url'            => '/instant1x3/index',
        ];
        if ($l6x45 instanceof L6x45) {
            $items[] = [
                'name'                => '«6 из 45»',
                'logo_url'            => '/img/lotto_2.png',
                'countdown_timestamp' => $l6x45->draw_at,
                'superprize'          => $l6x45->superprizeReadable,
                'game_url'            => '/lottery6x45/index',
            ];
        }
        $items[] = [
            'name'                => '«3 из 9»',
            'logo_url'            => '/img/lotto_5.png',
            'countdown_timestamp' => 'Мгновенная игра!',
            'superprize'          => 'Испытай удачу здесь и сейчас!',
            'game_url'            => '/instant3x9/index',
        ];
        $items[] = [
            'name'       => 'Случайная ставка',
            'logo_url'   => '/img/topical-4.png',
//                'countdown_timestamp' => $l6x45->draw_at,
            'superprize' => 'Купи билет на&nbsp;случайную игру и&nbsp;выиграй!',
            'game_url'   => '/site/random-game',
        ];
        return $this->render('index', [
                    'items'   => $items,
//                    'l6x45' => $l6x45,
                    'jackpot' => ['summ' => $jackpot_summ, 'draw_at' => $jackpot_draw_at],
        ]);
    }

    public function actionLotteries()
    {
        return $this->render('lotteries', [
                    'lotteries' => [
                        'l6x45'     => L6x45::findCurrent(),
                        'l6x45prev' => L6x45::findLastFinished(),
                    ]
        ]);
    }

//    public function actionCheckTicket()
//    {
//        $model             = new LCheckTicketForm();
////        $modelsCheckTicket = Model::createMultiple(LCheckTicketForm::classname());
////            if(count($modelsCheckTicket)){
////                print_r($modelsCheckTicket);
////            }
//        $modelsCheckTicket = [];
//        $post              = Yii::$app->request->post();
//        if ($model->load(Yii::$app->request->post())) {
//            foreach (Yii::$app->request->post('LCheckTicketForm') as $key => $modelData) {
//                if (is_int($key)) {
//                    $modelsCheckTicket[] = clone $model;
//                }
//            }
//            if (Model::loadMultiple($modelsCheckTicket, Yii::$app->request->post()) && Model::validateMultiple($modelsCheckTicket)) {
////Вывести
//            }
//            //FIXME renderPartial for multiple tickets
//            if ($model->validate()) {
//                return $this->redirect(['/lottery' . $model->lottery_type . '/view_ticket', 'id' => $model->ticket_id]);
//            }
//        }
//        //Fields collection
////        https://habrahabr.ru/post/239147/
////        http://www.yiiframework.com/wiki/666/handling-tabular-data-loading-and-validation-in-yii-2/
////        https://github.com/wbraganca/yii2-dynamicform
//        //http://formvalidation.io/examples/adding-dynamic-field/
////        https://github.com/yiisoft/yii2/issues/9535
////        http://yiiframework.ru/forum/viewtopic.php?t=21698
//        return $this->render('check_ticket_form', [
//                    'model' => $model,
//        ]);
//    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->contact(Yii::$app->params['contactEmail'])) {
                Yii::$app->session->addFlash('info', Yii::t('frontend', 'Thank you for contacting us. We will respond to you as soon as possible.')
                );
                return $this->refresh();
            }
            else {
                Yii::$app->session->addFlash('warning', Yii::t('frontend', 'There was an error sending email.')
                );
            }
        }

        return $this->render('contact', [
                    'model' => $model
        ]);
    }

    public function actionRandomGame()
    {
        $urls = [
            Url::to('/instant1x3/index'),
            Url::to('/lottery6x45/index'),
            Url::to('/instant3x9/index'),
        ];
        $key  = array_rand($urls);
        return $this->redirect($urls[$key]);
    }

    public function actionInviteFriend()
    {
//        echo Url::to('',1);
//die(Yii::$app->request->referrer);

        $model = new InviteFriendForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                //Редирект на страницу отправки, если приглашение отправлено
                $prevUrl     = Url::previous('referrals');
                $normPrevUrl = Url::to($prevUrl, true);
                if (!empty($prevUrl) && $normPrevUrl == Yii::$app->request->referrer) {
                    Yii::$app->session->addFlash('info', "Приглашение вашему другу отправлено на адрес {$model->email}.");
                    return $this->redirect($prevUrl);
                }
                return $this->render('invite_friend_success', ['email' => $model->email]);
            }
            else {
                Yii::$app->session->addFlash('warning', 'Не удалось отправить приглашение. Пожалуйста, повторите попытку позже.');
            }
        }

        $page        = Page::find()->where(['slug' => 'invitefriend', 'status' => Page::STATUS_PUBLISHED])->one();
        $inviteText  = is_null($page) ? '' : $page->body;
        $model->name = Yii::$app->user->isGuest ? null : Yii::$app->user->identity->getPublicIdentity();


        return $this->render('invite_friend', [
                    'inviteText' => $inviteText,
                    'model'      => $model,
                    'isGuest'    => Yii::$app->user->isGuest,
        ]);
    }

    public function actionPartner()
    {
        return $this->render('partner');
    }

    public function actionHowitworks()
    {
        return $this->render('howitworks');
    }

}
