@section('title', 'Absensi Siswa')

<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card.card-default class="static">
                @if (session()->has('success'))
                    <x-alert.success :message="session('success')" />
                @endif

                <div class="flex justify-start space-x-4">
                    <x-form id="export-form" action="{{ route('attendance.export') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="fromExport" name="fromExport" value="">
                        <input type="hidden" id="toExport" name="toExport" value="">
                        <x-button.info-button id="export-button" type="submit">
                            <i class="fa-regular fa-file-excel"></i>
                            Export
                        </x-button.info-button>
                    </x-form>
                </div>

                <div class="flex justify-start space-x-4">
                    <div class="mt-4">
                        <x-input.text-input id="from" class="mt-1 w-full" type="date" name="from" required />
                    </div>
                    <div class="mt-4">
                        <x-input.text-input id="to" class="mt-1 w-full" type="date" name="to" required />
                    </div>
                </div>

                <div class="relative overflow-x-auto mt-5">
                    <table id="attendances" class="table">
                        <thead>
                            <tr>
                                <th scope="col" class="px-6 py-3">
                                    No
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Nama Siswa
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Nama Mapel
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Lokasi / File
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
                $('#export-button').on('click', function() {
                    let from = $('#from').val();
                    let to = $('#to').val();

                    $('#export-form #fromExport').val(from);
                    $('#export-form #toExport').val(to);
                });

                let dataTable = $('#attendances').DataTable({
                    buttons: [
                        // 'copy', 'excel', 'csv', 'pdf', 'print',
                        'colvis'
                    ],
                    processing: true,
                    search: {
                        return: true
                    },
                    serverSide: true,
                    ajax: {
                        url: '{{ route('attendance.index') }}',
                        data: function(d) {
                            d.from = $('#from').val();
                            d.to = $('#to').val();
                        }
                    },
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
                            data: null,
                            render: function(data) {
                                return data.student.name;
                            },
                            orderable: false,
                            searchable: false,
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: null,
                            render: function(data) {

                                return data?.schedule?.subject?.name || '-';
                            },
                            orderable: false,
                            searchable: false,
                        },
                        {
                            data: null,
                            render: function(data, type, full, meta) {
                                if (full.status != 'Izin' || full.status != 'Sakit') {
                                    let link = `https://maps.google.com/?q=${full.coordinate}`
                                    return `<a target="_blank" href="${link}">
                                        <x-button.info-button type="button" class="btn-sm text-white"><i class="fa-regular fa-eye"></i>Lihat Peta</x-button.info-button>
                                    </a>`
                                } else {
                                    if (full.status == 'Sakit') {
                                        let link =
                                            `{{ asset('storage/attendance/${full.student.name}/${full.note}') }}`
                                        return `<a target="_blank" href="${link}">
                                            <x-button.info-button type="button" class="btn-sm text-white"><i class="fa-regular fa-eye"></i>Lihat Peta</x-button.info-button>
                                        </a>`
                                    } else {
                                        return `${full.note}`
                                    }
                                }
                            },
                            orderable: false,
                            searchable: false,
                        },
                        {
                            data: null,
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, full, meta) {
                                if (full.status == 1) {
                                    return '';
                                } else {
                                    return `
                                    <a href="{{ url('/attendance/${full.id}') }}">
                                        <x-button.info-button type="button" class="btn-sm text-white"><i class="fa-regular fa-eye"></i>Detail</x-button.info-button>
                                    </a>
                                `;
                                }
                            }
                        },
                    ]
                });

                $('#from, #to').change(function() {
                    dataTable.ajax.reload();
                });
            });
        </script>
    </x-slot>
</x-app-layout>
