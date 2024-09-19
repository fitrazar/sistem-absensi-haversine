@section('title', 'Data Mapel')

<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-form method="GET" action="{{ route('student.subject.index') }}">
                <x-input.text-input type="search" name="search" placeholder="Cari..." :value="$search" />
                <x-button.primary-button type="submit">Cari</x-button.primary-button>
            </x-form>
            <div class="pt-10">
                <div class="grid lg:grid-cols-3 gap-6 grid-cols-1 md:grid-cols-2">
                    @forelse ($schedules as $schedule)
                        <x-card.card-default class="static">
                            <h2 class="text-left text-green-400 font-bold text-lg">
                                {{ $schedule->subject->name }} ({{ $schedule->day }})
                            </h2>
                            <span class="text-xs">{{ $schedule->time_in }} - {{ $schedule->time_out }}</span>
                            <p class="text-justify">
                                {{ $schedule->subject->description ?? 'Deksripsi Kosong' }}
                            </p>
                            <div class="card-actions justify-end">
                                <div class="badge badge-outline">
                                    {{ $schedule->subject->teacher->gender == 'Laki - Laki' ? 'Pak ' : 'Bu ' }}
                                    {{ $schedule->subject->teacher->name }}
                                </div>
                            </div>
                        </x-card.card-default>
                    @empty
                        <x-alert.warning message="Uppss: Data Tidak Ditemukan" />
                    @endforelse
                </div>
            </div>
            <div class="join">
                {{ $schedules->appends(['search' => $search])->links() }}
            </div>
        </div>
    </div>

</x-app-layout>
