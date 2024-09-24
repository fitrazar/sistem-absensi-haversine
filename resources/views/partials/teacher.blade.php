<section class="py-20">
    <x-card.card-default class="static bg-green-100">
        <div class="w-full h-full">
            <div class="flex gap-6 w-full flex-col justify-center items-center lg:flex-row">
                <canvas id="chart" class="w-full"></canvas>

                <div class="w-full">
                    <div>
                        <p class="text-left">
                            "Cek kehadiran siswa pada mapel anda"
                        </p>
                    </div>
                    <div class="flex items-center justify-center mt-4">
                        <a href="{{ route('attendance.index') }}">
                            <x-button.primary-button class="ms-3" type="button">
                                Data Absensi
                            </x-button.primary-button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </x-card.card-default>
</section>
