<?php

namespace App\Http\Controllers\API;

use Illuminate\Validation\Rule;
use App\Repository\ApprovalRepository;

class ApprovalController extends BaseAPIController
{
    function __construct(ApprovalRepository $repo)
    {
        parent::__construct($repo);
    }

    public function rules()
    {
        $rules = [
            'table' => 'required',
            'fk_id' => 'required'
        ];

        return $rules;
    }

    public function customParams(&$params = null)
    {
        $params['pageSize'] = request('pageSize');
        $params['query'] = request('query');
        $params['column'] = ['table'];
    }

    public function customData(&$data, &$id = null)
    {
        foreach ($data as $value)
        {
            $value->data = json_decode($value->data);
        }
    }

    public function beforeAdd(&$postdata)
    {
        $postdata['created_at'] = now()->format('Y-m-d h:i:s');
        $postdata['created_by'] = auth()->user()->name;
    }

    public function beforeEdit(&$id, &$postdata)
    {
        $postdata['updated_at'] = now()->format('Y-m-d h:i:s');
        $postdata['updated_by'] = auth()->user()->name;
    }

    public function approve($id)
    {
        $approval = $this->repo->detail($id);

        if(!$approval)
        {
            return response()->json([
                'status' => 0,
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

        $data = $approval;
        $data->data = json_decode($data->data);

        $approve = $this->repo->approve($data);

        if(!$approve)
        {
            return response()->json([
                'status' => 0,
                'message' => 'Gagal menyetujui Data.'
            ]);
        }

        return response()->json([
            'status' => 1,
            'message' => 'Successfully Approved.'
        ]);
    }

    public function reject($id)
    {
        $approval = $this->repo->detail($id);

        if(!$approval)
        {
            return response()->json([
                'status' => 0,
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

        $data = $approval;
        $data->data = json_decode($data->data);

        $reject = $this->repo->reject($data);

        if(!$reject)
        {
            return response()->json([
                'status' => 0,
                'message' => 'Failed to reject Data.'
            ]);
        }

        return response()->json([
            'status' => 1,
            'message' => 'Successfully Rejected.'
        ]);
    }
}
