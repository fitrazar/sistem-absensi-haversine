<div {!! $attributes->merge(['class' => 'card']) !!}>
    <div class="card-body items-center text-center">
        <h2 class="card-title">Quick Menu</h2>
        <p>Akses beberapa menu dengan cepat</p>
        <div class="card-actions justify-center">
            {{ $slot }}
        </div>
    </div>
</div>
