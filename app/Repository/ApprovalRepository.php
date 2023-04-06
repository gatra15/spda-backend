<?php

namespace App\Repository;

use App\Models\User;

class ApprovalRepository extends BaseAPIRepository
{
    function __construct()
    {
        $this->tableName = 'tapprovals';
        $this->pk = 'id';
    }

    public function data()
    {
        return $this->table();
    }

    public function approve($data)
    {
        $approve = $this->customTable($data->table)->where('id', $data->fk_id)->update([
            'approved_at' => now()->format('Y-m-d h:i:s'),
            'approved_by' => auth()->user()->name,
            'deleted_at' => $data->data->deleted_at,
            'deleted_by' => User::findOrFail($data->data->user_id)->name,
            'rejected_at' => NULL,
            'rejected_by' => NULL
        ]);

        $updateApprove = $this->update([
            'status' => 'DISETUJUI'
        ], $data->id);

        $data->data =  $this->customTable($data->table)->where('id', $data->fk_id)->first();

        if($approve)
        {
            (new LogRepository)->insert(auth()->user(), request(), $this->tableName, 'Approve', $data->id, json_encode($data->data));
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Something went wrong.'
            ]);
        }

        return $approve;
    }

    public function reject($data)
    {
        $reject = $this->customTable($data->table)->where('id', $data->fk_id)->update([
            'approved_at' => NULL,
            'approved_by' => NULL,
            'deleted_at' => NULL,
            'deleted_by' => NULL,
            'rejected_at' => now()->format('Y-m-d h:i:s'),
            'rejected_by' => auth()->user()->name
        ]);

        $data->data =  $this->customTable($data->table)->where('id', $data->fk_id)->first();

        $updateReject = $this->update([
            'status' => 'DITOLAK'
        ], $data->id);

        if($reject)
        {
            (new LogRepository)->insert(auth()->user(), request(), $this->tableName, 'Reject', $data->id, json_encode($data->data));
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Something went wrong.'
            ]);
        }

        return $reject;
    }
}
