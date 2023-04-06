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

    public function delete($id)
    {
        $postdata = $this->detail($id);
        $postdata->user_id = auth()->user()->id;
        $postdata->deleted_at = now()->format('Y-m-d h:i:s');

        // unset data
        unset($postdata->id);
        unset($postdata->uuid);

        $data = json_encode($postdata);
        $data = (new ApprovalRepository)->create([
            'table' => $this->tableName,
            'fk_id' => $id,
            'data' => $data
        ]);

        return $data;
    }
}
