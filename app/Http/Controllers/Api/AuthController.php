<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\resetPassword;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\CoachModel;
use App\ParticipantModel;
use App\AdminModel;
use Validator;

class AuthController extends Controller
{
    public function register(Request $request){
        if($request->has('email_coach')){
            $register_coach = $request->all();
            $validate = Validator::make($register_coach, [
                'nama_coach' => 'required|max:100',
                'email_coach' => 'required|email:rfc,dns|unique:coachs',
                'password' => 'required',
                'telp_coach' => 'required|digits_between:10,12',
                'domisili_coach' => 'required',
                'tgl_lahir_coach' => 'required',
                'jk_coach' => 'required',
            ]);
    
            if($validate->fails())
                return response(['message' => $validate->errors()], 400);
    
            $register_coach['password'] = bcrypt($request->password);
            $coach = CoachModel::create($register_coach);
            return response([
                'message' => 'Register Coach Berhasil',
                'coach' => $coach,
            ], 200);
        } else{
            $register_peserta = $request->all();
            $validate = Validator::make($register_peserta, [
                'nama_peserta' => 'required|max:100',
                'email_peserta' => 'required|email:rfc,dns|unique:participants',
                'password' => 'required',
                'telp_peserta' => 'required|digits_between:10,12',
            ]);
    
            if($validate->fails())
                return response(['message' => $validate->errors()], 400);
    
            $register_peserta['password'] = bcrypt($request->password);
            $peserta = ParticipantModel::create($register_peserta);
            return response([
                'message' => 'Register Peserta Berhasil',
                'peserta' => $peserta,
            ], 200);
        }
    }

    public function login(Request $request){        
        $login_data = $request->all();

        if($request->has('username')) {
            $validate = Validator::make($login_data, [
                'username' => 'required',
                'password' => 'required'
            ]);

            if($validate->fails())
                return response(['message' => $validate->errors()], 400);

            if(!Auth::guard('admin')->attempt($login_data))
                return response(['message' => 'Email atau Password salah'], 401);
                
            $user = Auth::guard('admin')->user();

            $token = $user->createToken('Authentication Token')->accessToken;

            return response([
                'message' => 'Login Admin Berhasil',
                'admin' => $user,
                'token_type' => 'Bearer',
                'access_token' => $token
            ]);
        } else{
            $validate = Validator::make($login_data, [
                'email' => 'required|email:rfc,dns',
                'password' => 'required'
            ]);
    
            if($validate->fails())
                return response(['message' => $validate->errors()], 400);
    
            if(CoachModel::where('email_coach', '=', $login_data['email'])->first() != null){
                $isCoach = true;
                $user_data = [
                    'email_coach' => $login_data['email'],
                    'password' => $login_data['password'],
                ];
    
                if(!Auth::attempt($user_data))
                    return response(['message' => 'Email atau Kata Sandi salah'], 401);
    
                $user = Auth::user();    
            }else {
                $isCoach = false;
                $user_data = [
                    'email_peserta' => $login_data['email'],
                    'password' => $login_data['password'],
                ];
    
                if(!Auth::guard('participant')->attempt($user_data))
                    return response(['message' => 'Email atau Password salah'], 401);
                    
                $user = Auth::guard('participant')->user();
            }
    
            $token = $user->createToken('Authentication Token')->accessToken;
    
            if($isCoach){
                return response([
                    'message' => 'Login Coach Berhasil',
                    'coach' => $user,
                    'token_type' => 'Bearer',
                    'access_token' => $token
                ]);
            }else {
                return response([
                    'message' => 'Login Peserta Berhasil',
                    'participant' => $user,
                    'token_type' => 'Bearer',
                    'access_token' => $token
                ]);
            }        
        }
    }

    public function forgetPassword(Request $request) {
        $data = $request->all();

        $validate = Validator::make($data, [
            'email' => 'required|email:rfc,dns',            
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $coach = CoachModel::where('email_coach', '=', $data['email'])->first();
        $participant = ParticipantModel::where('email_peserta', '=', $data['email'])->first();

        $random = Str::random(5);        
        
        if ($coach != null) {
            $password = $random.'_'.$coach->id;
            $coach->password = bcrypt($password);

            if($coach->save()){
                $coach->setAttribute('password_sementara', $password);
                \Mail::to($data['email'])->send(new \App\Mail\resetPassword($coach));
                return response([
                    'message' => 'Atur Ulang Kata Sandi Berhasil, Silahkan Periksa Emailmu',
                    ],200);
            }
        } else if ($participant != null) {
            $password = $random.'_'.$participant->id;
            $participant->password = bcrypt($password);

            if($participant->save()){
                $participant->setAttribute('password_sementara', $password);
                \Mail::to($data['email'])->send(new \App\Mail\resetPassword($participant));
                return response([
                    'message' => 'Atur Ulang Kata Sandi Berhasil, Silahkan Periksa Emailmu',
                    ],200);
            }
        } else {
            return response([
                'message' => 'Email Tidak Terdaftar Dalam Sistem',  
                ],400);
        }

    }

    public function logout(Request $request){
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Logout Berhasil'
        ]);
    }
}
