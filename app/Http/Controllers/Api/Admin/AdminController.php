<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminCollection;
use App\Http\Resources\AdminResource;
use App\Repositories\AdminRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    private $adminRepo;

    /**
     * UserController constructor.
     *
     * @param AdminRepository $adminRepo
     */
    public function __construct(AdminRepository $adminRepo)
    {
        $this->adminRepo = $adminRepo;
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
        $admins = $this->adminRepo->adminSearch($request)->paginate($pages);
        return $this->response(200, ['admins' => new AdminCollection($admins)], __('text.retrieved_successfully'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'email:rfc,dns',
            'name' => 'string|max:255',
            'phone' => 'string|max:20',
            'old_password' => ['nullable', 'min: 8', 'max: 20'],
            'password' => 'nullable|confirmed|min:8|max:20',
        ]);

        if ($validator->fails()) {
            return $this->response(422, [], '', $validator->errors());
        }

        $admin = $this->adminRepo->find($id);

        if (empty($admin)) {
            return $this->response(422, [], __('text.not_found'));
        }

        // Validate Password
        $oldPasword = trim($request->old_password);
        if (!empty($oldPasword)) {
            if(!Hash::check($oldPasword, $admin->password)){
                return $this->response(422, [], __('text.old_password_is_incorrect'));
            }

            if (!$this->validateRulePassword($oldPasword)) {
                return $this->response(422, [], __('text.password_invalid_rule'));
            }
        }

        $newPasword = trim($request->password);
        if (!empty($newPasword)) {
            if (!$this->validateRulePassword(request()->password)) {
                return $this->response(422, [], __('text.password_invalid_rule'));
            }
        }

        $input = $request->only(['email', 'name', 'phone']);

        $pasword = trim($request->password);
        $input['password'] = empty($pasword) ? $admin->password : Hash::make($pasword);

        $updatedAdmin = $this->adminRepo->update($input, $id);

        return $this->response(200, ['admin' => new AdminResource($updatedAdmin)], __('text.update_successfully'));
    }

    /**
     * {{DOMAIN}}/admin/admin/switch-ban-at
     *
     * @return JsonResponse
     */
    public function switchBanAt(Request $request)
    {
        $admin = $this->adminRepo->find($request->admin_id);
        if(!$admin){
            return $this->response(422, [], __('text.not_found', ['model' => 'Admin']));
        }

        $admin = $this->adminRepo->update([
            'ban_at' => empty($admin->ban_at) ? \Carbon\Carbon::now() : null,
        ], $admin->id);

        return $this->response(200, ['admin' => new AdminResource($admin)]);
    }
}
