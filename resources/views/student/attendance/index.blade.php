@section('title', 'Absen')

<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div id="toast-top-left" class="hidden toast toast-top toast-start">
                <div class="alert alert-info">
                    <span id="notif"></span>
                </div>
            </div>
            <x-card.card-default class="static">
                <div class="flex flex-col p-6 bg-base-100 mx-auto text-center w-full">
                    @include('student.attendance.selector')
                    <div id="locationIframe"></div>

                    <span class="loading loading-spinner text-primary hidden" id="loading"></span>
                    <x-button.primary-button id="send" class="mt-3 hidden">Absen</x-button.primary-button>
                    <x-button.primary-button id="sendMapel" class="mt-3 hidden">Absen</x-button.primary-button>
                    <x-button.success-button id="sendPermit" class="mt-3 hidden">Absen</x-button.success-button>
                    <x-button.warning-button id="sendSick" class="mt-3 hidden">Absen</x-button.warning-button>
                    <p id="note-error" class="hidden font-light text-gray-500 sm:text-lg dark:text-gray-400">
                        Anda Tidak Bisa Melakukan Absen, Silahkan Refresh Browser Lalu Aktifkan / Izinkan /
                        Allow Lokasi Anda.
                    </p>
                    {{-- @include('student.attendance.camera') --}}
                </div>
            </x-card.card-default>
        </div>
    </div>
    <x-slot name="script">
        @include('student.attendance.script')
    </x-slot>
</x-app-layout>
