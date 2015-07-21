<?php

namespace wh\queue\console\controllers;

use Yii;
use yii\console\Controller;

/**
 * Queue Process Command
 *
 * Class QueueController
 * @package wh\queue\console\controllers
 */
class QueueController extends Controller
{
    /**
     * Process a job
     *
     * @param string $queueName
     * @param string $queueObjectName
     * @throws \Exception
     */
    public function actionWork($queueName = null, $queueObjectName = 'queue')
    {
        $this->process($queueName, $queueObjectName);
    }

    /**
     * Continuously process jobs
     *
     * @param string $queueName
     * @param string $queueObjectName
     * @throws \Exception
     */
    public function actionListen($queueName = null, $queueObjectName = 'queue')
    {
        while (true) {
            $this->process($queueName, $queueObjectName);
        }
    }

    protected function process($queueName, $queueObjectName)
    {
        $job = Yii::$app->{$queueObjectName}->pop($queueName);

        if ($job) {
            try {
                $job->run();
            } catch (\Exception $e) {
                Yii::error($e->getMessage(), __METHOD__);
            }
        }
    }
} 
