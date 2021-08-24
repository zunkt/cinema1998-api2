<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\TheaterCollection;
use App\Http\Resources\TheaterResource;
use App\Repositories\MovieRepository;
use App\Repositories\TheaterRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TheaterController extends Controller
{
    private $theaRepo;
    private $movieRepo;

    /**
     * MediaController constructor.
     * @param TheaterRepository $theaRepo
     * @param MovieRepository $movieRepo
     */
    public function __construct(TheaterRepository $theaRepo, MovieRepository $movieRepo)
    {
        $this->theaRepo = $theaRepo;
        $this->movieRepo = $movieRepo;
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
        $theater = $this->theaRepo->theaterSearch($request)->with('room')->paginate($pages);
        return $this->response(200, ['theater' => new TheaterCollection($theater)], __('text.retrieved_successfully'), [], null, true);
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
            'address' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:100',
            'direction' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return $this->response(200, [], '', $validator->errors(), [], false);
        }
        $input = $request->only(['name', 'address', 'phone', 'direction']);

        $theater = $this->theaRepo->create($input);
        return $this->response(200, ['theater' => new TheaterResource($theater = $this->theaRepo->find($theater->id))], __('text.register_successfully'), [], null, true);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $theater = $this->theaRepo->makeModel()->with('room')->find($id);

        if (empty($theater)) {
            return $this->response(200, [], __('text.not_found', ['model' => 'Theater']), [], null, false);
        }

        return $this->response(200, ['theater' => new TheaterResource($theater)], __('text.retrieved_successfully'), [], null, true);
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
            'address' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:100',
            'direction' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return $this->response(200, [], '', $validator->errors(), [], false);
        }

        $input = $request->only(['name', 'address', 'phone', 'direction']);

        if (empty($this->theaRepo->find($id))) {
            return $this->response(200, [], __('text.not_found', ['model' => 'Theater']), [], false);
        }

        $theater = $this->theaRepo->update($input, $id);
        return $this->response(200, ['theater' => new TheaterResource($theater)], __('text.update_successfully'), [], null, true);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $theater = $this->theaRepo->find($id);

        if (empty($theater)) {
            return $this->response(200, [], __('text.delete_not_found'), [], false);
        }

        $this->theaRepo->delete($id);

        return $this->response(200, null, __('text.delete_successfully'), [], true);
    }
}
