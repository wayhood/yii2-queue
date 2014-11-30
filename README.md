Queue component for Yii2
====================
This component providers simple queue warpper

Requirements
------------

redis

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist wayhood/yii2-queue "*"
```

or add

```
"wayhood/yii2-queue": "*"
```

to the require section of your `composer.json` file.


Usage
-----

To use this extension, simply add the following code in your application configuration:

```php
return [
    //....
    'components' => [
        'queue' => [
            'class' => 'wh\queue\RedisQueue',
            'redis' => [
                'hostname' => 'localhost',
                'port' => 6379,
                'database' => 0
            ]
        ],
    ],
];
```



The first create a Job process Class

```php
namespace console\jobs;

class MyJob
{
    public function run($job, $data)
    {
        //process $data;
        var_dump($data);
    }
} 
```

than set data to queue

```
#Default is run "run" method
Yii::$app->queue->push('\console\jobs\MyJob', ['a', 'b', 'c']);

#or other method name
Yii::$app->queue->push('\console\jobs\MyJob@run', ['a', 'b', 'c']);

```  

Command woker and listen

```php
return [
    // ...
    'controllerMap' => [
        'queue' => 'wh\queue\console\controllers\QueueController'
    ],
];
```

Below are some command usages of this command:

```
#process a job and run, than exit process
yii queue/work 

#a loop process job
yii queue/listen
```
