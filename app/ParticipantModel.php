<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Carbon\Carbon;

class ParticipantModel extends Authenticatable
{
    use Notifiable, HasApiTokens;

    protected $table = 'participants';

    protected $fillable = [
        'nama_peserta', 'email_peserta', 'password', 'telp_peserta',
        'foto_profil_peserta'
    ];

    protected $hidden = [
        'password',
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
