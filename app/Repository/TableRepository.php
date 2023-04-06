<?php

namespace App\Repository;

class TableRepository extends BaseAPIRepository
{
    function __construct()
    {
        $this->tableName = 'mtables';
        $this->pk = 'id';
    }

    public function customList($room_id)
    {
        return $this->table()->where('room_id', $room_id)->select('id', 'name')->get();
    }
}
