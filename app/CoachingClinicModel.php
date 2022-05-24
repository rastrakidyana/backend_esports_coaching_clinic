<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class CoachingClinicModel extends Model
{
    use SoftDeletes;

    protected $table = 'coaching_clinics';

    protected $fillable = [
        'id_coach', 'judul_coaching_clinic', 'game_coaching_clinic',
        'harga_coaching_clinic', 'desc_coaching_clinic', 'tipe_coaching_clinic',
        'at_coaching_clinic', 'link_coaching_clinic', 'gambar_coaching_clinic',
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
