@props(['page'])

@php($hasChildren = $page->children->count() > 0)

<li @class(['relative group', 'submenu-item' => $hasChildren])>
    <span :class="{
        'text-base text-white group-[.sticky]/menu:text-dark py-2 lg:py-6 lg:inline-flex lg:px-0 flex mx-8 lg:mr-0 cursor-pointer': true,
        'text-dark hover:text-primary': sticky
    }">
        @if(!$hasChildren)
            <a href="{{ $page->getLink() }}">
                {{ $page->title }}
            </a>
        @else
            {{ $page->title }}
        @endif
    </span>

    @if($hasChildren)
        <div
            class="submenu hidden relative lg:absolute w-[250px] top-full lg:top-[110%] left-0 lg:shadow-lg p-4 lg:block lg:opacity-0 lg:invisible group-hover:opacity-100 lg:group-hover:visible lg:group-hover:top-full bg-white transition-[top] duration-300">
            @foreach($page->children as $child)
                <a href="{{ $child->getLink() }}"
                   class=" block text-sm text-body-color rounded hover:text-primary py-3 px-4">
                    {{ $child->title }}
                </a>
            @endforeach
        </div>
    @endif
</li>

