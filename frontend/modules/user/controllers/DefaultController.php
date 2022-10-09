<?php

namespace frontend\modules\user\controllers;

use common\base\MultiModel;
use frontend\modules\user\models\AccountForm;
use frontend\modules\user\models\PasswordChangeForm;
use frontend\modules\user\models\ProfileChangeForm;
use Intervention\Image\ImageManagerStatic;
use trntv\filekit\actions\DeleteAction;
use trntv\filekit\actions\UploadAction;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\helpers\Url;
//use common\components\lottery\models\BaseTicketForDataProvider;
use common\components\lottery\models\L6x45Ticket;
use common\components\lottery\models\L1x3Ticket;
use common\components\lottery\models\L3x9Ticket;
use common\components\slots\models\SlotsFiveTicket;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use common\models\User;
use common\models\UserReferralsStat;
use frontend\modules\user\models\NotifyEmailForm;
use backend\models\AccountLog;
use backend\models\search\AccountLogSearch;
use frontend\modules\user\models\StatSearchForm;
use frontend\modules\user\models\GAForm;
use frontend\modules\user\models\EmailForm;
use common\commands\SendEmailCommand;
use common\models\UserAccountStat;

class DefaultController extends Controller
{

    public $layout                         = 'default';
    public $defaultRoute                   = 'profile';
    protected $payoutControlByUserStatuses = [
        UserAccountStat::STATUS_POSTPONED,
        UserAccountStat::STATUS_ERRORED,
        UserAccountStat::STATUS_REQUESTED,
    ];

    /**
     * @return array
     */
    public function actions()
    {
        return [
            'avatar-upload' => [
                'class' => UploadAction::className(),
                'deleteRoute' => 'avatar-delete',
                'on afterSave' => function ($event) {
                    /* @var $file \League\Flysystem\File */
                    $file = $event->file;
                    $img  = ImageManagerStatic::make($file->read())->fit(215, 215);
                    $file->put($img->encode());
                }
            ],
            'avatar-delete' => [
                'class' => DeleteAction::className()
            ]
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@']
                    ]
                ]
            ]
        ];
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionIndex()
    {
        return $this->redirect('profile');
        $accountForm = new AccountForm();
        $accountForm->setUser(Yii::$app->user->identity);

        $model = new MultiModel([
            'models' => [
                'account' => $accountForm,
                'profile' => Yii::$app->user->identity->userProfile
            ]
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $locale = $model->getModel('profile')->locale;
            Yii::$app->session->setFlash('forceUpdateLocale');
            Yii::$app->session->setFlash('alert', [
                'options' => ['class' => 'alert-success'],
                'body' => Yii::t('frontend', 'Your account has been successfully saved', [], $locale)
            ]);
            return $this->refresh();
        }
        return $this->render('index', ['model' => $model]);
    }

    public function actionProfile()
    {
        $model = new PasswordChangeForm();
        $model->setUser(Yii::$app->user->identity);

        $modelProfile = new ProfileChangeForm();
        $modelProfile->setUser(Yii::$app->user->identity);

        $res1 = $model->load(Yii::$app->request->post()) && $model->save();
        $res2 = $modelProfile->load(Yii::$app->request->post()) && $modelProfile->save();
        if ($res1 || $res2) {
//            return $this->redirect('/');
            return $this->refresh();
        }
        return $this->render('profile', ['model' => $model, 'modelProfile' => $modelProfile]);
    }

    public function actionVerify()
    {
        return $this->render('verify');
    }

    public function actionNotify()
    {
        $model = new NotifyEmailForm();
//        if ($model->load(Yii::$app->request->post()) && $model->verify()) {
//            //TODO Сохранить в список рассылки
//        }
//        $model->email = Yii::$app->user->identity->email;
        return $this->render('notify', ['model' => $model, 'email' => Yii::$app->user->identity->email]);
    }

    public function actionSecurity()
    {
        $model = new GAForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $user = Yii::$app->user->identity;
                if ($model->enable) {
                    $user->ga_token = $model->token;
                    $message        = Yii::t('frontend', 'Двухэтапная аутентификация Google Authenticator включена.');
                }
                if (!$model->enable) {
                    $user->ga_token = null;
                    $message        = Yii::t('frontend', 'Двухэтапная аутентификация Google Authenticator отключена.');
                }
                if ($user->save()) {
                    Yii::$app->session->addFlash('info', $message);
                } else {
                    Yii::$app->session->addFlash('warning', 'Параметры не сохранены.');
                    Yii::error('Параметры безопасности пользователя не сохранены.');
                }
            } else {
                Yii::$app->session->addFlash('warning', 'Параметры не сохранены.');
            }
        }
        $modelEmail = new EmailForm();
        if ($modelEmail->load(Yii::$app->request->post())) {
            if ($modelEmail->validate()) {
                $user = Yii::$app->user->identity;
                if ($modelEmail->enable) {
                    $user->is_ip_check = $modelEmail->enable;
                    $message           = Yii::t('frontend', 'Двухэтапная аутентификация E-mail включена.');
                }
                if (!$modelEmail->enable) {
                    $user->is_ip_check = $modelEmail->enable;
                    $message           = Yii::t('frontend', 'Двухэтапная аутентификация E-mail отключена.');
                }
                if ($user->save()) {
                    Yii::$app->session->addFlash('info', $message);
                } else {
                    Yii::$app->session->addFlash('warning', 'Параметры не сохранены.');
                    Yii::error('Параметры безопасности пользователя не сохранены.');
                }
            } else {
                Yii::$app->session->addFlash('warning', 'Параметры не сохранены.');
            }
        }

        $modelEmail = new EmailForm();
        $model      = new GAForm();
        return $this->render('security', [
                    'model' => $model,
                    'modelEmail' => $modelEmail,
        ]);
    }

    public function actionSendAuthCode()
    {
        $user                         = Yii::$app->user->identity;
        Yii::$app->random->attributes = ['min' => 1e3, 'max' => 1e4];
        $user->email_auth_code        = Yii::$app->random->number;
        $user->save();
        $type                         = $user->is_ip_check ? 'Отключение' : 'Подключение';
        Yii::$app->commandBus->handle(new SendEmailCommand([
            'subject' => Yii::t('frontend', Yii::$app->name . " $type двухэтапной аутентификации e-mail."),
            'view' => '2fa_mail_enable',
            'to' => $user->email,
            'params' => ['code' => $user->email_auth_code, 'enable' => !$user->is_ip_check]
        ]));
        return 1;
    }

    public function actionAutopay()
    {
        return $this->render('autopay');
    }

//    public function actionAdvertise()
//    {
//        $model = Yii::$app->user->identity;
//        return $this->render('advertise', [
//                    'model' => $model,
//        ]);
//    }

    public function actionStat()
    {
        $searchModel = new StatSearchForm();
        if (!($searchModel->load(Yii::$app->request->post()) && $searchModel->validate())) {
            $searchModel = new StatSearchForm();
        }

        $user = Yii::$app->user->identity;

        $q1 = "SELECT a.id, a.lottery_id, a.created_at, a.bet, a.win_cnt, a.paid, a.paid_out, b.draw, b.draw_at, '6 out of 45' as 'name', '6x45' as 'type' FROM `lottery_l6x45_ticket` a INNER JOIN `lottery_l6x45` b ON a.lottery_id = b.id where a.user_id = :user_id";
//        $command1 = Yii::$app->db->createCommand($q1);
        $q2 = "SELECT c.id, c.lottery_id, c.created_at, c.bet, c.win_cnt, c.paid, c.paid_out, c.win_combination as 'draw', c.created_at as 'draw_at', '1 out of 3' as 'name', '1x3' as 'type' FROM `lottery_l1x3_ticket` c where c.user_id = :user_id";
        $q3 = "SELECT d.id, d.lottery_id, d.created_at, d.bet, d.win_cnt, d.paid, d.paid_out, d.win_combination as 'draw', d.created_at as 'draw_at', '3 out of 9' as 'name', '3x9' as 'type' FROM `lottery_l3x9_ticket` d where d.user_id = :user_id";
        $q4 = "SELECT e.id, e.lottery_id, e.created_at, e.bet, e.win_cnt, e.paid, e.paid_out, e.win_combination as 'draw', e.created_at as 'draw_at', 'slots five' as 'name', 'slots_five' as 'type' FROM `lottery_slots_five_ticket` e where e.user_id = :user_id";
//        $command2 = Yii::$app->db->createCommand($q2);
//        $command1->union($command2);
        $q  = [$q1, $q2, $q3, $q4];

        $cnt = L6x45Ticket::find()->where(['user_id' => $user->id])->count('id');
        $cnt += L1x3Ticket::find()->where(['user_id' => $user->id])->count('id');
        $cnt += L3x9Ticket::find()->where(['user_id' => $user->id])->count('id');
        $cnt += SlotsFiveTicket::find()->where(['user_id' => $user->id])->count('id');

        $sql = implode(' UNION ', $q) . ' ORDER BY draw_at desc, created_at desc';

        //Фильтрация
        switch ($searchModel->category) {
            case 'win':
                $criteria = 'win_cnt > 0';
//                $criteria = ['>', 'win_cnt', 0];
                break;
            case 'loose':
                $criteria = 'win_cnt IS NULL AND draw IS NOT NULL';
//                $criteria = ['win_cnt' => 0];
                break;
            case 'notdrawed':
                $criteria = 'draw IS NULL';
//                $criteria = ['draw' => null];
                break;
            default:
                $criteria = null;
                break;
        }

        if (!is_null($criteria)) {
            $sql = "SELECT * FROM ($sql) as resTable WHERE $criteria";
        }

        $dataProvider = new SqlDataProvider([
            'sql' => $sql,
            'params' => [':user_id' => $user->id],
            'totalCount' => $cnt,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
//                'defaultOrder' => [
//                    'draw_at'=>SORT_DESC,
//                    'created_at' => SORT_DESC
//                    ]
//                'attributes' => [
//                    'title',
//                    'view_count',
//                    'created_at',
//                ],
            ],
        ]);
//        $models = $dataProvider->getModels();
//        print_r($models);die;
        return $this->render('stat', [
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel,
        ]);
    }

//    public function actionTickets()
//    {
//        $user = Yii::$app->user->identity;
//        BaseTicketForDataProvider::$type = L6x45Ticket::ID;
//        $q1   = BaseTicketForDataProvider::find()->where(['user_id' => $user->id])->joinWith('lottery', true, 'INNER JOIN')->with('lottery')->select([L6x45Ticket::tableName().'.*', 'LOWER(\'6x45\') as type']);
//        BaseTicketForDataProvider::$type = L1x3Ticket::ID;
//        $q2   = BaseTicketForDataProvider::find()->where(['user_id' => $user->id])->joinWith('lottery', true, 'INNER JOIN')->with('lottery')->select([BaseTicketForDataProvider::tableName().'.*', 'LOWER(\'1x3\') as type']);
//        $q1   = L6x45Ticket::find()->where(['user_id' => $user->id])->joinWith('lottery', true, 'INNER JOIN')->with('lottery')->select([L6x45Ticket::tableName() . '.*', 'LOWER(\'type_6x45\') as type']);
//        $q2   = L1x3Ticket::find()->where(['user_id' => $user->id])->select('*, created_at as draw_at');
//        $q2   = L1x3Ticket::find()->where(['user_id' => $user->id])->joinWith('lottery', true, 'INNER JOIN')->with('lottery')->select([L1x3Ticket::tableName().'.*', 'LOWER(\'type_1x3\') as type']);
//        $q2 = \common\components\lottery\models\L6x45Ticket::find()->where(['user_id' => $user->id]);
//        SELECT *, LOWER('type_6x45') as type FROM `lottery_l6x45_ticket` UNION ALL SELECT *, LOWER('type_1x3') as type FROM `lottery_l1x3_ticket`
//        Модифицировать модель добавить поле type
//        $q1->union($q2); //http://www.yiiframework.com/doc-2.0/guide-db-query-builder.html#
//        $q1->orderBy('draw_at desc, created_at desc');
//
//        $dataProvider = new ActiveDataProvider([ 'query' => $q1, 'pagination' => [ 'pageSize' => 10], 'sort' => false]);
//        return $this->render('tickets', [
//                    'dataProvider' => $dataProvider,
//        ]);
//    }

    public function actionReferrals()
    {

//        print_r(Yii::$app->request->post());
        $user = Yii::$app->user->identity;
        $refs = [
            1 => User::find()->refCount($user->id, 1)->queryScalar(),
            2 => User::find()->refCount($user->id, 2)->queryScalar(),
            3 => User::find()->refCount($user->id, 3)->queryScalar(),
        ];

        $q1 = UserReferralsStat::find()->where(['user_id' => $user->id])->select(
                        'sum(paid_in) AS paid_in,' .
                        'sum(paid_ref) AS paid_ref,' .
                        'EXTRACT(YEAR_MONTH FROM created_at) AS dt,' .
                        'created_at'
                )->groupBy('dt')
                ->orderBy('EXTRACT(YEAR FROM created_at) DESC, EXTRACT(MONTH FROM created_at) DESC');

        $dataProvider = new ActiveDataProvider([ 'query' => $q1, 'pagination' => [ 'pageSize' => 10], 'sort' => false]);

        return $this->render('referrals', [
                    'model' => $user,
                    'refs' => $refs,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionFinance()
    {
        $query               = UserAccountStat::find()->where([
                    'user_id' => getMyId(),
                    'status' => $this->payoutControlByUserStatuses,
                    'direction' => UserAccountStat::DIRECTION_OUT,
                ])
                ->orderBy('created_at DESC');
        $dataProviderAccount = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [ 'pageSize' => 10,],
            'sort' => false,
//            'sort' => [
//                'defaultOrder' => [
//                    'created_at' => SORT_DESC,
//                ]
//            ],
        ]);

        $searchModel  = new AccountLogSearch();
        $searchModel->load(Yii::$app->request->post());
        $dataProvider = $searchModel->search(['AccountLogSearch' => ['prefix' => getMyId()]]);

        Url::remember(Url::current(['#' => 'postponed']));
        return $this->render('finance', [
                    'model' => Yii::$app->user->identity,
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel,
                    'dataProviderAccount' => $dataProviderAccount,
        ]);
    }

    public function actionCancelPayout($id)
    {
        $stat = UserAccountStat::findOne(['id' => (int) $id, 'user_id' => getMyId()]);
        if (!empty($stat) && in_array($stat->status, $this->payoutControlByUserStatuses)) {
            $stat->cancel(UserAccountStat::STATUS_CANCELLED_BY_USER);
        }
        return $this->redirect(Url::previous());
    }

    public function actionConfirmPayout($id)
    {
        $stat = UserAccountStat::findOne(['id' => (int) $id, 'user_id' => getMyId()]);
        if (!empty($stat) && in_array($stat->status, $this->payoutControlByUserStatuses)) {
            $stat->confirm();
        }
        return $this->redirect(Url::previous());
    }

    /**
     * use yii\data\SqlDataProvider;$count = Yii::$app->db->createCommand('    SELECT COUNT(*) FROM post WHERE status=:status', [':status' => 1])->queryScalar();$provider = new SqlDataProvider([    'sql' => 'SELECT * FROM post WHERE status=:status',    'params' => [':status' => 1],    'totalCount' => $count,    'pagination' => [        'pageSize' => 10,    ],    'sort' => [        'attributes' => [            'title',            'view_count',            'created_at',        ],    ],]);// returns an array of data rows$models = $provider->getModels();
     */
}
