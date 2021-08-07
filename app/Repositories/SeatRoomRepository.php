<?php

namespace App\Repositories;

use App\Models\SeatRoom;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SeatRoomRepository
 * @package App\Repositories
 * @version August 7, 2021, 11:24 pm +07
*/

class SeatRoomRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'value',
        'room_id'
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
        return SeatRoom::class;
    }

    /**
     * @param Request $request
     * @return Builder
     */
    public function seatRoomSearch(Request $request)
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
