<x-filament-panels::page
    @class([
        'fi-resource-edit-record-page',
        'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
        'fi-resource-record-' . $record->getKey(),
    ])
>
    <div x-data="{ leftWidth: '50%', resizing: false }"
         @mousemove.window="if (resizing) { leftWidth = `${($event.clientX / $el.clientWidth) * 100}%` }"
         @mouseup.window="resizing = false"
         class="flex w-full h-full">
        <div :style="'width: ' + leftWidth" class="border-r border-gray-200 pr-4">
            @capture($form)
            <x-filament-panels::form
                id="form"
                :wire:key="$this->getId() . '.forms.' . $this->getFormStatePath()"
                wire:submit="save"
            >
                {{ $this->form }}

                <x-filament-panels::form.actions
                    :actions="$this->getCachedFormActions()"
                    :full-width="$this->hasFullWidthFormActions()"
                />
            </x-filament-panels::form>
            @endcapture

            @php
                $relationManagers = $this->getRelationManagers();
                $hasCombinedRelationManagerTabsWithContent = $this->hasCombinedRelationManagerTabsWithContent();
            @endphp

            @if ((! $hasCombinedRelationManagerTabsWithContent) || (! count($relationManagers)))
                {{ $form() }}
            @endif
            
            @if (count($relationManagers))
                <x-filament-panels::resources.relation-managers
                    :active-locale="isset($activeLocale) ? $activeLocale : null"
                    :active-manager="$this->activeRelationManager ?? ($hasCombinedRelationManagerTabsWithContent ? null : array_key_first($relationManagers))"
                    :content-tab-label="$this->getContentTabLabel()"
                    :content-tab-icon="$this->getContentTabIcon()"
                    :content-tab-position="$this->getContentTabPosition()"
                    :managers="$relationManagers"
                    :owner-record="$record"
                    :page-class="static::class"
                >
                    @if ($hasCombinedRelationManagerTabsWithContent)
                        <x-slot name="content">
                            {{ $form() }}
                        </x-slot>
                    @endif
                </x-filament-panels::resources.relation-managers>
            @endif

            <x-filament-panels::page.unsaved-data-changes-alert />
        </div>
        <div class="flex flex-grow">
            <!-- Resizer -->
            <div
                @mousedown="resizing = true"
                class="cursor-col-resize w-6 flex items-center justify-center"
            >
                <div class="w-1.5 border-x border-black h-8"></div>
            </div>
            <div class="w-full bg-white flex-grow">
                <iframe src="{{ $record->link }}" class="w-full h-full"></iframe>
            </div>
        </div>
    </div>
</x-filament-panels::page>
