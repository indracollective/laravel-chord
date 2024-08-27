@props(['component'])

@php
    // Remove the 'component' prop from the collection of props to avoid passing it twice
    $filteredProps = collect($attributes->getAttributes())->except('component')->all();
@endphp

<x-dynamic-component :component="$chordComponent($component)" {{ $attributes->merge($filteredProps) }}>
    {{ $slot }}
</x-dynamic-component>

