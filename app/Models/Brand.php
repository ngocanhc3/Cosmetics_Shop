<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Brand extends Model
{
    protected $fillable = ['name', 'slug', 'logo', 'website', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];   // 👈 thêm

    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo) {
            return null;
        }

        $logo = trim($this->logo);

        if (Str::startsWith($logo, ['http://', 'https://', '//'])) {
            return $logo;
        }

        $logo = preg_replace('~^storage/~', '', ltrim($logo, '/'));

        if (Storage::disk('public')->exists($logo)) {
            return Storage::disk('public')->url($logo);
        }

        return asset($logo);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', 1);
    } // 👈 thêm
}
