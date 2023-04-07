<?php

namespace Playcat\Queue\Process;

use ErrorException;
use Playcat\Queue\Protocols\ProducerData;
use Workerman\Timer;
use Workerman\Worker;
use Workerman\Connection\TcpConnection;
use Playcat\Queue\Manager;

class TimerServer
{
    private $manager;

    /**
     * onWorkerStart.
     */
    public function onWorkerStart(Worker $worker)
    {
        $this->manager = Manager::getInstance();
    }

    public function onConnect(TcpConnection $connection)
    {

    }

    public function onMessage(TcpConnection $connection, $data)
    {
        try {
            $payload = unserialize($data);
            if ($payload && is_object($payload)) {
                Timer::add(floatval($payload->getDelayTime()), function (ProducerData $payload) {
                    $payload->setDelayTime();
                    $this->manager->push($payload);
                }, [$payload], false);
            }
            $connection->send(json_encode(['code' => 200, 'msg' => 'ok', 'data' => '']));
        } catch (ErrorException $e) {

        }
    }

    public function onClose(TcpConnection $connection)
    {

    }
}
