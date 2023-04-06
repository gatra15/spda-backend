<?php

namespace App\Http\Controllers\API;

use Illuminate\Validation\Rule;
use App\Repository\RoomRepository;
use App\Repository\TableRepository;

class RoomController extends BaseAPIController
{
    function __construct(RoomRepository $repo)
    {
        parent::__construct($repo);
    }

    public function rules()
    {
        $id = request('id');
        if($id)
        {
            $rules = [
                'name' => 'required',
                'code' => ['required', Rule::unique('mrooms', 'code')->ignore($id)->whereNull('deleted_at')]
            ];
        } else
        {
            $rules = [
                'name' => 'required',
                'code' => ['required', Rule::unique('mrooms', 'code')->whereNull('deleted_at')]
            ];
        }


        return $rules;
    }

    public function customParams(&$params = null)
    {
        $params['pageSize'] = request('pageSize');
        $params['query'] = request('query');
        $params['column'] = ['name', 'code'];
    }

    public function customPostdata(&$postdata)
    {
        if(isset($postdata['id']))
        {
            $postdata['code'] = $postdata['code'] ?? $this->repo->detail($postdata['id'])->code;
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
}
