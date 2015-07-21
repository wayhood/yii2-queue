<?php

namespace wh\queue;

use Yii;
use yii\redis\Connection;

class RedisQueue extends Queue
{
    /**
     * @var string Default redis component name
     */
    public $redis = 'redis';

    /**
     * Class initialization logic
     *
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->redis = Yii::$app->get($this->redis);
        if (!$this->redis instanceof Connection) {
            throw new InvalidConfigException("Queue::redis must be either a Redis connection instance or the application component ID of a Redis connection.");
        }
    }

    protected function pushInternal($payload, $queue = null, array $options = [])
    {
        $this->redis->rpush($this->getQueue($queue), $payload);
        $payload = json_decode($payload, true);

        return $payload['id'];
    }


    public function popInternal($queue = null)
    {
        $payload = $this->redis->lpop($this->getQueue($queue));
        if ($payload) {
            //$this->redis->zadd($queue.':reserved', $this->getTime() + 60, $job);
            return new Job($this, $payload, $queue);
        }

        return null;
    }
} 
