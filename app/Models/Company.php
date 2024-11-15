<?php

namespace App\Models;

use App\Enums\CompanyPersonType;
use App\Traits\HasTags;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use WatheqAlshowaiter\ModelRequiredFields\RequiredFields;

/**
 * @property int $id
 * @property int $category_id
 * @property int $exit_strategy_id
 * @property int $funding_level_id
 * @property int $company_size_id
 * @property \Carbon\Carbon $approved_at
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property string $notes
 * @property int $valuation
 * @property int $exit_valuation
 * @property string $stock_symbol
 * @property string $url
 * @property int $total_funding
 * @property \Carbon\Carbon $last_funding_date
 * @property string $headquarter
 * @property \Carbon\Carbon $founded_at
 * @property string $office_locations
 * @property int $employee_count
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Company extends Model
{
    use HasFactory, HasSlug, HasTags, RequiredFields;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'exit_strategy_id',
        'funding_level_id',
        'company_size_id',
        'approved_at',
        'name',
        'slug',
        'description',
        'short_description',
        'notes',
        'valuation',
        'exit_valuation',
        'stock_symbol',
        'url',
        'total_funding',
        'last_funding_date',
        'headquarter',
        'founded_at',
        'office_locations',
        'employee_count',
        'stock_quote',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'category_id' => 'integer',
        'exit_strategy_id' => 'integer',
        'funding_level_id' => 'integer',
        'company_size_id' => 'integer',
        'approved_at' => 'timestamp',
        'last_funding_date' => 'date',
        'founded_at' => 'date',
        'office_locations' => 'array',
    ];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    /**
     * @return Attribute
     *
     * The source provides only the founding year. When stored in the database,
     * * the current month and day are also recorded, which is incorrect. This method
     * * ensures we only retrieve and store the founding year.
     */
    protected function foundedAt(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? Carbon::parse($value)->format('Y') : null
        );
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function exitStrategy(): BelongsTo
    {
        return $this->belongsTo(ExitStrategy::class);
    }

    public function companySize(): BelongsTo
    {
        return $this->belongsTo(CompanySize::class);
    }

    public function fundingLevel(): BelongsTo
    {
        return $this->belongsTo(FundingLevel::class, 'funding_level_id');
    }

    public function people(): BelongsToMany
    {
        return $this->belongsToMany(Person::class)->withPivot('type');
    }

    public function founders(): BelongsToMany
    {
        return $this->belongsToMany(Person::class)->wherePivot('type', CompanyPersonType::Founder);
    }

    public function alternatives(): BelongsToMany
    {
        return $this->belongsToMany(Alternative::class);
    }

    public function investors(): BelongsToMany
    {
        return $this->belongsToMany(Investor::class);
    }

    public function resources(): MorphMany
    {
        return $this->morphMany(Resource::class, 'resourceable');
    }

    public function logo(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }
   
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable')->withTimestamps();
    }

    public function officeLocations()
    {
        return $this->belongsToMany(OfficeLocation::class, 'company_office_location')->withTimestamps();
    }

}
