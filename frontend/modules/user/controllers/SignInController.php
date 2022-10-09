<?php

namespace frontend\modules\user\controllers;

use common\commands\SendEmailCommand;
use common\models\User;
use common\models\UserToken;
use frontend\modules\user\models\LoginForm;
use frontend\modules\user\models\PasswordResetRequestForm;
use frontend\modules\user\models\ResetPasswordForm;
use frontend\modules\user\models\SignupForm;
use Yii;
use yii\base\Exception;
use yii\base\InvalidParamException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
//use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;
use common\filters\SetReferrer;
use frontend\modules\user\models\GAForm;
use frontend\modules\user\models\TwoStepAuthStep2Form;
use cheatsheet\Time;

/**
 * Class SignInController
 * @package frontend\modules\user\controllers
 * @author Eugene Terentev <eugene@terentev.net>
 */
class SignInController extends \yii\web\Controller
{

    public $layout = '@app/views/layouts/container.php';

    /**
     * @return array
     */
    public function actions()
    {
        return [
            'oauth' => [
                'class'           => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'successOAuthCallback']
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
                        'actions' => [
                            'signup', 'login', 'request-password-reset', 'reset-password', 'oauth', 'activation'
                        ],
                        'allow'   => true,
                        'roles'   => ['?']
                    ],
                    [
                        'actions'      => [
                            'signup', 'login', 'request-password-reset', 'reset-password', 'oauth', 'activation'
                        ],
                        'allow'        => false,
                        'roles'        => ['@'],
                        'denyCallback' => function () {
                    return Yii::$app->controller->redirect(['/user/default/index']);
                }
                    ],
                    [
                        'actions' => ['logout'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ]
                ]
            ],
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post']
                ]
            ]
        ];
    }

    /**
     * @return array|string|Response
     */
    public function actionLogin()
    {
        $model    = new LoginForm();
//        $model2fa = new GAForm();
        $model2fa = new TwoStepAuthStep2Form();
        if (Yii::$app->request->isAjax) {
            $model->load($_POST);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            //Второй этап при двухэтапной аутентификации Google Authenticator. Вывод формы
                $user = Yii::$app->user->identity;
            if ($user instanceof User && ((bool) $user->is_ip_check && !$user->isIPKnown(Yii::$app->request->userIP)) || !empty($user->ga_token)) {
                Yii::$app->user->logout();

                if ((bool) $user->is_ip_check && !$user->isIPKnown(Yii::$app->request->userIP)) {
                    $token                        = UserToken::create(
                                    $user->id, UserToken::TYPE_2FA_MAIL, 5 * Time::SECONDS_IN_A_MINUTE
                    );
                    $model2fa->attributes         = [
                        'token' => $token->token,
                        'type'  => TwoStepAuthStep2Form::MAIL,
                    ];
                    Yii::$app->random->attributes = ['min' => 1e3, 'max' => 1e6];
                    $user->email_auth_code        = Yii::$app->random->number;
                    $user->save();
                    Yii::$app->commandBus->handle(new SendEmailCommand([
                        'subject' => Yii::t('frontend', Yii::$app->name . ' Вход в учетную запись'),
                        'view'    => '2fa_mail_login_step2',
                        'to'      => $user->email,
                        'params'  => ['code' => $user->email_auth_code]
                    ]));
                    $view                         = '_2fa_mail_login_step2';
                }

                if (!empty($user->ga_token)) {
                    $token                = UserToken::create(
                                    $user->id, UserToken::TYPE_2FA_GA, 5 * Time::SECONDS_IN_A_MINUTE
                    );
                    $model2fa->attributes = [
                        'token' => $token->token,
                        'type'  => TwoStepAuthStep2Form::GA,
                    ];
                    $view                 = '_2fa_ga_login_step2';
                }


                return $this->render($view, [
                            'model' => $model2fa
                ]);
            }
//            return $this->goBack();
            //Стандартный вход
            return $this->redirect('/site/lotteries');
        }

        //Авторизация 2 этап Google Authenticator. Проверка кода
        if ($model2fa->load(Yii::$app->request->post()) && $model2fa->validate()) {
            $loginToken = $model2fa->token;
            switch ($model2fa->type) {
                case UserToken::TYPE_2FA_GA:
                    $user                   = $this->_loginByToken($loginToken, UserToken::TYPE_2FA_GA, 'Данные авторизации просрочены или недействительны. Пожалуйста, повторите процедуру входа на сайт.');
                    $model2faGA             = new GAForm;
                    $model2faGA->attributes = ['token' => $user->ga_token, 'enable' => 1, 'code' => $model2fa->code];
                    if ($model2faGA->validate()) {
                        $user->touchIP();
                        return $this->redirect('/site/lotteries');
                    }
                    break;
                case UserToken::TYPE_2FA_MAIL:
                    $user = $this->_loginByToken($loginToken, UserToken::TYPE_2FA_MAIL, 'Данные авторизации просрочены или недействительны. Пожалуйста, повторите процедуру входа на сайт.');
                    if ($user->email_auth_code == $model2fa->code) {
                        $user->touchIP();
                        return $this->redirect('/site/lotteries');
                    }
                    break;

                default:
                    break;
            }
            Yii::$app->user->logout();
            Yii::$app->getSession()->addFlash('warning', Yii::t(
                            'frontend', 'Неверный код двухэтапной аутентификации.'
            ));
        }
        return $this->render('login', [
                    'model' => $model
        ]);
    }

    /**
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * @return string|Response
     */
    public function actionSignup()
    {
        $model = new SignupForm();

//        Ajax validation
//        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
//            $res = ActiveForm::validate($model);
//            if (count($res)) {
//                Yii::$app->response->format = Response::FORMAT_JSON;
//                return $res;
//            }
//        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = $model->signup();
            if ($user) {
                if ($model->shouldBeActivated()) {
                    Yii::$app->session->addFlash('info', Yii::t(
                                    'frontend', 'Вам отправлено письмо. Подтвердите регистрацию на сайте.'
                    ));
                }
                else {
                    Yii::$app->getUser()->login($user);
                }
                if ($user->referrer_id == Yii::$app->request->cookies->getValue(SetReferrer::COOKIE_REF)) {
                    Yii::$app->response->cookies->remove(SetReferrer::COOKIE_REF);
                }
//                return $this->goHome();
                return $this->redirect('/site/lotteries');
            }
        }

//        $model->ref = Yii::$app->request->get('ref', null);
        $model->load(Yii::$app->request->get(), '');
        $cr = Yii::$app->request->cookies->getValue(SetReferrer::COOKIE_REF);
        if (empty($model->ref) && !empty($cr)) {
            $model->ref = $cr;
        }
        return $this->render('signup', [
                    'model' => $model
        ]);
    }

    protected function _loginByToken($token, $type, $errorMessage = 'Ссылка активации недействительна.')
    {
        $token = UserToken::find()
                ->byType($type)
                ->byToken($token)
                ->notExpired()
                ->one();

        if (!$token) {
            throw new BadRequestHttpException($errorMessage);
        }

        $user = $token->user;
        if (!$user) {
            throw new BadRequestHttpException($errorMessage);
        }
        $token->delete();
        Yii::$app->getUser()->login($user);
        return $user;
    }

    public function actionActivation($token)
    {
//        $token = UserToken::find()
//                ->byType(UserToken::TYPE_ACTIVATION)
//                ->byToken($token)
//                ->notExpired()
//                ->one();
//
//        if (!$token) {
//            throw new BadRequestHttpException('Ссылка активации недействительна.');
//        }
//
//        $user = $token->user;
//        if (!$user) {
//            throw new BadRequestHttpException('Ссылка активации недействительна.');
//        }
//        $user->updateAttributes([
//            'status' => User::STATUS_ACTIVE
//        ]);
//        $token->delete();
//        Yii::$app->getUser()->login($user);
        $user = $this->_loginByToken($token, UserToken::TYPE_ACTIVATION);
        Yii::$app->getSession()->addFlash('success', Yii::t('frontend', 'Ваша учетная запись активирована.'));
        $user->updateAttributes([
            'status' => User::STATUS_ACTIVE
        ]);

        return $this->redirect('/site/lotteries');
//        return $this->goHome();
    }

    /**
     * @return string|Response
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        $model->load(Yii::$app->request->post());
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->getSession()->addFlash('success', Yii::t('frontend', 'На ваш e-mail отправлено письмо.'));
                return $this->redirect('/site/lotteries');
            }
            else {
                Yii::$app->getSession()->addFlash('warning', Yii::t('frontend', 'К сожалению, для указанных данных восстановление пароля невозможно.'));
            }
        }

        return $this->render('requestPasswordResetToken', [
                    'model' => $model,
        ]);
    }

    /**
     * @param $token
     * @return string|Response
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->getSession()->addFlash('success', Yii::t('frontend', 'Пароль изменен.'));
            return $this->redirect('/site/lotteries');
        }

        return $this->render('resetPassword', [
                    'model' => $model,
        ]);
    }

}
