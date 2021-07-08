<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\TicketCollection;
use App\Http\Resources\TicketResource;
use App\Repositories\TheaterRepository;
use App\Repositories\TicketRepository;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    private $ticRepo;

    /**
     * MediaController constructor.
     * @param TicketRepository $ticRepo
     */
    public function __construct(TicketRepository $ticRepo)
    {
        $this->ticRepo = $ticRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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
        //
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
            return $this->response(200, [], __('text.is_invalid'), [], null, false);
        }

        return $this->response(200, ['ticket' => new TicketResource($ticket)], __('text.retrieved_successfully'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
