<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ScheduleCollection;
use App\Http\Resources\ScheduleResource;
use App\Repositories\ScheduleRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScheduleController extends Controller
{
    private $scheRepo;

    /**
     * MediaController constructor.
     * @param ScheduleRepository $scheRepo
     */
    public function __construct(ScheduleRepository $scheRepo)
    {
        $this->scheRepo = $scheRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $pages = intval($request->size);
        $schedule = $this->scheRepo->scheduleSearch($request)->paginate($pages);
        return $this->response(200, ['schedule' => new ScheduleCollection($schedule)], __('text.retrieved_successfully'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'time_start' => 'nullable',
            'time_end' => 'nullable',
            'movie_id' => 'required|integer|max:100',
        ]);

        if ($validator->fails()) {
            return $this->response(422, [], '', $validator->errors());
        }
        $input = $request->only(['name', 'time_start', 'time_end', 'movie_id']);

        $checkName = $this->scheRepo->all(['name' => $input['name']]);

        if (count($checkName)) {
            return $this->response(422, [], __('text.has_been_registered', ['model' => 'Name']));
        }
        $schedule = $this->scheRepo->create($input);
        return $this->response(200, ['schedule' => new ScheduleResource($schedule)], __('text.register_successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $schedule = $this->scheRepo->find($id);

        if (empty($schedule)) {
            return $this->response(200, [], __('text.not_found', ['model' => 'Schedule']), [], false);
        }

        return $this->response(200, ['schedule' => new ScheduleResource($schedule)], __('text.retrieved_successfully'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'time_start' => 'nullable',
            'time_end' => 'nullable',
            'movie_id' => 'required|integer|max:100',
        ]);

        if ($validator->fails()) {
            return $this->response(422, [], '', $validator->errors());
        }

        $input = $request->only(['name', 'time_start', 'time_end', 'movie_id']);

        $schedule = $this->scheRepo->find($id);

        if (empty($schedule)) {
            return $this->response(422, [], __('text.not_found', ['model' => 'Schedule']));
        }

        $schedule = $this->scheRepo->update($input, $id);

        return $this->response(200, ['schedule' => new ScheduleResource($schedule)], __('text.update_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $schedule = $this->scheRepo->find($id);

        if (empty($schedule)) {
            return $this->response(422, [], __('text.delete_not_found'));
        }

        $this->scheRepo->delete($id);

        return $this->response(200, null,  __('text.delete_successfully'));
    }
}
