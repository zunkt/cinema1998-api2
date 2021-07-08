<?php

namespace App\Repositories;

use App\Models\Movie;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class MovieRepository
 * @package App\Repositories
 * @version July 8, 2021, 3:04 pm UTC
 */
class MovieRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'image',
        'trailer_url',
        'director',
        'language',
        'actor',
        'year',
        'long_time',
        'rating',
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
        return Movie::class;
    }

    /**
     * @param Request $request
     * @return Builder
     */
    public function movieSearch(Request $request)
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
