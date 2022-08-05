<?php

namespace PrasanthJ\Otpify\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'identifier',
        'token',
        'validity',
        'otp_type',
        'verified'
    ];
}
