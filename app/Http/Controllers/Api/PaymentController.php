<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\BankAccountModel;
use App\PaymentModel;
use App\RegisterCoachingClinicModel;
use App\ParticipantModel;
use App\OtherParticipantModel;
use App\CoachModel;
use App\CoachingClinicModel;
use App\ScheduleCoachingClinicModel;
use App\MyCoachingClinicModel;
use Validator;

class PaymentController extends Controller
{
    public function getAllPaymentByIdCoach($id){
        $coach = CoachModel::where('id', '=', $id)->first([
            'id', 'nama_coach'
        ]);

        $bank = BankAccountModel::where('id_coach', '=', $coach->id)->first();

        if(!is_null($coach)){
            $payments = PaymentModel::where('id_coach', '=', $id)->get();

            for ($i=0; $i < $payments->count(); $i++) { 
                $participant = ParticipantModel::where('id', '=', $payments[$i]->id_peserta)
                    ->select('nama_peserta', 'telp_peserta', 'email_peserta')->first(); 
                $payments[$i]->setAttribute('nama_peserta_pemesan', $participant->nama_peserta);
                $payments[$i]->setAttribute('telp_peserta_pemesan', $participant->telp_peserta);
                $payments[$i]->setAttribute('email_peserta_pemesan', $participant->email_peserta);
                $payments[$i]->setAttribute('data_bank_account', $bank);
                
                $list_coaching_clinic = [];

                $registers = RegisterCoachingClinicModel::where('id_pembayaran', '=', $payments[$i]->id)->get();                
                for ($j=0; $j < $registers->count(); $j++) { 
                    $coaching_clinic = CoachingClinicModel::where('id', '=', $registers[$j]->id_coaching_clinic)->first();

                    $list_coaching_clinic[] = $coaching_clinic->judul_coaching_clinic; 

                    $list_participant = OtherParticipantModel::where('id_peserta', '=', $registers[$j]->id_peserta)
                        ->where('id_pendaftaran_coaching_clinic', '=', $registers[$j]->id)                    
                        ->get()
                        ->pluck('nama_peserta_tambahan');

                    if($registers[$j]->status_pendaftaran == 'Personal') {
                        $list_participant[] = $participant->nama_peserta;
                    }

                    $registers[$j]->setAttribute('judul_coaching_clinic', $coaching_clinic->judul_coaching_clinic);
                    $registers[$j]->setAttribute('game_coaching_clinic', $coaching_clinic->game_coaching_clinic);
                    $registers[$j]->setAttribute('gambar_coaching_clinic', $coaching_clinic->gambar_coaching_clinic);
                    $registers[$j]->setAttribute('data_participant', $list_participant);
                }

                $payments[$i]->setAttribute('daftar_coaching_clinic', $list_coaching_clinic);
                $payments[$i]->setAttribute('data_register_coaching_clinic', $registers);
                                
            }
                        
            $coach->setAttribute('data_payment_coaching_clinic', $payments);
            return response ([
                'message' => 'Tampil Data Pembayaran Coaching Clinic Dari Coach Berhasil',
                'data_coach' => $coach,
            ], 200);
        }
    }

    public function getAllPaymentByIdParticipant($id){
        $participant = ParticipantModel::where('id', '=', $id)->first([
            'id', 'nama_peserta'
        ]);        

        if(!is_null($participant)){
            $payments = PaymentModel::where('id_peserta', '=', $id)->get();

            for ($i=0; $i < $payments->count(); $i++) { 
                $coach = CoachModel::join('bank_accounts', 'bank_accounts.id', '=', 'coachs.id_bank_account')
                    ->select('coachs.nama_coach', 'coachs.id_bank_account', 'bank_accounts.an_no_rek', 
                        'bank_accounts.nama_bank')
                    ->where('coachs.id', '=', $payments[$i]->id_coach)->first();
                $payments[$i]->setAttribute('data_coach', $coach);

                $list_coaching_clinic = [];

                $registers = RegisterCoachingClinicModel::where('id_pembayaran', '=', $payments[$i]->id)->get();
                for ($j=0; $j < $registers->count(); $j++) { 
                    $coaching_clinic = CoachingClinicModel::where('id', '=', $registers[$j]->id_coaching_clinic)->first();

                    $list_coaching_clinic[] = $coaching_clinic->judul_coaching_clinic; 

                    $list_participant = OtherParticipantModel::where('id_peserta', '=', $id)
                        ->where('id_pendaftaran_coaching_clinic', '=', $registers[$j]->id)                    
                        ->get()
                        ->pluck('nama_peserta_tambahan');

                    if($registers[$j]->status_pendaftaran == 'Personal') {
                        $list_participant[] = $participant->nama_peserta;
                    }

                    $registers[$j]->setAttribute('judul_coaching_clinic', $coaching_clinic->judul_coaching_clinic);
                    $registers[$j]->setAttribute('game_coaching_clinic', $coaching_clinic->game_coaching_clinic);
                    $registers[$j]->setAttribute('gambar_coaching_clinic', $coaching_clinic->gambar_coaching_clinic);
                    $registers[$j]->setAttribute('data_participant', $list_participant);
                }

                $payments[$i]->setAttribute('daftar_coaching_clinic', $list_coaching_clinic);
                $payments[$i]->setAttribute('data_register_coaching_clinic', $registers);
                                
            }
                        
            $participant->setAttribute('data_payment_coaching_clinic', $payments);
            return response ([
                'message' => 'Tampil Data Pembayaran Coaching Clinic Dari Peserta Berhasil',
                'data_participant' => $participant
            ], 200);
        }

    }

    public function getReportTransactionsBasedOnDate($dtF, $dtL, $id){
        $start = date($dtF);
        $end = date($dtL);       

        $payments = PaymentModel::join('participants', 'participants.id', '=', 'payments.id_peserta')
            ->select('payments.id', 'payments.id_coach', 'payments.is_confirmed', 'participants.nama_peserta', 'payments.total_pembayaran', 'payments.created_at')
            ->where('payments.id_coach', '=', $id)
            ->where('payments.is_confirmed', '=', 1)
            ->whereBetween('payments.created_at', [$start, $end])
            ->get();

        if (!is_null($payments)) {
            return response ([
                'message' => 'Tampil Data Laporan Pembayaran Coaching Clinic Berhasil',
                'data_report' => $payments
            ], 200);
        }

        return response([
            'message' => 'Tampil Data Laporan Pembayaran Coaching Clinic Gagal',
        ],400);
    }

    public function getReportIncomeInYear($year, $id) {
        $months = array(
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli ',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember',
        );

        $total = 0;

        for ($bln=0; $bln < 12; $bln++) {             

            $prices = DB::table('payments')
                ->selectRaw('ifnull(sum(payments.total_pembayaran), 0) as priceBln')
                ->where('payments.is_confirmed', '=', 1)
                ->where('payments.id_coach', '=', $id)
                ->whereMonth('payments.created_at', '=', $bln+1)
                ->whereYear('payments.created_at', '=', $year)
                ->first();

            $temp = $prices->priceBln;

            $payments = DB::table('payments')
                ->selectRaw('ifnull(count(payments.total_pembayaran), 0) as paymentBln')
                ->where('payments.is_confirmed', '=', 1)
                ->where('payments.id_coach', '=', $id)
                ->whereMonth('payments.created_at', '=', $bln+1)
                ->whereYear('payments.created_at', '=', $year)
                ->first();
            
            $total = $total + $temp;

            $laporan[$bln] = array( 
                "no" => $bln+1,               
                "bulan" => $months[$bln],
                "keuntungan" => $temp,
                "jumlah_transaksi" => $payments->paymentBln,                
            );
        }
        
        return response([
            'message' => 'Tampil laporan pendapatan bulanan berhasil',
            'data_all' => $laporan,
            'data_total' => $total,
            ],200);
    }

    public function getDashboardCoach($id) {
        $coach = CoachModel::where('id', '=', $id)->first([
            'id', 'nama_coach'
        ]);        

        if(!is_null($coach)){
            $month = Carbon::now()->month;
            $year = Carbon::now()->year;

            $income = DB::table('payments')
                ->selectRaw('ifnull(sum(payments.total_pembayaran), 0) as priceBln')
                ->where('payments.is_confirmed', '=', 1)
                ->where('payments.id_coach', '=', $id)
                ->whereMonth('payments.created_at', '=', $month)
                ->whereYear('payments.created_at', '=', $year)
                ->first();

            $payments = PaymentModel::where('is_confirmed', '=', 1)
                ->where('id_coach', '=', $id)
                ->whereMonth('created_at', '=', $month)
                ->whereYear('created_at', '=', $year)
                ->get();

            $kuota = 0;

            for ($i=0; $i < $payments->count(); $i++) { 
                $temp = DB::table('register_coaching_clinics')
                    ->selectRaw('ifnull(sum(jml_beli_kuota), 0) as jmlKuota')
                    ->where('is_my', '=', 1)
                    ->where('id_pembayaran', '=', $payments[$i]->id)
                    ->where('deleted_at', '=', null)
                    ->whereMonth('created_at', '=', $month)
                    ->whereYear('created_at', '=', $year)
                    ->first();

                $kuota = $kuota + $temp->jmlKuota;
            }            

            $confirm = DB::table('payments')
                ->selectRaw('ifnull(count(id), 0) as payment')
                ->where('is_confirmed', '=', null)
                ->where('id_coach', '=', $id)
                ->where('nama_pengirim', '!=', null)
                ->first();
            

            return response([
                'message' => 'Tampil data dashboard berhasil',
                'income_month' => $income->priceBln,
                'kuota_month' => $kuota,
                'payment_confirm' => $confirm->payment,
                ],200);
        }
    }

    public function getPaymentById($id){
        $payment = PaymentModel::find($id);        

        if(!is_null($payment)){
            $bank = BankAccountModel::where('id_coach', '=', $payment->id_coach)->first();
            $payment->setAttribute('data_bank_account', $bank);
            return response ([
                'message' => 'Tampil Data Pembayaran Coaching Clinic Berhasil',
                'data_payment_coaching_clinic' => $payment
            ], 200);
        }

        return response([
            'message' => 'Tampil Data Pembayaran Coaching Clinic Gagal',
        ],400);
    }

    public function addPayment(Request $request){
        $add_data = $request->all();

        $validate = Validator::make($add_data, [
            'id_coach' => 'required',
            'id_peserta' => 'required',
            'id_pendaftaran_coaching_clinic' => 'required',            
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);
        
        $add_data_payment = [
            'id' => $this->number_payment(Carbon::now()->toDateString(), $add_data['id_coach']),
            'id_coach' => $add_data['id_coach'],
            'id_peserta' => $add_data['id_peserta'],
            'total_pembayaran' => 0,
            'countdown' => Carbon::now()->addDays(1)->toDateTimeString()
        ];        

        $payment = PaymentModel::create($add_data_payment);

        $total_pembayaran = 0;

        for ($i=0; $i < count($request->input('id_pendaftaran_coaching_clinic')); $i++) { 
            $register = RegisterCoachingClinicModel::find($request->input("id_pendaftaran_coaching_clinic.$i"));

            $total_pembayaran = $total_pembayaran + $register->harga_pendaftaran;
            $register->id_pembayaran = $payment->id;

            $schedule = ScheduleCoachingClinicModel::find($register->id_jadwal_coaching_clinic);
            $schedule->kuota_coaching_clinic = $schedule->kuota_coaching_clinic - $register->jml_beli_kuota;

            $schedule->save();
            $register->save();
        }

        $payment->total_pembayaran = $total_pembayaran;
        $payment->save();

        return response([
            'message' => 'Tambah Data Pembayaran Coaching Clinic Berhasil',
            'data_payment_coaching_clinic' => $payment,
        ], 200);
    }

    public function updatePayment(Request $request, $id){
        $payment = PaymentModel::find($id);

        if(is_null($payment)){
            return response([
                'message' => 'Data Pembayaran Coaching Clinic Tidak Ditemukan',
            ],404);
        }

        $update_data = $request->all();
        $validate = Validator::make($update_data, [
            'nama_pengirim' => 'required',
            'nama_bank_pengirim' => 'required',
            'no_rek_pengirim' => 'required|numeric',                                   
            'nominal_pembayaran' => 'required|numeric',
            'gambar_bukti_pembayaran' => 'required|file|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()],400);

        if($update_data['nominal_pembayaran'] != $payment->total_pembayaran) {
            return response([
                'message' => 'Nominal Pembayaran Tidak Sama Dengan Total Pembayaran',
            ],400);
        }

        $img = $request->file('gambar_bukti_pembayaran');
        $img_name = $img->getClientOriginalName();
        $img->move(public_path('images'), $img_name);

        $payment->nama_pengirim = $update_data['nama_pengirim'];
        $payment->nama_bank_pengirim = $update_data['nama_bank_pengirim'];
        $payment->no_rek_pengirim = $update_data['no_rek_pengirim'];
        $payment->nominal_pembayaran = $update_data['nominal_pembayaran'];
        $payment->gambar_bukti_pembayaran = $img_name;

        if($payment->save()){
            return response([
                'message' => 'Perbaharui Data Pembayaran Coaching Clinic Berhasil',
                'data_payment_coaching_clinic' => $payment,
            ], 200);
        }

        return response([
            'message' => 'Perbaharui Data Pembayaran Coaching Clinic Gagal',
        ],400);
    }

    public function confirmPayment(Request $request, $id){
        $payment = PaymentModel::find($id);

        if(is_null($payment)){
            return response([
                'message' => 'Data Pembayaran Coaching Clinic Tidak Ditemukan',
            ],404);
        }

        $confirm_data = $request->all();
        $validate = Validator::make($confirm_data, [
            'is_confirmed' => 'required',            
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()],400);

        $payment->is_confirmed = $confirm_data['is_confirmed'];
        
        $registers = RegisterCoachingClinicModel::where('id_pembayaran', '=', $payment->id)->get();
        for ($i=0; $i < $registers->count(); $i++) { 
            if($confirm_data['is_confirmed'] == 1) {                 
                $add_data_my = [
                    'id_coaching_clinic' => $registers[$i]->id_coaching_clinic,
                    'id_jadwal_coaching_clinic' => $registers[$i]->id_jadwal_coaching_clinic,
                    'id_peserta' => $registers[$i]->id_peserta,
                    'status_my_coaching_clinic' => 'Coaching Clinic Belum Dimulai',
                ];

                $my_coaching_clinic = MyCoachingClinicModel::create($add_data_my);
    
                $registers[$i]->is_my = 1;
                $registers[$i]->id_my_coaching_clinic = $my_coaching_clinic->id;
                $registers[$i]->save();
            } else if($confirm_data['is_confirmed'] == 0) {
                $schedule = ScheduleCoachingClinicModel::find($registers[$i]->id_jadwal_coaching_clinic);
                $schedule->kuota_coaching_clinic = $schedule->kuota_coaching_clinic + $registers[$i]->jml_beli_kuota;
    
                $schedule->save();                            
            }  
        } 

        if($payment->save()){
            if($confirm_data['is_confirmed'] == 1) {
                return response([
                    'message' => 'Data Pembayaran Coaching Clinic Valid',
                    'data_payment_coaching_clinic' => $payment,
                ], 200);
            }else {
                return response([
                    'message' => 'Data Pembayaran Coaching Clinic Tidak Valid',
                    'data_payment_coaching_clinic' => $payment,
                ], 200);
            }
        }      
    }

    public function number_payment($date, $id){                
        $number = PaymentModel::whereDate('created_at', '=', $date)->get();

        $date_format = Carbon::parse($date)->format('dmy');

        if($number != '[]'){
            $count = $number->count() + 1;
            if ($count >= 10) {
                return 'ECC-'.$id.'-'.$date_format.'-'.$count;
            } else
                return 'ECC-'.$id.'-'.$date_format.'-0'.$count;
        } else {
            return 'ECC-'.$id.'-'.$date_format.'-01';
        }
    }
}
