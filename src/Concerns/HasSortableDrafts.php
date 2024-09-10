<?php

namespace LiveSource\Chord\Concerns;

use ArrayAccess;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Event;
use InvalidArgumentException;
use Spatie\EloquentSortable\EloquentModelSortedEvent;
use Spatie\EloquentSortable\SortableTrait;

trait HasSortableDrafts
{
    use SortableTrait;

    public static function setNewOrder(
        $ids,
        int $startOrder = 1,
        ?string $primaryKeyColumn = null,
        ?callable $modifyQuery = null
    ): void {
        if (! is_array($ids) && ! $ids instanceof ArrayAccess) {
            throw new InvalidArgumentException('You must pass an array or ArrayAccess object to setNewOrder');
        }

        $model = new static;

        $orderColumnName = $model->determineOrderColumnName();

        if (is_null($primaryKeyColumn)) {
            $primaryKeyColumn = $model->getQualifiedKeyName();
        }

        if (config('eloquent-sortable.ignore_timestamps', false)) {
            static::$ignoreTimestampsOn = array_values(array_merge(static::$ignoreTimestampsOn, [static::class]));
        }

        foreach ($ids as $id) {
            $record = static::withoutGlobalScope(SoftDeletingScope::class)
                ->when(is_callable($modifyQuery), function ($query) use ($modifyQuery) {
                    return $modifyQuery($query);
                })
                ->where($primaryKeyColumn, $id)
                ->first();

            // if the existing order is the same as the new order, don't worry about updating
            // consider a PR to the package to allow for this

            $newOrder = $startOrder++;
            if ($newOrder === $record->$orderColumnName) {
                continue;
            }

            // if the record is published, make the change as a draft
            if ($record->isPublished()) {
                $record->asDraft();
            }

            $record->update([$orderColumnName => $newOrder]);
        }

        Event::dispatch(new EloquentModelSortedEvent(static::class));

        if (config('eloquent-sortable.ignore_timestamps', false)) {
            static::$ignoreTimestampsOn = array_values(array_diff(static::$ignoreTimestampsOn, [static::class]));
        }
    }
}
