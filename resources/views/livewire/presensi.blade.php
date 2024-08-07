<div>
    {{-- <h1>Ini adalah presensi</h1> --}}
    <div class="container mx-auto p-6">
        <div class="bg-white p-8 rounded-lg shadow-lg card">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h2 class="text-2xl font-bold mb-4 text-blue-800">Informasi Pegawai</h2>
                    <div class="bg-gray-100 p-6 rounded-lg shadow-inner">
                        <p class="mb-2"><strong>Nama Pegawai: </strong>{{ auth()->user()->name }}</p>
                        <p class="mb-2"><strong>Kantor: </strong>{{ $schedule->office->name }}</p>
                        <p class="mb-2"><strong>Shift: </strong>{{ $schedule->shift->name }}
                            ({{ $schedule->shift->start_time }} - {{ $schedule->shift->end_time }})</p>
                    </div>
                </div>
                <div>
                    <h2 class="text-2xl font-bold mb-4 text-blue-800">Presensi</h2>
                    <div id="map" class="mb-4 rounded-lg border border-gray-300 h-64"></div>
                    <button type="button" onclick="tagLocation()"
                        class="px-6 py-3 bg-blue-500 text-white rounded shadow-lg hover:bg-blue-600 transition duration-300 ease-in-out">Tag
                        Location</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        // Initialize the map
        const mymap = L.map('map').setView([{{ $schedule->office->latitude }}, {{ $schedule->office->longitude }}], 18);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(mymap);

        // Office coordinates and circle radius
        const office = [{{ $schedule->office->latitude }}, {{ $schedule->office->longitude }}];
        const radius = {{ $schedule->office->radius }};
        const circle = L.circle(office, {
            color: 'blue',
            fillColor: '#3182ce',
            fillOpacity: 0.5,
            radius: radius
        }).addTo(mymap);

        // Marker initialization
        const marker = L.marker(office).addTo(mymap);

        // Function to get user's location and check attendance
        function tagLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition((position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    const userLocation = [lat, lng];
                    marker.setLatLng(userLocation);
                    mymap.setView(userLocation, 18);

                    const distance = marker.getLatLng().distanceTo(circle.getLatLng());
                    if (distance <= radius) {
                        showModal('Presensi Berhasil', 'Anda telah berhasil melakukan presensi.', 'bg-blue-500',
                            'hover:bg-blue-600');
                    } else {
                        showModal('Presensi Gagal', 'Anda diluar jangkauan kantor.', 'bg-red-500',
                            'hover:bg-red-600');
                    }
                });
            } else {
                alert('Geolocation is not available');
            }
        }

        // Function to show modal
        function showModal(title, message, buttonColor, buttonHover) {
            const modal = document.createElement('div');
            modal.classList.add('fixed', 'inset-0', 'flex', 'items-center', 'justify-center', 'z-50');
            modal.style.zIndex = '1000'; // Ensure the modal is on top
            modal.innerHTML = `
        <div class="bg-white rounded-lg shadow-lg p-6 max-w-md mx-auto relative z-50">
            <h2 class="text-2xl font-bold mb-4">${title}</h2>
            <p class="mb-4">${message}</p>
            <button onclick="closeModal()" class="px-4 py-2 ${buttonColor} text-white rounded shadow-lg ${buttonHover} transition duration-300 ease-in-out">Tutup</button>
        </div>
    `;
            document.body.appendChild(modal);
        }

        // Function to close modal
        function closeModal() {
            const modal = document.querySelector('.fixed.inset-0');
            if (modal) modal.remove();
        }
    </script>

</div>
