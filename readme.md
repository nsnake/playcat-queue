<h1 align="center">Playcat Queue</h1>

<p align="center">基于 webman 的消息队列服务，支持 Redis、Kafka 和 RabbitMQ 三种消息系统，支持消息延迟和异常重试，以及大数据量处理场景</p>

**注意2.0与1.0并不完全兼容**


## 支持的消息系统

- Redis和Redis集群 (须大于5.0)
- Kafka (最新版)
- RabbitMQ (最新版)

## 环境需求

- PHP >= 7.2
- webman >= 1.4
- PHP Redis扩展 (redis)
- PHP RdKafka扩展 (kafka)
- PHP php-amqplib/php-amqplib(RabbitMQ)

## 安装

```shell
$ composer require "playcat/queue"
```

## 使用方法

### 1.选择自己的消息服务

- Redis Stream(默认)
  编辑`config\plugin\playcat\queue\redis.php`为对应的redis的配置


- Redis集群
  编辑`config\plugin\playcat\queue\manager.php`里的`driver`为如下内容

```php
'driver' => \Playcat\Queue\Driver\Rediscluster::class,
```
  编辑`config\plugin\playcat\queue\rediscluster.php`为对应的redis的配置

- 使用Kafka

- 首先在Kafka上创建好topic

```shell
./kaftopics.sh --create --bootstrap-server xxx:9092 --replication-factor 1 --partitions 1 --topic 任务名称
```

 编辑`config\plugin\playcat\queue\manager.php`修改里面的`driver`为如下内容

```php
'driver' => \Playcat\Queue\Driver\Kafka::class,
```

 编辑`config\plugin\playcat\queue\Kafka.php`,为对应的Kafka的配置

- 使用RabbitMQ
  编辑`config\plugin\playcat\queue\manager.php`修改里面的`driver`为如下内容

```php
'driver' => \Playcat\Queue\Driver\RabbitMQ::class,
```
 编辑`config\plugin\playcat\queue\rabbitmq.php`,为对应的Rabbitmq的配置

### 2.创建消费者任务

#### 新建一个名为'Test.php'文件添加以下内容:

```php
<?php

namespace app\queue\playcat;

use Playcat\Queue\Protocols\ConsumerDataInterface;
use Playcat\Queue\Protocols\ConsumerInterface;

class Test implements ConsumerInterface
{
    //任务名称
    public $queue = 'test';

    public function consume(ConsumerData $payload)
    {
        //获取自定义传入的内容
        $data = $payload->getQueueData();
        ...
    }
}

```

#### 将'Test.php'保存到'

*app/queue/playcat/*'目录下。如果目录不存在就创建它(
==可以编辑config/plugin/playcat/queue/process.php中的consumer_dir的地址来改变==)

#### 启动webman的服务

```shell
$ php start.php start
```

### 添加任务

```php
use Playcat\Queue\Manager;
use Playcat\Queue\Protocols\ProducerData;
$payload = new ProducerData();
//对应消费队列里的任务名称
$payload->setChannel('test');
//对应消费队列里的任务使用的数据
$payload->setQueueData([1,2,3,4]);
//创建一个立即执行的任务
Manager::getInstance()->push($payload);

$payload_delay = new ProducerData();
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

- getID: 当前任务的唯一id(可能为空)
- getRetryCount(): 当前任务已经重试过的次数
- getQueueData():  当前任务传入的参数
- getChannel(): 当前所执行的任务名称

- - -
QQ:318274085

## License

MIT
