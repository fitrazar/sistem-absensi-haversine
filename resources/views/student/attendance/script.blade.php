<script>
    document.addEventListener('DOMContentLoaded', function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        const status = document.getElementById('status');
        const userId = '{{ auth()->user()->student?->id }}';
        const apiUrl = '{{ route('student.attendance.store') }}';
        const roleId = '{{ auth()->user()->role }}';
        const locationIframe = document.getElementById('locationIframe'); // Tambahkan elemen untuk iframe

        status.addEventListener('change', handleStatusChange);

        function handleStatusChange() {
            const statusValue = status.value;
            hideAll();
            switch (statusValue) {
                case 'Hadir':
                    showLocation();
                    break;
                case 'Absen Mapel':
                    showLocationMapel();
                    break;
                case 'Izin':
                    showPermit();
                    break;
                case 'Sakit':
                    showSick();
                    break;
                default:
                    break;
            }
        }

        function hideAll() {
            $('#fileUpload, #permit, #send, #sendMapel, #sendPermit, #sendSick, #locationSection, #toast-top-left')
                .hide();
            locationIframe.innerHTML = ''; // Hapus iframe saat status berubah
        }

        function showLocation() {
            $('#locationSection, #send').show();
            initializeLocation();
        }

        function showLocationMapel() {
            $('#locationSection, #sendMapel').show();
            initializeLocationMapel();
        }

        function showPermit() {
            $('#permit, #sendPermit').show();
            initializePermit();
        }

        function showSick() {
            $('#fileUpload, #sendSick').show();
            initializeSick();
        }

        function initializePermit() {
            const btnIzin = document.getElementById('sendPermit');
            btnIzin.onclick = function() {
                sendFormData({
                    id: userId,
                    status: status.value,
                    izin: document.getElementById('izin').value
                });
            };
        }

        function initializeSick() {
            const btnSakit = document.getElementById('sendSick');
            btnSakit.onclick = function() {
                const formData = new FormData();
                const fileInput = document.getElementById('sickFile');

                formData.append('id', userId);
                formData.append('status', status.value);
                if (fileInput.files.length > 0) {
                    formData.append('file', fileInput.files[0]);
                }

                sendFormData(formData, true);
            };
        }

        function initializeLocation() {
            const btn = document.getElementById('send');
            navigator.geolocation.getCurrentPosition(
                position => {
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;
                    const formData = {
                        id: userId,
                        role: roleId,
                        latitude: latitude,
                        longitude: longitude,
                    };

                    // Tampilkan iframe dengan peta lokasi
                    displayLocationIframe(latitude, longitude);

                    btn.onclick = function() {
                        sendFormData(formData);
                    }
                },
                error => {
                    console.error('Geolocation error:', error);
                    $('#note-error').show();
                }
            );
        }

        function initializeLocationMapel() {
            const btn = document.getElementById('sendMapel');
            navigator.geolocation.getCurrentPosition(
                position => {
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;
                    const formData = {
                        id: userId,
                        role: roleId,
                        latitude: latitude,
                        longitude: longitude,
                        status: status.value,
                    };

                    // Tampilkan iframe dengan peta lokasi
                    displayLocationIframe(latitude, longitude);

                    btn.onclick = function() {
                        sendFormData(formData);
                    }
                },
                error => {
                    console.error('Geolocation error:', error);
                    $('#note-error').show();
                }
            );
        }

        // Fungsi untuk menampilkan iframe Google Maps dengan lat dan long
        function displayLocationIframe(latitude, longitude) {
            const googleMapsUrl =
                `https://www.google.com/maps?q=${latitude},${longitude}&hl=es;z=14&output=embed`;
            locationIframe.innerHTML =
                `<iframe width="100%" height="300" src="${googleMapsUrl}" frameborder="0" allowfullscreen></iframe>`;
        }

        function sendFormData(data, isFile = false) {
            hideAll();
            $('#loading').show();
            $.ajax({
                url: apiUrl,
                method: 'POST',
                data: data,
                processData: !isFile,
                contentType: isFile ? false : 'application/x-www-form-urlencoded; charset=UTF-8',
                success: function(response) {
                    $('#notif').text(response.message);
                    $('#toast-top-left').addClass("block").removeClass("hidden").show();
                },
                error: function(error) {
                    $('#notif').text(error.responseJSON.message);
                    $('#toast-top-left').addClass("block").removeClass("hidden").show();
                },
                complete: function() {
                    $('#loading').hide();
                    status.value = '';
                    document.getElementById('izin').value = '';
                }
            });
        }
    });
</script>
