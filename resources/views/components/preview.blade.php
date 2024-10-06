@props(['page'])
@php
    $url = $page->getLink(true) . '?draft';
@endphp
<div x-data="{
    iframe: null,
    updating: false,
    init() {
        this.iframe = document.getElementById('page-preview');

        $wire.on('page-updated', (event) => {
            if (this.updating) return;
            this.reloadIframe()
        })
    },
    reloadIframe() {
        this.updating = true;

        // Create a new invisible iframe element
        const newIframe = document.createElement('iframe');
        newIframe.style.width = 0;
        newIframe.style.height = 0;
        newIframe.style.opacity = 0;
        newIframe.style.position = 'absolute';
        newIframe.src = this.iframe.src;

        // Put it in the DOM so it loads the page
        this.iframe.insertAdjacentElement('afterend', newIframe);

        const handleLoad = () => {
            // Copy all attributes from the old iframe to the new one,
            // except src as that will cause the iframe to be reloaded
            Array.from(this.iframe.attributes).forEach((key) => {
                if (key.nodeName === 'src') return;
                newIframe.setAttribute(key.nodeName, key.nodeValue);
            })

            // Restore scroll position
            newIframe.contentWindow.scroll(
                this.iframe.contentWindow.scrollX,
                this.iframe.contentWindow.scrollY,
            )

            // Remove the old iframe and swap it with the new one
            this.iframe.remove()
            this.iframe = newIframe

            // Make the new iframe visible
            newIframe.style = null

            // Ready for another update
            this.updating = false
        };

        newIframe.addEventListener('load', handleLoad);
    }
}">
    <iframe id="page-preview" src="{{ $url }}" class="w-full h-full pointer-events-none"></iframe>
</div>
