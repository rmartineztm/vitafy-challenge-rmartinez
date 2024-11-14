<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class Client extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = ['lead_id'];
    
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
