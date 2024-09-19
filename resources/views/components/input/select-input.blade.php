@props(['disabled' => false])

<select {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
    'class' => 'select select-bordered text-base-content',
]) !!}>
    {{ $slot }}

</select>
