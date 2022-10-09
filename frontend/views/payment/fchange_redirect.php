<?php

echo common\components\payment\fchange\RedirectForm::widget([
    'api' => Yii::$app->get('fchange'),
    'payment_num' => $id,
    'amount' => $amount,
    'send_paysys_identificator' => $send_paysys_identificator,
    'payment_info' => sprintf('Пополнение счёта %s пользователя %s (%s)', Yii::$app->name, $public_id, $username),
//    'message' => 'Выполняется переадресация на систему оплаты Payeer...',
]);
