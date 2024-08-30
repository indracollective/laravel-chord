@props(['block'])
<div class="bg-slate-800 text-white py-20">
    <div>Title: {{ $block->title }}</div>
    <div>Content: {!! $block->content !!}</div>
</div>
