<?php

namespace App\Repository;

class DeviceRepository extends BaseAPIRepository
{
    function __construct()
    {
        $this->tableName = 'mdevices';
        $this->pk = 'id';
    }
}
