@props(['messages'])

@if ($messages)
    <div role="alert" {{ $attributes->merge(['class' => 'alert alert-error']) }}>
        @foreach ((array) $messages as $message)
            <span>{{ $message }}</span>
        @endforeach
    </div>
    {{-- <ul {{ $attributes->merge(['class' => 'text-sm text-red-600 dark:text-red-400 space-y-1']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul> --}}
@endif
