<?php

namespace App\Http\Controllers\Api\User;

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
            'password' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'name' => 'required|string|max:100',
            'full_name' => 'required|string|max:255',
            'token' => 'string',
            'phone' => 'required|string|max:255',
            'is_phone_verified' => 'nullable|boolean',
            'is_verified_email' => 'nullable|boolean',
            'is_token_phone' => 'nullable|boolean',
            'ban_at' => 'nullable',
            'id_social' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->response(422, [], '', $validator->errors());
        }

        $input = $request->only(['phone', 'email', 'name',
            'status', 'is_65', 'is_pregnant', 'is_child', 'ticket', 'no_show',
            'child_age', 'sc_name', 'sc_phone', 'birthday', 'nationality', 'gender']);

        if ($input['phone']) {
            $isRegistered = $this->userRepo->all(['phone' => $input['phone']]);

            if (count($isRegistered)) {
                return $this->response(422, [], __('text.phone_number_has_been_registered'), null, self::PHONE_REGISTERED);
            }
        }

        $user = auth('user')->user();

        if (empty($user)) {
            return $this->response(422, [], __('text.this_user_is_invalid'));
        }

        $user = $this->userRepo->update($input, $user->id);

        return $this->response(200, ['user' => new UserResource($user)], __('text.update_successfully'));
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
