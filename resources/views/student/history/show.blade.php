@section('title', 'Detail Absen')

<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card.card-default class="static">

                <div class="flex justify-between items-center">
                    <a href="{{ route('student.history.index') }}">
                        <x-button.info-button>
                            <i class="fa-solid fa-arrow-left"></i>
                            Kembali
                        </x-button.info-button>
                    </a>
                    @php
                        $date = Carbon\Carbon::parse($attendance->created_at);
                        $timeInEarly = Carbon\Carbon::createFromFormat('H:i:s', $waktuAbsen->time_in_early);
                        $timeInLate = Carbon\Carbon::createFromFormat('H:i:s', $waktuAbsen->time_in_lately);
                        $timeOutEarly = Carbon\Carbon::createFromFormat('H:i:s', $waktuAbsen->time_out_early);
                        $timeOutLate = Carbon\Carbon::createFromFormat('H:i:s', $waktuAbsen->time_out_lately);
                        $currentTime = Carbon\Carbon::now();

                    @endphp
                    @if (
                        ($date->isToday() && $currentTime->between($timeInEarly, $timeInLate)) ||
                            ($date->isToday() && $currentTime->gt($timeInLate) && $currentTime->lt($timeOutEarly)))
                        <x-button.danger-button class="btn text-white" onclick="my_modal_1.showModal()">
                            <i class="fa-regular fa-trash-can"></i>
                            Hapus
                        </x-button.danger-button>
                        <dialog id="my_modal_1" class="modal bg-base-200">
                            <div class="modal-box p-4">
                                <h3 class="text-lg font-bold">Halo {{ auth()->user()->student->name }}</h3>

                                <p class="mt-4">Note : Absen yang bisa dihapus hanya absen masuk </p>
                                <p>jika kamu menghapus absen masuk di jam setelah absen masuk berakhir </p>
                                <p>lalu kamu melakukan absen lagi, maka status absen masuk kamu berubah menjadi
                                    terlambat.</p>
                                <p>Penghapusan Absen dilakukan hanya jika kamu salah menginput status absen.</p>
                                <p class="py-4">Apakah kamu yakin ingin menghapus absen ini?</p>
                                <div class="modal-action flex justify-start mt-4 space-x-4">
                                    <x-form action="{{ route('student.history.destroy', $attendance->id) }}"
                                        style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <x-button.danger-button type="submit" class="text-white"
                                            onclick="return confirm('Kamu Yakin Ingin Menghapus Absen Ini?')">
                                            Yakin
                                        </x-button.danger-button>
                                    </x-form>
                                    <x-form method="dialog" class="inline">
                                        <!-- if there is a button in form, it will close the modal -->
                                        <button class="btn">Tidak</button>
                                    </x-form>
                                </div>
                            </div>
                        </dialog>
                    @endif
                </div>

                <div class="stats shadow">

                    <div class="stat place-items-center">
                        <div class="stat-title text-lg">Biodata</div>
                        <div class="stat-value text-base">{{ $attendance->student->name }}</div>
                        <div class="stat-desc">{{ $attendance->student->grade->name }}
                            {{ $attendance->student->major->acronym }} {{ $attendance->student->group->number }}</div>
                    </div>

                    <div class="stat place-items-center">
                        <div class="stat-title text-lg">Status</div>
                        <div class="stat-value text-base">
                            {{ $attendance->status == 'Absen Mapel' ? $attendance->status . ' ' . $attendance->schedule->subject->name : $attendance->status }}
                        </div>

                        <div class="stat-desc">{{ $attendance->created_at }}</div>
                    </div>

                    @if ($attendance->status == 'Izin' || $attendance->status == 'Sakit')

                        <div class="stat place-items-center">
                            <div class="stat-title text-lg">Keterangan</div>
                            <div class="stat-value text-base">
                                @if ($attendance->status == 'Izin')
                                    {{ $attendance->note }}
                                @else
                                    <a href="{{ asset('storage/attendance/' . $attendance->student->name . '/' . $attendance->note) }}"
                                        target="_blank">
                                        <span class="badge badge-primary">Surat Sakit</span>
                                    </a>
                                @endif
                            </div>

                        </div>

                    @endif

                </div>

            </x-card.card-default>
        </div>
    </div>

</x-app-layout>
