<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class ScheduleCoachingClinicModel extends Model
{
    use SoftDeletes;

    protected $table = 'schedule_coaching_clinics';

    protected $fillable = [
        'id_coaching_clinic', 'tgl_coaching_clinic', 'mulai_coaching_clinic',
        'berakhir_coaching_clinic', 'kuota_coaching_clinic',
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
