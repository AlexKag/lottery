<?php

namespace common\filters;

use Yii;
use yii\web\Cookie;

class SetReferrer
{

    const PARAM_NAME = 'ref';
    const COOKIE_REF = 'ref_id';

    public static function run()
    {
        $params = Yii::$app->request->get();
        if (isset($params[self::PARAM_NAME])) {
            Yii::$app->response->cookies->add(new Cookie([
                'name'  => self::COOKIE_REF,
                'value' => $params[self::PARAM_NAME]
            ]));
        }
    }

}
