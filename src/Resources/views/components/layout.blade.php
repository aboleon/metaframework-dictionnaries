@props([
    'containerClass' => 'container-fluid py-4',
])

@stack('css')

<div class="{{ $containerClass }}">
    @isset($header)
        <div class="mb-4">
            {{ $header }}
        </div>
    @endisset

    {{ $slot }}
</div>

@stack('js')
