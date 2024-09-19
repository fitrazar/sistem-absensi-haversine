@section('title', 'Data Siswa')

<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card.card-default class="static">
                @if (session()->has('success'))
                    <x-alert.success :message="session('success')" />
                @endif

                <div class="flex justify-start space-x-4">
                    <a href="{{ route('student.create') }}">
                        <x-button.primary-button>
                            <i class="fa-solid fa-plus"></i>
                            Tambah Data
                        </x-button.primary-button>
                    </a>
                    <x-form action="{{ route('student.import') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="relative">
                            <x-input.input-file type="file" id="file" name="file" required />
                            <x-button.success-button>
                                <i class="fa-solid fa-file-import"></i>
                                Import
                            </x-button.success-button>
                        </div>
                    </x-form>
                    <x-form id="export-form" action="{{ route('student.export') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="jurusanExport" name="jurusanExport" value="">
                        <input type="hidden" id="kelasExport" name="kelasExport" value="">
                        <x-button.info-button id="export-button" type="submit">
                            <i class="fa-regular fa-file-excel"></i>
                            Export
                        </x-button.info-button>
                    </x-form>

                </div>
                <div class="flex justify-start space-x-4">
                    <div class="mt-4">
                        <x-input.select-input id="kelas" class="mt-1 w-full" type="text" name="kelas">
                            <option value="" disabled selected>Pilih Kelas</option>
                            <option value="All">Semua
                            </option>
                            <option value="X">X
                            </option>
                            <option value="XI">XI
                            </option>
                            <option value="XII">XII
                            </option>
                        </x-input.select-input>
                    </div>
                    <div class="mt-4">
                        <x-input.select-input id="jurusan" class="mt-1 w-full" type="text" name="jurusan">
                            <option value="" disabled selected>Pilih Jurusan</option>
                            <option value="All">Semua
                            </option>
                            @foreach ($majors as $major)
                                <option value="{{ $major->id }}">{{ $major->acronym }}
                                </option>
                            @endforeach
                        </x-input.select-input>
                    </div>
                </div>
                <div class="relative overflow-x-auto mt-5">
                    <table id="students" class="table">
                        <thead>
                            <tr>
                                <th scope="col" class="px-6 py-3">
                                    No
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    NISN
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Nama Siswa
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Kelas
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
                    // Ambil nilai dari elemen #jurusan, #from, dan #to
                    let jurusan = $('#jurusan').val();
                    let kelas = $('#kelas').val();

                    // Set nilai input field pada form
                    $('#export-form #jurusanExport').val(jurusan);
                    $('#export-form #kelasExport').val(kelas);
                });


                let dataTable = $('#students').DataTable({
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
                        url: '{{ route('student.index') }}',
                        data: function(d) {
                            d.jurusan = $('#jurusan').val();
                            d.kelas = $('#kelas').val();
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
                            data: 'nisn',
                            name: 'nisn'
                        },
                        {
                            data: 'name',
                            name: 'name'
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
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, full, meta) {
                                return `
                                <a href="{{ url('/student/${full.id}/edit') }}">
                                    <x-button.info-button type="button" class="btn-sm text-white"><i class="fa-regular fa-pen-to-square"></i>Edit</x-button.info-button>
                                </a>
                                <x-form action="{{ url('/student/${full.id}') }}" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <x-button.danger-button type="submit" class="btn-sm text-white" onclick="return confirm('Are you sure?')"><i class="fa-regular fa-trash-can"></i>Hapus</x-button.danger-button>
                                </x-form>
                            `;
                            }
                        },
                    ]
                });
                $('#jurusan, #kelas').change(function() {
                    dataTable.ajax.reload();
                });
            });
        </script>
    </x-slot>
</x-app-layout>
