<?php
return [
    'default' => [
        'host' =>  sprintf('redis://%s:%s',getenv('QUEUEREDISHOST'), getenv('QUEUEREDISPORT')),
        'options' => [
            'auth' => getenv('QUEUEREDISAUTH'),       // 密码，字符串类型，可选参数
            'db' => 0,            // 数据库
            'prefix' => '',       // key 前缀
        ]
    ],
];
