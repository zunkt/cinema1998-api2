<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Resources\UserResource;
use App\Models\user;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
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

class AuthController extends Controller
{
    /**
     * @var UserRepository
     */
    private $userRepo;

    /**
     * @param UserRepository $userRepo
     */
    public function __construct(UserRepository $userRepo)
    {
        $this->guard = 'user';

        $this->middleware('auth:' . $this->guard, ['except' => ['login', 'sendPasswordResetEmail', 'resetPassword', 'me', 'register']]);

        $this->userRepo = $userRepo;
    }

    /**
     * {{DOMAIN}}/user/auth/login
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

        if (!$token = auth('user')->attempt($credentials)) {
            // Validate rule: Incorrect Password type cap :
            $email = trim(request()->email);
            $userFailed = User::where('email', $email)->first();

            if (isset($userFailed)) {
                $userFailed->increment('failed_login_attempts');

                if ($userFailed->failed_login_attempts > 5) {
                    $userFailed->update(['password' => Hash::make(uniqid())]);

                    $status = Password::sendResetLink(request()->only('email'));
                    $code = $status === Password::RESET_LINK_SENT ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST;

                    return $this->response(422, [], __('text.check_email_reset_pass'));
                }
                return $this->response(422, [], __('auth.password'));
            }
            return $this->response(401, [], __('auth.failed'));
        }
        $user = User::find(auth('user')->id());

        // Reset failed_login_attempts
        $user->update(['failed_login_attempts' => 0]);

        return $this->response(200, [
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('user')->factory()->getTTL() * 60
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = auth('user')->user();

        if (!$user) {
            return $this->response(401, [], __('auth.not_authenticated'));
        }

        return $this->response(200, ['user' => $user]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        User::find(auth('user')->user()->id)->update(['token' => null]);
        auth('user')->logout();
        return $this->response(200, [], __('auth.successfully_logged_out'));
    }

    /**
     * {{DOMAIN}}/user/auth/forgot-password
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

        $user = User::where(['email' => $request->email])->first();

        if (!$user) {
            return $this->response(404, null, __('text.this_user_is_invalid'));
        }

        $status = Password::broker('users')->sendResetLink($request->only('email'));
        $code = $status === Password::RESET_LINK_SENT ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST;

        return response()->json(['message' => __($status)], $code);
    }

    /**
     * {{DOMAIN}}/user/auth/reset-password
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

        $status = Password::broker('users')->reset(
            $request->only('email', 'password', 'token'),
            function ($user, $password) use ($request) {
                $user->forceFill(['password' => Hash::make($password)])->save();
                $user->setRememberToken(Str::random(60));
                event(new PasswordReset($user));
            }
        );
        $code = $status === Password::PASSWORD_RESET ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST;
        if ($code === Response::HTTP_OK) {
            $user = User::where(['email' => $request->email])->first();
            $user->update(['failed_login_attempts' => 0]);
        }
        return response()->json(['message' => __($status)], $code);
    }

    /**
     * {{DOMAIN}}/user/auth/register
     *
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

        $isRegistered = $this->userRepo->all(['email' => $input['email']]);

        if (count($isRegistered)) {
            return $this->response(422, [], __('text.email_has_been_registered'));
        }

        $password = $request->request->get('password');
        $input['password'] = bcrypt($password);

        DB::beginTransaction();
        try{
            $user = $this->userRepo->create($input);

            $data = [
                'name' => $user->email,
                'password' => $password
            ];
            $sender = [
                'address' => env('MAIL_FROM_ADDRESS', 'tiendang212@gmail.com'),
                'name' => env('MAIL_FROM_NAME', 'Deal Cp')
            ];

            Mail::send('email.user_created', $data, function($message) use ($user, $sender) {
                $message
                    ->to($user->email, $user->name)
                    ->subject("Create User account successfully.");
                $message
                    ->from($sender['address'], $sender['name']);
            });

            DB::commit();
            return $this->response(200, ['user' => new UserResource($user)], __('text.register_successfully'));

        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->response(400, [], $th->getMessage());
        }
    }
}
