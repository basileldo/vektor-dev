<?php

namespace Vektor\Checkout;

use Gloudemans\Shoppingcart\CanBeBought;
use Gloudemans\Shoppingcart\Contracts\Buyable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends Model implements Buyable
{
    use CanBeBought;
    use HasSlug;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'slug',
        'name',
        'name_label',
        'sku',
        'price',
        'images',
        'configuration',
        'attributes',
        'sort_order',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that are to be casted.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'float',
        'images' => 'array',
        'configuration' => 'array',
        'attributes' => 'array',
        'sort_order' => 'integer',
    ];

    public function setImagesAttribute($value)
    {
        $images = [];

        if (!empty($value) && is_array($value)) {
            foreach ($value as $array_key => $array_item) {
                if (!is_null($array_item)) {
                    $images[$array_key] = $array_item;
                }
            }
        }

        $this->attributes['images'] = json_encode($images);
    }

    public function setConfigurationAttribute($value)
    {
        $configuration = [];

        if (!empty($value) && is_array($value)) {
            foreach ($value as $array_key => $array_item) {
                if (!is_null($array_item)) {
                    $configuration[$array_key] = $array_item;
                }
            }
        }

        $this->attributes['configuration'] = json_encode($configuration);
    }

    public function setAttributesAttribute($value)
    {
        $attributes = [];

        if (!empty($value) && is_array($value)) {
            foreach ($value as $array_key => $array_item) {
                if (!is_null($array_item)) {
                    $attributes[$array_key] = $array_item;
                }
            }
        }

        $this->attributes['attributes'] = json_encode($attributes);
    }

    /**
     * Get the products for the product.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'parent_id');
    }

    /**
     * Get the product that owns the product.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'parent_id');
    }

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
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
