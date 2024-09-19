@section('title', 'Pengaturan Website')

<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card.card-default class="static">
                @if (session()->has('success'))
                    <x-alert.success :message="session('success')" />
                @endif

                <x-form action="{{ route('setting.store') }}" class="md:grid md:grid-cols-2 gap-4"
                    enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" name="oldImage" value="{{ $setting?->logo }}">
                    <div class="mt-4">
                        @if ($setting?->logo)
                            <div class="avatar">
                                <div class="w-32 rounded-xl">
                                    <img src="{{ asset('storage/' . $setting?->logo) }}" />
                                </div>
                            </div>
                        @endif
                        <img class="imgPreview h-auto max-w-lg mx-auto hidden" alt="logo">
                        <x-input.input-label for="logo" :value="__('Logo')" />
                        <x-input.input-file id="logo" class="mt-1 w-full" type="file" name="logo"
                            :value="old('logo', $setting?->logo)" autofocus autocomplete="logo" onchange="previewImage()" />
                        <x-input.input-error :messages="$errors->get('logo')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input.input-label for="name" :value="__('Nama Sekolah')" />
                        <x-input.text-input id="name" class="mt-1 w-full" type="text" name="name"
                            :value="old('name', $setting->name ?? '')" required autofocus autocomplete="name" />
                        <x-input.input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div class="mt-4">
                        <x-input.input-label for="coordinate" :value="__('Titik Koordinat')" />
                        <x-input.text-input id="coordinate" class="mt-1 w-full" type="text" name="coordinate"
                            :value="old('coordinate', $setting->coordinate ?? '')" required autofocus autocomplete="coordinate" />
                        <x-input.input-error :messages="$errors->get('coordinate')" class="mt-2" />
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
    <x-slot name="script">
        <script>
            function previewImage() {
                const image = document.querySelector('#logo')
                const imgPreview = document.querySelector('.imgPreview')

                imgPreview.style.display = 'block'

                const oFReader = new FileReader()
                oFReader.readAsDataURL(image.files[0])
                oFReader.onload = function(oFREvent) {
                    imgPreview.src = oFREvent.target.result
                }
            }
        </script>
    </x-slot>
</x-app-layout>
