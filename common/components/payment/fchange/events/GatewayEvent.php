<?php

namespace common\components\payment\fchange\events;

use yii\base\Event;
use yii\db\ActiveRecord;

class GatewayEvent extends Event
{

    const EVENT_PAYMENT_REQUEST = 'eventPaymentRequest';
    const EVENT_PAYMENT_SUCCESS = 'eventPaymentSuccess';

    /** @var ActiveRecord|null */
    public $invoice;

    /** @var array */
    public $gatewayData = [];

}
