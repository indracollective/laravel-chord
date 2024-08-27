<x-dynamic-component :component="$page->getBaseLayout()" :page="$page">
    <h1 class="font-bold">{{ $page->title }}</h1>
    <p>This is the default chord page layout</p>
</x-dynamic-component>
