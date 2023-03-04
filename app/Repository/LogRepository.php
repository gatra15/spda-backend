<?php

namespace App\Repository;

class LogRepository extends BaseAPIRepository
{
    function __construct()
    {
        $this->tableName = 'logs';
        $this->pk = 'id';
    }

    public function insert($auth, $request, $tableName, $action, $id, $data)
    {
        return $this->table()->insertGetId([
            'user_id' => $auth->id ?? Null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'activity' => $action." data in table ".$tableName." id = ".$id,
            'data' => json_encode($data),
            'created_at' => Date("Y-m-d H:i:s")
        ]);
    }
}
