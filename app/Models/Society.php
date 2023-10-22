<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Society extends Model
{
    use HasFactory;
    
    /**
     * This function return the active societies ̰
     * @param mixed $query 
     * @return mixed 
     */
    public function scopeActive($query)
    {
        return $query->where(['active' => true]);
    }
}
