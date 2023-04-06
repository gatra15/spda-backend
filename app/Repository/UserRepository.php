<?php

namespace App\Repository;

use Spatie\Permission\Models\Role;

class UserRepository extends BaseAPIRepository
{
    function __construct()
    {
        $this->tableName = 'musers';
        $this->pk = 'id';
    }

    public function roleList()
    {
        $data = Role::select('id', 'name')->get();
        return $data;
    }
}
