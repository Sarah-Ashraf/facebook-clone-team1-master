<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSign_UpRequest;
use App\Http\Requests\CreateValidate_ChangePassRequest;
use App\Http\Requests\Createedit_profileRequest;
use App\Mail\verifyEmail;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
//use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str; //ana gibtha 34an EL Mail kan mo4 4ghal

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'signup', 'verify', 'sendresetpasswordemail', 'confirm_pin', 'resetpassword']]);
    }

    ///////////////////Sign Up//////////////////
    public function signup(CreateSign_UpRequest $request)
    {
        $vcode = Str::random(70);
        //dd($vcode);
        $validate = $request->validate(
            [
                'firstname' => 'string',
                'lastname' => 'string',
                'email' => 'string',
                'password' => 'string',
                'gender' => 'boolean', //1=>female & 0=>male
                'birthdate' => 'date',
                'phone' => 'string', //new row

            ]
        );
        $user = User::create(
            [
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'password' => $request->password,
                'email' => $request->email,
                'gender' => $request->gender,
                'birthdate' => $request->birthdate,
                'vcode' => $vcode,
                'phone' => $request->phone,
            ]
        );

        Mail::to($user)->send(new verifyEmail($user->firstname, $vcode));

        return response()->json(['message' => 'Successfully sign up ,Look at your email inbox'], 201);
    }

    public function verify($code)
    {
        //dd($code);
        $user = User::where('vcode', $code)->first();
        //dd($user);
        if ($user == null) {
            return response()->json(['message' => 'Code Invalid'], 401);
        } else {
            if ($user->email_verified_at == null) {
                $user->update(['email_verified_at' => now()]);

                return view('emailverifiedsuccessfly')->with('name', $user->firstname); //view ishtaghal
            } else {
                return view('Youremailisalreadyverified')->with('name', $user->firstname); //view
            }
        }
    }

    /////////////////////// Change Password ///////////////////////

    public function changepassword(CreateValidate_ChangePassRequest $request)
    {
        $user = auth()->User();
        if (!$user) {
            return response()->json(["error" => "old password is not correct"], 406); // 406 is not acceptable
        } else if ($request->old_password == $request->password) {
            return response()->json(["error" => "Old password is same as new password"], 400); // 200 ok
        } else {
            $user->password = $request->new_password;
            $user->save();
            return response()->json(["success" => "Password Changed Successfully"], 200); // 200 ok
        }
    }
    /////////////////////// End Change Password ///////////////////////



    /////////////////////// Reset Password == Forgetten Password ///////////////////////

    public function sendresetpasswordemail(Request $request)
    {
        $user = DB::table('users')->where('email', $request->email)->first();
        if ($user) {
            $token = mt_rand(000000, 999999);
            DB::table('password_resets')->insert([
                'email' => $request->email,
                'token' => $token,
                'created_at' => Carbon::now(),
            ]);
            $email = $request->email;
            $name = $user->firstname;
            $subject = 'Resetting Password';
            Mail::send(
                'sendrestpassemail',
                ['name' => $user->firstname, 'token' => $token],
                function ($mail) use ($email, $name, $subject) {
                    $mail->from('team3@facebookclone.com');
                    $mail->to($email, $name);
                    $mail->subject($subject);
                }
            );

            return response()->json(['success' => 'Check your email inbox for pin '], 200);
        } else {
            return response()->json(['success' => 'Check your email inbox for pin '], 200);
        }
    }

    public function confirm_pin(Request $request)
    {
        //dd( $request->token);
        $user = DB::table('password_resets')->where('email', $request->email)->where('token', $request->token)->first(); //get()
        if ($user) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false, 'message' => 'invalid pin'], 422);
        }
    }

    //url = POST api/reset_password , new password , email , pin

    public function resetpassword(Request $request)
    {
        $email = DB::table('password_resets')->where('token', $request->token)->where('email', $request->email)->first();
        if ($email) {

            $user = User::where('email', $request->email)->first();
            $user->password = $request->password;
            $user->save();
            //dd($user);
            DB::table('password_resets')->where('email', $request->email)->delete();
            $credentials = $request->only(['email', 'password']);
            if ($token = auth()->attempt($credentials)) {
                return $this->respondWithToken($token);
            } else {
                return response()->json('login failed');
            }
        } else {
            return response()->json(["error" => "Pin is not valid"], 422);
        }
    }

    ///////////////////////End Reset Password///////////////////////




    /////////////////////// Edit Profile///////////////////////

    /*
        User can Edit profile details
        I can update my profile info  Name Phone Birthday
        i think mobile send old data in field and user choice to edit or not
    */
    public function edit_profile(Createedit_profileRequest $request) //Commnt: can user use same mobile with 2 account
    {
        //DB::table('users')->where('phone', request('phone'))->first();
        $user = auth()->User();
         //mosh mi7taga if because user already loged in
            $user->firstname = $request->firstname;
            $user->lastname = $request->lastname;
            $user->phone = $request->phone;
            $user->birthdate = $request->birthdate;
            $user->save();

            return response()->json(['success' => 'Your profile updated successfully'], 200);
       
    }
    ///////////////////////End Edit Profile///////////////////////

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        /*$credentials = $request->only('email', 'password');

         if ($token =  auth()->attempt($credentials)) {
             if (is_null(auth()->user()->email_verified_at)) {
                 return response()->json(['error' => 'Please check your email inbox for verfication email'], 405);
             }

             return $this->respondWithToken($token);
         } else {
             return response()->json(['error' => 'Wrong credintials, Please try to login with a valid e-mail or password'], 401);
         }*/

        $credentials = $request->only(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Wrong credintials, Please try to login with a valid e-mail and password'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }
}
