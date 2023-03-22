<h1 align="center">Playcat Queue</h1>

<p align="center">基于Webman的消息队列系统</p>

## 特点

1. 支持多种消息队列(Redis stream,Kafka等)
1. 更好的延迟机制

## 支持的消息队列

Redis单机(已完成)

Redis集群(todo)

Kafka(todo)

更多。。。

## 环境需求

- PHP >= 7.2
- Redis >= 5.0

## 安装

```shell
$ composer require "playcat/queue"
```

## 使用

### 1.创建消费者任务

#### 编辑'config/plugin/playcat/queue/redis.php',修改配置为你的redis配置

#### 新建一个名为'Test.php'文件加入以下内容:

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
        $data = $payload->getQueueData();
        ...
    }
}

```

#### 将'Test.php'保存到'app/queue/playcat/'下。如果目录不存在就创建它(

可以编辑config/plugin/playcat/queue/process.php中的consumer_dir的地址来改变)

#### 启动Webman

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
//创建一个等待60S后执行的任务
Manager::getInstance()->push($payload,60);        
```

## License

MIT
