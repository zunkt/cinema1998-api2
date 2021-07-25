<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\SeatCollection;
use App\Http\Resources\SeatResource;
use App\Repositories\SeatRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SeatController extends Controller
{
    private $seatRepo;

    /**
     * MediaController constructor.
     * @param SeatRepository $seatRepo
     */
    public function __construct(SeatRepository $seatRepo)
    {
        $this->seatRepo = $seatRepo;
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
        $seat = $this->seatRepo->seatSearch($request)->paginate($pages);
        return $this->response(200, ['seat' => new SeatCollection($seat)], __('text.retrieved_successfully'));
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
            'name' => 'required|string|max:100',
            'seat_number' => 'required|integer|max:100',
            'status' => 'required|integer|max:100',
            'ticket_id' => 'required|integer|max:100',
            'room_id' => 'required|integer|max:100',
        ]);

        if ($validator->fails()) {
            return $this->response(422, [], '', $validator->errors(), [], false);
        }

        $input = $request->only(['name', 'seat_number', 'ticket_id', 'room_id', 'status']);
        $seat = $this->seatRepo->create($input);
        return $this->response(200, ['seat' => new SeatResource($seat = $this->seatRepo->find($seat->id))], __('text.register_successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $seat = $this->seatRepo->find($id);

        if (empty($seat)) {
            return $this->response(422, [], __('text.not_found', ['model' => 'Seat']), [], null, false);
        }

        return $this->response(200, ['seat' => new SeatResource($seat)], __('text.retrieved_successfully'), [], null, true);
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
            'name' => 'required|string|max:100',
            'seat_number' => 'required|integer|max:100',
            'status' => 'required|integer|max:100',
            'ticket_id' => 'required|integer|max:100',
            'room_id' => 'required|integer|max:100',
        ]);

        if ($validator->fails()) {
            return $this->response(422, [], '', $validator->errors(), [], false);
        }

        $input = $request->only(['name', 'seat_number', 'ticket_id', 'room_id', 'status']);

        if (empty($this->seatRepo->find($id))) {
            return $this->response(404, [], __('text.not_found', ['model' => 'Seat']), [], false);
        }

        $seat = $this->seatRepo->update($input, $id);
        return $this->response(200, ['theater' => new SeatResource($seat)], __('text.update_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $seat = $this->seatRepo->find($id);

        if (empty($seat)) {
            return $this->response(404, [], __('text.delete_not_found'), [], false);
        }

        $this->seatRepo->delete($id);

        return $this->response(200, null, __('text.delete_successfully', ['model' => 'Seat']), [], true);
    }
}
