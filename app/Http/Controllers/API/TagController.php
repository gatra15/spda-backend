<?php

namespace App\Http\Controllers\API;

use App\Repository\TagRepository;

class TagController extends BaseAPIController
{
    function __construct(TagRepository $repo)
    {
        parent::__construct($repo);
    }

    public function rules()
    {
        $rules = [
            'name' => 'required',
        ];

        return $rules;
    }

    public function customParams(&$params = null)
    {
        $params['pageSize'] = request('pageSize');
        $params['query'] = request('query');
        $params['column'] = ['name'];
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

    public function list()
    {
        $data = $this->repo->list();
        return response()->json([
            'status' => 1,
            'message' => 'success',
            'data' => $data
        ]);
    }
}
