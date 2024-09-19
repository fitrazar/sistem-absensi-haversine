@props(['title'])

<div {!! $attributes->merge(['class' => 'card']) !!}>
    <div class="card-body">
        <h2 class="card-title">{{ $title ?? '' }}</h2>
        {{ $slot }}
    </div>
</div>
