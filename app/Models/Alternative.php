<?php

namespace App\Models;

use App\Traits\HasTags;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property Carbon $approved_at
 * @property string $logo
 * @property string $notes
 * @property string $url
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Alternative extends Model
{
    use HasFactory;
    use HasTags;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description', 'approved_at', 'logo', 'notes', 'url'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'approved_at' => 'timestamp',
    ];

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class);
    }

    public function resources(): MorphMany
    {
        return $this->morphMany(Resource::class, 'resourceable');
    }

    public function logo(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}
