<x-filament-panels::page
    @class([
        'fi-resource-edit-record-page',
        'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
        'fi-resource-record-' . $record->getKey(),
        'chord-edit-page-container'
    ])
>
    <div class="flex chord-edit-page-panel"
         x-data="{
            leftOffset: false,
            leftWidth: $persist('50%').as('chord-edit-page-form-width'),
            resizing: false,
            init() {
                setTimeout(() => {
                    this.leftOffset = $el.getBoundingClientRect().left + 20
                    this.clientWidth = $el.clientWidth
                    console.log('init with', this.leftOffset)
                }, 100)
            },
            handleMouseMove($event) {
                if (this.resizing && Math.abs($event.movementX) > 2) {
                    this.pauseIframePointerEvents()
                    this.leftWidth = `${Math.round( ((($event.clientX - this.leftOffset)) / this.clientWidth) * 100)}%`
                }
            },
            handleMouseUp($event) {
                this.resizing = false
                this.resumeIframePointerEvents()
            },
            pauseIframePointerEvents() {
                //this.$refs.iframe.style.pointerEvents = 'none'
            },
            resumeIframePointerEvents() {
                //this.$refs.iframe.style.pointerEvents = ''
            }
         }"
         @mousemove.window="handleMouseMove($event)"
         @mouseup.window="handleMouseUp($event)"
    >
        <!-- Left Column -->
        <div class="h-full overflow-y-auto px-0.5" :style="'width: ' + leftWidth"
        >
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

        <!-- Right Column -->
        <div class="flex flex-grow h-full overflow-y-auto">
            <!-- Resizer -->
            <div
                @mousedown="resizing = true"
                class="cursor-col-resize w-6 flex items-center justify-center"
            >
                <div class="w-1.5 border-x border-black h-8"></div>
            </div>
            <div class="w-full bg-white flex-grow" wire:ignore>
                <x-chord::preview :page="$record" />
            </div>
        </div>
    </div>
</x-filament-panels::page>
