@props(['method'])

<form method="{{ $method ?? 'POST' }}" {!! $attributes->merge(['class' => 'ml-1']) !!}>
    {{ $slot }}
</form>
