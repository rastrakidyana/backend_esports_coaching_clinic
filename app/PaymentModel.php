<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PaymentModel extends Model
{
    protected $table = 'payments';

    public $incrementing = false;

    protected $fillable = [
        'id', 'id_coach', 'id_peserta', 'total_pembayaran', 'countdown', 
        'gambar_bukti_pembayaran', 'nama_pengirim', 'nama_bank_pengirim', 
        'no_rek_pengirim', 'nominal_pembayaran', 'is_confirmed',
    ];

    public function getCreatedAt(){
        if(!is_null($this->attributes['created_at'])){
            return Carbon::parse($this->attributes['created_at'])->format('Y-m-d H:i:s');
        }
    }

    public function getUpdatedAt(){
        if(!is_null($this->attributes['updated_at'])){
            return Carbon::parse($this->attributes['updated_at'])->format('Y-m-d H:i:s');
        }
    }
}
