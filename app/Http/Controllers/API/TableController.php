<?php

namespace App\Http\Controllers\API;

use Illuminate\Validation\Rule;
use App\Repository\TagRepository;
use App\Repository\RoomRepository;
use App\Repository\TableRepository;

class TableController extends BaseAPIController
{
    function __construct(TableRepository $repo)
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
                'code' => ['required', Rule::unique('mtables', 'code')->ignore($id)->whereNull('deleted_at')]
            ];
        } else
        {
            $rules = [
                'name' => 'required',
                'code' => ['required', Rule::unique('mtables', 'code')->whereNull('deleted_at')]
            ];
        }


        return $rules;
    }

    public function customData(&$data, &$id = null)
    {
        if($id)
        {
            $room = (new RoomRepository)->detail($data->room_id);
            if(!$room)
            {
                return response()->json([
                    'status' => 0,
                    'message' => 'Harap isi data Ruangan terlebih dahulu.'
                ]);
            }
            $data->room_name = $room->name;
        }
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

    public function customQuery(&$data)
    {
        $data->leftJoin('mrooms', $this->repo->tableName.'.room_id', '=', 'mrooms.id');
        $data->select($this->repo->tableName.'.*', 'mrooms.name as room_name');
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
        $room_id = request('room_id');
        $data = $this->repo->customList($room_id);
        return response()->json([
            'status' => 1,
            'message' => 'Success',
            'data' => $data
        ]);
    }

    public function approve($id)
    {
        $data = $this->repo->detail($id);
        dd($data);
    }
}
