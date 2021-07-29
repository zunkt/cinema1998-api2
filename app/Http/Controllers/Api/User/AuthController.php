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
            return $this->response(200, [], '', $validator->errors(), [], null, false);
        }

        // Validate Password
        // Password
        if (!$this->validateRulePassword(request()->password)) {
            return $this->response(200, [], __('text.password_invalid_rule'), [], null, false);
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

                    return $this->response(200, [], __('text.check_email_reset_pass'), [], null, false);
                }
                return $this->response(200, [], __('auth.password'), [], null, false);
            }
            return $this->response(200, [], __('auth.failed'), [], null, false);
        }
        $user = User::find(auth('user')->id());

        // Reset failed_login_attempts
        $user->update(['failed_login_attempts' => 0]);

        return $this->response(200, [
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('user')->factory()->getTTL() * 60
        ], '', [], null, 'true');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = auth('user')->user();

        if (!$user) {
            return $this->response(200, [], __('auth.not_authenticated'), [], null, false);
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
            return $this->response(200, [], '', $validator->errors(), [],  false);
        }

        $user = User::where(['email' => $request->email])->first();

        if (!$user) {
            return $this->response(200, null, __('text.this_user_is_invalid'), [], null, false);
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
            return $this->response(200, [], '', $validator->errors(), [], false);
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
            'full_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'identityNumber' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->response(200, [], '', $validator->errors(), [], false);
        }

        $input = $request->only(['email', 'full_name', 'address', 'identityNumber']);

        $isRegistered = $this->userRepo->all(['email' => $input['email']]);

        if (count($isRegistered)) {
            return $this->response(200, [], __('text.email_has_been_registered'), [], null, false);
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
                'name' => env('MAIL_FROM_NAME', 'Couple Cinema 1998')
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
            return $this->response(200, [], $th->getMessage(), [], null, false);
        }
    }
}
