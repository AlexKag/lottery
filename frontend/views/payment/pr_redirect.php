<?php

echo \yiidreamteam\payeer\RedirectForm::widget([
    'api' => Yii::$app->get('payeer'),
    'invoiceId' => $id,
    'amount' => $amount,
    'description' => sprintf('Пополнение счёта %s пользователя %s (%s)', Yii::$app->name, $public_id, $username),
//    'message' => 'Выполняется переадресация на систему оплаты Payeer...',
]);
