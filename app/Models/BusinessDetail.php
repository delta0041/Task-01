<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'business_name',
        'owner_name',
        'email',
        'phone',
        'gst_number',
        'address',
        'logo',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
