<?php

namespace App\Http\Controllers\API;

use App\Helper\Helper;
use Illuminate\Validation\Rule;
use App\Repository\TagRepository;
use App\Repository\DeviceRepository;
use App\Repository\DeviceTagRepository;
use App\Http\Controllers\API\BaseAPIController;
use App\Repository\RoomRepository;
use App\Repository\TableRepository;
use Carbon\Carbon;

class DeviceController extends BaseAPIController
{
    function __construct(DeviceRepository $repo)
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
                'table_id' => 'required',
                'room_id' => 'required',
                'photo' => 'required',
                'code' => ['required', Rule::unique('mdevices', 'code')->ignore($id)->whereNull('deleted_at')],
            ];
        } else {
            $rules = [
                'name' => 'required',
                'table_id' => 'required',
                'room_id' => 'required',
                'photo' => 'required',
                'code' => ['required', Rule::unique('mdevices', 'code')->whereNull('deleted_at')],
            ];
        }


        return $rules;
    }

    public function customPostdata(&$postdata)
    {
        if(isset($postdata['tag']))
        {
            if($postdata['tag'][0] != null)
            {
                $postdata['tag'] = json_encode($postdata['tag']);
            } else {
                $postdata['tag'] = NULL;
            }
        }
        if(isset($postdata['id']))
        {
            $exist = $this->repo->detail($postdata['id']);
            if(!isset($postdata['photo']))
            {
                $postdata['photo'] = $exist->photo;
            }
            $postdata['code'] = $postdata['code'] ?? $this->repo->detail($postdata['id'])->code;
        }
    }

    public function customData(&$data, &$id = null)
    {
        if($id)
        {
            $data->tag = json_decode($data->tag) ?? [];
            $data->status = $data->status == 1 ? true : false;
            $data->photo = Helper::getUrl($data->photo);
            $data->room_name = (new RoomRepository)->detail($data->room_id)->name;
            if(!$data->room_name)
            {
                return response()->json([
                    'status' => 0,
                    'message' => 'Harap isi data Ruangan terlebih dahulu.'
                ]);
            }
            $data->table_name = (new TableRepository)->detail($data->table_id)->name;
            if(!$data->room_name)
            {
                return response()->json([
                    'status' => 0,
                    'message' => 'Harap isi data Meja terlebih dahulu.'
                ]);
            }
        } else {
            foreach($data as $value)
            {
                $value->tag = json_decode($value->tag) ?? [];
                $value->photo = Helper::getUrl($value->photo);
                $value->status = $value->status == 1 ? true : false;
            }
        }
        return $data;
    }

    public function customQuery(&$data)
    {
        $data->leftJoin('mrooms', $this->repo->tableName.'.room_id', '=', 'mrooms.id');
        $data->leftJoin('mtables', $this->repo->tableName.'.table_id', '=', 'mtables.id');
        $data->select($this->repo->tableName.'.*', 'mrooms.name as room_name', 'mtables.name as table_name');
    }

    public function customParams(&$params = null)
    {
        $params['pageSize'] = request('pageSize');
        $params['query'] = request('query');
        $params['column'] = ['name', 'tag', 'code'];
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

    public function checkStatus($id)
    {
        $data = $this->repo->detail($id);
        $time = Carbon::parse($data->checked_at);
        $now = now();

        $diff = now()->diffInSeconds($time);

        if($diff > 60)
        {
            $new_data['status'] = 0;
            $this->repo->update($new_data, $id);
        } else {
            $new_data['status'] = 1;
            $this->repo->update($new_data, $id);
        }
        // $this->checkStatus($id);
    }

    public function startCheckStatus()
    {
        $postdata = request()->all();
        $new_data['checked_at'] = now();
        $new_data['status'] = 1;
        $this->repo->update($new_data, $postdata['id']);

        return response()->json([
            'status' => 1,
            'message' => 'Success',
        ]);
    }
}
