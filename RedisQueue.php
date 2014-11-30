<?php
/**
 * Created by PhpStorm.
 * User: yiistudio
 * Date: 11/29/14
 * Time: 5:10 PM
 */

namespace wh\queue;

use yii\redis\Connection;
use Yii;

class RedisQueue extends Queue
{
    public $redis = 'redis';

    //默认队列名
    public $default = 'default';

    public function init()
    {
        parent::init();
        if (is_string($this->redis)) {
            $this->redis = Yii::$app->get($this->redis);
        } elseif (is_array($this->redis)) {
            if (!isset($this->redis['class'])) {
                $this->redis['class'] = Connection::className();
            }
            $this->redis = Yii::createObject($this->redis);
        }
        if (!$this->redis instanceof Connection) {
            throw new InvalidConfigException("Queue::redis must be either a Redis connection instance or the application component ID of a Redis connection.");
        }
    }

    /**
     * 写入数据到队列
     *
     * @param $payload
     * @param null $queue
     * @param array $options
     * @return mixed
     */
    protected function pushInternal($payload, $queue = null, array $options = [])
    {
        $this->redis->rpush($this->getQueue($queue), $payload);
        $payload = json_decode($payload, true);
        return $payload['id'];
    }

    /**
     * 获得队列名
     * @param $queue
     * @return string
     */
    protected function getQueueInternal($queue = null)
    {
        return ($queue ?: $this->default);
    }


    /**
     * 出队列
     * @param null $queue
     * @return RedisJob
     */
    public function popInternal($queue = null)
    {
        $payload = $this->redis->lpop($this->getQueue($queue));
        if (!is_null($payload)) {
            //$this->redis->zadd($queue.':reserved', $this->getTime() + 60, $job);
            return new Job($this, $payload, $queue);
        }
    }


} 