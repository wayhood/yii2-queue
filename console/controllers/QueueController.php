<?php
/**
 * Created by PhpStorm.
 * User: yiistudio
 * Date: 11/30/14
 * Time: 8:46 PM
 */

namespace wh\queue\console\controllers;

use Yii;

/**
 * Queue Process Command
 *
 * Class QueueController
 * @package wh\queue\console\controllers
 */
class QueueController extends \yii\console\Controller
{


    /**
     * process a job
     *
     * @param string $queueObjectName
     * @param string $queueName
     * @throws \Exception
     */
    public function actionWork($queueObjectName = 'queue', $queueName = '')
    {
        $this->process($queueObjectName, $queueName);
    }

    public function actionListen($queueObjectName = 'queue', $queueName = '')
    {
        while(true) {
            $this->process($queueObjectName, $queueName = '');
        }
    }

    protected function process($queueObjectName = 'queue', $queueName = '')
    {
        $queue = Yii::$app->{$queueObjectName};

        $queueName = $queueName == '' ? null : $queueName;

        $job = $queue->pop($queueName);

        if (!is_null($job)) {
            try {
                $job->run();

                //删除失败的 TODO
                /*
                if ($job->autoDelete()) {

                }*/
            } catch (\Exception $e) {
                //
                throw $e;
            }
        }
    }

} 
