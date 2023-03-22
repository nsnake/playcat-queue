<?php
return [
    'default' => [
        'host' => sprintf('redis://%s:%s', '127.0.0.1', 6379),
        'options' => [
            'auth' => '',       // 密码，字符串类型，可选参数
        ]
    ],
];
