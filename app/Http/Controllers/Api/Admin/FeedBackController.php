<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\FeedBackCollection;
use App\Http\Resources\FeedBackResource;
use App\Repositories\FeedbackRepository;
use App\Repositories\MovieRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeedBackController extends Controller
{
    private $feedRepo;
    private $movieRepo;

    /**
     * MediaController constructor.
     * @param FeedbackRepository $feedRepo
     * @param MovieRepository $movieRepo
     */
    public function __construct(FeedbackRepository $feedRepo, MovieRepository $movieRepo)
    {
        $this->feedRepo = $feedRepo;
        $this->movieRepo = $movieRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pages = intval($request->size);
        $feedback = $this->feedRepo->feedSearch($request)->paginate($pages);
        return $this->response(200, ['feedback' => new FeedBackCollection($feedback)], __('text.retrieved_successfully'), [], null, true);
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
            'content' => 'required|string|max:100',
            'movie_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return $this->response(200, [], '', $validator->errors(), [], false);
        }
        $input = $request->only(['content', 'movie_id']);

        $isExitMovie = $this->movieRepo->find($request->movie_id);

        if (!$isExitMovie) {
            return $this->response(200, [], __('text.not_found', ['model' => 'Movie Id']), [], null, false);
        }

        $feedback = $this->feedRepo->create($input);
        return $this->response(200, ['feedback' => new FeedBackResource($feedback)], __('text.register_successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $feedback = $this->feedRepo->find($id);

        if (empty($feedback)) {
            return $this->response(200, [], __('text.is_invalid'), [], null, false);
        }

        return $this->response(200, ['feedback' => new FeedBackResource($feedback)], __('text.retrieved_successfully'), [], null, true);
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
            'content' => 'required|string|max:100',
            'movie_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return $this->response(200, [], '', $validator->errors(), [], false);
        }

        $input = $request->only(['content', 'movie_id']);

        if (empty($this->feedRepo->find($id))) {
            return $this->response(200, [], __('text.not_found', ['model' => 'FeedBack']), [], null, false);
        }

        $isExitMovie = $this->movieRepo->find($request->movie_id);
        if (!$isExitMovie) {
            return $this->response(200, [], __('text.not_found', ['model' => 'Movie Id']), [], null, false);
        }

        $feedback = $this->feedRepo->update($input, $id);
        return $this->response(200, ['feedback' => new FeedBackResource($feedback)], __('text.update_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $feedback = $this->feedRepo->find($id);

        if (empty($feedback)) {
            return $this->response(200, [], __('text.delete_not_found'), [], false);
        }

        $this->feedRepo->delete($id);

        return $this->response(200, null, __('text.delete_successfully'));
    }
}
