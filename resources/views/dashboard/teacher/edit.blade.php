@section('title', 'Edit Data Guru')

<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card.card-default class="static">
                <a href="{{ route('teacher.index') }}">
                    <x-button.info-button>
                        <i class="fa-solid fa-arrow-left"></i>
                        Kembali
                    </x-button.info-button>
                </a>

                <x-form action="{{ route('teacher.update', $teacher->id) }}" class="md:grid md:grid-cols-2 gap-4"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="oldImage" value="{{ $teacher->photo }}">
                    @if ($teacher->photo)
                        <div class="avatar">
                            <div class="w-12 rounded-xl">
                                <img src="{{ asset('storage/teacher/photo/' . $teacher->photo) }}" />
                            </div>
                        </div>
                    @endif
                    <div class="mt-4">
                        <x-input.input-label for="photo" :value="__('Foto')" />
                        <x-input.input-file id="photo" class="mt-1 w-full" type="file" name="photo"
                            :value="old('photo', $teacher->photo)" />
                        <x-input.input-error :messages="$errors->get('photo')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input.input-label for="name" :value="__('Nama Guru')" />
                        <x-input.text-input id="name" class="mt-1 w-full" type="text" name="name"
                            :value="old('name', $teacher->name)" required autofocus autocomplete="name" />
                        <x-input.input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input.input-label for="gender" :value="__('Jenis Kelamin')" />
                        <x-input.select-input id="gender" class="mt-1 w-full" type="text" name="gender" required
                            autofocus autocomplete="gender">
                            <option value="" disabled selected>Pilih Jenis Kelamin</option>
                            <option value="Laki - Laki"
                                {{ old('gender', $teacher->gender) == 'Laki - Laki' ? ' selected' : ' ' }}>Laki - Laki
                            </option>
                            <option value="Perempuan"
                                {{ old('gender', $teacher->gender) == 'Perempuan' ? ' selected' : ' ' }}>
                                Perempuan</option>
                        </x-input.select-input>
                    </div>

                    <div class="mt-4">
                        <x-input.input-label for="phone" :value="__('No Telpon')" />
                        <x-input.text-input id="phone" class="mt-1 w-full" type="number" name="phone"
                            :value="old('phone', $teacher->phone)" required autofocus autocomplete="phone" />
                        <x-input.input-error :messages="$errors->get('phone')" class="mt-2" />
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
