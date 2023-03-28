<h1 align="center">Playcat Queue</h1>

<p align="center">php消息队列服务</p>

## 特点

1. 支持多种消息队列(Redis,Kafka)
2. 延迟消息机制
3. 易于使用和扩展

## 支持的消息队列

Redis单机(**已完成**)

Redis集群(**已完成**)

Kafka(**todo**)

更多。。。

## 环境需求

- PHP >= 7.2
- Redis >= 5.0

## 安装

本项目基于webman,所以请先安装好webman在执行下面操作。

```shell
$ composer require "playcat/queue"
```

## 使用

### 1.选择合适的消息服务端
- 使用redis单机(默认)
  修改config\plugin\playcat\queue\redis.php为自己redis的配置即可

- 使用redis集群机
  修改config\plugin\playcat\queue\manager.php里的driver为如下内容
```php
'driver' => \Playcat\Queue\Driver\Rediscluster::class,
```
修改config\plugin\playcat\queue\rediscluster.php为自己redis的配置即可



### 2.创建消费者任务

#### 编辑'*config/plugin/playcat/queue/redis.php*',修改对应的redis配置

#### 新建一个名为'Test.php'文件添加以下内容:

```php
<?php

namespace app\queue\playcat;

use Playcat\Queue\Model\Payload;
use Playcat\Queue\Protocols\Consumer;

class Test implements Consumer
{
    //任务名称
    public $queue = 'test';

    public function consume(Payload $payload)
    {
        //获取自定义传入的内容
        $data = $payload->getQueueData();
        ...
    }
}

```

#### 将'Test.php'保存到'
*app/queue/playcat/*'目录下。如果目录不存在就创建它(==可以编辑config/plugin/playcat/queue/process.php中的consumer_dir的地址来改变==)

#### 启动webman的服务

```shell
$ php start.php start
```

### 添加任务

```php
use Playcat\Queue\Manager;
use Playcat\Queue\Model\Payload;
$payload = new Payload();
//对应消费队列里的任务名称
$payload->setChannel('test');
//对应消费队列里的任务使用的数据
$payload->setQueueData([1,2,3,4]);
//创建一个立即执行的任务
Manager::getInstance()->push($payload);

$payload_delay = new Payload();
//对应消费队列里的任务名称
$payload_delay->setChannel('test');
//对应消费队列里的任务使用的数据
$payload_delay->setQueueData([6,7,8,9]);
//设置60秒后执行的任务
$payload_delay->setDelayTime(60);
Manager::getInstance()->push($payload_delay);`
```

### 异常与重试机制

任务在执行过程中未抛出异常则默认执行成功，否则则进入重试阶段.
重试次数和时间由配置控制，重试间隔时间为当前重试次数的幂函数。
**Playcat\Queue\Exceptions\DontRetry**异常会忽略掉重试

### Playcat\Queue\Model\Payload

- getID: 当前任务的唯一id
- getRetryCount(): 当前任务已经重试过的次数
- getQueueData():  当前任务传入的参数
- getChannel(): 当前所执行的任务名称


- - -
QQ:318274085

## License

MIT
