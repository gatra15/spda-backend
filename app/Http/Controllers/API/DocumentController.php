<?php

namespace App\Http\Controllers\API;

use App\Helper\Helper;
use Illuminate\Validation\Rule;
use App\Repository\DeviceRepository;
use App\Repository\DocumentRepository;


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
        $data->select($this->repo->tableName.'.*', 'mdevices.name as device_name', 'mdevices.room as room_name', 'mdevices.table as table_name', 'mdevices.photo as photo');

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
            $data->device_name = $device->name;
            $data->room_name = $device->room;
            $data->table_name = $device->table;
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
        $postdata['id'] = request('id');
        if(isset($postdata['tag']))
        {
            $postdata['tag'] = json_encode($postdata['tag']);
        }
    }

    public function customParams(&$params = null)
    {
        $params['pageSize'] = request('pageSize');
        $params['query'] = request('query');
        $params['column'] = ['mdevices.name', 'mdocuments.name', 'mdocuments.tag'];
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
