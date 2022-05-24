<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Validator;
use App\CoachModel;
use App\BankAccountModel;
use App\AchievementModel;


class CoachController extends Controller
{
    public function getAllCoach(){        
        $coachs = CoachModel::where('is_verified', '=', 1)
        ->get([
            'id', 'nama_coach', 'email_coach', 'domisili_coach', 
            'telp_coach', 'jk_coach', 'tgl_lahir_coach', 'desc_coach',
            'total_rate', 'foto_profil_coach', 'is_verified',
            'id_bank_account AS akun_bank', 'created_at', 'updated_at', 
        ]);

        if(count($coachs) > 0){
            return response ([
                'message' => 'Tampil Semua Data Coach Berhasil',
                'data_coachs' => $coachs
            ], 200);
        }

        return response ([
            'message' => 'Data Coach Belum Tersedia'
        ], 404);
    }

    public function getCoachById($id){
        $coach = CoachModel::find($id, [
            'id', 'nama_coach', 'email_coach', 'domisili_coach', 
            'telp_coach', 'jk_coach', 'tgl_lahir_coach', 'desc_coach',
            'total_rate', 'foto_profil_coach', 'is_verified',
            'id_bank_account AS akun_bank', 'created_at', 'updated_at', 
        ]);
        $bank_account = BankAccountModel::find($coach->akun_bank, [
            'id as no_rek', 'an_no_rek', 'nama_bank', 'created_at',
            'updated_at', 'deleted_at'
        ]);

        $coach->akun_bank = $bank_account;

        if(!is_null($coach)){
            return response([
                'message' => 'Tampil Data Coach Berhasil',
                'data_coach' => $coach
            ], 200);
        }

        return response ([
            'message' => 'Data Coach Tidak Ditemukan'
        ], 404);
    }

    public function getAllAchievementByIdCoach($id){
        $coach = CoachModel::find($id);

        if(is_null($coach)){
            return response([
                'message' => 'Data Coach Tidak Ditemukan',
            ], 404);
        }

        if(AchievementModel::where('id_coach', '=', $id)->get() != null){
            $achievements = AchievementModel::where('id_coach', '=', $id)->get(['id',
                'nama_prestasi', 'gambar_prestasi', 'created_at', 'updated_at'
                ,'deleted_at']);
            return response ([
                'message' => 'Tampil Semua Data Prestasi Coach Berhasil',
                'data_coach' => ['id' => $coach->id, 
                    'nama_coach' => $coach->nama_coach,
                    'daftar_prestasi' => $achievements
                ],                
            ], 200);
        }

        return response ([
            'message' => 'Data Prestasi Coach Belum Tersedia'
        ], 404);
    }

    public function getReportListCoachBasedOnRegisterDate($dtF, $dtL) {
        $start = date($dtF);
        $end = date($dtL);

        $coachs = CoachModel::where('is_verified', '=', 1)
        ->whereBetween('created_at', [$start, $end])        
        ->get([
            'id', 'nama_coach', 'email_coach', 'telp_coach',
            'total_rate', 'created_at',            
        ]);        

        if(count($coachs) > 0){

            for ($i=0; $i < $coachs->count(); $i++) { 
                $count_class = DB::table('coaching_clinics')
                    ->selectRaw('ifnull(count(id), 0) as jmlClass')
                    ->where('deleted_at', '=', null)
                    ->where('id_coach', '=', $coachs[$i]->id)
                    ->first();

                $laporan[$i] = array( 
                    "no" => $i+1,               
                    "terdaftar" => $coachs[$i]->created_at,
                    "nama" => $coachs[$i]->nama_coach,
                    "email" => $coachs[$i]->email_coach,
                    "telp" => $coachs[$i]->telp_coach,
                    "total_rate" => $coachs[$i]->total_rate,
                    "jml_coaching_clinic" => $count_class->jmlClass,
                );
        }

            return response ([
                'message' => 'Tampil Laporan Semua Data Coach Berhasil',
                'data_coachs' => $laporan
            ], 200);
        }

        return response ([
            'message' => 'Data Coach Belum Tersedia',
            'data_coachs' => []
        ], 200);
    }

    public function updateCoach(Request $request, $id){
        $coach = CoachModel::find($id, [
            'id', 'nama_coach', 'email_coach', 'domisili_coach', 
            'telp_coach', 'jk_coach', 'tgl_lahir_coach', 'desc_coach',
            'total_rate', 'foto_profil_coach', 'is_verified',
            'id_bank_account', 'created_at', 'updated_at', 
        ]);

        if(is_null($coach)){
            return response([
                'message' => 'Data Coach Tidak Ditemukan',
            ], 404);
        }

        $update_data = $request->all();

        if($request->has('foto_profil_coach')){
            $validate = Validator::make($update_data, [                
                'foto_profil_coach' => 'required|file|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            if($validate->fails())
                return response(['message' => $validate->errors()],400);

            $img = $request->file('foto_profil_coach');
            $img_name = $img->getClientOriginalName();
            $img->move(public_path('images'), $img_name);
            
            $coach->foto_profil_coach = $img_name;

            $coach->save();
    
            return response([
                'message' => 'Perbaharui Data Foto Profil Coach Berhasil',
                'data_coach' => $coach,                
            ],200);            
        }else {
            $validate = Validator::make($update_data, [
                'nama_coach' => 'required|max:100',
                'domisili_coach' => 'required|required',
                'telp_coach' => 'required|digits_between:10,12',
                'jk_coach' => 'required',
                'tgl_lahir_coach' => 'required',
                'no_rek' => 'required|numeric',
                'an_no_rek' => 'required|max:100',
                'nama_bank' => 'required',
            ]);
    
            if($validate->fails())
                return response(['message' => $validate->errors()],400);
    
            if($coach->id_bank_account == null) {
                if(BankAccountModel::where('id', '=', $update_data['no_rek'])->first() != null){
                    return response(['message' => 'No rekening sudah terpakai'],400);
                }
    
                $create_data = [
                    'id' => $update_data['no_rek'],
                    'id_coach' => $coach->id,
                    'an_no_rek' => $update_data['an_no_rek'],
                    'nama_bank' => $update_data['nama_bank'],
                ];
                $bank_account = BankAccountModel::create($create_data);
                $coach->id_bank_account = $bank_account->id;
            }else {
                $bank_account = BankAccountModel::find($coach->id_bank_account);
    
                if($bank_account->id != $update_data['no_rek']){
                    if(BankAccountModel::where('id', '=', $update_data['no_rek'])->first() != null){
                        return response(['message' => 'No rekening sudah terpakai'],400);
                    }
                    $bank_account->id = $update_data['no_rek'];
                    $coach->id_bank_account = $bank_account->id;
                }
        
                $bank_account->an_no_rek = $update_data['an_no_rek'];
                $bank_account->nama_bank = $update_data['nama_bank'];
                $bank_account->save();
            }            
    
            $coach->nama_coach = $update_data['nama_coach'];
            $coach->domisili_coach = $update_data['domisili_coach'];
            $coach->telp_coach = $update_data['telp_coach'];
            $coach->jk_coach = $update_data['jk_coach'];
            $coach->tgl_lahir_coach = $update_data['tgl_lahir_coach'];
            $coach->desc_coach = $update_data['desc_coach'];
    
            $coach->save();
    
            return response([
                'message' => 'Perbaharui Data Coach Berhasil',
                'data_coach' => $coach,                
            ],200);
        }        
    }

    public function changePassword(Request $request){
        $coach = Auth::user();

        if(is_null($coach)){
            return response([
                'message' => 'Data Coach Tidak Ditemukan',                
            ],404);
        }
 
        $update_data = $request->all();

        // $out = new \Symfony\Component\Console\Output\ConsoleOutput();
        // $out->writeln('new');

        // $old_pass_hash = bcrypt($update_data['old_pass']);        
        if (Hash::check($update_data['old_pass'], $coach->password)) {
            $validate = Validator::make($update_data, [
                'new_pass' => 'required',
                'confirm_new_pass' => 'required',                
            ]);
        } else {
            return response(['message' => 'Kata sandi lama salah'],400);
        }        
 
        if($validate->fails())
            return response(['message' => $validate->errors()],400);

        if($update_data['new_pass'] != $update_data['confirm_new_pass']) {
            return response(['message' => 'Konfirmasi kata sandi baru dengan kata sandi baru berbeda'],400);
        }
  
        $coach->password = bcrypt($update_data['new_pass']);       
 
        if($coach->save()){
            return response([
                'message' => 'Perbaharui Data Password Coach Berhasil',                
                ],200);
        }
 
        return response([
            'message' => 'Perbaharui Data Password Coach Gagal',            
            ],400);
    }

    public function addAchievement(Request $request){
        $add_data = $request->all();
        $validate = Validator::make($add_data, [
            'nama_prestasi' => 'required|max:100',
            'id_coach' => 'required',
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);

        $achievement = AchievementModel::create($add_data);
        return response([
            'message' => 'Tambah Data Prestasi Berhasil',
            'data_achievement' => $achievement,
        ], 200);
    }

    public function updateAchievement(Request $request, $id){
        $achievement = AchievementModel::find($id);

        if(is_null($achievement)){
            return response([
                'message' => 'Data Prestasi Tidak Ditemukan',
            ],404);
        }

        $update_data = $request->all();

        if ($request->has('gambar_prestasi')) {
            $validate = Validator::make($update_data, [
                'nama_prestasi' => 'required|max:100',
                'gambar_prestasi' => 'required|file|image|mimes:jpeg,png,jpg|max:2048',
            ]);
            
            if($validate->fails())
                return response(['message' => $validate->errors()],400);

            $img = $request->file('gambar_prestasi');
            $img_name = $img->getClientOriginalName();
            $img->move(public_path('images'), $img_name);
            
            $achievement->gambar_prestasi = $img_name;
        } else {
            $validate = Validator::make($update_data, [
                'nama_prestasi' => 'required|max:100',
            ]);
    
            if($validate->fails())
                return response(['message' => $validate->errors()],400);
        }

        $achievement->nama_prestasi = $update_data['nama_prestasi'];        

        if($achievement->save()){
            return response([
                'message' => 'Perbaharui Data Prestasi Berhasil',
                'data_achievement' => $achievement,
            ], 200);
        }

        return response([
            'message' => 'Perbaharui Data Prestasi Gagal',
        ],400);
    }

    public function deleteAchievement($id){
        $achievement = AchievementModel::find($id);

        if(is_null($achievement)){
            return response([
                'message' => 'Data Prestasi Tidak Ditemukan',
            ],404);
        }

        if($achievement->delete()){
            return response([
                'message' => 'Hapus Data Prestasi Berhasil',
                'data_achievement' => $achievement,
            ],200);
        }
        
        return response([
            'message' => 'Hapus Data Prestasi Gagal',
        ],400);
    }
}
