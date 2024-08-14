<?php

namespace LiveSource\Chord\Filament\Resources\PageResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use LiveSource\Chord\Filament\Resources\PageResource;
use LiveSource\Chord\Models\Page;

class ListPages extends ListRecords
{
    protected static string $resource = PageResource::class;

    protected ?string $maxContentWidth = 'full';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function reorderTable(array $order): void
    {

        Page::setNewOrder($order);
    }

    protected function getTableQuery(): Builder
    {
        $orderedIds = static::getNestedArray();

        // if database is sqlite...

        if (DB::connection()->getDriverName() == 'sqlite') {
            return static::getResource()::getEloquentQuery()->orderByRaw(
                'CASE id ' .
                implode(' ', array_map(
                    fn ($id, $index) => "WHEN $id THEN " . ($index + 1),
                    $orderedIds,
                    array_keys($orderedIds)
                )) . ' ELSE 9999 END'
            );
        } elseif (DB::connection()->getDriverName() == 'mysql') {
            return static::getResource()::getEloquentQuery()->orderByRaw(sprintf('parent_id(id, %s)', implode(',', $orderedIds)));
        } else {
            throw new \Exception('Unsupported database driver');
        }
    }

    public static function getNestedArray($parent_id = null): array
    {
        static $records = null;
        static $ids = [];
        if ($records == null) {
            $records = self::getResource()::getModel()::get(['id', 'parent_id'])->sortByDesc('id')->groupBy('parent_id')->sortBy('parent_id')->toArray();
        }

        if (isset($records[$parent_id]) && count($records[$parent_id])) {
            foreach ($records[$parent_id] as $_id => $_item) {
                $ids[] = $_item['id'];
                if (isset($records[$_item['id']]) && is_array($records[$_item['id']]) && count($records[$_item['id']])) {
                    self::getNestedArray($_item['id']);
                }
            }
        }

        return $ids;
    }
}
