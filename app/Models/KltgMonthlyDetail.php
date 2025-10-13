<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $master_file_id
 * @property int $year
 * @property string|null $client
 * @property int $month
 * @property string|null $category
 * @property string $field_type
 * @property string|null $value
 * @property string|null $value_text
 * @property \Illuminate\Support\Carbon|null $value_date
 * @property bool $is_date
 * @property string|null $type
 * @property string|null $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\MasterFile $masterFile
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgMonthlyDetail key(array $key)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgMonthlyDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgMonthlyDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgMonthlyDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgMonthlyDetail whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgMonthlyDetail whereClient($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgMonthlyDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgMonthlyDetail whereFieldType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgMonthlyDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgMonthlyDetail whereIsDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgMonthlyDetail whereMasterFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgMonthlyDetail whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgMonthlyDetail whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgMonthlyDetail whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgMonthlyDetail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgMonthlyDetail whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgMonthlyDetail whereValueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgMonthlyDetail whereValueText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgMonthlyDetail whereYear($value)
 * @mixin \Eloquent
 */
class KltgMonthlyDetail extends Model
{
    protected $table = 'kltg_monthly_details';

    // Allow mass-assign only the columns we actually write
    protected $fillable = [
        'master_file_id','year','month','category','type','field_type',
        'value','value_text','value_date','is_date','status',
    ];

    protected $casts = [
        'year'       => 'integer',
        'month'      => 'integer',
        'is_date'    => 'boolean',
        'value_date' => 'date:Y-m-d', // ensures <input type="date"> gets YYYY-MM-DD
    ];

    /* =========================================================
     |  Relationships
     * =======================================================*/
    public function masterFile()
    {
        return $this->belongsTo(\App\Models\MasterFile::class, 'master_file_id');
    }

    /* =========================================================
     |  Normalizers (mutators)
     * =======================================================*/
    public function setCategoryAttribute($v): void
    {
        $this->attributes['category'] = strtoupper(trim((string) $v));
    }

    public function setTypeAttribute($v): void
    {
        $this->attributes['type'] = strtoupper(trim((string) $v));
    }

    public function setFieldTypeAttribute($v): void
    {
        $v = strtolower(trim((string) $v));
        // only allow 'text' or 'date'
        $this->attributes['field_type'] = $v === 'date' ? 'date' : 'text';
        // keep is_date aligned automatically
        $this->attributes['is_date'] = $this->attributes['field_type'] === 'date';
    }

    // Optional: if someone sets "value", mirror into the correct column
    public function setValueAttribute($v): void
    {
        $this->attributes['value'] = $v;

        $ft = $this->attributes['field_type'] ?? 'text';
        if ($ft === 'date') {
            // accept YYYY-MM-DD only; let controller validate format
            $this->attributes['value_text'] = null;
            $this->attributes['value_date'] = $v ?: null;
        } else {
            $this->attributes['value_text'] = $v ?: null;
            $this->attributes['value_date'] = null;
        }
    }

    /* =========================================================
     |  Scopes & helpers
     * =======================================================*/
    // Scope to pull the exact composite "row" you consider unique
    public function scopeKey($q, array $key)
    {
        $key = array_change_key_case($key, CASE_LOWER);

        return $q->where('master_file_id', Arr::get($key, 'master_file_id'))
                 ->where('year',          Arr::get($key, 'year'))
                 ->where('month',         Arr::get($key, 'month'))
                 ->where('category',      strtoupper((string) Arr::get($key, 'category')))
                 ->where('type',          strtoupper((string) Arr::get($key, 'type')))
                 ->where('field_type',    strtolower((string) Arr::get($key, 'field_type', 'text')));
    }

    // Build the composite key array consistently (use in controller)
    public static function makeKey(
        int $masterFileId, int $year, int $month, string $category, string $type, string $fieldType
    ): array {
        return [
            'master_file_id' => $masterFileId,
            'year'           => $year,
            'month'          => $month,
            'category'       => strtoupper($category),
            'type'           => strtoupper($type),
            'field_type'     => strtolower($fieldType) === 'date' ? 'date' : 'text',
        ];
    }

    // Convenience upsert if you want to centralize logic here (optional)
    public static function upsertDetail(array $key, ?string $value, ?string $status = 'ACTIVE'): self
    {
        $key['category']   = strtoupper($key['category']);
        $key['type']       = strtoupper($key['type']);
        $key['field_type'] = strtolower($key['field_type']) === 'date' ? 'date' : 'text';

        $attrs = [
            'value'   => $value,
            'status'  => $status,
            'is_date' => $key['field_type'] === 'date',
        ];

        return static::updateOrCreate($key, $attrs);
    }
}
