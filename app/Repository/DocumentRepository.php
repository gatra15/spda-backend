<?php

namespace App\Repository;

class DocumentRepository extends BaseAPIRepository
{
    function __construct()
    {
        $this->tableName = 'mdocuments';
        $this->pk = 'id';
    }

    public function updateDocument($postdata)
    {
        return $this->table()->where('uuid', $postdata['uuid'])->update([
            'device_id' => $postdata['device_id']
        ]);
    }
}
