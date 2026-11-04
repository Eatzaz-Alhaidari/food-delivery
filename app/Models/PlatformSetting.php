<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatformSetting extends Model
{
    use HasFactory;

    /**
     * الحقول القابلة للتعيين
     *
     * @var list<string>
     */
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    /**
     * استرجاع قيمة إعداد محدد مع دعم القيمة الافتراضية والتحويل للنوع الصحيح
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        if (! $setting) {
            return $default;
        }

        return match ($setting->type) {
            'float' => (float) $setting->value,
            'integer', 'int' => (int) $setting->value,
            'boolean', 'bool' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            default => $setting->value,
        };
    }

    /**
     * حفظ أو تحديث إعداد
     */
    public static function set(string $key, mixed $value, ?string $type = null, ?string $description = null): self
    {
        $setting = static::firstOrNew(['key' => $key]);
        $setting->value = (string) $value;

        if ($type) {
            $setting->type = $type;
        }
        if ($description) {
            $setting->description = $description;
        }

        $setting->save();
        return $setting;
    }

    /**
     * نسبة عمولة المنصة الحالية كنسبة مئوية (الافتراضي 10%)
     */
    public static function getCommissionPercentage(): float
    {
        return (float) static::get('commission_percentage', 10.0);
    }
}

