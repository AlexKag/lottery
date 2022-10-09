<?php

/**
 * Yii2 Shortcuts
 * @author Eugene Terentev <eugene@terentev.net>
 * -----
 * This file is just an example and a place where you can add your own shortcuts,
 * it doesn't pretend to be a full list of available possibilities
 * -----
 */

/**
 * @return int|string
 */
function getMyId()
{
    return Yii::$app->user->getId();
}

/**
 * @return string
 */
function getMyRefUrl()
{
    return Yii::$app->user->isGuest ? Url::to('/user/sign-in/signup', true) : Yii::$app->user->identity->refUrl;
}

/**
 * @param string $view
 * @param array $params
 * @return string
 */
function render($view, $params = [])
{
    return Yii::$app->controller->render($view, $params);
}

/**
 * @param $url
 * @param int $statusCode
 * @return \yii\web\Response
 */
function redirect($url, $statusCode = 302)
{
    return Yii::$app->controller->redirect($url, $statusCode);
}

/**
 * @param $form \yii\widgets\ActiveForm
 * @param $model
 * @param $attribute
 * @param array $inputOptions
 * @param array $fieldOptions
 * @return string
 */
function activeTextinput($form, $model, $attribute, $inputOptions = [], $fieldOptions = [])
{
    return $form->field($model, $attribute, $fieldOptions)->textInput($inputOptions);
}

/**
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function env($key, $default = false)
{

    $value = getenv($key);

    if ($value === false) {
        return $default;
    }

    switch (strtolower($value)) {
        case 'true':
        case '(true)':
            return true;

        case 'false':
        case '(false)':
            return false;
    }

    return $value;
}

function extendedBetWins($bet_cnt, $win_cnt, $max_cnt, $level = 1)
{
    $comb = function($n, $k) {
        return gmp_fact($n) / gmp_fact($k) / gmp_fact($n - $k);
    };

//    $subComb = function($bet_cnt, $win_cnt, $max_cnt, $level) {
//        $k1 = $win_cnt - $level + 1;
//        $m  = $bet_cnt - $k1;
//        $k2 = $max_cnt - $k1;
//        return gmp_fact($a);
//    };
    switch ($level) {
        case 1:
            $n  = $bet_cnt - $win_cnt;
            $k  = $max_cnt - $win_cnt;
            return $comb($n, $k);
            break;
        case 2:
        case 3:
        case 4:
        case 5:
        case 6:
        case 7:
            $n1 = $win_cnt;
            $k1 = $win_cnt - $level + 1;
            $n2 = $bet_cnt - $max_cnt;
            $k2 = $level;
            return $comb($n1, $k1) * $comb($n2, $k2);
            break;
        default :
            return 0;
    }
}

/*
 * Радлеление числа на группы разрядов
 */

function numberExplode($val)
{
    $tmp                        = explode(',', number_format($val));
    $res['ones']                = array_pop($tmp);
    $res['thousands']           = array_pop($tmp);
    $res['millions']            = array_pop($tmp);
//    $res['strlen']['ones']      = strlen($res['ones']);
//    $res['strlen']['thousands'] = strlen($res['thousands']);
//    $res['strlen']['millions']  = strlen($res['millions']);
//    $digits                     = function($val) {
//        $cnt = strlen($val);
//        switch ($val) {
//            case 1:
//                return 'one';
//                break;
//            case 2:
//                return 'two';
//                break;
//            case 3:
//                return 'three';
//                break;
//            default:
//                return null;
//        }
//    };
////    $i = count($tmp) - 1;
//    $i = 0;
//    $res['millions'] = '';
//    if (count($tmp) > 2) {
//        $res['millions'] = $tmp[$i++];
//    }
//    $res['thousands'] = '';
//    if (count($tmp) > 1) {
//        $res['thousands'] = $tmp[$i++];
//    }
//    $res['ones'] = isset($tmp[$i]) ? $tmp[$i++] : '';
    return $res;
}
