<?php

namespace LiveSource\Chord\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LiveSource\Chord\Models\Site;

trait HasSite
{
    // boot
    public static function bootHasSite(): void
    {
        static::saving(function (Model $record) {
            $record->site_id = $record->site_id ?? $record->getDefaultSiteId();
        });
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    public function getDefaultSiteId(): int
    {
        // todo: update to allow for multiple sites and determine
        // the default site based on the current context
        return Site::firstOrFail()->id;
    }
}
