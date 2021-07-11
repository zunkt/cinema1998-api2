<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\TicketCollection;
use App\Http\Resources\TicketResource;
use App\Repositories\BillRepository;
use App\Repositories\ScheduleRepository;
use App\Repositories\TheaterRepository;
use App\Repositories\TicketRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    private $ticRepo;
    private $scheRepo;
    private $userRepo;
    private $billRepo;

    /**
     * MediaController constructor.
     * @param TicketRepository $ticRepo
     * @param ScheduleRepository $scheRepo
     * @param UserRepository $userRepo
     * @param BillRepository $billRepo
     */
    public function __construct(TicketRepository $ticRepo, ScheduleRepository $scheRepo, UserRepository $userRepo, BillRepository $billRepo)
    {
        $this->ticRepo = $ticRepo;
        $this->scheRepo = $scheRepo;
        $this->userRepo = $userRepo;
        $this->billRepo = $billRepo;
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
        $ticket = $this->ticRepo->ticketSearch($request)->paginate($pages);
        return $this->response(200, ['ticket' => new TicketCollection($ticket)], __('text.retrieved_successfully'));
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
            'schedule_id' => 'required|int',
            'user_id' => 'required|int',
        ]);

        if ($validator->fails()) {
            return $this->response(422, [], '', $validator->errors());
        }
        $input = $request->only(['name', 'schedule_id', 'user_id']);
        $isExitSche = $this->scheRepo->find($request->schedule_id);
        $isExitUser = $this->userRepo->find($request->user_id);

        if (!$isExitUser) {
            return $this->response(200, [], __('text.not_found', ['model' => 'User Id']), [], null, false);
        }

        if (!$isExitSche) {
            return $this->response(200, [], __('text.not_found', ['model' => 'Sche Id']), [], null, false);
        }

        $checkName = $this->ticRepo->all(['name' => $input['name']]);

        if (count($checkName)) {
            return $this->response(422, [], __('text.has_been_registered', ['model' => 'Name']));
        }

        $ticket = $this->ticRepo->create($input);
        return $this->response(200, ['ticket' => new TicketResource($ticket)], __('text.register_successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $ticket = $this->ticRepo->find($id);

        if (empty($ticket)) {
            return $this->response(200, [], __('text.not_found', ['model' => 'Ticket']), [], false);
        }

        return $this->response(200, ['ticket' => new TicketResource($ticket)], __('text.retrieved_successfully'));
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
            'schedule_id' => 'required|integer',
            'user_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->response(422, [], '', $validator->errors());
        }

        $input = $request->only(['name', 'schedule_id', 'user_id']);

        $ticket = $this->ticRepo->find($id);

        if (empty($ticket)) {
            return $this->response(422, [], __('text.not_found', ['model' => 'Ticket']));
        }

        $ticket = $this->ticRepo->update($input, $id);

        return $this->response(200, ['ticket' => new TicketResource($ticket)], __('text.update_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ticket = $this->ticRepo->find($id);

        if (empty($ticket)) {
            return $this->response(422, [], __('text.not_found', ['model' => 'Ticket']));
        }

        $this->ticRepo->delete($id);

        return $this->response(200, null, __('text.delete_successfully', ['model' => 'Ticket']));
    }
}
