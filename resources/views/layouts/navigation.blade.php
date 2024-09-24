<div class="navbar bg-base-100 shadow-sm z-10">

    <div class="navbar-start ml-10">
        <x-application-logo class="w-14 h-14 fill-current text-gray-500 md:flex hidden" />
    </div>
    <div class="navbar-center hidden lg:flex">
        <ul class="menu menu-horizontal px-1">
            <li><a href="{{ route('dashboard') }}">Beranda</a></li>
            @hasanyrole('admin|teacher')
                @hasrole('admin')
                    <li>
                        <details>
                            <summary>Master Data</summary>
                            <ul class="p-2 z-10">
                                <li><a href="{{ route('student.index') }}">Data Siswa</a></li>
                                <li><a href="{{ route('teacher.index') }}">Data Guru</a></li>
                                <li><a href="{{ route('subject.index') }}">Data Mapel</a></li>
                                <li><a href="{{ route('schedule.index') }}">Jadwal Mapel</a></li>
                            </ul>
                        </details>
                    </li>
                @endrole
                <li><a href="{{ route('attendance.index') }}">Data Absensi</a></li>
                @hasrole('teacher')
                    <li><a href="{{ route('teacher.agenda.index') }}">Agenda</a></li>
                @endrole
                @hasrole('admin')
                    <li><a href="{{ route('time.index') }}">Waktu Absen</a></li>
                    <li><a href="{{ route('setting.index') }}">Pengaturan</a></li>
                @endrole
            @else
                <li><a href="{{ route('student.attendance.index') }}">Absen</a></li>
                <li><a href="{{ route('profile.edit') }}">Profile</a></li>
                <li><a href="{{ route('student.subject.index') }}">Mapel</a></li>
                <li><a href="{{ route('student.history.index') }}">Riwayat Absen</a></li>
            @endhasanyrole
        </ul>
    </div>
    <div class="navbar-end mr-10">
        <div class="dropdown dropdown-end">
            @hasanyrole('student|teacher')
                <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                    @php
                        $words = explode(
                            ' ',
                            Auth::user()->student ? Auth::user()->student->name : Auth::user()->teacher->name,
                        );
                        $acronym = mb_substr($words[0] ?? 'I', 0, 1) . mb_substr($words[1] ?? 'T', 0, 1);

                        // foreach ($words as $w) {
                        //     $acronym .= mb_substr($w, 0, 1);
                        // }

                    @endphp
                    <div class="avatar placeholder">
                        <div class="bg-neutral text-neutral-content rounded-full w-8">
                            <span class="text-xs">{{ $acronym }}</span>
                        </div>
                    </div>
                </div>
                <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                    @hasrole('student')
                        <li>
                            <a href="{{ route('profile.edit') }}">
                                {{ __('Profile') }}
                            </a>
                        </li>
                    @endhasrole
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <li>
                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                        this.closest('form').submit();">{{ __('Log Out') }}</a>
                        </li>
                    </form>
                </ul>
            @else
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}"
                        onclick="event.preventDefault();
                                        this.closest('form').submit();">{{ Auth::user()->username }}</a>
                </form>
            @endhasanyrole

        </div>

    </div>


</div>
<div class="btm-nav lg:hidden shadow-sm z-10">
    @hasanyrole('admin|teacher|student')
        <a href="{{ route('dashboard') }}" class="{{ Request::is('dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-house" class="h-5 w-5"></i>
            <span class="btm-nav-label text-xs">Beranda</span>
        </a>
        @hasrole('admin')
            <div class="dropdown dropdown-top relative">
                <div tabindex="0" class="flex flex-col justify-center items-center mt-[0.80rem] gap-1">
                    <i class="fa-solid fa-house" class="h-5 w-5"></i>
                    <span class="btm-nav-label text-xs">Data Master</span>
                </div>
                <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-52 p-2 shadow">
                    <li><a href="{{ route('student.index') }}">Data Siswa</a></li>
                    <li><a href="{{ route('teacher.index') }}">Data Guru</a></li>
                    <li><a href="{{ route('subject.index') }}">Data Mapel</a></li>
                    <li><a href="{{ route('schedule.index') }}">Jadwal Mapel</a></li>
                </ul>
            </div>
        @endrole
        @hasanyrole('admin|teacher')
            <a href="{{ route('attendance.index') }}" class="{{ Request::is('attendance') ? 'active' : '' }}">
                <i class="fa-solid fa-house" class="h-5 w-5"></i>
                <span class="btm-nav-label text-xs">Data Absensi</span>
            </a>
        @endhasanyrole
        @hasrole('teacher')
            <a href="{{ route('teacher.agenda.index') }}" class="{{ Request::is('guru/agenda') ? 'active' : '' }}">
                <i class="fa-solid fa-house" class="h-5 w-5"></i>
                <span class="btm-nav-label text-xs">Agenda</span>
            </a>
        @endrole
        @hasrole('student')
            <a href="{{ route('student.attendance.index') }}" class="{{ Request::is('siswa/attendance') ? 'active' : '' }}">
                <i class="fa-solid fa-house" class="h-5 w-5"></i>
                <span class="btm-nav-label text-xs">Absen</span>
            </a>
            <a href="{{ route('student.subject.index') }}" class="{{ Request::is('siswa/subject') ? 'active' : '' }}">
                <i class="fa-solid fa-house" class="h-5 w-5"></i>
                <span class="btm-nav-label text-xs">Mapel</span>
            </a>
            <a href="{{ route('student.history.index') }}" class="{{ Request::is('siswa/history') ? 'active' : '' }}">
                <i class="fa-solid fa-house" class="h-5 w-5"></i>
                <span class="btm-nav-label text-xs">Riwayat Absen</span>
            </a>
        @endrole
    @endhasanyrole

</div>
