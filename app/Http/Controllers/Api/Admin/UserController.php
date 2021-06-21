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
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'string|max:20',
            'name' => 'string|max:255',
            'ticket' => 'integer',
            'no_show' => 'integer',
            'is_65' => 'boolean',
            'is_pregnant' => 'boolean',
            'is_child' => 'boolean',
            'child_age' => 'integer',
            'approve' => 'boolean',
            'reject' => 'required_if:approve,=,false'
        ]);

        if ($validator->fails()) {
            return $this->response(422, [], '', $validator->errors());
        }

        $user = $this->userRepo->find($id);

        if (empty($user)) {
            return $this->response(422, [], __('text.this_user_is_invalid'));
        }

        $input = $request->only(['phone', 'name', 'ticket', 'no_show', 'is_65', 'is_pregnant', 'is_child', 'child_age']);

        if (isset($request->approve)) {
            $input['is_approved'] = $request->approve;
            if ($request->approve) {
                $admin = auth()->id();
                $input['approved_by_id'] = $admin;
            } else {
                $input['reject_status'] = $request->reject;
            }
            $input['approved_at'] = Carbon::now();
        }

        $updatedUser = $this->userRepo->update($input, $id);

        return $this->response(200, ['user' => new UserResource($updatedUser)], __('text.update_successfully'));
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
