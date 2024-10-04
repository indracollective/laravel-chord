<?php

namespace LiveSource\Chord\Concerns;

use Filament\Forms\Form;
use Illuminate\Support\Collection;
use LiveSource\Chord\Facades\Chord;
use LiveSource\Chord\Facades\ModifyChord;
use LiveSource\Chord\Filament\Forms\PageBuilder;

trait HasContentBlocks
{
    protected static function bootHasContentBlocks(): void
    {
        ModifyChord::contentForm(function (Form $form) {
            if (! in_array(HasContentBlocks::class, class_uses($form->getRecord()))) {
                return;
            }

            $form->getComponent('main')->schema([
                ...$form->getComponent('main')->getChildComponents(),
                PageBuilder::make('content.blocks'),
            ]);
        });
    }

    public function blocks(): Collection
    {
        $blocks = collect($this->content['blocks'] ?? []);

        if ($blocks->isEmpty()) {
            return $blocks;
        }

        return $blocks->map(function ($block) {
            $key = $block['type'] ?? null;

            if (! $key) {
                throw new \Exception('Block has no type: ' . json_ecncode($block));
            }

            $class = Chord::getBlockTypeClass($key);

            if (! $class) {
                throw new \Exception("Block Class for key '{$key}' does not exist");
            }

            return $class::from($block['data']);
        });
    }
}
