<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Validator;
use App\ParticipantModel;

class ParticipantController extends Controller
{
    public function getAllParticipant(){
        $participants = ParticipantModel::all();

        if(count($participants) > 0){
            return response ([
                'message' => 'Tampil Semua Data Peserta Berhasil',
                'data_particapants' => $participants
            ], 200);
        }

        return response ([
            'message' => 'Data Peserta Belum Tersedia'
        ], 404);
    }

    public function getParticipantById($id){
        $participant = ParticipantModel::find($id);

        if(!is_null($participant)){
            return response([
                'message' => 'Tampil Data Peserta Berhasil',
                'data_participant' => $participant
            ], 200);
        }

        return response ([
            'message' => 'Data Peserta Tidak Ditemukan'
        ], 404);
    }

    public function getAllParticipantByIdSchedule($id){

    }

    public function getReportListParticipantBasedOnRegisterDate($dtF, $dtL) {
        $start = date($dtF);
        $end = date($dtL);

        $participants = ParticipantModel::whereBetween('created_at', [$start, $end])
        ->get([
            'id', 'nama_peserta', 'email_peserta', 'telp_peserta',
            'created_at',            
        ]);        

        if(count($participants) > 0){
            $coaching_clinics = DB::table('coaching_clinics')
                ->where('deleted_at', '=', null)->get();

            for ($i=0; $i < $participants->count(); $i++) { 
                $count_transactions = DB::table('payments')
                    ->selectRaw('ifnull(count(id), 0) as jmlTransaksi')
                    ->where('is_confirmed', '=', 1)
                    ->where('id_peserta', '=', $participants[$i]->id)
                    ->first();

                $temp = 0;

                for ($j=0; $j < $coaching_clinics->count(); $j++) { 
                    $count = DB::table('my_coaching_clinics')
                        ->select('id')
                        ->where('id_coaching_clinic', '=', $coaching_clinics[$j]->id)
                        ->where('id_peserta', '=', $participants[$i]->id)
                        ->first();
                    
                    if (!is_null($count)) {
                        $temp = $temp + 1;
                    }                    
                }

                $laporan[$i] = array( 
                    "no" => $i+1,               
                    "terdaftar" => $participants[$i]->created_at,
                    "nama" => $participants[$i]->nama_peserta,
                    "email" => $participants[$i]->email_peserta,
                    "telp" => $participants[$i]->telp_peserta,                    
                    "jml_transaksi" => $count_transactions->jmlTransaksi,
                    "jml_kelas" => $temp,
                );
            }

            return response ([
                'message' => 'Tampil Laporan Semua Data Peserta Berhasil',
                'data_participants' => $laporan
            ], 200);
        }

        return response ([
            'message' => 'Data Coach Belum Tersedia',
            'data_participants' => []
        ], 200);
    }

    public function updateParticipant(Request $request, $id){
        $participant = ParticipantModel::find($id);

        if(is_null($participant)){
            return response([
                'message' => 'Data Peserta Tidak Ditemukan',
            ], 404);
        }

        $update_data = $request->all();
        // !is_string($request->input('foto_profil_peserta')) && 
        if (!is_string($request->input('foto_profil_peserta'))){
            $validate = Validator::make($update_data, [                
                'nama_peserta' => 'required|max:100',
                'telp_peserta' => 'required|digits_between:10,12',
                'foto_profil_peserta' => 'required|file|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            if($validate->fails())
                return response(['message' => $validate->errors()],400);

            $img = $request->file('foto_profil_peserta');
            $img_name = $img->getClientOriginalName();
            $img->move(public_path('images'), $img_name);
            
            $participant->foto_profil_peserta = $img_name;
        } else {
            $validate = Validator::make($update_data, [                
                'nama_peserta' => 'required|max:100',
                'telp_peserta' => 'required|digits_between:10,12',
            ]);
    
            if($validate->fails())
                return response(['message' => $validate->errors()],400);
        }        

        $participant->nama_peserta = $update_data['nama_peserta'];
        $participant->telp_peserta = $update_data['telp_peserta'];        

        $participant->save();

        return response([
            'message' => 'Perbaharui Data Peserta Berhasil',
            'data_participant' => $participant,                
        ],200);
    }

    public function changePassword(Request $request){
        $participant = Auth::user();

        if(is_null($participant)){
            return response([
                'message' => 'Data Peserta Tidak Ditemukan'             
            ],404);
        }
 
        $update_data = $request->all();
        if (Hash::check($update_data['old_pass'], $participant->password)) {
            $validate = Validator::make($update_data, [
                'new_pass' => 'required',
                'confirm_new_pass' => 'required',                
            ]);
        } else {
            return response(['message' => 'Password lama salah'],400);
        }        
 
        if($validate->fails())
            return response(['message' => $validate->errors()],400);

        if($update_data['new_pass'] != $update_data['confirm_new_pass']) {
            return response(['message' => 'Konfirmasi Password Baru Dengan Password Baru Berbeda'],400);
        }
  
        $participant->password = bcrypt($update_data['new_pass']);       
 
        if($participant->save()){
            return response([
                'message' => 'Perbaharui Data Password Peserta Berhasil',                
                ],200);
        }
 
        return response([
            'message' => 'Perbaharui Data Password Peserta Gagal',            
            ],400);
    }
}
