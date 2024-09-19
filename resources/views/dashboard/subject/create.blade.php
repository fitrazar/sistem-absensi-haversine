@section('title', 'Tambah Data Mapel')

<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card.card-default class="static">
                <a href="{{ route('subject.index') }}">
                    <x-button.info-button>
                        <i class="fa-solid fa-arrow-left"></i>
                        Kembali
                    </x-button.info-button>
                </a>

                <x-form action="{{ route('subject.store') }}" class="md:grid md:grid-cols-2 gap-4"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="mt-4">
                        <x-input.input-label for="name" :value="__('Nama Mapel')" />
                        <x-input.text-input id="name" class="mt-1 w-full" type="text" name="name"
                            :value="old('name')" required autofocus autocomplete="name" />
                        <x-input.input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input.input-label for="teacher_id" :value="__('Guru')" />
                        <x-input.select-input id="teacher_id" class="select2 mt-1 w-full" name="teacher_id" required
                            autofocus autocomplete="teacher_id">
                            <option value="" disabled selected>Pilih Guru</option>
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}"
                                    {{ old('teacher_id') == $teacher->id ? ' selected' : '' }}>
                                    {{ $teacher->name }}
                                </option>
                            @endforeach
                        </x-input.select-input>
                    </div>

                    <div class="mt-4">
                        <x-input.input-label for="description" :value="__('Deskripsi')" />
                        <x-input.text-input id="description" class="mt-1 w-full" type="text" name="description"
                            :value="old('description')" required autofocus autocomplete="description" />
                        <x-input.input-error :messages="$errors->get('description')" class="mt-2" />
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
