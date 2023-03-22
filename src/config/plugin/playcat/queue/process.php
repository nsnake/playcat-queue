<?php
return [
    'PlaycatQueueServer' => [
        'handler' => Playcat\Queue\Process\Consumer::class,
        'count' => 1, // 可以设置多进程同时消费
        'constructor' => [
            // 消费者类目录
            'consumer_dir' => app_path() . '/queue/redis/tingsong',
            'max_attempts' => 3, // 消费失败后，重试次数
            'retry_seconds' => 60, // 重试间隔，单位秒
        ]
    ],
    'PlaycatTimerServer' => [
        'handler' => Playcat\Queue\Process\TimerServer::class,
        'listen' => 'text://localhost:6678',
        // 当前进程是否支持reload （可选，默认true）
        'reloadable' => true,
    ],
];
