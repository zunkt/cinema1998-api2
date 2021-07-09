<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\FaqCollection;
use App\Http\Resources\FaqResource;
use App\Repositories\FaqRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FaqController extends Controller
{
    private $faqRepo;

    /**
     * MediaController constructor.
     * @param FaqRepository $faqRepo
     */
    public function __construct(FaqRepository $faqRepo)
    {
        $this->faqRepo = $faqRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pages = intval($request->size);
        $faq = $this->faqRepo->faqSearch($request)->paginate($pages);
        return $this->response(200, ['faqs' => new FaqCollection($faq)], __('text.retrieved_successfully'), [], null, true);
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
            'question' => 'required|string|max:100',
            'answer' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return $this->response(422, [], '', $validator->errors());
        }
        $input = $request->only(['question', 'answer']);

        $faq = $this->faqRepo->create($input);
        return $this->response(200, ['faq' => new FaqResource($faq)], __('text.register_successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $faq = $this->faqRepo->find($id);

        if (empty($faq)) {
            return $this->response(200, [], __('text.is_invalid'), [], null, false);
        }

        return $this->response(200, ['faq' => new FaqResource($faq)], __('text.retrieved_successfully'), [], null, true);
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
            'question' => 'required|string|max:100',
            'answer' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return $this->response(422, [], '', $validator->errors(), [], false);
        }

        $input = $request->only(['question', 'answer']);

        if (empty($this->faqRepo->find($id))) {
            return $this->response(404, [], __('text.not_found', ['model' => 'Faq']), [], false);
        }

        $faq = $this->faqRepo->update($input, $id);
        return $this->response(200, ['faq' => new FaqResource($faq)], __('text.update_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $faq = $this->faqRepo->find($id);

        if (empty($faq)) {
            return $this->response(200, [], __('text.delete_not_found'), [], false);
        }

        $this->faqRepo->delete($id);

        return $this->response(200, null, __('text.delete_successfully'));
    }
}
