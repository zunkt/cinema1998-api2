<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\BillCollection;
use App\Http\Resources\BillResource;
use App\Repositories\BillRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BillController extends Controller
{
    private $billRepo;

    /**
     * MediaController constructor.
     * @param BillRepository $billRepo
     */
    public function __construct(BillRepository $billRepo)
    {
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
        $bill = $this->billRepo->billSearch($request)->paginate($pages);
        return $this->response(200, ['bill' => new BillCollection($bill)], __('text.retrieved_successfully'), [], null, true);
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
            'price' => 'required|int',
            'status' => 'nullable|string|max:255',
            'ticket_id' => 'required|integer|max:100',
        ]);

        if ($validator->fails()) {
            return $this->response(200, [], '', $validator->errors(), [], false);
        }
        $input = $request->only(['price', 'status', 'ticket_id']);

        $bill = $this->billRepo->create($input);
        return $this->response(200, ['bill' => new BillResource($this->billRepo->find($bill->id))], __('text.register_successfully'), [], true, true);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $bill = $this->billRepo->find($id);

        if (empty($bill)) {
            return $this->response(200, [], __('text.is_invalid'), [], null, false);
        }

        return $this->response(200, ['bill' => new BillResource($bill)], __('text.retrieved_successfully'));
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
            'price' => 'required|int',
            'status' => 'required|string|max:255',
            'ticket_id' => 'required|integer|max:100',
        ]);

        if ($validator->fails()) {
            return $this->response(422, [], '', $validator->errors(), [], false);
        }
        $input = $request->only(['price', 'status', 'ticket_id']);

        $bill = $this->billRepo->find($id);

        if (empty($bill)) {
            return $this->response(422, [], __('text.not_found', ['model' => 'Bill']), [], false);
        }

        $bill = $this->billRepo->update($input, $id);
        return $this->response(200, ['bill' => new BillResource($bill)], __('text.register_successfully'), [], true, false);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $bill = $this->billRepo->find($id);

        if (empty($bill)) {
            return $this->response(200, [], __('text.delete_not_found'), [], false);
        }

        $this->billRepo->delete($id);

        return $this->response(200, null,  __('text.delete_successfully'));
    }
}
