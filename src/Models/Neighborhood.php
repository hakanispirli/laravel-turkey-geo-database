<?php

namespace Webmarka\TurkeyGeo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Neighborhood extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'neighborhoods';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'id',
        'district_id',
        'name',
        'area',
        'postal_code',
    ];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Get the district that owns the neighborhood.
     *
     * @return BelongsTo
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    /**
     * Get the city through the district.
     *
     * @return BelongsTo
     */
    public function city(): BelongsTo
    {
        return $this->district()->getRelated()->city();
    }

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable(): string
    {
        return config('turkey-geo.tables.neighborhoods', parent::getTable());
    }
}
