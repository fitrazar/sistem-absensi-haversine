@section('title', 'Waktu Absen')

<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card.card-default class="static">
                @if (session()->has('success'))
                    <x-alert.success :message="session('success')" />
                @endif

                <x-form action="{{ route('time.store') }}" class="md:grid md:grid-cols-2 gap-4">
                    @csrf

                    <div class="mt-4">
                        <x-input.input-label for="time_in_early" :value="__('Jam Masuk Awal')" />
                        <x-input.text-input id="time_in_early" class="mt-1 w-full" type="time" name="time_in_early"
                            :value="old('time_in_early', $time->time_in_early)" required autofocus autocomplete="time_in_early" />
                        <x-input.input-error :messages="$errors->get('time_in_early')" class="mt-2" />
                    </div>
                    <div class="mt-4">
                        <x-input.input-label for="time_in_lately" :value="__('Jam Masuk Akhir')" />
                        <x-input.text-input id="time_in_lately" class="mt-1 w-full" type="time" name="time_in_lately"
                            :value="old('time_in_lately', $time->time_in_lately)" required autofocus autocomplete="time_in_lately" />
                        <x-input.input-error :messages="$errors->get('time_in_lately')" class="mt-2" />
                    </div>
                    <div class="mt-4">
                        <x-input.input-label for="time_out_early" :value="__('Jam Pulang Awal')" />
                        <x-input.text-input id="time_out_early" class="mt-1 w-full" type="time" name="time_out_early"
                            :value="old('time_out_early', $time->time_out_early)" required autofocus autocomplete="time_out_early" />
                        <x-input.input-error :messages="$errors->get('time_out_early')" class="mt-2" />
                    </div>
                    <div class="mt-4">
                        <x-input.input-label for="time_out_lately" :value="__('Jam Pulang Akhir')" />
                        <x-input.text-input id="time_out_lately" class="mt-1 w-full" type="time"
                            name="time_out_lately" :value="old('time_out_lately', $time->time_out_lately)" required autofocus
                            autocomplete="time_out_lately" />
                        <x-input.input-error :messages="$errors->get('time_out_lately')" class="mt-2" />
                    </div>

                    <div class="col-span-2">
                        <x-button.primary-button type="submit">
                            {{ __('Simpan') }}
                        </x-button.primary-button>
                    </div>

                </x-form>
            </x-card.card-default>

        </div>
    </div>
</x-app-layout>
