<div>
    <div class="container mx-auto p-6 max-w-sm">
        <div class="bg-white p-8 rounded-lg shadow-lg card">
            <div class="grid grid-cols-1 gap-6 mb-6">
                <div>
                    <h2 class="text-2xl font-bold mb-4 text-blue-800">Informasi Pegawai</h2>
                    <div class="bg-gray-100 p-6 rounded-lg shadow-inner">
                        <p class="mb-2"><strong>Nama Pegawai:</strong> {{ auth()->user()->name }}</p>
                        <p class="mb-2"><strong>Kantor:</strong> {{ $schedule->office->name }}</p>
                        <p class="mb-2"><strong>Shift:</strong> {{ $schedule->shift->name }}
                            ({{ $schedule->shift->start_time }} - {{ $schedule->shift->end_time }})</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-2">
                        <div class="bg-gray-100 p-4 rounded-lg">
                            <h4 class="text-l font-bold mb-2">Jam Masuk</h4>
                            <p><strong>{{$attendance->start_time}}</strong></p>
                        </div>
                        <div class="bg-gray-100 p-4 rounded-lg">
                            <h4 class="text-l font-bold mb-2">Jam Pulang</h4>
                            <p><strong>{{$attendance->end_time}}</strong></p>
                        </div>
                    </div>
                </div>
                <div>
                    <h2 class="text-2xl font-bold mb-4 text-blue-800">Presensi</h2>
                    <div id="map" class="mb-4 rounded-lg border border-gray-300 h-64" wire:ignore></div>
                    <form class="row g-3" wire:submit="store" enctype="multipart/form-data">
                    <button type="submit" onclick="tagLocation()"
                        class="px-6 py-3 bg-blue-500 text-white rounded shadow-lg hover:bg-blue-600 transition duration-300 ease-in-out">Tag
                        Location</button>
                    @if ($insideRadiuse)
                        <button type="submit"
                            class="mt-3 px-4 py-2 bg-green-500 text-white rounded shadow-lg hover:bg-green-600 transition duration-300 ease-in-out">Submit
                            Presensi</button>
                    @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        let mymap;
        let lat;
        let lng;
        const office = [{{ $schedule->office->latitude }}, {{ $schedule->office->longitude }}];
        const radius = {{ $schedule->office->radius }};
        document.addEventListener('livewire:initialized', function() {
            component = @this;
            mymap = L.map('map').setView(office, 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
            }).addTo(mymap);

            const circle = L.circle(office, {
                color: 'blue',
                fillColor: '#3182ce',
                fillOpacity: 0.5,
                radius: radius
            }).addTo(mymap);
            const marker = L.marker(office).addTo(mymap);
            function tagLocation() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition((position) => {
                        lat = position.coords.latitude;
                        lng = position.coords.longitude;
                        const userLocation = [lat, lng];
                        marker.setLatLng(userLocation);
                        mymap.setView(userLocation, 18);

                        const distance = marker.getLatLng().distanceTo(circle.getLatLng());
                        if (distance <= radius) {
                            // showModal('Anda di dalam jangkauan kantor', 'Anda berada di dalam jangkauan', 'bg-blue-500',
                            //     'hover:bg-blue-600');
                            component.set('insideRadiuse', true);
                            component.set('latitude', lat);
                            component.set('longitude', lng);
                        } else {
                            showModal('Presensi Gagal', 'Anda diluar jangkauan kantor.', 'bg-red-500',
                                'hover:bg-red-600');
                        }
                    });
                } else {
                    alert('Geolocation is not available');
                }
            }
            window.tagLocation = tagLocation;
        });

        function showModal(title, message, buttonColor, buttonHover) {
            const modal = document.createElement('div');
            modal.classList.add('fixed', 'inset-0', 'flex', 'items-center', 'justify-center', 'z-50');
            modal.style.zIndex = '1000';
            modal.innerHTML = `
                <div class="bg-white rounded-lg shadow-lg p-6 max-w-md mx-auto relative z-50">
                    <h2 class="text-2xl font-bold mb-4">${title}</h2>
                    <p class="mb-4">${message}</p>
                    <button onclick="closeModal()" class="px-4 py-2 ${buttonColor} text-white rounded shadow-lg ${buttonHover} transition duration-300 ease-in-out">Tutup</button>
                </div>
            `;
            document.body.appendChild(modal);
        }
        function closeModal() {
            const modal = document.querySelector('.fixed.inset-0');
            if (modal) modal.remove();
        }
    </script>
</div>
