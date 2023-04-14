<?php

namespace Playcat\Queue;


use Dotenv\Exception\ValidationException;
use Playcat\Queue\Exceptions\ConnectFailExceptions;
use Playcat\Queue\Protocols\ProducerData;

class TimerClient
{
    protected static $instance;
    protected static $client;
    private $config;

    public static function getInstance($config = []): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * @return false|resource
     * @throws ConnectFailExceptions
     */
    private function client()
    {
        if (!self::$client) {
            self::$client = stream_socket_client('tcp://' . $this->config ['timerserver'], $errno, $errstr);
            if (!self::$client) {
                throw new ConnectFailExceptions('Connect to playcat time server failed. ' . $errstr);
            }
        }

        return self::$client;
    }


    public function send(ProducerData $payload): string
    {
        try {
            fwrite($this->client(), serialize($payload) . "\n");
            $result = fread($this->client(), 1024);
            return $result;
        } catch (ConnectFailExceptions $e) {
            return '';
        }
    }

}