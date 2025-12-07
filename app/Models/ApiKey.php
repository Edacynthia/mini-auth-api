<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'service_name', 'key', 'expires_at', 'revoked'
    ];

    protected $casts = [
        'revoked'    => 'boolean',
        'expires_at' => 'datetime',
    ];

    /**
     * Check if API key is valid
     */
    public function isValid(): bool
{
    if ($this->revoked) return false;
    if ($this->expires_at && now()->greaterThan($this->expires_at)) return false;
    return true;
}

}
