<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\SeatRoomCollection;
use App\Http\Resources\SeatRoomResource;
use App\Repositories\SeatRoomRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SeatRoomController extends Controller
{
    private $seatRoomRepo;

    /**
     * MediaController constructor.
     * @param SeatRoomRepository $seatRepo
     */
    public function __construct(SeatRoomRepository $seatRoomRepo)
    {
        $this->seatRoomRepo = $seatRoomRepo;
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
        $seat = $this->seatRoomRepo->seatRoomSearch($request)->with('room', 'seat')->paginate($pages);
        return $this->response(200, ['seat' => new SeatRoomCollection($seat)], __('text.retrieved_successfully'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'value' => 'required|string|max:100',
            'room_id' => 'required|integer|max:100',
        ]);

        if ($validator->fails()) {
            return $this->response(200, [], '', $validator->errors(), [], false);
        }

        $input = $request->only(['value', 'room_id']);
        $seat = $this->seatRoomRepo->create($input);
        return $this->response(200, ['seat_room' => new SeatRoomResource($seat = $this->seatRoomRepo->find($seat->id))], __('text.register_successfully'),[], [], true);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $seat = $this->seatRoomRepo->makeModel()->with('room', 'seat')->find($id);

        if (empty($seat)) {
            return $this->response(200, [], __('text.not_found', ['model' => 'Seat Room']), [], null, false);
        }

        return $this->response(200, ['seat_room' => new SeatRoomResource($seat)], __('text.retrieved_successfully'), [], null, true);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'value' => 'required|string|max:100',
            'room_id' => 'required|integer|max:100',
        ]);

        if ($validator->fails()) {
            return $this->response(200, [], '', $validator->errors(), [], false);
        }

        $input = $request->only(['value', 'room_id']);

        if (empty($this->seatRoomRepo->find($id))) {
            return $this->response(200, [], __('text.not_found', ['model' => 'Seat']), [], false, false);
        }

        $seat = $this->seatRoomRepo->update($input, $id);
        return $this->response(200, ['seat_room' => new SeatRoomResource($seat)], __('text.update_successfully'), [], false, true);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $seat = $this->seatRoomRepo->find($id);

        if (empty($seat)) {
            return $this->response(200, [], __('text.delete_not_found'), [], false, false);
        }

        $this->seatRoomRepo->delete($id);

        return $this->response(200, null, __('text.delete_successfully', ['model' => 'Seat Room']), [], true, true);
    }
}
