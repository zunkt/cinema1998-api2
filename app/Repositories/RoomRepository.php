<?php

namespace App\Repositories;

use App\Models\Room;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RoomRepository
 * @package App\Repositories
 * @version July 8, 2021, 3:09 pm UTC
*/

class RoomRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'room_number',
        'theater_id',
        'schedule_id',
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
        return Room::class;
    }

    /**
     * @param Request $request
     * @return Builder
     */
    public function roomSearch(Request $request)
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

        if (isset($request->theater) && isset($request->schedule)) {
            $query->where('theater_id', $request->theater)
                  ->where('schedule_id', $request->schedule)
            ;
        }

        return $query;
    }
}
