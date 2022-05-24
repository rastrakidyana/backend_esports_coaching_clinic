<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\RegisterCoachingClinicModel;
use App\ParticipantModel;
use App\CoachModel;
use App\OtherParticipantModel;
use App\CoachingClinicModel;
use App\ScheduleCoachingClinicModel;
use Validator;

class RegisterCoachingClinicController extends Controller
{
    public function getAllRegisterCoachingClinicByIdParticipant($id){
        $participant = ParticipantModel::where('id', '=', $id)->first([
            'id', 'nama_peserta'
        ]);

        if(!is_null($participant)){                        
            $coachs = CoachModel::get(['id', 'nama_coach']);

            $show_coachs = [];            

            $registers = RegisterCoachingClinicModel::join('coaching_clinics', 'coaching_clinics.id', '=', 'register_coaching_clinics.id_coaching_clinic')
                ->join('schedule_coaching_clinics', 'schedule_coaching_clinics.id', '=', 'register_coaching_clinics.id_jadwal_coaching_clinic')
                ->join('coachs', 'coachs.id', '=', 'coaching_clinics.id_coach')
                ->select('register_coaching_clinics.id', 'register_coaching_clinics.id_peserta', 'register_coaching_clinics.id_my_coaching_clinic', 'register_coaching_clinics.id_coaching_clinic', 
                    'register_coaching_clinics.id_jadwal_coaching_clinic', 'register_coaching_clinics.id_pembayaran', 'register_coaching_clinics.jml_beli_kuota', 'register_coaching_clinics.harga_pendaftaran',
                    'register_coaching_clinics.status_pendaftaran', 'register_coaching_clinics.is_my', 'coaching_clinics.judul_coaching_clinic', 'schedule_coaching_clinics.tgl_coaching_clinic', 
                    'schedule_coaching_clinics.mulai_coaching_clinic', 'schedule_coaching_clinics.berakhir_coaching_clinic', 'coachs.id AS id_coach')
                ->where('id_peserta', '=', $id)
                ->where('id_pembayaran', '=', null)->get();

            for ($i=0; $i < $coachs->count(); $i++) { 
                $dt_registers = [];
                for ($j=0; $j < $registers->count(); $j++) {                    
                    if ($registers[$j]->id_coach == $coachs[$i]->id) {
                        
                        $list_participant = OtherParticipantModel::where('id_peserta', '=', $id)
                        ->where('id_pendaftaran_coaching_clinic', '=', $registers[$j]->id)                    
                        ->get()
                        ->pluck('nama_peserta_tambahan');
                    
                        if($registers[$j]->status_pendaftaran == 'Personal') {
                            $list_participant[] = $participant->nama_peserta;
                        }
                                
                        $registers[$j]->setAttribute('data_participant', $list_participant);
                        $dt_registers[] = $registers[$j];
                    }
                }                
                if ($dt_registers != []) {
                    $coachs[$i]->setAttribute('data_register', $dt_registers);
                    $show_coachs[] = $coachs[$i];                    
                }
            }                                  

            $participant->setAttribute('daftar_register_coaching_clinic', $show_coachs);

            return response ([
                'message' => 'Tampil Data Pendaftaran Coaching Clinic Dari Peserta Berhasil',
                'data_participant' => $participant
            ], 200);
        }

        return response ([
            'message' => 'Data Peserta Belum Tersedia'
        ], 404);

    }

    // public function getAllRegisterCoachingClinicByIdParticipant($id){
    //     $participant = ParticipantModel::where('id', '=', $id)->first([
    //         'id', 'nama_peserta'
    //     ]);

    //     if(!is_null($participant)){            
    //         $coaching_clinics = CoachingClinicModel::join('coachs', 'coachs.id', '=', 'coaching_clinics.id_coach')
    //             ->select('coachs.id AS id_coach', 'coachs.nama_coach', 'coaching_clinics.id', 'coaching_clinics.judul_coaching_clinic', 'coaching_clinics.game_coaching_clinic')
    //             ->get();

    //         $show_coaching_clinics = [];

    //         $registers = RegisterCoachingClinicModel::join('schedule_coaching_clinics', 'schedule_coaching_clinics.id', '=', 'register_coaching_clinics.id_jadwal_coaching_clinic')
    //             ->select('register_coaching_clinics.id', 'register_coaching_clinics.id_peserta', 'register_coaching_clinics.id_my_coaching_clinic', 'register_coaching_clinics.id_coaching_clinic', 
    //                 'register_coaching_clinics.id_jadwal_coaching_clinic', 'register_coaching_clinics.id_pembayaran', 'register_coaching_clinics.jml_beli_kuota', 'register_coaching_clinics.harga_pendaftaran',
    //                 'register_coaching_clinics.status_pendaftaran', 'register_coaching_clinics.is_my', 'schedule_coaching_clinics.tgl_coaching_clinic', 'schedule_coaching_clinics.mulai_coaching_clinic',
    //                 'schedule_coaching_clinics.berakhir_coaching_clinic')
    //             ->where('id_peserta', '=', $id)->get();

    //         for ($i=0; $i < $coaching_clinics->count(); $i++) { 
    //             $dt_registers = [];
    //             for ($j=0; $j < $registers->count(); $j++) {                    
    //                 if ($registers[$j]->id_coaching_clinic == $coaching_clinics[$i]->id) {
                        
    //                     $list_participant = OtherParticipantModel::where('id_peserta', '=', $id)
    //                     ->where('id_pendaftaran_coaching_clinic', '=', $registers[$j]->id)                    
    //                     ->get()
    //                     ->pluck('nama_peserta_tambahan');
                    
    //                     if($registers[$j]->status_pendaftaran == 'Personal') {
    //                         $list_participant[] = $participant->nama_peserta;
    //                     }
                                
    //                     $registers[$j]->setAttribute('data_participant', $list_participant);
    //                     $dt_registers[] = $registers[$j];                        
    //                 }
    //             }                
    //             if ($dt_registers != []) {
    //                 $coaching_clinics[$i]->setAttribute('data_register', $dt_registers);
    //                 $show_coaching_clinics[] = $coaching_clinics[$i];                    
    //             }
    //         }                      

    //         $participant->setAttribute('daftar_register_coaching_clinic', $show_coaching_clinics);

    //         return response ([
    //             'message' => 'Tampil Data Pendaftaran Coaching Clinic Dari Peserta Berhasil',
    //             'data_participant' => $participant
    //         ], 200);
    //     }

    //     return response ([
    //         'message' => 'Data Peserta Belum Tersedia'
    //     ], 404);

    // }    

    public function getRegisterCoachingClinicById($id){        
        $register = RegisterCoachingClinicModel::join('coaching_clinics', 'coaching_clinics.id', 
                '=', 'register_coaching_clinics.id_coaching_clinic')
            ->join('schedule_coaching_clinics', 'schedule_coaching_clinics.id', 
                '=', 'register_coaching_clinics.id_jadwal_coaching_clinic')
            ->select('register_coaching_clinics.id', 'register_coaching_clinics.id_peserta', 'register_coaching_clinics.id_my_coaching_clinic',
                'register_coaching_clinics.id_jadwal_coaching_clinic', 'register_coaching_clinics.id_pembayaran', 'register_coaching_clinics.jml_beli_kuota',
                'register_coaching_clinics.harga_pendaftaran', 'register_coaching_clinics.status_pendaftaran', 'register_coaching_clinics.is_my', 'coaching_clinics.judul_coaching_clinic',
                'schedule_coaching_clinics.tgl_coaching_clinic', 'schedule_coaching_clinics.mulai_coaching_clinic', 'schedule_coaching_clinics.berakhir_coaching_clinic', 
                'schedule_coaching_clinics.kuota_coaching_clinic', 'coaching_clinics.id_coach')
            ->where('register_coaching_clinics.id', '=', $id)->first();

        if(!is_null($register)){                                    
            $list_participant = OtherParticipantModel::where('id_peserta', '=', $register->id_peserta)
                ->where('id_pendaftaran_coaching_clinic', '=', $register->id)                    
                ->get()
                ->pluck('nama_peserta_tambahan');
            
            if($register->status_pendaftaran == 'Personal') {
                $participant = ParticipantModel::find($register->id_peserta);
                $list_participant[] = $participant->nama_peserta;
            }
                        
            $register->setAttribute('data_participant', $list_participant);
            
            return response([
                'message' => 'Tampil Data Pendaftaran Coaching Clinic Berhasil',
                'data_register' => $register
            ], 200);
        }

        return response ([
            'message' => 'Data Pendaftaran Coaching Clinic Tidak Ditemukan'
        ], 404);
    }

    public function addRegisterCoachingClinic(Request $request){
        $add_data_register = $request->only(['jml_beli_kuota', 'harga_pendaftaran',
            'status_pendaftaran', 'id_peserta', 'id_coaching_clinic',
            'id_jadwal_coaching_clinic']);

        $validate = Validator::make($add_data_register, [
            'jml_beli_kuota' => 'required|numeric|max:5',
            'harga_pendaftaran' => 'required|numeric',
            'status_pendaftaran' => 'required',            
            'id_peserta' => 'required',            
            'id_coaching_clinic' => 'required',
            'id_jadwal_coaching_clinic' => 'required',
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);

        $register = RegisterCoachingClinicModel::create($add_data_register);

        $other_participant = [];
        if($add_data_register['jml_beli_kuota'] > 1 || $add_data_register['status_pendaftaran'] == 'Other'){                        
            if($add_data_register['status_pendaftaran'] == 'Other'){
                for ($i = 1; $i <= $add_data_register['jml_beli_kuota']; $i++) {                 
                    $add_data_other_participant = [
                        'id_peserta' => $request->input('id_peserta'),
                        'id_pendaftaran_coaching_clinic' => $register->id,
                        'nama_peserta_tambahan' => $request->input('nama_peserta_'.$i),
                    ];
                    $other_participant[$i] = OtherParticipantModel::create($add_data_other_participant);
                }                                
            } else if($add_data_register['status_pendaftaran'] != 'Other'){
                for ($i = 1; $i <= $add_data_register['jml_beli_kuota']-1; $i++) {                 
                    $add_data_other_participant = [
                        'id_peserta' => $request->input('id_peserta'),
                        'id_pendaftaran_coaching_clinic' => $register->id,
                        'nama_peserta_tambahan' => $request->input('nama_peserta_'.$i),
                    ];
                    $other_participant[$i] = OtherParticipantModel::create($add_data_other_participant);
                }
            }            
        }

        $register->setAttribute('data_other_participant', $other_participant);  

        return response([
            'message' => 'Tambah Data Pendaftaran Coaching Clinic Berhasil',
            'data_register_coaching_clinic' => $register,
        ], 200);
    }

    public function updateRegisterCoachingClinic(Request $request, $id){
        $register = RegisterCoachingClinicModel::find($id);

        if(is_null($register)){
            return response([
                'message' => 'Data Pendaftaran Coaching Clinic Tidak Ditemukan',
            ],404);
        }

        $update_data_register = $request->all();
        $validate = Validator::make($update_data_register, [
            'jml_beli_kuota' => 'required|numeric|max:5',
            'harga_pendaftaran' => 'required|numeric',
            'status_pendaftaran' => 'required',                                   
            'id_coaching_clinic' => 'required',
            'id_jadwal_coaching_clinic' => 'required',            
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()],400);

        $list_participant = OtherParticipantModel::where('id_peserta', '=', $register->id_peserta)
            ->where('id_pendaftaran_coaching_clinic', '=', $register->id)                    
            ->get()
            ->pluck('id');

        if($update_data_register['status_pendaftaran'] == 'Other') {
            if($list_participant->count() > $update_data_register['jml_beli_kuota']){
                $different = $list_participant->count() - $update_data_register['jml_beli_kuota'];
                $count = $list_participant->count();

                for ($i=1; $i <= $different; $i++) { 
                    $id_other = $list_participant[$count-$i];
    
                    $delete_other = OtherParticipantModel::find($id_other);
                    $delete_other->delete();
                }

                for ($i=1; $i <= $count - $different; $i++) { 
                    $id_other = $list_participant[$i-1];
    
                    $update_other = OtherParticipantModel::find($id_other);
                    $update_other->nama_peserta_tambahan = $request->input('nama_peserta_'.$i);
                    $update_other->save();
                }
            } else{
                for ($i=1; $i <= $update_data_register['jml_beli_kuota']; $i++) { 
                    if($i > $list_participant->count()){
                        $add_data_other_participant = [
                            'id_peserta' => $register->id_peserta,
                            'id_pendaftaran_coaching_clinic' => $register->id,
                            'nama_peserta_tambahan' => $request->input('nama_peserta_'.$i),
                        ];
                        OtherParticipantModel::create($add_data_other_participant);
                    }else {
                        $id_other = $list_participant[$i-1];

                        $update_other = OtherParticipantModel::find($id_other);
                        $update_other->nama_peserta_tambahan = $request->input('nama_peserta_'.$i);
                        $update_other->save();
                    }
                }
            }
        } else if($update_data_register['status_pendaftaran'] != 'Other'){
            if($list_participant->count()+1 > $update_data_register['jml_beli_kuota']){                
                $different = $list_participant->count()+1 - $update_data_register['jml_beli_kuota'];
                $count = $list_participant->count();

                for ($i=1; $i <= $different; $i++) { 
                    $id_other = $list_participant[$count-$i];
    
                    $delete_other = OtherParticipantModel::find($id_other);
                    $delete_other->delete();
                }

                for ($i=1; $i <= $count - $different; $i++) { 
                    $id_other = $list_participant[$i-1];
    
                    $update_other = OtherParticipantModel::find($id_other);
                    $update_other->nama_peserta_tambahan = $request->input('nama_peserta_'.$i);
                    $update_other->save();
                }
            } else{
                for ($i=1; $i <= $update_data_register['jml_beli_kuota']-1; $i++) { 
                    if($i > $list_participant->count()){
                        $add_data_other_participant = [
                            'id_peserta' => $register->id_peserta,
                            'id_pendaftaran_coaching_clinic' => $register->id,
                            'nama_peserta_tambahan' => $request->input('nama_peserta_'.$i),
                        ];
                        OtherParticipantModel::create($add_data_other_participant);
                    }else {
                        $id_other = $list_participant[$i-1];

                        $update_other = OtherParticipantModel::find($id_other);
                        $update_other->nama_peserta_tambahan = $request->input('nama_peserta_'.$i);
                        $update_other->save();
                    }
                }
            }
        }

        $register->jml_beli_kuota = $update_data_register['jml_beli_kuota'];
        $register->harga_pendaftaran = $update_data_register['harga_pendaftaran'];
        $register->status_pendaftaran = $update_data_register['status_pendaftaran'];
        $register->id_coaching_clinic = $update_data_register['id_coaching_clinic'];
        $register->id_jadwal_coaching_clinic = $update_data_register['id_jadwal_coaching_clinic'];

        if($register->save()){
            return response([
                'message' => 'Perbaharui Data Pendaftaran Coaching Clinic Berhasil',
                'data_register_coaching_clinic' => $register,
            ], 200);
        }

        return response([
            'message' => 'Perbaharui Data Pendaftaran Coaching Clinic Gagal',
        ],400);

    }

    public function deleteRegisterCoachingClinic($id){
        $register = RegisterCoachingClinicModel::find($id);

        if(is_null($register)){
            return response([
                'message' => 'Data Pendaftaran Coaching Clinic Tidak Ditemukan',
            ],404);
        }

        if($register->delete()){
            $list_participant = OtherParticipantModel::where('id_peserta', '=', $register->id_peserta)
                ->where('id_pendaftaran_coaching_clinic', '=', $register->id)
                ->delete();

            return response([
                'message' => 'Hapus Data Pendaftaran Coaching Clinic Berhasil',
                'data_register_coaching_clinic' => $register,
            ],200);
        }
        
        return response([
            'message' => 'Hapus Data Pendaftaran Coaching Clinic Gagal',
        ],400);
    }
}
