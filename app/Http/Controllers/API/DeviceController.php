<?php

namespace App\Http\Controllers\API;

use App\Helper\Helper;
use App\Repository\DeviceRepository;
use App\Repository\DeviceTagRepository;
use App\Http\Controllers\API\BaseAPIController;
use App\Repository\TagRepository;

class DeviceController extends BaseAPIController
{
    function __construct(DeviceRepository $repo)
    {
        parent::__construct($repo);
    }

    public function rules()
    {
        $rules = [
            'name' => 'required',
            'table' => 'required',
            'room' => 'required',
            'photo' => 'required'
        ];

        return $rules;
    }

    public function customPostdata(&$postdata)
    {
        if(isset($postdata['tag']))
        {
            $postdata['tag'] = json_encode($postdata['tag']);
        }
        if(isset($postdata['id']))
        {
            $exist = $this->repo->detail($postdata['id']);
            if(!isset($postdata['photo']))
            {
                $postdata['photo'] = $exist->photo;
            }
        }
    }

    public function customData(&$data, &$id = null)
    {
        if($id)
        {
            $data->tag = json_decode($data->tag) ?? [];
            $data->photo = Helper::getUrl($data->photo);
        } else {
            foreach($data as $value)
            {
                $value->tag = json_decode($value->tag) ?? [];
                $value->photo = Helper::getUrl($value->photo);
            }
        }
        return $data;
    }

    public function customParams(&$params = null)
    {
        $params['pageSize'] = request('pageSize');
        $params['query'] = request('query');
        $params['column'] = ['name', 'tag'];
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
            'message' => 'Success',
            'data' => $data
        ]);
    }
}
