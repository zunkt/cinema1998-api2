<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;

/**
 * Class UserRepository
 * @package App\Repositories
 * @version January 26, 2021, 10:23 am UTC
*/

class UserRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id',
        'name',
        'password',
        'full_name',
        'email',
        'phone',
        'is_verified_email',
        'is_phone_verified',
        'is_token_phone',
        'token',
        'failed_login_attempts',
        'ban_at',
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return User::class;
    }

    public function getUserWithTrashById($id) {
        return $this->model->newQuery()->withTrashed()->find($id);
    }

    public function customerSearch(Request $request)
    {
        $query = $this->model->newQuery();

        if (isset($request->filters) && ! empty($request->filters)) {
            foreach ($request->filters as $filter) {
                $query->where($filter['field'], $filter['type'], '%'.$filter['value'].'%');
            }
        }

        if (isset($request->sorters) && ! empty($request->sorters)) {
            foreach ($request->sorters as $sort) {
                $query->orderBy($sort['field'], $sort['dir']);
            }
        }

        if (isset($request->approved)) {
            if ($request->approved == 1) {
                $query->whereNotNull('approved_at');
            } else {
                $query->whereNull('approved_at');
            }
        }

        if (isset($request->name)) {
            $query->where('name', 'like', '%' . urldecode($request->name) . '%');
        }

        return $query;
    }
}
