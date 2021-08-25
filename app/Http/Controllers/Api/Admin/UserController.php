<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Http\Resources\UserCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    private $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $pages = intval($request->size);
        $users = $this->userRepo->customerSearch($request)->paginate($pages);
        return $this->response(200, ['users' => new UserCollection($users)], __('text.retrieved_successfully'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email:rfc,dns',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return $this->response(422, [], '', $validator->errors());
        }

        $input = $request->only(['email', 'name', 'phone']);

        $isRegistered = $this->userRepo->all(['email' => $input['email']]);

        if (count($isRegistered)) {
            return $this->response(422, [], __('text.email_has_been_registered'));
        }

        $password = $request->request->get('password');
        $input['password'] = bcrypt($password);

        $user = $this->userRepo->create($input);

        DB::commit();
        return $this->response(200, ['user' => new UserResource($user)], __('text.register_successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        /** @var User $user */
        $user = $this->userRepo->find($id);

        if (empty($user)) {
            return $this->response(422, [], __('text.this_user_is_invalid'));
        }

        return $this->response(200, ['user' => new UserResource($user)], __('text.retrieved_successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function searchByName(Request $request)
    {
        $pages = intval($request->size);
        $user = $this->userRepo->customerSearch($request)->paginate($pages);;

        if (empty($user)) {
            return $this->response(422, [], __('text.this_user_is_invalid'));
        }

        return $this->response(200, ['user' => new UserResource($user)], __('text.retrieved_successfully'));
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
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        /** @var User $user */
        $user = $this->userRepo->find($id);

        if (empty($user)) {
            return $this->response(422, [], __('text.this_user_is_invalid'));
        }

        $this->userRepo->delete($id);

        return $this->response(200, null, 'User deleted successfully');
    }


    /**
     * {{DOMAIN}}/admin/user/switch-ban-at
     *
     * @return Response
     */
    public function switchBanAt()
    {
        $userDetail = $this->userRepo->find(intval(request()->user_id));
        if (!$userDetail) {
            return $this->response(422, [], __('text.this_user_is_invalid'));
        }

        $reason = request()->reason ? request()->reason : '';

        $userDetail = $this->userRepo->update([
            'ban_at' => empty($userDetail->ban_at) && $reason == 'Banned' ? \Carbon\Carbon::now() : null,
            'status' => $reason
        ], $userDetail->id);

        return $this->response(200, ['user' => new UserResource($userDetail)]);
    }

    /**
     * {{DOMAIN}}/admin/user/switch-delete-at
     *
     * @return Response
     */
    public function switchDeleteAt()
    {
        $userDetail = $this->userRepo->getUserWithTrashById(intval(request()->user_id));
        if (!$userDetail) {
            return $this->response(422, [], __('text.this_user_is_invalid'));
        }

        if (empty($userDetail->deleted_at)) {
            $userDetail->delete();
            return $this->response(200, [], __('text.this_user_has_deleted'));
        }

        $userDetail->restore();
        return $this->response(200, [], __('text.this_user_has_restored'));
    }
}
