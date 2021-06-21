<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminResource;
use App\Models\Admin;
use App\Repositories\AdminRepository;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use TYPO3\CMS\Reports\Status;

class AuthController extends Controller
{
    private $adminRepo;

    /**
     * Create a new instance.
     *
     * @return void
     */
    public function __construct(AdminRepository $adminRepo)
    {
        $this->middleware('auth:admin', ['except' => ['login', 'sendPasswordResetEmail', 'resetPassword', 'me', 'register']]);
        $this->adminRepo = $adminRepo;
    }

    /**
     * {{DOMAIN}}/admin/auth/login
     *
     * @return JsonResponse
     */
    public function login()
    {
        $validator = Validator::make(request()->all(), [
            'email' => 'required|email',
            'password' => [
                'required',
                'string',
                'min: 8',
                'max: 20',
            ]
        ]);

        if ($validator->fails()) {
            return $this->response(422, [], '', $validator->errors());
        }

        // Validate Password
        // Password
        if (!$this->validateRulePassword(request()->password)) {
            return $this->response(422, [], __('text.password_invalid_rule'));
        }

        $credentials = request(['email', 'password']);

//        dd(!$token = auth()->attempt($credentials));
        if (!$token = auth()->attempt($credentials)) {
            // Validate rule: Incorrect Password type cap :
            // 5 times > cannot use the previous password and password reset email is auto sent.
            $email = trim(request()->email);
            $adminFailed = Admin::where('email', $email)->first();

            if(isset($adminFailed)){
                $adminFailed->increment('failed_login_attempts');

                if($adminFailed->failed_login_attempts > 4){
                    $adminFailed->update(['password' => Hash::make(uniqid())]);

                    $status = Password::sendResetLink(request()->only('email'));
                    $code = $status === Password::RESET_LINK_SENT ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST;

                    return $this->response(422, [], __('text.check_email_reset_pass'));
                }
                return $this->response(422, [], __('auth.password'));
            }
            return $this->response(401, [], __('auth.failed'));
        }

        $admin = Admin::find(auth()->id());

        // Reset failed_login_attempts
        $admin->update(['failed_login_attempts' => 0]);

        return $this->response(200, [
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    /**
     * {{DOMAIN}}/admin/auth/me
     *
     * @return JsonResponse
     */
    public function me()
    {
        $user = auth()->user();

        if (!$user) {
            return $this->response(401, [], __('auth.not_authenticated'));
        }

        return $this->response(200, ['user' => $user]);
    }

    /**
     * {{DOMAIN}}/admin/auth/logout
     *
     * @return JsonResponse
     */
    public function logout()
    {
        Admin::find(auth()->user()->id)->update(['token' => null]);
        auth()->logout();
        return $this->response(200, [], __('auth.successfully_logged_out'));
    }

    /**
     * {{DOMAIN}}/admin/auth/forgot-password
     * @param Request $request
     * @return JsonResponse
     */
    public function sendPasswordResetEmail(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'email' => 'required|email'
        ]);
        if ($validator->fails()) {
            return $this->response(422, [], '', $validator->errors());
        }

        $admin = Admin::where(['email' => $request->email])->first();

        if (!$admin) {
            return $this->response(404, null, __('text.this_user_is_invalid'));
        }

        $status = Password::sendResetLink($request->only('email'));

        $code = $status === Password::RESET_LINK_SENT ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST;

        return response()->json(['message' => __($status)], $code);
    }

    /**
     * {{DOMAIN}}/admin/auth/reset-password
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response(422, [], '', $validator->errors());
        }

        $status = Password::reset(
            $request->only('email', 'password', 'token'),
            function ($user, $password) use ($request) {
                $user->forceFill(['password' => Hash::make($password)])->save();
                $user->setRememberToken(Str::random(60));
                event(new PasswordReset($user));
            }
        );
        $code = $status === Password::PASSWORD_RESET ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST;
        if ($code === Response::HTTP_OK) {
            $admin = Admin::where(['email' => $request->email])->first();
            $admin->update(['failed_login_attempts' => 0]);
        }
        return response()->json(['message' => __($status)], $code);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email:rfc,dns',
            'name' => 'required|string|max:255',
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return $this->response(422, [], '', $validator->errors());
        }

        $input = $request->only(['email', 'name', 'phone', 'full_name']);

        $isRegistered = $this->adminRepo->all(['email' => $input['email']]);

        if (count($isRegistered)) {
            return $this->response(422, [], __('text.email_has_been_registered'));
        }

        $password = $request->request->get('password');
        $input['password'] = bcrypt($password);

        DB::beginTransaction();
        try{
            $admin = $this->adminRepo->create($input);

            $data = [
                'name' => $admin->email,
                'password' => $password
            ];
            $sender = [
                'address' => env('MAIL_FROM_ADDRESS', 'lexus@gmail.com'),
                'name' => env('MAIL_FROM_NAME', 'Lexus CP')
            ];

            Mail::send('email.admin_created', $data, function($message) use ($admin, $sender) {
                $message
                    ->to($admin->email, $admin->name)
                    ->subject("Create Admin account successfully.");
                $message
                    ->from($sender['address'], $sender['name']);
            });

            DB::commit();
            return $this->response(200, ['admin' => new AdminResource($admin)], __('text.register_successfully'));

        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->response(400, [], $th->getMessage());
        }
    }
}
