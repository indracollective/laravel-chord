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
        <div :style="'width: ' + leftWidth">
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
        <div class="flex">
            <!-- Resizer -->
            <div
                @mousedown="resizing = true"
                class="bg-gray-500 cursor-col-resize w-1"
                style="min-width: 5px; border: 1px solid red"
            ></div>
            <div class="flex-1">
            PREVIEW
            </div>
        </div>
    </div>
</x-filament-panels::page>
