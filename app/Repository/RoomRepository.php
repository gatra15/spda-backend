<?php

namespace App\Repository;

class RoomRepository extends BaseAPIRepository
{
    function __construct()
    {
        $this->tableName = 'mrooms';
        $this->pk = 'id';
    }
}
