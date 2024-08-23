<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\View\View;
use App\Models\User;
use App\Mail\OTPMail;
use App\Helper\JWTToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    function LoginPage():View{
        return view('pages.auth.login-page');
    }

    function RegistrationPage():View{
        return view('pages.auth.registration-page');
    }

    function SendOtpPage():View{
        return view('pages.auth.send-otp-page');
    }

    function VerifyOTPPage():View{
        return view('pages.auth.verify-otp-page');
    }

    function ResetPasswordPage():View{
        return view('pages.auth.reset-pass-page');
    }

    function ProfilePage():View{
        return view('pages.dashboard.profile-page');
    }


    // API
    function userRegistration(Request $request){
        try{
            User::create([
                'firstName' => $request->input('firstName'),
                'lastName'  => $request->input('lastName'),
                'email'     => $request->input('email'),
                'mobile'    => $request->input('mobile'),
                'password'  => $request->input('password'),
            ]);
            return response()->json([
                'status'=>'success',
                'message'=>'User Created Successfully'
            ]);
        }catch (Exception $e){
            return response()->json([
                'status'=>'failed',
                'message'=>'Something Went Wrong, Please Try Again'
            ]);
        }
    }

    function userLogin(Request $request)
    {
        $count=User::where('email','=',$request->input('email'))
            ->where('password','=',$request->input('password'))
            ->select('id')->first();

        if($count!==null){
            // User Login-> JWT Token Issue
            $token=JWTToken::CreateToken($request->input('email'),$count->id);
            return response()->json([
                'status' => 'success',
                'message' => 'User Login Successful'
            ],200)->cookie('token',$token,time()+60*24*30);
        }
        else{
            return response()->json([
                'status' => 'failed',
                'message' => 'unauthorized'
            ],401);

        }
    }

    function SendOTPCode(Request $request)
    {
        $email = $request->input('email');
        $otp = rand(100000, 999999);
        $count = User::where('email','=',$email)->count();
        if($count==1){
            // OTP Email send email
            Mail::to($email)->send(new OTPMail($otp));
            // OTP Update User table
            User::where('email','=',$email)->update(['OTP'=>$otp]);
            return response()->json([
                'status'=>'success',
                'message'=>'6 Digit OTP code has been sent successfully'
            ]);
        }else{
            return response()->json([
               'status'=>'failed',
               'message'=>'Unauthorized eMail'
            ], status: 401);
        }
    }

    function verifyOTP(Request $request)
    {
        //
        $email = $request->input('email');
        $otp = $request->input('OTP');
        $count = User::where('email','=',$email)->where('OTP','=',$otp)->count();
        if($count==1){
            // Database OTP Update
            User::where('email','=',$email)->update(['OTP'=>0]);
            // Password reset token issue
            $token=JWTToken::CreateTokenForSetPassword($request->input('email'));
            return response()->json([
               'status'=>'success',
               'message'=>'OTP Verified Successfully',
            ], status: 200)->cookie('token', $token, 60*24*30);
        }else{
            return response()->json([
                'status'=>'failed',
                'message'=>'Invalid OTP'
            ], status: 401);
        }
    }

    function resetPassword(Request $request){
        try{
            $email=$request->header('email');
            $password=$request->input('password');
            User::where('email','=',$email)->update(['password'=>$password]);
            return response()->json([
                'status' => 'success',
                'message' => 'Password Reset Successfully'
            ],200);

        }catch (Exception $exception){
            return response()->json([
                'status' => 'fail',
                'message' => 'Something Went Wrong, Please Try Again'
            ],401);
        }
    }

    function UserProfile(Request $request){
        $email=$request->header('email');
        $user=User::where('email','=',$email)->first();
        return response()->json([
            'status' => 'success',
            'message' => 'Request Successful',
            'data' => $user
        ],200);
    }

    function UpdateProfile(Request $request){
        try{
            $email=$request->header('email');
            $firstName=$request->input('firstName');
            $lastName=$request->input('lastName');
            $mobile=$request->input('mobile');
            $password=$request->input('password');
            User::where('email','=',$email)->update([
                'firstName'=>$firstName,
                'lastName'=>$lastName,
                'mobile'=>$mobile,
                'password'=>$password
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Request Successful',
            ],200);

        }catch (Exception $exception){
            return response()->json([
                'status' => 'fail',
                'message' => 'Something Went Wrong',
            ],200);
        }
    }

    function userLogout(){
        return redirect('/userLogin')->cookie('token','',-1);
    }
}
