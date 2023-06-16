<?php

namespace App\Http\Controllers\API;

use App\Helper\Helper;
use Illuminate\Validation\Rule;
use App\Repository\DeviceRepository;
use App\Repository\DocumentRepository;
use App\Repository\TableRepository;

class DocumentController extends BaseAPIController
{
    function __construct(DocumentRepository $repo)
    {
        parent::__construct($repo);
    }

    public function rules()
    {
        $id = request('id');
        if($id)
        {
            $rules = [
                'uuid' => ['required', Rule::unique('mdocuments', 'uuid')->ignore($id)->whereNull('deleted_at')],
                'name' => 'required',
                'device_id' => 'required',
            ];
        } else {
            $rules = [
                'uuid' => ['required', Rule::unique('mdocuments', 'uuid')->ignore($id)->whereNull('deleted_at')],
                'name' => 'required',
                'device_id' => 'required',
            ];
        }

        return $rules;
    }

    public function customQuery(&$data, &$id = null)
    {
        $data->leftJoin('mdevices', $this->repo->tableName.'.device_id', '=', 'mdevices.id');
        $data->leftJoin('mrooms', 'mdevices.room_id', '=', 'mrooms.id');
        $data->leftJoin('mtables', 'mdevices.table_id', '=', 'mtables.id');
        $data->select($this->repo->tableName.'.*', 'mdevices.photo as photo', 'mrooms.name as room', 'mtables.name as table');

        if($id)
        {
            $data = $data->where($this->repo->tableName.'.id', $id);
        }
        return $data;
    }

    public function customData(&$data, &$id = null)
    {
        if($id)
        {
            $data->tag = json_decode($data->tag) ?? [];
            $device = (new DeviceRepository)->detail($data->device_id);
            if(!$device)
            {
                return response()->json([
                    'status' => 0,
                    'message' => 'Harap isi data Alat terlebih dahulu.'
                ]);
            }
            $room = (new DeviceRepository)->detail($device->room_id);
            if(!$room)
            {
                return response()->json([
                    'status' => 0,
                    'message' => 'Harap isi data Alat terlebih dahulu.'
                ]);
            }
            $table = (new TableRepository)->detail($device->table_id);
            if(!$table)
            {
                return response()->json([
                    'status' => 0,
                    'message' => 'Harap isi data Alat terlebih dahulu.'
                ]);
            }
            $data->device_name = $device->name;
            $data->room_name = $room->name;
            $data->table_name = $table->name;
            $data->photo = Helper::getUrl($device->photo);

        } else {
            foreach($data as $value)
            {
                // $value->tag_name = (new DocumentTagRepository)->getByDocumentId($value->id);
                $value->tag = json_decode($value->tag) ?? [];
                $value->photo = Helper::getUrl($value->photo);

            }
        }
        return $data;
    }

    public function customPostdata(&$postdata)
    {
        // $postdata['id'] = request('id');
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
            $postdata['code'] = $postdata['code'] ?? $this->repo->detail($postdata['id'])->code;
        }
    }

    public function customParams(&$params = null)
    {
        $params['pageSize'] = request('pageSize');
        $params['query'] = request('query');
        $params['column'] = ['mdevices.name', 'mdocuments.name', 'mdocuments.tag', 'mdocuments.code'];
    }

    public function beforeAdd(&$postdata)
    {
        $postdata['created_at'] = now()->format('Y-m-d h:i:s');
        $postdata['created_by'] = auth()->user()->name;
        // $this->tag = json_encode($postdata['tag_id']);
        // unset($postdata['tag_id']);
    }

    // public function afterAdd(&$id, &$postdata)
    // {
    //     // $tag = json_decode($this->tag);
    //     // $addTag = (new DocumentTagRepository)->multiInsert($tag, $id);
    // }

    public function beforeEdit(&$id, &$postdata)
    {
        $postdata['updated_at'] = now()->format('Y-m-d h:i:s');
        $postdata['updated_by'] = auth()->user()->name;
        // $this->tag = json_encode($postdata['tag_id']);
        // unset($postdata['tag_id']);
        // dd(($postdata));
    }

    // public function afterEdit(&$id, &$postdata)
    // {
    //     $tag = json_decode($this->tag);
    //     $addTag = (new DocumentTagRepository)->multiUpdate($tag, $id);
    // }

    public function updateDocument()
    {
        $postdata = request()->all();
        $data = $this->repo->updateDocument($postdata);

        return response()->json([
            'status' => 1,
            'message' => 'Success',
        ]);
    }
}
