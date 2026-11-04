<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Address extends Model
{
    use HasFactory;

    /**
     * الحقول القابلة للتعيين
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'district',
        'street',
        'details',
    ];

    /**
     * المستخدم صاحب هذا العنوان
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * الطلبات التي تمت على هذا العنوان
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * العنوان مجمّعاً في نص واحد واضح
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([$this->district, $this->street, $this->details]);
        return implode(' - ', $parts);
    }
}

