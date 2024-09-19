@section('title', 'Edit Data Jadwal Mapel')

<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card.card-default class="static">
                <a href="{{ route('schedule.index') }}">
                    <x-button.info-button>
                        <i class="fa-solid fa-arrow-left"></i>
                        Kembali
                    </x-button.info-button>
                </a>

                <x-form action="{{ route('schedule.update', $schedule->id) }}" class="md:grid md:grid-cols-2 gap-4"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mt-4">
                        <x-input.input-label for="subject_id" :value="__('Nama Mapel')" />
                        <x-input.select-input id="subject_id" class="select2 mt-1 w-full" name="subject_id" required
                            autofocus autocomplete="subject_id">
                            <option value="" disabled selected>Pilih Nama Mapel</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}"
                                    {{ old('subject_id', $schedule->subject_id) == $subject->id ? ' selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </x-input.select-input>
                    </div>

                    <div class="mt-4">
                        <x-input.input-label for="rombel" :value="__('Kelas')" />
                        <x-input.select-input id="rombel" class="mt-1 w-full" type="text" name="rombel" required
                            autofocus autocomplete="rombel">
                            <option value="" disabled selected>Pilih Kelas</option>
                            @foreach ($rombels as $rombel)
                                <option value="{{ $rombel['id'] }}"
                                    {{ old('rombel', $schedule->grade_id . ' ' . $schedule->major_id . ' ' . $schedule->group_id) == $rombel['id'] ? ' selected' : ' ' }}>
                                    {{ $rombel['name'] }}</option>
                            @endforeach
                        </x-input.select-input>
                    </div>

                    <div class="mt-4">
                        <x-input.input-label for="day" :value="__('Hari')" />
                        <x-input.select-input id="day" class="mt-1 w-full" type="text" name="day" required
                            autofocus autocomplete="day">
                            <option value="" disabled selected>Pilih Hari</option>
                            <option value="Senin" {{ old('day', $schedule->day) == 'Senin' ? ' selected' : '' }}>Senin
                            </option>
                            <option value="Selasa" {{ old('day', $schedule->day) == 'Selasa' ? ' selected' : '' }}>
                                Selasa
                            </option>
                            <option value="Rabu" {{ old('day', $schedule->day) == 'Rabu' ? ' selected' : '' }}>Rabu
                            </option>
                            <option value="Kamis" {{ old('day', $schedule->day) == 'Kamis' ? ' selected' : '' }}>Kamis
                            </option>
                            <option value="Jumat" {{ old('day', $schedule->day) == 'Jumat' ? ' selected' : '' }}>Jumat
                            </option>
                        </x-input.select-input>
                    </div>

                    <div class="mt-4">
                        <x-input.input-label for="time_in" :value="__('Jam Masuk')" />
                        <x-input.text-input id="time_in" class="mt-1 w-full" type="time" name="time_in"
                            :value="old('time_in', $schedule->time_in)" required autofocus autocomplete="time_in" />
                        <x-input.input-error :messages="$errors->get('time_in')" class="mt-2" />
                    </div>
                    <div class="mt-4">
                        <x-input.input-label for="time_out" :value="__('Jam Selesai')" />
                        <x-input.text-input id="time_out" class="mt-1 w-full" type="time" name="time_out"
                            :value="old('time_out', $schedule->time_out)" required autofocus autocomplete="time_out" />
                        <x-input.input-error :messages="$errors->get('time_out')" class="mt-2" />
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
