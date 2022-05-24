<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Arr;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\MyCoachingClinicModel;
use App\ParticipantModel;
use App\OtherParticipantModel;
use App\ModuleModel;
use App\CoachModel;
use App\CoachingClinicModel;
use App\RegisterCoachingClinicModel;
use App\ScheduleCoachingClinicModel;

class MyCoachingClinicController extends Controller
{
    public function getMyCoachingClinicById($id){
        // $my = MyCoachingClinicModel::where('id', '=', $id)->get([
        //     'id', 'id_coaching_clinic', 'id_jadwal_coaching_clinic', 'id_peserta', 'id_penilaian_coach', 'status_my_coaching_clinic'
        // ]);
    }

    public function getAllMyCoachingClinicByIdParticipant($id){
        $participant = ParticipantModel::where('id', '=', $id)->first([
            'id', 'nama_peserta'
        ]);

        if(!is_null($participant)) {
            $list_my_coaching_clinics = MyCoachingClinicModel::where('id_peserta', '=', $id)->get([
                'id', 'id_coaching_clinic', 'id_jadwal_coaching_clinic', 'id_peserta', 'id_penilaian_coach', 'status_my_coaching_clinic'
            ]);

            $my_coaching_clinics = [];            
            $count = 0;
            for($i=0; $i < $list_my_coaching_clinics->count(); $i++) { 
                if ($my_coaching_clinics == []) {
                    $my_coaching_clinics[$count] = $list_my_coaching_clinics[$i];
                    $id_my = [];
                    $id_my[] =  $list_my_coaching_clinics[$i]->id;                    

                    $coaching_clinic = CoachingClinicModel::join('coachs', 'coachs.id', '=', 'coaching_clinics.id_coach')
                        ->select('coaching_clinics.id', 'coaching_clinics.id_coach', 'coaching_clinics.judul_coaching_clinic',
                            'coaching_clinics.game_coaching_clinic', 'coaching_clinics.harga_coaching_clinic', 'coaching_clinics.desc_coaching_clinic',
                            'coaching_clinics.tipe_coaching_clinic', 'coaching_clinics.at_coaching_clinic', 'coaching_clinics.link_coaching_clinic',
                            'coaching_clinics.gambar_coaching_clinic', 'coaching_clinics.id', 'coachs.nama_coach', 'coachs.total_rate',
                            'coachs.foto_profil_coach')
                        ->where('coaching_clinics.id', '=', $list_my_coaching_clinics[$i]->id_coaching_clinic)->first();
                    $my_coaching_clinics[$count]->setAttribute('data_coaching_clinic', $coaching_clinic);
                    $my_coaching_clinics[$count]->data_coaching_clinic->setAttribute('id_my', $id_my);

                    $module = ModuleModel::where('id_coaching_clinic', '=', $my_coaching_clinics[$count]->id_coaching_clinic)->get();
                    $my_coaching_clinics[$count]->data_coaching_clinic->setAttribute('data_module', $module);

                    $register = RegisterCoachingClinicModel::join('schedule_coaching_clinics', 'schedule_coaching_clinics.id', 'register_coaching_clinics.id_jadwal_coaching_clinic')
                        ->select('register_coaching_clinics.id', 'register_coaching_clinics.id_my_coaching_clinic', 'register_coaching_clinics.id_jadwal_coaching_clinic',
                            'schedule_coaching_clinics.tgl_coaching_clinic', 'schedule_coaching_clinics.mulai_coaching_clinic', 'schedule_coaching_clinics.berakhir_coaching_clinic',)
                        ->where('register_coaching_clinics.id_my_coaching_clinic', '=', $list_my_coaching_clinics[$i]->id)->first();

                    $register_status = RegisterCoachingClinicModel::select('status_pendaftaran')
                        ->where('id_my_coaching_clinic', '=', $list_my_coaching_clinics[$i]->id)->first();

                    $list_participant = OtherParticipantModel::where('id_peserta', '=', $list_my_coaching_clinics[$i]->id_peserta)
                    ->where('id_pendaftaran_coaching_clinic', '=', $register->id)                    
                    ->get()
                    ->pluck('nama_peserta_tambahan');                    
                
                    if($register_status->status_pendaftaran == 'Personal') {
                        $list_participant[] = $participant->nama_peserta;
                    }
                            
                    $register->setAttribute('data_participant', $list_participant);
                    $dt_register = [];
                    $dt_register[] = $register;
                    $my_coaching_clinics[$count]->data_coaching_clinic->setAttribute('data_register', $dt_register);

                    $count = $count + 1;
                } else{
                    for ($j=0; $j < count($my_coaching_clinics); $j++) { 
                        if ($my_coaching_clinics[$j]->id_coaching_clinic != $list_my_coaching_clinics[$i]->id_coaching_clinic) {
                            $my_coaching_clinics[$count] = $list_my_coaching_clinics[$i];
                            $id_my = [];
                            $id_my[] =  $list_my_coaching_clinics[$i]->id;                                              
                                    
                            $coaching_clinic = CoachingClinicModel::join('coachs', 'coachs.id', '=', 'coaching_clinics.id_coach')
                                ->select('coaching_clinics.id', 'coaching_clinics.id_coach', 'coaching_clinics.judul_coaching_clinic',
                                    'coaching_clinics.game_coaching_clinic', 'coaching_clinics.harga_coaching_clinic', 'coaching_clinics.desc_coaching_clinic',
                                    'coaching_clinics.tipe_coaching_clinic', 'coaching_clinics.at_coaching_clinic', 'coaching_clinics.link_coaching_clinic',
                                    'coaching_clinics.gambar_coaching_clinic', 'coaching_clinics.id', 'coachs.nama_coach', 'coachs.total_rate',
                                    'coachs.foto_profil_coach')
                                ->where('coaching_clinics.id', '=', $list_my_coaching_clinics[$i]->id_coaching_clinic)->first();
                            $my_coaching_clinics[$count]->setAttribute('data_coaching_clinic', $coaching_clinic);
                            $my_coaching_clinics[$count]->data_coaching_clinic->setAttribute('id_my', $id_my);

                            $register = RegisterCoachingClinicModel::join('schedule_coaching_clinics', 'schedule_coaching_clinics.id', 'register_coaching_clinics.id_jadwal_coaching_clinic')
                                ->select('register_coaching_clinics.id', 'register_coaching_clinics.id_my_coaching_clinic', 'register_coaching_clinics.id_jadwal_coaching_clinic',
                                    'register_coaching_clinics.status_pendaftaran', 'schedule_coaching_clinics.tgl_coaching_clinic', 
                                    'schedule_coaching_clinics.mulai_coaching_clinic', 'schedule_coaching_clinics.berakhir_coaching_clinic',)
                                ->where('register_coaching_clinics.id_my_coaching_clinic', '=', $list_my_coaching_clinics[$i]->id)->first();

                            $register_status = RegisterCoachingClinicModel::select('status_pendaftaran')
                                ->where('id_my_coaching_clinic', '=', $list_my_coaching_clinics[$i]->id)->first();

                            $list_participant = OtherParticipantModel::where('id_peserta', '=', $list_my_coaching_clinics[$i]->id_peserta)
                            ->where('id_pendaftaran_coaching_clinic', '=', $register->id)                    
                            ->get()
                            ->pluck('nama_peserta_tambahan');                            
                        
                            if($register_status->status_pendaftaran == 'Personal') {
                                $list_participant[] = $participant->nama_peserta;
                            }
                                    
                            $register->setAttribute('data_participant', $list_participant);
                            $dt_register = [];
                            $dt_register[] = $register;
                            $my_coaching_clinics[$count]->data_coaching_clinic->setAttribute('data_register', $dt_register);                            
                            
                            $count = $count + 1;
                        } else{
                            $my_coaching_clinics[$j]->data_coaching_clinic->id_my = Arr::prepend($my_coaching_clinics[$j]->data_coaching_clinic->id_my, $list_my_coaching_clinics[$i]->id);

                            if ($my_coaching_clinics[$j]->id_jadwal_coaching_clinic != $list_my_coaching_clinics[$i]->id_jadwal_coaching_clinic) {                                
                                $register = RegisterCoachingClinicModel::join('schedule_coaching_clinics', 'schedule_coaching_clinics.id', 'register_coaching_clinics.id_jadwal_coaching_clinic')
                                    ->select('register_coaching_clinics.id', 'register_coaching_clinics.id_my_coaching_clinic', 'register_coaching_clinics.id_jadwal_coaching_clinic',
                                        'schedule_coaching_clinics.tgl_coaching_clinic', 'schedule_coaching_clinics.mulai_coaching_clinic', 'schedule_coaching_clinics.berakhir_coaching_clinic',)
                                    ->where('register_coaching_clinics.id_my_coaching_clinic', '=', $list_my_coaching_clinics[$i]->id)->first();

                                $register_status = RegisterCoachingClinicModel::select('status_pendaftaran')
                                    ->where('id_my_coaching_clinic', '=', $list_my_coaching_clinics[$i]->id)->first();

                                $list_participant = OtherParticipantModel::where('id_peserta', '=', $list_my_coaching_clinics[$i]->id_peserta)
                                ->where('id_pendaftaran_coaching_clinic', '=', $register->id)                    
                                ->get()
                                ->pluck('nama_peserta_tambahan');                                
                            
                                if($register_status->status_pendaftaran == 'Personal') {
                                    $list_participant[] = $participant->nama_peserta;
                                }
                                        
                                $register->setAttribute('data_participant', $list_participant);
                                $my_coaching_clinics[$j]->data_coaching_clinic->data_register[] = $register;
                            } else {
                                $register = RegisterCoachingClinicModel::select('id', 'id_my_coaching_clinic', 'status_pendaftaran')
                                    ->where('id_my_coaching_clinic', '=', $list_my_coaching_clinics[$i]->id)->first();                                

                                $list_participant = OtherParticipantModel::where('id_peserta', '=', $list_my_coaching_clinics[$i]->id_peserta)
                                ->where('id_pendaftaran_coaching_clinic', '=', $register->id)
                                ->get()
                                ->pluck('nama_peserta_tambahan');

                                $list_registers = $my_coaching_clinics[$j]->data_coaching_clinic->data_register;
                                $index = null;

                                for ($c=0; $c < count($list_registers) ; $c++) { 
                                    if ($list_registers[$c]->id_jadwal_coaching_clinic == $list_my_coaching_clinics[$i]->id_jadwal_coaching_clinic) {
                                        $index = $c;
                                        break;
                                    }
                                }

                                
                                if($register->status_pendaftaran == 'Personal') {                                    
                                    $temp = $my_coaching_clinics[$j]->data_coaching_clinic->data_register[$index]->data_participant;
                                    $check = 0;
                                    for ($z=0; $z < count($temp); $z++) { 
                                        if ($temp[$z] == $participant->nama_peserta) {
                                            $check = 1;                                            
                                        }
                                    }
                                    if ($check == 0) {
                                        $list_participant[] = $participant->nama_peserta;
                                    }
                                }
                            
                                for ($x=0; $x < count($list_participant); $x++) {                                                                        
                                    $my_coaching_clinics[$j]->data_coaching_clinic->data_register[$index]->data_participant->prepend($list_participant[$x]);                                    
                                }
                            }
                        }
                    }                    
                }
            }            

            $list_coaching_clinic = [];
            for ($i=0; $i < count($my_coaching_clinics); $i++) {
                $create_data = [
                    'judul' => $my_coaching_clinics[$i]->data_coaching_clinic->judul_coaching_clinic,
                    'game' => $my_coaching_clinics[$i]->data_coaching_clinic->game_coaching_clinic,
                    'tipe' => $my_coaching_clinics[$i]->data_coaching_clinic->tipe_coaching_clinic,
                ];               
                $list_coaching_clinic[] = $create_data;
            }

            $participant->setAttribute('daftar_coaching_clinics', $list_coaching_clinic);
            $participant->setAttribute('data_my_coaching_clinics', $my_coaching_clinics);

            return response ([
                'message' => 'Tampil Data Coaching Clinic Saya Dari Peserta Berhasil',
                'data_participant' => $participant,
            ], 200);
        } 
    }

    
}
