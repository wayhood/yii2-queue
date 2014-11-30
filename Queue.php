<?php

namespace wh\queue;

use yii\base\Component;
use Jeremeamia\SuperClosure\SerializableClosure;
use yii\helpers\StringHelper;

abstract class Queue extends Component
{
    /**
     * @var string 队名前缀
     */
    public $queuePrefix;

    /**
     * @param $key
     * @return string
     */
    public function buildPrefix($name)
    {
        if (is_string($name)) {
            $name = ctype_alnum($name) && StringHelper::byteLength($name) <= 32 ? $name : md5($name);
        } else {
            $name = md5(json_encode($name));
        }

        return $this->queuePrefix . $name;
    }

    /**
     * 入队列
     * @param $job 执行任务的类或回调
     * @param string $data 数据
     * @param null $queue 队列名
     * @return mixed
     */
    public function push($job, $data = '', $queue = null)
    {
        return $this->pushInternal($this->createPayload($job, $data), $queue);
    }

    /**
     * 出队列
     * @param null $queue
     * @return mixed
     */
    public function pop($queue = null)
    {
        return $this->popInternal($queue);
    }

    /**
     * 创建消息体
     * @param $job
     * @param string $data
     * @param null $queue
     * @return string
     */
    protected function createPayload($job, $data = '', $queue = null)
    {
        /*if ($job instanceof Closure)
        {
            return json_encode($this->createClosurePayload($job, $data));
        }*/

        $payload = [
            'job'  => $job,
            'data' => $data
        ];
        $payload = $this->setMeta(json_encode($payload), 'id', $this->getRandomId());
        return $payload;
    }

    /**
     * Create a payload string for the given Closure job.
     *
     * @param  \Closure  $job
     * @param  mixed     $data
     * @return string
     */
    /*protected function createClosurePayload($job, $data)
    {
        $closure = $this->crypt->encrypt(serialize(new SerializableClosure($job)));

        return array('job' => 'IlluminateQueueClosure', 'data' => compact('closure'));
    }*/

    /**
     * Set additional meta on a payload string.
     *
     * @param  string  $payload
     * @param  string  $key
     * @param  string  $value
     * @return string
     */
    protected function setMeta($payload, $key, $value)
    {
        $payload = json_decode($payload, true);
        $payload[$key] = $value;
        return json_encode($payload);
    }

    /**
     * Get a random ID string.
     *
     * @return string
     */
    protected function getRandomId()
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($pool, 5)), 0, 32);
    }

    /**
     * 获取队列名
     * @param $queue
     * @return string
     */
    protected function getQueue($queue)
    {
        return $this->buildPrefix($queue) . $this->getQueueInternal($queue);
    }

    /**
     * 入队列内部实现
     * @param $payload
     * @param null $queue
     * @param array $options
     * @return mixed
     */
    abstract protected function pushInternal($payload, $queue = null, array $options = []);

    /**
     * 获得队列名内部实现
     * @param $queue
     * @return mixed
     */
    abstract protected function getQueueInternal($queue = null);

    abstract protected function popInternal($queue = null);

}
