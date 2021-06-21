<?php

namespace App\Repositories;

use App\Models\Admin;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Class AdminRepository
 * @package App\Repositories
 * @version January 26, 2021, 10:22 am UTC
*/

class AdminRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'email',
        'password',
        'name',
        'phone'
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
        return Admin::class;
    }

    /**
     * @param Request $request
     * @return Builder
     */
    public function adminSearch(Request $request)
    {
        $query = $this->model->newQuery();

        if (isset($request->filters) && !empty($request->filters)) {
            foreach ($request->filters as $filter) {
                $query->where($filter['field'], $filter['type'], '%' . $filter['value'] . '%');
            }
        }

        if (isset($request->sorters) && !empty($request->sorters)) {
            foreach ($request->sorters as $sort) {
                $query->orderBy($sort['field'], $sort['dir']);
            }
        }

        return $query;
    }
}
