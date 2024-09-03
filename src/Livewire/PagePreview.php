<?php

namespace LiveSource\Chord\Livewire;

use LiveSource\Chord\Models\ChordPage;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class PagePreview extends Component
{
    #[Reactive]
    public ChordPage $page;

    public function mount(ChordPage $page): void
    {
        $this->page = $page;
    }

    public function render(): string
    {
        return <<<'HTML'
        <iframe
            x-ref="iframe"
            src="{{ $page->getLink(absolute: true) }}"
            class="w-full h-full">
        </iframe>
        HTML;
    }
}
