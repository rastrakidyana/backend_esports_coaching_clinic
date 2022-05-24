<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\ParticipantModel;
use App\CoachModel;
use App\CoachingClinicModel;
use App\CoachAssessmentModel;
use App\MyCoachingClinicModel;
use Validator;

class CoachAssessmentController extends Controller
{
    public function getAllAssessment(){        
        $coach_assessments = CoachAssessmentModel::join('participants', 'participants.id', '=', 'coach_assessments.id_peserta')
                ->join('my_coaching_clinics', 'my_coaching_clinics.id_penilaian_coach', '=', 'coach_assessments.id')
                ->join('coachs', 'coachs.id', '=', 'coach_assessments.id_coach')
                ->join('coaching_clinics', 'my_coaching_clinics.id_coaching_clinic', '=', 'coaching_clinics.id')
                ->select('coach_assessments.id', 'coach_assessments.id_coach', 'coachs.nama_coach', 'coach_assessments.id_peserta', 'participants.nama_peserta',
                    'participants.foto_profil_peserta', 'coach_assessments.rate', 'coach_assessments.komentar', 'coaching_clinics.judul_coaching_clinic')                
                ->groupBy('coaching_clinics.judul_coaching_clinic')->get();

        if(count($coach_assessments) > 0) {
            return response ([
                'message' => 'Tampil Semua Data Penilaian Coach Berhasil',
                'data_coach_assessments' => $coach_assessments
            ], 200);
        }

        return response ([
            'message' => 'Data Penilaian Coach Belum Tersedia'
        ], 404);
    }

    public function getAllAssessmentByIdCoach($id){
        $coach = CoachModel::find($id);

        if(!is_null($coach)) {            
            $coach_assessments = CoachAssessmentModel::join('participants', 'participants.id', '=', 'coach_assessments.id_peserta')
                ->join('my_coaching_clinics', 'my_coaching_clinics.id_penilaian_coach', '=', 'coach_assessments.id')
                ->join('coaching_clinics', 'my_coaching_clinics.id_coaching_clinic', '=', 'coaching_clinics.id')
                ->select('coach_assessments.id', 'coach_assessments.id_coach', 'coach_assessments.id_peserta', 'participants.nama_peserta',
                    'participants.foto_profil_peserta', 'coach_assessments.rate', 'coach_assessments.komentar', 'coaching_clinics.judul_coaching_clinic')
                ->where('coach_assessments.id_coach', '=', $id)
                ->groupBy('coaching_clinics.judul_coaching_clinic')->get();

            return response ([
                'message' => 'Tampil Semua Data Penilaian Dari Coach Berhasil',
                'data_coach_assessments' => $coach_assessments
            ], 200);
        }

        return response ([
            'message' => 'Data Penilaian Coach Belum Tersedia'
        ], 404);
    }

    public function getAssessmentByIdMyCoachingClinic($id){
        $my = MyCoachingClinicModel::find($id);

        if(!is_null($my)) {
            $coach_assessment = CoachAssessmentModel::find($my->id_penilaian_coach);            

            return response ([
                'message' => 'Tampil Data Penilaian Dari Coaching Clinic Saya Berhasil',
                'data_coach_assessment' => $coach_assessment
            ], 200);
        }

        return response ([
            'message' => 'Data Penilaian Coach Belum Tersedia'
        ], 404);
    }

    public function getTotalRateCoachByIdCoach($id){
        $coach = CoachModel::find($id);

        if(!is_null($coach)) {
            $coach_assessments = CoachAssessmentModel::where('id_coach', '=', $id)->get();

            $total_rate = 0;
            for ($i=0; $i < count($coach_assessments); $i++) { 
                $total_rate = $total_rate + $coach_assessments[$i]->rate;
            }

            $total_rate = $total_rate / count($coach_assessments);
            $coach->total_rate = $total_rate;
            $coach->save();

            // return response ([
            //     'message' => 'Tampil Data Rating Penilaian Dari Coach Berhasil',
            //     'data_coach' => $coach,
            // ], 200);
        }
    }

    public function AddAssessment(Request $request){
        $add_data = $request->except(['id_my']);

        $validate = Validator::make($add_data, [
            'rate' => 'required|numeric|max:5',
            'komentar' => 'required',
            'id_coach' => 'required',            
            'id_peserta' => 'required',            
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);

        $coach_assessment = CoachAssessmentModel::create($add_data);

        $get_id_my = $request->input('id_my');

        for ($i=0; $i < count($get_id_my); $i++) { 
            $my_coaching_clinic = MyCoachingClinicModel::find($get_id_my[$i]);

            $my_coaching_clinic->id_penilaian_coach = $coach_assessment->id;
            $my_coaching_clinic->save();
        }

        $this->getTotalRateCoachByIdCoach($add_data['id_coach']);

        return response([
            'message' => 'Tambah Data Penilaian CoachBerhasil',
            'data_coach_assessment' => $coach_assessment,
        ], 200);
    }
}
