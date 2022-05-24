<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\CoachModel;
use App\CoachingClinicModel;
use App\ScheduleCoachingClinicModel;
use App\RegisterCoachingClinicModel;
use App\ModuleModel;
use Validator;
use Carbon\Carbon;

class CoachingClinicController extends Controller
{
    public function getAllCoachingClinic(){            
        $coaching_clinics = CoachingClinicModel::join('coachs', 'coachs.id', '=', 'coaching_clinics.id_coach')
            ->select('coaching_clinics.id', 'coaching_clinics.id_coach', 'coachs.nama_coach', 'coaching_clinics.judul_coaching_clinic',
                'coaching_clinics.game_coaching_clinic', 'coaching_clinics.harga_coaching_clinic', 'coaching_clinics.desc_coaching_clinic',
                'coaching_clinics.tipe_coaching_clinic', 'coaching_clinics.at_coaching_clinic', 'coaching_clinics.link_coaching_clinic',
                'coaching_clinics.gambar_coaching_clinic', 'coaching_clinics.created_at', 'coaching_clinics.updated_at')
            ->orderBy('coaching_clinics.created_at', 'ASC')->get();

        if(count($coaching_clinics) > 0){
            return response ([
                'message' => 'Tampil Semua Data Coaching Clinic Berhasil',
                'data_coaching_clinics' => $coaching_clinics
            ], 200);
        }

        return response ([
            'message' => 'Data Coaching Clinic Belum Tersedia'
        ], 404);
    }

    public function getAllActiveCoachingClinic($category){
        $now_dt = Carbon::now()->format('Y-m-d');
        $now_tm = Carbon::now()->format('H:i:s');
        $now = Carbon::now()->format('Y-m-d H:i:s');        

        if ($category != 'Semua') {
            $coaching_clinics = CoachingClinicModel::join('coachs', 'coachs.id', '=', 'coaching_clinics.id_coach')
                ->join('schedule_coaching_clinics', 'schedule_coaching_clinics.id_coaching_clinic', '=', 'coaching_clinics.id')
                ->select('coaching_clinics.id', 'coaching_clinics.id_coach', 'coachs.nama_coach', 'coachs.foto_profil_coach', 'coachs.total_rate', 'coaching_clinics.judul_coaching_clinic',
                    'coaching_clinics.game_coaching_clinic', 'coaching_clinics.harga_coaching_clinic', 'coaching_clinics.desc_coaching_clinic',
                    'coaching_clinics.tipe_coaching_clinic', 'coaching_clinics.at_coaching_clinic', 'coaching_clinics.link_coaching_clinic',
                    'coaching_clinics.gambar_coaching_clinic', 'schedule_coaching_clinics.tgl_coaching_clinic', 'coaching_clinics.created_at', 'coaching_clinics.updated_at')
                ->groupBy('coaching_clinics.judul_coaching_clinic')
                ->where('coaching_clinics.game_coaching_clinic', '=', $category)
                ->where('schedule_coaching_clinics.deleted_at', '=', null)
                ->where('schedule_coaching_clinics.kuota_coaching_clinic', '!=', 0)
                ->whereRaw("CONCAT(schedule_coaching_clinics.tgl_coaching_clinic, ' ', schedule_coaching_clinics.mulai_coaching_clinic) > ?", $now)
                // ->whereDate('schedule_coaching_clinics.tgl_coaching_clinic', '>', $now_dt)
                // ->whereTime('schedule_coaching_clinics.mulai_coaching_clinic', '>', $now_tm)
                ->orderBy('coaching_clinics.created_at', 'ASC')->get();
        } else {
            $coaching_clinics = CoachingClinicModel::join('coachs', 'coachs.id', '=', 'coaching_clinics.id_coach')
                ->join('schedule_coaching_clinics', 'schedule_coaching_clinics.id_coaching_clinic', '=', 'coaching_clinics.id')
                ->select('coaching_clinics.id', 'coaching_clinics.id_coach', 'coachs.nama_coach', 'coachs.foto_profil_coach', 'coachs.total_rate', 'coaching_clinics.judul_coaching_clinic',
                    'coaching_clinics.game_coaching_clinic', 'coaching_clinics.harga_coaching_clinic', 'coaching_clinics.desc_coaching_clinic',
                    'coaching_clinics.tipe_coaching_clinic', 'coaching_clinics.at_coaching_clinic', 'coaching_clinics.link_coaching_clinic',
                    'coaching_clinics.gambar_coaching_clinic', 'schedule_coaching_clinics.tgl_coaching_clinic', 'coaching_clinics.created_at', 'coaching_clinics.updated_at')
                ->groupBy('coaching_clinics.judul_coaching_clinic')                
                ->where('schedule_coaching_clinics.deleted_at', '=', null)
                ->where('schedule_coaching_clinics.kuota_coaching_clinic', '!=', 0)
                ->whereRaw("CONCAT(schedule_coaching_clinics.tgl_coaching_clinic, ' ', schedule_coaching_clinics.mulai_coaching_clinic) > ?", $now)
                // ->whereDate('schedule_coaching_clinics.tgl_coaching_clinic', '>', $now_dt)
                // ->whereTime('schedule_coaching_clinics.mulai_coaching_clinic', '>', $now_tm)
                ->orderBy('coaching_clinics.created_at', 'ASC')->get();
        }
        
        if(count($coaching_clinics) > 0){            
            return response ([
                'message' => 'Tampil Semua Data Coaching Clinic Berhasil',
                'data_coaching_clinics' => $coaching_clinics
            ], 200);
        }

        return response ([
            'message' => 'Data Coaching Clinic Belum Tersedia'
        ], 404);
    }

    public function getCoachingClinicById($id){
        $coaching_clinic = CoachingClinicModel::join('coachs', 'coachs.id', '=', 'coaching_clinics.id_coach')
        ->select('coaching_clinics.id', 'coaching_clinics.id_coach', 'coachs.nama_coach', 'coachs.foto_profil_coach', 'coachs.total_rate', 
            'coaching_clinics.judul_coaching_clinic', 'coaching_clinics.game_coaching_clinic', 'coaching_clinics.harga_coaching_clinic', 
            'coaching_clinics.desc_coaching_clinic', 'coaching_clinics.tipe_coaching_clinic', 'coaching_clinics.at_coaching_clinic', 
            'coaching_clinics.link_coaching_clinic', 'coaching_clinics.gambar_coaching_clinic', 'coaching_clinics.created_at', 'coaching_clinics.updated_at')
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

    public function getAllCoachingClinicByIdCoach($id){
        $coach = CoachModel::find($id);

        if(is_null($coach)){
            return response([
                'message' => 'Data Coach Tidak Ditemukan',
            ], 404);
        }

        if(CoachingClinicModel::where('id_coach', '=', $id)->get() != null){
            $coaching_clinics = CoachingClinicModel::join('coachs', 'coachs.id', '=', 'coaching_clinics.id_coach')                
                ->select('coaching_clinics.id', 'coaching_clinics.id_coach', 'coachs.nama_coach', 'coachs.foto_profil_coach', 'coachs.total_rate', 'coaching_clinics.judul_coaching_clinic',
                    'coaching_clinics.game_coaching_clinic', 'coaching_clinics.harga_coaching_clinic', 'coaching_clinics.desc_coaching_clinic',
                    'coaching_clinics.tipe_coaching_clinic', 'coaching_clinics.at_coaching_clinic', 'coaching_clinics.link_coaching_clinic',
                    'coaching_clinics.gambar_coaching_clinic', 'coaching_clinics.created_at', 'coaching_clinics.updated_at')                                
                ->where('id_coach', '=', $id)               
                ->orderBy('coaching_clinics.created_at', 'DESC')->get();
            
            for ($i=0; $i < count($coaching_clinics); $i++) { 
                $schedules = ScheduleCoachingClinicModel::where('id_coaching_clinic', '=', $coaching_clinics[$i]->id)->get();

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
            }

            return response ([
                'message' => 'Tampil Semua Data Coaching Clinic Coach Berhasil',
                'data_coach' => ['id' => $coach->id, 
                    'nama_coach' => $coach->nama_coach,
                    'daftar_coaching_clinic' => $coaching_clinics,                    
                ],                
            ], 200);
        }

        return response ([
            'message' => 'Data Coaching Clinic Coach Belum Tersedia'
        ], 404); 
    }

    public function getAllScheduleByIdCoachingClinic($id){
        $coaching_clinic = CoachingClinicModel::find($id);

        if(is_null($coaching_clinic)){
            return response([
                'message' => 'Data Coaching Clinic Tidak Ditemukan',
            ], 404);
        }

        if(ScheduleCoachingClinicModel::where('id_coaching_clinic', '=', $id)->get() != null){
            $schedule = ScheduleCoachingClinicModel::where('id_coaching_clinic', '=', $id)
                ->orderBy('tgl_coaching_clinic', 'ASC')
                ->get(['id','tgl_coaching_clinic', 'mulai_coaching_clinic', 'berakhir_coaching_clinic',
                    'kuota_coaching_clinic', 'created_at', 'updated_at']);

            return response ([
                'message' => 'Tampil Semua Data Jadwal Dari Satu Coaching Clinic Berhasil',
                'data_coaching_clinic' => ['id' => $coaching_clinic->id,
                    'id_coach' => $coaching_clinic->id_coach, 
                    'judul_coaching_clinic' => $coaching_clinic->judul_coaching_clinic,
                    'game_coaching_clinic' => $coaching_clinic->game_coaching_clinic,
                    'daftar_jadwal_coaching_clinic' => $schedule
                ],                
            ], 200);
        }

        return response ([
            'message' => 'Data Jadwal Dari Satu Coaching Clinic Belum Tersedia'
        ], 404); 
    }

    public function getAllModuleByIdCoachingClinic($id){
        $coaching_clinic = CoachingClinicModel::find($id);

        if(is_null($coaching_clinic)){
            return response([
                'message' => 'Data Coaching Clinic Tidak Ditemukan',
            ], 404);
        }

        if(ModuleModel::where('id_coaching_clinic', '=', $id)->get() != null){
            $module = ModuleModel::where('id_coaching_clinic', '=', $id)->get(['id',
                'file_modul', 'created_at', 'updated_at']);

            return response ([
                'message' => 'Tampil Semua Data Modul Dari Satu Coaching Clinic Berhasil',
                'data_module' => ['id' => $coaching_clinic->id,
                    'id_coach' => $coaching_clinic->id_coach, 
                    'judul_coaching_clinic' => $coaching_clinic->judul_coaching_clinic,
                    'game_coaching_clinic' => $coaching_clinic->game_coaching_clinic,
                    'daftar_modul_coaching_clinic' => $module
                ],                
            ], 200);
        }

        return response ([
            'message' => 'Data Module Dari Satu Coaching Clinic Belum Tersedia'
        ], 404); 
    }

    public function getReportActivityCoachingClinic($dtF, $dtL, $id) {
        $start = date($dtF);
        $end = date($dtL);

        if ($id != 'all') {            
            $coaching_clinics = CoachingClinicModel::join('coachs', 'coachs.id', '=', 'coaching_clinics.id_coach')
                ->join('schedule_coaching_clinics', 'schedule_coaching_clinics.id_coaching_clinic', '=', 'coaching_clinics.id')
                ->select('coaching_clinics.id', 'coaching_clinics.id_coach', 'schedule_coaching_clinics.id AS id_schedule', 'coachs.nama_coach',
                    'coaching_clinics.judul_coaching_clinic', 'coaching_clinics.game_coaching_clinic', 'schedule_coaching_clinics.tgl_coaching_clinic',
                    'schedule_coaching_clinics.mulai_coaching_clinic', 'schedule_coaching_clinics.berakhir_coaching_clinic')
                ->where('coachs.id', '=', $id)->whereBetween('schedule_coaching_clinics.tgl_coaching_clinic', [$start, $end])
                ->where('coaching_clinics.deleted_at', '=', null)->where('schedule_coaching_clinics.deleted_at', '=', null)
                ->get();
        } else {
            $coaching_clinics = CoachingClinicModel::join('coachs', 'coachs.id', '=', 'coaching_clinics.id_coach')
                ->join('schedule_coaching_clinics', 'schedule_coaching_clinics.id_coaching_clinic', '=', 'coaching_clinics.id')
                ->select('coaching_clinics.id', 'coaching_clinics.id_coach', 'schedule_coaching_clinics.id AS id_schedule', 'coachs.nama_coach',
                    'coaching_clinics.judul_coaching_clinic', 'coaching_clinics.game_coaching_clinic', 'schedule_coaching_clinics.tgl_coaching_clinic',
                    'schedule_coaching_clinics.mulai_coaching_clinic', 'schedule_coaching_clinics.berakhir_coaching_clinic')
                ->whereBetween('schedule_coaching_clinics.tgl_coaching_clinic', [$start, $end])
                ->where('coaching_clinics.deleted_at', '=', null)->where('schedule_coaching_clinics.deleted_at', '=', null)
                ->get();
        }

        if ($coaching_clinics->count() < 1) {
            return response([
                'message' => 'Tampil laporan aktivitas coaching clinic berhasil',
                'data' => [],
                ],200);
        }
        

        for ($i=0; $i < $coaching_clinics->count(); $i++) { 
            
            $count_participant = DB::table('register_coaching_clinics')->selectRaw('ifnull(count(jml_beli_kuota), 0) as jmlPeserta')
                ->where('id_jadwal_coaching_clinic', '=', $coaching_clinics[$i]->id_schedule)
                ->where('deleted_at', '=', null)
                ->where('is_my', '=', 1)
                ->first();                      
                
            $laporan[$i] = array( 
                "no" => $i+1,
                "coach" => $coaching_clinics[$i]->nama_coach,
                "judul_coaching_clinic" => $coaching_clinics[$i]->judul_coaching_clinic,
                "game_coaching_clinic" => $coaching_clinics[$i]->game_coaching_clinic,
                "tanggal" => $coaching_clinics[$i]->tgl_coaching_clinic,
                "mulai" => $coaching_clinics[$i]->mulai_coaching_clinic,
                "berakhir" => $coaching_clinics[$i]->berakhir_coaching_clinic,
                "zona" => 'WIB',
                "jml_pendaftar" => $count_participant->jmlPeserta,
            );        
        }

        return response([
            'message' => 'Tampil laporan aktivitas coaching clinic berhasil',
            'data' => $laporan,
            ],200); 
    }

    public function addCoachingClinic(Request $request){
        $add_data = $request->all();

        $validate = Validator::make($add_data, [
            'judul_coaching_clinic' => 'required|max:100',
            'game_coaching_clinic' => 'required|max:100',
            'harga_coaching_clinic' => 'required|numeric',            
            'tipe_coaching_clinic' => 'required|max:20',
            'at_coaching_clinic' => 'required|max:100',
            'gambar_coaching_clinic' => 'required|file|image|mimes:jpeg,png,jpg|max:2048',
            'id_coach' => 'required',
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);

        $img = $request->file('gambar_coaching_clinic');
        $img_name = $img->getClientOriginalName();
        $img->move(public_path('images'), $img_name);
        $add_data['gambar_coaching_clinic'] = $img_name;

        $coaching_clinic = CoachingClinicModel::create($add_data);
        return response([
            'message' => 'Tambah Data Coaching Clinic Berhasil',
            'data_coaching_clinic' => $coaching_clinic,
        ], 200);
    }

    public function updateCoachingClinic(Request $request, $id){
        $coaching_clinic = CoachingClinicModel::find($id);

        if(is_null($coaching_clinic)){
            return response([
                'message' => 'Data Coaching Clinic Tidak Ditemukan',
            ],404);
        }

        $update_data = $request->all();

        if ($request->has('gambar_coaching_clinic')) {
            $validate = Validator::make($update_data, [
                'judul_coaching_clinic' => 'required|max:100',
                'game_coaching_clinic' => 'required|max:100',
                'harga_coaching_clinic' => 'required|numeric',            
                'tipe_coaching_clinic' => 'required|max:20',
                'at_coaching_clinic' => 'required|max:100',
                'gambar_coaching_clinic' => 'required|file|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            // $out = new \Symfony\Component\Console\Output\ConsoleOutput();
            // $out->writeln($validate->errors());
    
            if($validate->fails())
                return response(['message' => $validate->errors()],400);

            $img = $request->file('gambar_coaching_clinic');
            $img_name = $img->getClientOriginalName();
            $img->move(public_path('images'), $img_name);
            
            $coaching_clinic->gambar_coaching_clinic = $img_name;
        } else {
            $validate = Validator::make($update_data, [
                'judul_coaching_clinic' => 'required|max:100',
                'game_coaching_clinic' => 'required|max:100',
                'harga_coaching_clinic' => 'required|numeric',            
                'tipe_coaching_clinic' => 'required|max:20',
                'at_coaching_clinic' => 'required|max:100',                
            ]);
    
            if($validate->fails())
                return response(['message' => $validate->errors()],400);
        }

        $coaching_clinic->judul_coaching_clinic = $update_data['judul_coaching_clinic'];
        $coaching_clinic->game_coaching_clinic = $update_data['game_coaching_clinic'];
        $coaching_clinic->harga_coaching_clinic = $update_data['harga_coaching_clinic'];
        $coaching_clinic->tipe_coaching_clinic = $update_data['tipe_coaching_clinic'];
        $coaching_clinic->at_coaching_clinic = $update_data['at_coaching_clinic'];        
        $coaching_clinic->desc_coaching_clinic = $update_data['desc_coaching_clinic'];
        $coaching_clinic->link_coaching_clinic = $update_data['link_coaching_clinic'];

        if($coaching_clinic->save()){
            return response([
                'message' => 'Perbaharui Data Coaching Clinic Berhasil',
                'data_coaching_clinic' => $coaching_clinic,
            ], 200);
        }

        return response([
            'message' => 'Perbaharui Data Coaching Clinic Gagal',
        ],400);
    }

    public function deleteCoachingClinic($id){
        $coaching_clinic = CoachingClinicModel::find($id);

        if(is_null($coaching_clinic)){
            return response([
                'message' => 'Data Coaching Clinic Tidak Ditemukan',
            ],404);
        }

        if($coaching_clinic->delete()){
            return response([
                'message' => 'Hapus Data Coaching Clinic Berhasil',
                'data_coaching_clinic' => $coaching_clinic,
            ],200);
        }
        
        return response([
            'message' => 'Hapus Data Coaching Clinic Gagal',
        ],400);
    }

    public function addScheduleCoachingClinic(Request $request){
        $add_data = $request->all();

        $validate = Validator::make($add_data, [
            'tgl_coaching_clinic' => 'required',
            'mulai_coaching_clinic' => 'required|',
            'kuota_coaching_clinic' => 'required|numeric',            
            'id_coaching_clinic' => 'required',
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);

        $schedule = ScheduleCoachingClinicModel::create($add_data);
        return response([
            'message' => 'Tambah Data Jadwal Coaching Clinic Berhasil',
            'data_schedule_coaching_clinic' => $schedule,
        ], 200);
    }

    public function updateScheduleCoachingClinic(Request $request, $id){
        $schedule = ScheduleCoachingClinicModel::find($id);

        if(is_null($schedule)){
            return response([
                'message' => 'Data Jadwal Coaching Clinic Tidak Ditemukan',
            ],404);
        }

        $update_data = $request->all();
        $validate = Validator::make($update_data, [
            'tgl_coaching_clinic' => 'required',
            'mulai_coaching_clinic' => 'required',            
            'kuota_coaching_clinic' => 'required|numeric',                  
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()],400);

        $schedule->tgl_coaching_clinic = $update_data['tgl_coaching_clinic'];
        $schedule->mulai_coaching_clinic = $update_data['mulai_coaching_clinic'];
        $schedule->berakhir_coaching_clinic = $update_data['berakhir_coaching_clinic'];
        $schedule->kuota_coaching_clinic = $update_data['kuota_coaching_clinic'];        

        if($schedule->save()){
            return response([
                'message' => 'Perbaharui Data Jadwal Coaching Clinic Berhasil',
                'data_schedule_coaching_clinic' => $schedule,
            ], 200);
        }

        return response([
            'message' => 'Perbaharui Data Jadwal Coaching Clinic Gagal',
        ],400);
    }

    public function deleteScheduleCoachingClinic($id){
        $schedule = ScheduleCoachingClinicModel::find($id);

        if(is_null($schedule)){
            return response([
                'message' => 'Data Jadwal Coaching Clinic Tidak Ditemukan',
            ],404);
        }

        if($schedule->delete()){
            return response([
                'message' => 'Hapus Data Jadwal Coaching Clinic Berhasil',
                'data_schedule_coaching_clinic' => $schedule,
            ],200);
        }
        
        return response([
            'message' => 'Hapus Data Jadwal Coaching Clinic Gagal',
        ],400);
    }

    public function addModule(Request $request){
        $add_data = $request->all();

        $validate = Validator::make($add_data, [
            'file_modul' => 'required|file|mimes:pdf,docx,pptx|max:5120',            
            'id_coaching_clinic' => 'required',
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);

        $file = $request->file('file_modul');
        $file_name = $file->getClientOriginalName();
        $file->move(public_path('modules'), $file_name);
        $add_data['file_modul'] = $file_name;

        $module = ModuleModel::create($add_data);  
        if (!is_null($module)) {
            return response([
                'message' => 'Tambah Data Modul Coaching Clinic Berhasil',
                'data_module' => $module,
            ], 200);
        }
        return response([
            'message' => 'Tambah Data Modul Coaching Clinic Gagal',
        ],400);
        
    }

    public function deleteModule($id){
        $module = ModuleModel::find($id);

        if(is_null($module)){
            return response([
                'message' => 'Data Modul Coaching Clinic Tidak Ditemukan',
            ],404);
        }

        if($module->delete()){
            return response([
                'message' => 'Hapus Data Modul Coaching Clinic Berhasil',
                'data_module' => $module,
            ],200);
        }
        
        return response([
            'message' => 'Hapus Data Module Coaching Clinic Gagal',
        ],400);
    }
}
