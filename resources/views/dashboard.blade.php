<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @hasrole('admin')
                @include('partials.admin')
            @endrole
            @hasrole('student')
                @include('partials.student')
            @endrole
            @hasrole('teacher')
                @include('partials.teacher')
            @endrole
        </div>
    </div>

    <x-slot name="script">
        <script>
            const chart = $('#chart').get(0).getContext('2d')
            chart.canvas.parentNode.style.width = '500px';
            chart.canvas.parentNode.style.height = '500px';

            const labels = ['Hadir', 'Sakit', 'Izin']
            const data = {
                labels: labels,
                datasets: [{
                    label: 'Persentasi Absensi Siswa',
                    data: [{{ $hadir }},
                        {{ $sakit }},
                        {{ $izin }}
                    ],
                    backgroundColor: [
                        'rgb(0, 224, 49)',
                        'rgb(2, 204, 174)',
                        'rgb(234, 237, 19)',
                    ],
                    color: '#ffff',
                    hoverOffset: 4
                }]
            };


            new Chart(chart, {
                type: 'doughnut',
                data: data,
            })
        </script>
    </x-slot>
</x-app-layout>
