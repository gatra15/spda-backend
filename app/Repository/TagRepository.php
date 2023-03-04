<?php

namespace App\Repository;

class TagRepository extends BaseAPIRepository
{
    function __construct()
    {
        $this->tableName = 'mtags';
        $this->pk = 'id';
    }
}
