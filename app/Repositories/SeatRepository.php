<?php

namespace App\Repositories;

use App\Models\Seat;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SeatRepository
 * @package App\Repositories
 * @version July 21, 2021, 2:47 pm UTC
*/

class SeatRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [

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
        return Seat::class;
    }

    /**
     * @param Request $request
     * @return Builder
     */
    public function seatSearch(Request $request)
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
