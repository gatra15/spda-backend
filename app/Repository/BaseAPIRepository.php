<?php

namespace App\Repository;

use Illuminate\Support\Facades\DB;

class BaseAPIRepository
{
    public $tableName = '';
    public $pk = '';

    function __construct()
    {
        $this->tableName = '';
        $this->pk = '' ?? 'id';
    }

    public function table()
    {
        return DB::table($this->tableName);
    }

    public function all($paginate = null)
    {
        return $this->table()->paginate($paginate ?? 15);
    }

    public function data()
    {
        return $this->table()->whereNull($this->tableName.'.deleted_at');
    }

    public function list()
    {
        return $this->table()->select('id', 'name')->get();
    }

    public function detail($id)
    {
        return $this->table()->where('id', $id)->first();
    }

    public function create($postdata)
    {
        return $this->table()->insertGetId($postdata);
    }

    public function update($postdata, $id)
    {
        return $this->table()->where('id', $id)->update($postdata);
    }

    public function delete($id)
    {
        return $this->table()->where($this->pk, $id)->update([
            'deleted_at' => now(),
            'deleted_by' => auth()->user()->name
        ]);
    }

    public function restore($id)
    {
        return $this->table()->where($this->pk, $id)->update([
            'deleted_at' => null,
            'deleted_by' => null
        ]);
    }

    public function permanentDelete($id)
    {
        return $this->table()->where($this->pk, $id)->delete();
    }
}
