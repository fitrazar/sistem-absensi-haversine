@section('title', 'Edit Data Agenda')

<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card.card-default class="static">
                <a href="{{ route('teacher.agenda.index') }}">
                    <x-button.info-button>
                        <i class="fa-solid fa-arrow-left"></i>
                        Kembali
                    </x-button.info-button>
                </a>

                <x-form action="{{ route('teacher.agenda.update', $agenda->id) }}" class="md:grid md:grid-cols-2 gap-4"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mt-4">
                        <x-input.input-label for="schedule_id" :value="__('Jadwal Mapel')" />
                        <x-input.select-input id="schedule_id" class="select2 mt-1 w-full" name="schedule_id" required
                            autofocus autocomplete="schedule_id">
                            <option value="" disabled selected>Pilih Jadwal Mapel</option>
                            @foreach ($schedules as $schedule)
                                <option value="{{ $schedule->id }}"
                                    {{ old('schedule_id', $agenda->schedule_id) == $schedule->id ? ' selected' : '' }}>
                                    {{ $schedule->subject->name }} ({{ $schedule->time_in }} -
                                    {{ $schedule->time_out }})
                                </option>
                            @endforeach
                        </x-input.select-input>
                    </div>

                    <div class="mt-4">
                        <x-input.input-label for="note" :value="__('Deksripsi')" />
                        <x-input.text-input id="note" class="mt-1 w-full" type="text" name="note"
                            :value="old('note', $agenda->note)" required autofocus autocomplete="note" />
                        <x-input.input-error :messages="$errors->get('note')" class="mt-2" />
                    </div>

                    <div class="col-span-2">
                        <x-button.primary-button type="submit">
                            {{ __('Simpan') }}
                        </x-button.primary-button>
                    </div>

                </x-form>
            </x-card.card-default>

            <x-card.card-default class="static mt-6">
                <div class="mt-8">
                    <table id="resultTable" class="w-full border-collapse border border-gray-200">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2">Nama Siswa</th>
                                <th class="border px-4 py-2">Status Kehadiran</th>
                                <th class="border px-4 py-2">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($studentAttendance as $attendance)
                                <tr>
                                    <td class="border px-4 py-2">{{ $attendance['name'] }}</td>
                                    <td class="border px-4 py-2">{{ $attendance['status'] }}</td>
                                    <td class="border px-4 py-2">{{ $attendance['created_at'] }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card.card-default>
        </div>
    </div>

    <x-slot name="script">
        <script>
            $(document).ready(function() {
                $('#schedule_id').change(function() {
                    var scheduleId = $(this).val();

                    if (scheduleId) {
                        $.ajax({
                            url: "{{ route('teacher.search') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                schedule_id: scheduleId
                            },
                            success: function(response) {
                                $('#resultTable tbody').empty();

                                function formatDate(dateString) {
                                    var options = {
                                        year: 'numeric',
                                        month: '2-digit',
                                        day: '2-digit',
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    };
                                    var date = new Date(dateString);
                                    return date.toLocaleDateString('id-ID',
                                        options);
                                }

                                $.each(response, function(index, data) {
                                    var formattedDate = formatDate(data
                                        .created_at);
                                    formattedDate = formattedDate == 'Invalid Date' ? '-' :
                                        formattedDate;

                                    $('#resultTable tbody').append(
                                        '<tr>' +
                                        '<td class="border px-4 py-2">' + data.name +
                                        '</td>' +
                                        '<td class="border px-4 py-2">' + data.status +
                                        '</td>' +
                                        '<td class="border px-4 py-2">' +
                                        formattedDate + '</td>' +
                                        '</tr>'
                                    );
                                });
                            },
                            error: function(xhr) {
                                console.log(xhr.responseText);
                            }
                        });
                    }
                });
            });
        </script>
    </x-slot>

</x-app-layout>
