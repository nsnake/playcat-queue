<?php

namespace Playcat\Queue\Process;

use ErrorException;
use Playcat\Queue\Protocols\ProducerData;
use Playcat\Queue\Protocols\TimerClientProtocols;
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
            $result = '';
            $protocols = unserialize($data);
            if ($protocols instanceof TimerClientProtocols) {
                switch ($protocols->getCMD()) {
                    case TimerClientProtocols::CMD_PUSH:
                        $result = $this->cmdPush($protocols->getPayload());
                        break;
                    case TimerClientProtocols::CMD_DEL:
                        break;
                }

            }
            $connection->send(json_encode(['code' => 200, 'msg' => 'ok', 'data' => $result]));
        } catch (ErrorException $e) {
            $connection->send(json_encode(['code' => 500, 'msg' => $e->getMessage(), 'data' => '']));
        }
    }

    public function onClose(TcpConnection $connection)
    {

    }

    private function cmdPush(ProducerData $payload): int
    {
        return Timer::add(floatval($payload->getDelayTime()), function (ProducerData $payload) {
            $payload->setDelayTime();
            $this->manager->push($payload);
        }, [$payload], false);
    }
}
