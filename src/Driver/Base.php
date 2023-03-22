<?php

namespace Playcat\Queue\Driver;
class Base
{
    protected $iconic_id = 0;

    /**
     * @param int $iconic_id
     * @return void
     */
    public function setIconicId(int $iconic_id = 0):void
    {
        $this->iconic_id = $iconic_id;
    }
}