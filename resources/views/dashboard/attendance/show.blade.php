@section('title', 'Detail Absen')

<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card.card-default class="static">
                <a href="{{ route('attendance.index') }}">
                    <x-button.info-button>
                        <i class="fa-solid fa-arrow-left"></i>
                        Kembali
                    </x-button.info-button>
                </a>

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
