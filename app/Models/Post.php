<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Post extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'meta_title',
        'meta_description',
        'canonical',
        'og_image',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    protected function metaTitle(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ?: $this->title
        );
    }

    protected function metaDescription(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ?: $this->excerpt
        );
    }
}
