<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MyCoachingClinicModel extends Model
{
    protected $table = 'my_coaching_clinics';

    protected $fillable = [
        'id_coaching_clinic', 'id_jadwal_coaching_clinic', 'id_peserta',
        'id_penilaian_coach', 'status_my_coaching_clinic',
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
