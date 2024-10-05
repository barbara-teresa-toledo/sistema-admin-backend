<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ServiceOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'service_date',
        'service_description',
        'client_id',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
