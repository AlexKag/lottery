<?php

echo \yiidreamteam\perfectmoney\RedirectForm::widget([
    'api' => Yii::$app->get('pm'),
    'invoiceId' => $id,
    'amount' => $amount,
    'description' => sprintf('Пополнение счёта %s пользователя %s (%s)', Yii::$app->name, $public_id, $username),
    'message' => 'Выполняется переадресация на систему оплаты Perfect Money...',
]);
