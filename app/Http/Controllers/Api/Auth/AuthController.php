<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPasswordMail;
use App\Models\Tier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Traits\ApiResponseTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return $this->NotFoundError();
        };

        $tierData = Tier::find($user->tier_id);

        $data = [
            'user' => $user,
            'request_limit' => $tierData->request_limit
        ];

        return $data;
    }


    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', 'min:3'],
            'email' => ['required', 'email', 'unique:users,email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:255', 'confirmed'],
            'type' => 'required',
        ]);


        if ($validator->fails()) {
            return 'invalid data';
        }

        $user = new User();

        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->monthly_number_of_requests = 0;
        $user->tier_id = 1;


        $user->save();

        $token = $user->createToken('auth_token')->plainTextToken;
        $tierData = Tier::find($user->tier_id);


        $data = [
            'access_token' => $token,
            'user' => $user,
        ];


        return $this->apiResponse($data, 'User registered successfully');
    }

    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);


        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors(), 200);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        $tierData = Tier::find($user->tier_id);
        $data = [
            'access_token' => $token,
            'user' => $user,
            'request_limit' => $tierData->request_limit
        ];

        return $this->apiResponse($data, 'User logged successfully');
    }


    //***************** About reset password */
    public function validatePasswordRequest(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        //Check if the user exists
        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors(), 200);
        }
        $user = User::where('email', $request->email)->first();

        //Create Password Reset Token
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => Str::random(),
            'created_at' => Carbon::now()
        ]);
        //Get the token just created above
        $tokenData = DB::table('password_resets')->where('email', $request->email)->first();

        // dd($tokenData);
        Mail::to($user->email)->send(new ForgotPasswordMail($user->name, $tokenData->token));

        // if ($this->getResetPassword($request->email, $tokenData->token)) {
        if ($this->getResetPassword($tokenData->token)) {

            $data = ['msg' => 'message has been send'];

            return $data;
        } else {
            return $this->apiResponse(null, $validator->errors(), 200);
        }
    }

    public function getResetPassword($token)
    {
        $password_reset_data = DB::table('password_resets')->where('token', $token)->first();


        if (!$password_reset_data->token || Carbon::now()->subMinutes(10) > $password_reset_data->created_at) {
            $msg = 'Invalid password reset link or link expired';
            return $msg;
        }

        $confirm_token = compact('token');
        $url = 'http://localhost:4200/auth/reset-password/' . $confirm_token['token'];
        return redirect()->away($url);
    }







    public function resetPassword($token, Request $request)
    {
        $password_reset_data = DB::table('password_resets')->where('token', $token)->first();
        if (!$password_reset_data->token || Carbon::now()->subMinutes(10) > $password_reset_data->created_at) {
            $data = ['msg' => 'Invalid token'];
            return $data;
        } else {

            $user = User::where('email', $password_reset_data->email)->first();
            if ($user->email !== $request->email) {
                $data = ['msg' => 'Enter Correct Email'];
                return $data;
            }

            $user->update([
                'password' => bcrypt($request->password)
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;
            $tierData = Tier::find($user->tier_id);
            $data = [
                'access_token' => $token,
                'user' => $user,
                'request_limit' => $tierData->request_limit
            ];

            DB::table('password_resets')->where('email', $request->email)->delete();

            return $data;
        }
    }
}
