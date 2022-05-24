<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class RegisterCoachingClinicModel extends Model
{
    use SoftDeletes;

    protected $table = 'register_coaching_clinics';

    protected $fillable = [
        'id_peserta', 'id_my_coaching_clinic', 'id_coaching_clinic', 
        'id_jadwal_coaching_clinic', 'id_pembayaran', 'jml_beli_kuota', 
        'harga_pendaftaran', 'status_pendaftaran', 'is_my',
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

    public function getDeletedAt(){
        if(!is_null($this->attributes['deleted_at'])){
            return Carbon::parse($this->attributes['deleted_at'])->format('Y-m-d H:i:s');
        }
    }
}
