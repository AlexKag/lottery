<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use himiklab\yii2\recaptcha\ReCaptchaValidator;
//use Swift_Plugins_Loggers_ArrayLogger;
//use Swift_Plugins_LoggerPlugin;

/**
 * ContactForm is the model behind the contact form.
 */
class ContactForm extends Model
{

    public $name;
    public $email;
    public $subject;
    public $body;
    public $reCaptcha;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['name', 'email', 'subject', 'body'], 'required'],
            // We need to sanitize them
            [['name', 'subject', 'body'], 'filter', 'filter' => 'strip_tags'],
            // email has to be a valid email address
            ['email', 'email'],
            // verifyCode needs to be entered correctly
//            ['verifyCode', 'captcha'],
//            [['reCaptcha'], ReCaptchaValidator::className(), 'secret' => Yii::$app->params['reCaptchaSecretKey']],
            [['reCaptcha'], ReCaptchaValidator::className()],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'name'    => Yii::t('frontend', 'Имя'),
            'email'   => Yii::t('frontend', 'E-mail'),
            'subject' => Yii::t('frontend', 'Тема'),
            'body'    => Yii::t('frontend', 'Сообщение'),
        ];
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     * @param  string  $email the target email address
     * @return boolean whether the model passes validation
     */
    public function contact($email)
    {
        if ($this->validate()) {
            $body    = $this->body . "\r\n---\r\n" . implode("\r\n", ['Имя: ' . $this->name, 'E-mail: ' . $this->email, !Yii::$app->request->isConsoleRequest && !empty(Yii::$app->request->userIP) ? 'IP: ' . Yii::$app->request->userIP : '']);
            return Yii::$app->mailer->compose()
                            ->setTo($email)
                            ->setFrom(Yii::$app->params['contactEmail'])
                            ->setReplyTo([$this->email => $this->name])
                            ->setSubject($this->subject)
                            ->setTextBody($body)
                            ->send();
/*            $message = Yii::$app->mailer->compose()
                    ->setTo($email)
                    ->setFrom(Yii::$app->params['robotEmail'])
                    ->setReplyTo([$this->email => $this->name])
                    ->setSubject($this->subject)
                    ->setTextBody($body);
            $logger  = new Swift_Plugins_Loggers_ArrayLogger();
            Yii::$app->mailer->getSwiftMailer()->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));
            if (!$message->send()) {
                echo $logger->dump();
            }
            else {
                echo 'success';
                echo $logger->dump();
            }
            die;*/
        }
        else {
            return false;
        }
    }

}
