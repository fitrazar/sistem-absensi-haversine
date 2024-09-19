@section('title', 'Data Jadwal Mapel')

<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card.card-default class="static">
                @if (session()->has('success'))
                    <x-alert.success :message="session('success')" />
                @endif

                <div class="flex justify-start space-x-4">
                    <a href="{{ route('schedule.create') }}">
                        <x-button.primary-button>
                            <i class="fa-solid fa-plus"></i>
                            Tambah Data
                        </x-button.primary-button>
                    </a>
                    {{-- <x-form action="{{ route('subject.import') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="relative">
                            <x-input.input-file type="file" id="file" name="file" required />
                            <x-button.success-button>
                                <i class="fa-solid fa-file-import"></i>
                                Import
                            </x-button.success-button>
                        </div>
                    </x-form>
                    <a href="{{ route('subject.export') }}">
                        <x-button.info-button>
                            <i class="fa-regular fa-file-excel"></i>
                            Export
                        </x-button.info-button>
                    </a> --}}
                </div>

                <div class="relative overflow-x-auto mt-5">
                    <table id="subjects" class="table">
                        <thead>
                            <tr>
                                <th scope="col" class="px-6 py-3">
                                    No
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Nama Mapel
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Kelas
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Hari
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Jam
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </x-card.card-default>
        </div>
    </div>

    <x-slot name="script">
        <script>
            $(document).ready(function() {


                let dataTable = $('#subjects').DataTable({
                    buttons: [
                        // 'copy', 'excel', 'csv', 'pdf', 'print',
                        'colvis'
                    ],
                    processing: true,
                    search: {
                        return: true
                    },
                    serverSide: true,
                    ajax: '{{ url()->current() }}',
                    columns: [{
                            data: null,
                            name: 'no',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            }
                        },
                        {
                            data: 'subject.name',
                            name: 'subject.name'
                        },
                        {
                            data: null,
                            render: function(data) {
                                return data.grade.name + ' ' + data.major.acronym + ' ' + data.group
                                    .number;
                            },
                            orderable: false,
                            searchable: false,
                        },
                        {
                            data: 'day',
                            name: 'day'
                        },
                        {
                            data: null,
                            render: function(data) {
                                return data.time_in + ' - ' + data.time_out;
                            },
                            orderable: false,
                            searchable: false,
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, full, meta) {
                                return `
                                <a href="{{ url('/schedule/${full.id}/edit') }}">
                                    <x-button.info-button type="button" class="btn-sm text-white"><i class="fa-regular fa-pen-to-square"></i>Edit</x-button.info-button>
                                </a>
                                <x-form action="{{ url('/schedule/${full.id}') }}" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <x-button.danger-button type="submit" class="btn-sm text-white" onclick="return confirm('Are you sure?')"><i class="fa-regular fa-trash-can"></i>Hapus</x-button.danger-button>
                                </x-form>
                            `;
                            }
                        },
                    ]
                });
            });
        </script>
    </x-slot>
</x-app-layout>
