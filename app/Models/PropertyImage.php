<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'path',
        'thumbnail_path',
        'original_name',
        'sort_order',
        'is_primary',
        'width',
        'height',
        'size',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_primary' => 'boolean',
        'width' => 'integer',
        'height' => 'integer',
        'size' => 'integer',
    ];

    // Relationships
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    // Helpers
    public function getUrl()
    {
        return asset('storage/' . $this->path);
    }

    /**
     * Public URL of the generated thumbnail, falling back to the full
     * image for records created before thumbnails existed.
     */
    public function getThumbnailUrl(): string
    {
        return asset('storage/' . ($this->thumbnail_path ?: $this->path));
    }

    /**
     * Human readable file size, e.g. "248 KB".
     */
    public function getReadableSize(): ?string
    {
        if (!$this->size) {
            return null;
        }

        return $this->size >= 1048576
            ? round($this->size / 1048576, 2) . ' MB'
            : round($this->size / 1024) . ' KB';
    }
}
