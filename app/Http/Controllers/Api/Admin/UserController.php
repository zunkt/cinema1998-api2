<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    private $userRepo;

    /**
     * UserController constructor.
     * @param UserRepository $userRepo
     */
    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        /** @var User $user */

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'name' => 'required|string|max:100',
            'full_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->response(422, [], '', $validator->errors());
        }

        $input = $request->only(['email', 'name', 'full_name']);

        $user = auth('user')->user();

        if (empty($user)) {
            return $this->response(200, [], __('text.this_user_is_invalid'), [], null, false);
        }

        $user = $this->userRepo->update($input, $user->id);

        return $this->response(200, ['user' => new UserResource($user)], __('text.update_successfully'), [], null, true);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function withdrawal()
    {
        $user = auth('user')->user();

        if (empty($user)) {
            return $this->response(422, [], __('text.this_user_is_invalid'));
        }

        $user = $this->userRepo->update(['withdrawal_at' => Carbon::now()], $user->id);

        auth()->logout();

        return $this->response(200, [], __('text.withdrawal_successfully'));
    }
}
