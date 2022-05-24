<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\CoachingClinicModel;
use App\ScheduleCoachingClinicModel;
use App\CoachAssessmentModel;
use App\CoachModel;
use App\AchievementModel;
use App\ParticipantModel;
use App\AdminModel;
use Validator;

class AdminController extends Controller
{
    public function getAdminById($id){

    }

    public function getReportListCoachBasedOnRegisterDate(){
        
    }

    public function getReportListParticipantBasedOnRegisterDate(){
        
    }

    public function getReportListActivityCoachingClinicBasedOnDate(){
        
    }

    public function getAllCoach(){
        $coachs = CoachModel::all(
            'id', 'nama_coach', 'email_coach', 'domisili_coach', 
            'telp_coach', 'jk_coach', 'tgl_lahir_coach', 'desc_coach',
            'total_rate', 'foto_profil_coach', 'is_verified',
            'created_at', 'updated_at', 
        );        

        if(count($coachs) > 0){
            for ($i=0; $i < count($coachs); $i++) { 
                $achievements = AchievementModel::where('deleted_at', '=', null)
                    ->where('id_coach', '=', $coachs[$i]->id)
                    ->get(['nama_prestasi', 'gambar_prestasi']);

                if (count($achievements) > 0) {
                    $coachs[$i]->setAttribute('daftar_prestasi', $achievements);
                } else {
                    $coachs[$i]->setAttribute('daftar_prestasi', null);
                }
            }

            return response ([
                'message' => 'Tampil Semua Data Coach Berhasil',
                'data_coachs' => $coachs
            ], 200);
        }

        return response ([
            'message' => 'Data Coach Belum Tersedia',
            'data_coachs' => null
        ], 200);
    }

    public function getAllCoachingClinic(){
        $coaching_clinics = CoachingClinicModel::join('coachs', 'coachs.id', '=', 'coaching_clinics.id_coach')
            ->select('coaching_clinics.id', 'coaching_clinics.id_coach', 'coachs.nama_coach', 'coachs.total_rate', 'coachs.foto_profil_coach', 
                'coaching_clinics.judul_coaching_clinic','coaching_clinics.game_coaching_clinic', 'coaching_clinics.harga_coaching_clinic', 
                'coaching_clinics.desc_coaching_clinic', 'coaching_clinics.tipe_coaching_clinic', 'coaching_clinics.at_coaching_clinic', 'coaching_clinics.link_coaching_clinic',
                'coaching_clinics.gambar_coaching_clinic', 'coaching_clinics.created_at', 'coaching_clinics.updated_at')
            ->orderBy('coaching_clinics.created_at', 'ASC')->get();

        if(count($coaching_clinics) > 0){
            for ($i=0; $i < count($coaching_clinics); $i++) { 
                $schedules = ScheduleCoachingClinicModel::where('deleted_at', '=', null)
                    ->where('id_coaching_clinic', '=', $coaching_clinics[$i]->id)
                    ->get();

                $status = "Tidak Aktif";
                $now = Carbon::now();

                for ($j=0; $j < count($schedules); $j++) { 
                    $date_time = $schedules[$j]->tgl_coaching_clinic." ".$schedules[$j]->mulai_coaching_clinic;
                    $create_date_time = Carbon::createFromFormat('Y-m-d H:i:s', $date_time, 'Asia/Jakarta')->addHours(6);
                    if ($create_date_time->gt($now)) {
                        $status = "Aktif";
                        break;
                    }
                }

                $coaching_clinics[$i]->setAttribute('status', $status);

                if (count($schedules) > 0) {
                    $coaching_clinics[$i]->setAttribute('daftar_jadwal', $schedules);
                } else {
                    $coaching_clinics[$i]->setAttribute('daftar_jadwal', null);
                }
            }

            return response ([
                'message' => 'Tampil Semua Data Coaching Clinic Berhasil',
                'data_coaching_clinics' => $coaching_clinics
            ], 200);
        }

        return response ([
            'message' => 'Data Coaching Clinic Belum Tersedia',
            'data_coaching_clinics' => null
        ], 200);
    }

    public function getCoachingClinicById($id){
        $coaching_clinic = CoachingClinicModel::join('coachs', 'coachs.id', '=', 'coaching_clinics.id_coach')
        ->select('coaching_clinics.id', 'coaching_clinics.id_coach', 'coachs.nama_coach', 'coaching_clinics.judul_coaching_clinic',
            'coaching_clinics.game_coaching_clinic', 'coaching_clinics.harga_coaching_clinic', 'coaching_clinics.desc_coaching_clinic',
            'coaching_clinics.tipe_coaching_clinic', 'coaching_clinics.at_coaching_clinic', 'coaching_clinics.link_coaching_clinic',
            'coaching_clinics.gambar_coaching_clinic', 'coaching_clinics.created_at', 'coaching_clinics.updated_at')
        ->where('coaching_clinics.id', '=', $id)->first();

        if(!is_null($coaching_clinic)){
            return response ([
                'message' => 'Tampil Data Coaching Clinic Berhasil',
                'data_coaching_clinic' => $coaching_clinic
            ], 200);
        }

        return response ([
            'message' => 'Data Coaching Clinic Belum Tersedia'
        ], 404);
    }

    public function getListAssessmentInMonth(){
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;

        $coach_assessments = CoachAssessmentModel::join('participants', 'participants.id', '=', 'coach_assessments.id_peserta')
            ->join('my_coaching_clinics', 'my_coaching_clinics.id_penilaian_coach', '=', 'coach_assessments.id')
            ->join('coachs', 'coachs.id', '=', 'coach_assessments.id_coach')
            ->join('coaching_clinics', 'my_coaching_clinics.id_coaching_clinic', '=', 'coaching_clinics.id')
            ->select('coach_assessments.id', 'coach_assessments.id_coach', 'coachs.nama_coach', 'coach_assessments.id_peserta', 'participants.nama_peserta',
                'participants.foto_profil_peserta', 'coach_assessments.rate', 'coach_assessments.komentar', 'coaching_clinics.judul_coaching_clinic')
            ->whereMonth('coach_assessments.created_at', '=', $month)
            ->whereYear('coach_assessments.created_at', '=', $year)
            ->groupBy('coaching_clinics.judul_coaching_clinic')->get();

        if(count($coach_assessments) > 0) {
            return response ([
                'message' => 'Tampil Semua Data Penilaian Coach Berhasil',
                'data_coach_assessments' => $coach_assessments
            ], 200);
        }

        return response ([
            'message' => 'Data Penilaian Coach Belum Tersedia',
            'data_coach_assessments' => []
        ], 200);
    }

    public function getDashboardAdmin(){
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;

        $start = Carbon::now()->startOfMonth()->toDateString();
        $end = Carbon::now()->endOfMonth()->toDateString();

        $count_coachs = DB::table('coachs')
            ->selectRaw('ifnull(count(id), 0) as jmlCoach')
            ->where('is_verified', '=', 1)
            ->whereMonth('created_at', '=', $month)
            ->whereYear('created_at', '=', $year)
            ->first();

        $count_participants = DB::table('participants')
            ->selectRaw('ifnull(count(id), 0) as jmlParticipant')            
            ->whereMonth('created_at', '=', $month)
            ->whereYear('created_at', '=', $year)
            ->first();

        $count_active_class = 0;

        $coaching_clinics = CoachingClinicModel::where('deleted_at', '=', null)->get();

        if ($coaching_clinics->count() > 0) {
            
            for ($i=0; $i < $coaching_clinics->count(); $i++) { 
                $temp = DB::table('schedule_coaching_clinics')
                    ->selectRaw('ifnull(count(id), 0) as jmlSchedule')            
                    ->whereBetween('tgl_coaching_clinic', [$start, $end])
                    ->where('deleted_at', '=', null)
                    ->where('id_coaching_clinic', '=', $coaching_clinics[$i]->id)
                    ->first();                    
                if ($temp->jmlSchedule != 0) {
                    $count_active_class = $count_active_class + 1;
                }
            }
        }

        $best = CoachModel::orderBy('total_rate', 'DESC')->first(['id', 'nama_coach', 'foto_profil_coach', 'total_rate']);      
        
        return response([
            'message' => 'Tampil data dashboard berhasil',
            'coach_month' => $count_coachs->jmlCoach,
            'participant_month' => $count_participants->jmlParticipant,
            'active_coaching_clinic' => $count_active_class,
            'best_rate' => $best,
            ],200);
    }

    public function verifyCoach($id){
        $coach = CoachModel::find($id);

        if(!is_null($coach)){
            if($coach->id_bank_account != NULL && $coach->foto_profil_coach != NULL) {
                $coach->is_verified = 1;
                $coach->save();

                return response ([
                    'message' => 'Verifikasi Coach Berhasil',
                    'data_coach' => $coach,
                ], 200);
            }

            return response ([
                'message' => 'Verifikasi Coach Gagal, Perbaharui Data Rekening Bank dan Foto Profil'
            ], 404);
        }

        return response ([
            'message' => 'Coach Tidak Tersedia'
        ], 404);
    }
}
