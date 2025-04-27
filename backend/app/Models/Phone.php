<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Phone extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'model',
        'price',
        'stock',
    ];

    /**
     * Get all of the credit applications for the phone model.
     */
    public function creditApplications(): HasMany
    {
        return $this->hasMany(CreditApplication::class);
    }
}
