<?php
/**
 * @author Alexey Samoylov <alexey.samoylov@gmail.com>
 */

namespace common\components\payment\fchange\actions;

use yii\base\Action;
use yii\base\InvalidConfigException;
use common\components\payment\fchange\Api;

class ResultAction extends Action
{
    public $componentName;
    public $redirectUrl;

    public $silent = false;

    /** @var Api */
    private $api;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->api = \Yii::$app->get($this->componentName);
        if (!$this->api instanceof Api)
            throw new InvalidConfigException('Invalid F-Change component configuration');

        parent::init();
    }

    public function run()
    {
        try {
            $this->api->processResult(\Yii::$app->request->post());
        } catch (\Exception $e) {
            if (!$this->silent)
                throw $e;
        }

        if (isset($this->redirectUrl))
            return \Yii::$app->response->redirect($this->redirectUrl);
    }
}