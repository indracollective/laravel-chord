<?php

namespace LiveSource\Chord\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Wildside\Userstamps\Userstamps;

class Site extends Model
{
    use Userstamps;

    protected $fillable = [
        'title',
        'protocol',
        'hostname',
        'is_default',
        'meta',
        'is_published',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    protected static string $defaultBaseLayout = 'pages.layout';

    public static function defaultBaseLayout(string $layout): void
    {
        static::$defaultBaseLayout = $layout;
    }

    public function getBaseLayout(): string
    {
        return $this->meta['baseLayout'] ?? static::$defaultBaseLayout;
    }

    public function getLink(): string
    {
        return "$this->protocol://$this->hostname";
    }

    public function pages(): HasMany
    {
        return $this->hasMany(ChordPage::class, 'site_id');
    }
}
