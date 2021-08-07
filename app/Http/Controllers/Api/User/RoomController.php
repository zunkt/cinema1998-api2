<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoomCollection;
use App\Http\Resources\RoomResource;
use App\Repositories\RoomRepository;
use App\Repositories\TheaterRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    private $roomRepo;
    private $theaRepo;

    /**
     * MediaController constructor.
     * @param RoomRepository $roomRepo
     * @param TheaterRepository $theaRepo
     */
    public function __construct(RoomRepository $roomRepo, TheaterRepository $theaRepo)
    {
        $this->roomRepo = $roomRepo;
        $this->theaRepo = $theaRepo;
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
        $room = $this->roomRepo->roomSearch($request)->with('theater', 'seat_room', 'schedule')->paginate($pages);
        return $this->response(200, ['room' => new RoomCollection($room)], __('text.retrieved_successfully'), [], null, true);
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
            'room_number' => 'required|integer|max:100',
            'theater_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->response(200, [], '', $validator->errors(), null, false);
        }
        $input = $request->only(['name', 'room_number', 'theater_id']);

        $isExitTheater = $this->theaRepo->find($request->theater_id);

        if (!$isExitTheater) {
            return $this->response(200, [], __('text.not_found', ['model' => 'Theater Id']), [], null, false);
        }

        $room = $this->roomRepo->create($input);
        return $this->response(200, ['room' => new RoomResource($room)], __('text.register_successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $room = $this->roomRepo->makeModel()->with('theater', 'schedule', 'seat_room')->find($id);

        if (empty($room)) {
            return $this->response(200, [], __('text.not_found', ['model' => 'Room']), [], null, false);
        }

        return $this->response(200, ['room' => new RoomResource($room)], __('text.retrieved_successfully'), [], null, true);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request)
    {
        $pages = intval($request->size);

        $room = $this->roomRepo->roomSearch($request)->with('seat_room')->paginate($pages);

        return $this->response(200, ['room' => new RoomCollection($room)], __('text.retrieved_successfully'), [], null, true);
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
            'room_number' => 'required|integer|max:100',
            'theater_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->response(200, [], '', $validator->errors(), null, false);
        }

        $input = $request->only(['name', 'room_number', 'theater_id']);

        if (empty($this->roomRepo->find($id))) {
            return $this->response(200, [], __('text.not_found', ['model' => 'Room']), null, [], false);
        }

        $room = $this->roomRepo->update($input, $id);

        return $this->response(200, ['schedule' => new RoomResource($room)], __('text.update_successfully'), null, [], true);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $room = $this->roomRepo->find($id);

        if (empty($room)) {
            return $this->response(200, [], __('text.delete_not_found'), [], false, false);
        }

        $this->roomRepo->delete($id);

        return $this->response(200, null, __('text.delete_successfully'), [], false, true);
    }
}
