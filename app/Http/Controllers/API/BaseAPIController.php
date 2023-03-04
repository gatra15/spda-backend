<?php

namespace App\Http\Controllers\API;

use App\Helper\Helper;
use Illuminate\Http\Request;
use App\Repository\LogRepository;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BaseAPIController extends Controller
{
    public $repo;
    function __construct($repo)
    {
        $this->repo = $repo;
    }

    public function index($params = [])
    {
        $this->customParams($params);
        $columns = $params['column'];
        $paginate = $params['pageSize'];
        $query = $params['query'];
        $data = $this->repo->data();
        $this->customQuery($data);

        if ($query) {
            $data->where(function ($q) use ($columns, $query) {
                foreach ($columns as $cl) {
                    $q->orWhere($cl, 'like', '%' . $query . '%');
                }
            });
        }

        $data = $data->paginate($paginate ?? 10);
        $this->customData($data);
        return $data;
    }

    public function customParams(&$params = null){}
    public function customQuery(&$data){}
    public function customData(&$data, &$id = null){}
    public function customPostdata(&$postdata){}

    public function beforeAdd(&$postdata){}
    public function afterAdd(&$id, &$postdata){}

    public function store(Request $request)
    {
        $postdata = request()->all();
        $this->customPostdata($postdata);
        $validator = $this->setValidate($postdata);

        if($validator->fails())
        {
            return response()->json([
                'status' => 0,
                'message' => $validator->errors()
            ], 400);
        }

        $file = request()->file();

        try {
            $this->beforeAdd($postdata);
            if($file)
            {
                foreach($file as $key => $value)
                {
                    $new_file = request()->file($key);
                    $original = $new_file->getClientOriginalName();
                    $path = Storage::putFile("public/".$key, $request->file($key));

                    $postdata[$key] = $path;
                }
            }

            $data = $this->repo->create(array_merge($validator->validated(), $postdata));

            if($data)
            {
                $this->afterAdd($data, $postdata);
                (new LogRepository)->insert(auth()->user(), request(), $this->repo->tableName, 'Create', $data, $postdata);
            }

            return response()->json([
                'status' => 1,
                'message' => 'Successfully Created.',
            ]);
        } catch (\Throwable $e)
        {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function beforeEdit(&$id, &$postdata){}
    public function afterEdit(&$id, &$postdata){}

    public function detail($id)
    {
        $data = $this->repo->detail($id);
        $this->customData($data, $id);

        if(!$data)
        {
            return response()->json([
                'status' => 0,
                'message' => 'Data not found',
            ]);
        }

        return response()->json([
            'status' => 1,
            'message' => 'Success',
            'data' => $data,
        ]);
    }

    public function update(Request $request, $id)
    {
        $postdata = request()->all();
        $postdata['id'] = $id;
        $this->customPostdata($postdata);
        unset($postdata['id']);
        $validator = $this->setValidate($postdata);
        if($validator->fails())
        {
            return response()->json([
                'status' => 0,
                'message' => $validator->errors()
            ], 400);
        }

        $file = request()->file();

        try {
            $this->beforeEdit($id, $postdata);
            if($file)
            {
                foreach($file as $key => $value)
                {
                    $new_file = request()->file($key);
                    $original = $new_file->getClientOriginalName();
                    $path = 'public/'.explode('/',$request->path())[1];
                    $save = $new_file->storeAs($path.'/'.$key, $original);
                    $postdata[$key] = $save;
                }
            }

            $data = $this->repo->update(array_merge($validator->validated(), $postdata), $id);

            if($data)
            {
                $this->afterEdit($id, $postdata);
                (new LogRepository)->insert(auth()->user(), request(), $this->repo->tableName, 'Update', $id, $postdata);
            }

            return response()->json([
                'status' => 1,
                'message' => 'Successfully Updated.',
            ]);
        } catch (\Throwable $e)
        {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function delete($id)
    {
        $oldData = $this->repo->detail($id);
        $data = $this->repo->delete($id);

        if($data)
        {
            (new LogRepository)->insert(auth()->user(), request(), $this->repo->tableName, 'Delete', $id, $oldData);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Something went wrong.'
            ]);
        }

        return response()->json([
            'status' => 1,
            'message' => 'Successfully Deleted.',
        ]);
    }

    public function restore($id)
    {
        $data = $this->repo->restore($id);

        $newData = $this->repo->detail($id);

        if($data)
        {
            (new LogRepository)->insert(auth()->user(), request(), $this->repo->tableName, 'Restore', $id, $newData);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Something went wrong.'
            ]);
        }

        return response()->json([
            'status' => 1,
            'message' => 'Successfully Restored.',
        ]);
    }

    public function destroy($id)
    {
        $oldData = $this->repo->detail($id);

        $data = $this->repo->permanentDelete($id);

        if($data)
        {
            (new LogRepository)->insert(auth()->user(), request(), $this->repo->tableName, 'Permanent Delete', $id, $oldData);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Something went wrong.'
            ]);
        }

        return response()->json([
            'status' => 1,
            'message' => 'Successfully Deleted.',
        ]);
    }

    public function setValidate($postdata)
    {
        $rules = $this->rules();
        $validator = Validator::make($postdata, $rules);

        return $validator;
    }

    public function rules(){}
}
