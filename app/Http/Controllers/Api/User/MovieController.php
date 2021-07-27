<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\MovieCollection;
use App\Http\Resources\MovieResource;
use App\Repositories\MovieRepository;
use App\Repositories\ScheduleRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MovieController extends Controller
{
    private $movieRepo;
    private $scheRepo;

    /**
     * MediaController constructor.
     * @param MovieRepository $movieRepo
     * @param ScheduleRepository $scheRepo
     */
    public function __construct(MovieRepository $movieRepo, ScheduleRepository $scheRepo)
    {
        $this->movieRepo = $movieRepo;
        $this->scheRepo = $scheRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pages = intval($request->size);
        $movie = $this->movieRepo->movieSearch($request)->paginate($pages);
        return $this->response(200, ['movie' => new MovieCollection($movie)], __('text.retrieved_successfully'), [], null, true);
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
            'image' => 'nullable',
            'trailer_url' => 'nullable|string|max:100',
            'director' => 'nullable|string|max:100',
            'language' => 'nullable|string|max:100',
            'actor' => 'nullable|string|max:100',
            'year' => 'nullable|integer',
            'long_time' => 'nullable|integer|max:100',
            'rating' => 'nullable|integer|max:100',
            'descriptionContent' => 'nullable|string|max:100',
            'type' => 'nullable|string|max:100',
            'slot' => 'nullable|integer|max:100',
            'imageText' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return $this->response(422, [], '', $validator->errors());
        }
        $input = $request->only(['name', 'image', 'trailer_url', 'director',
            'language', 'actor', 'year', 'long_time', 'rating', 'descriptionContent', 'type', 'slot', 'imageText']);

        $movie = $this->movieRepo->all()->count();

        $input['image'] = '';
        $input['slot'] = 0;

        $movie = $this->movieRepo->create($input);
        //Validate mine type image
        if ($request->image) {
            $file = request()->file('image');
            //Get mimetype
            $mimeType = $file->getMimeType();

            //Get image mimetype
            list($typeFile) = explode('/', $mimeType);

            //Validate image
            if ($typeFile != 'image') {
                return $this->response(422, null, __('text.only_upload_file_image'));
            }

            //Path save image upload
            $resPathUpload = 'uploads/movie/' . $movie->id . '/' . uniqid();

            //Delete old image
            if (Storage::exists($movie->image)) {
                Storage::delete($movie->image);
            }

            $path = Storage::put($resPathUpload, $file);
            $input['image'] = $path;
        }
        $input['slot'] = $movie->id;
        $movie = $this->movieRepo->update($input, $movie->id);
        return $this->response(200, ['movie' => new MovieResource($movie)], __('text.register_successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $movie = $this->movieRepo->find($id);

        if (empty($movie)) {
            return $this->response(200, [], __('text.not_found', ['model' => 'Movie']), [], null, false);
        }

        return $this->response(200, ['movie' => new MovieResource($movie)], __('text.retrieved_successfully'), [], null, true);
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
            'image' => 'nullable',
            'trailer_url' => 'nullable|string|max:100',
            'director' => 'nullable|string|max:100',
            'language' => 'nullable|string|max:100',
            'actor' => 'nullable|string|max:100',
            'year' => 'nullable|integer',
            'long_time' => 'nullable|integer|max:100',
            'rating' => 'nullable|integer|max:100',
            'descriptionContent' => 'nullable|string|max:100',
            'type' => 'nullable|string|max:100',
            'slot' => 'nullable|integer|max:100',
            'imageText' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return $this->response(422, [], '', $validator->errors());
        }

        $input = $request->only(['name', 'image', 'trailer_url', 'director',
            'language', 'actor', 'year', 'long_time', 'rating', 'descriptionContent', 'type', 'slot', 'imageText']);

        $movie = $this->movieRepo->find($id);

        if (empty($movie)) {
            return $this->response(200, [], __('text.not_found', ['model' => 'Movie']), [], false);
        }

        //Validate mine type image
        if (request()->image && !!$input['image']) {
            $file = request()->file('image');

            //Get mimetype
            $mimeType = $file->getMimeType();

            //Get image mimetype
            list($typeFile) = explode('/', $mimeType);

            //Validate image
            if ($typeFile != 'image') {
                return $this->response(422, null, __('text.only_upload_file_image'));
            }
            //Path save image upload
            $resPathUpload = 'uploads/movie/' . $movie->id . '/' . uniqid();

            //Delete old image
            if (Storage::exists($movie->image)) {
                Storage::delete($movie->image);
            }

            $path = Storage::put($resPathUpload, $file);
            $input['image'] = $path;
        }

        $movie = $this->movieRepo->update($input, $id);
        return $this->response(200, ['movie' => new MovieResource($movie)], __('text.update_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $movie = $this->movieRepo->find($id);

        if (empty($movie)) {
            return $this->response(200, [], __('text.delete_not_found'), [], false);
        }

        $this->movieRepo->delete($id);

        return $this->response(200, null, __('text.delete_successfully', ['model' => 'Movie']));
    }
}
