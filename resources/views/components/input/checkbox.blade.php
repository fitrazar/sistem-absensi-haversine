@props(['title', 'value'])

<span class="label-text">{{ $title ?? '' }}</span>
<input type="checkbox" {{ $value ?? '' }} {{ $attributes->merge(['class' => 'checkbox']) }} />
