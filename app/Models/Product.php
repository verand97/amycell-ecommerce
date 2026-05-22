<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'full_description',
        'price',
        'sale_price',
        'stock',
        'type',
        'image',
        'images',
        'digital_file',
        'sku',
        'weight',
        'is_active',
        'is_featured',
        'sold_count',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'images' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function isDigital(): bool
    {
        return $this->type === 'digital';
    }

    public function isPhysical(): bool
    {
        return $this->type === 'physical';
    }

    public function isInStock(): bool
    {
        if ($this->isDigital()) return true;
        return $this->stock > 0;
    }

    public function getEffectivePriceAttribute()
    {
        return $this->sale_price ?? $this->price;
    }

    public function getDiscountPercentageAttribute(): ?int
    {
        if (!$this->sale_price) return null;
        return (int) round((($this->price - $this->sale_price) / $this->price) * 100);
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return asset('images/product-placeholder.png');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory(Builder $query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }
}
