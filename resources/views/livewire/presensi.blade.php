<div>
    {{-- <h1>Ini adalah presensi</h1> --}}
  <div class="container mx-auto p-6">
        <div class="bg-white p-8 rounded-lg shadow-lg card">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h2 class="text-2xl font-bold mb-4 text-blue-800">Informasi Pegawai</h2>
                    <div class="bg-gray-100 p-6 rounded-lg shadow-inner">
                        <p class="mb-2"><strong>Nama Pegawai: </strong>{{ auth()->user()->name }}</p>
                        <p class="mb-2"><strong>Kantor: </strong>{{$schedule->office->name}}</p>
                        <p class="mb-2"><strong>Shift: </strong>{{$schedule->shift->name}} ({{$schedule->shift->start_time}} - {{$schedule->shift->end_time}})</p>
                    </div>
                </div>
                <div>
                    <h2 class="text-2xl font-bold mb-4 text-blue-800">Presensi</h2>
                    <div id="map" class="mb-4 rounded-lg border border-gray-300 h-64"></div>
                    <button type="button" onclick="tagLocation()" class="px-6 py-3 bg-blue-500 text-white rounded shadow-lg hover:bg-blue-600 transition duration-300 ease-in-out">Tag Location</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        const mymap = L.map('map').setView([{{$schedule->office->latitude}}, {{$schedule->office->longitude}}], 18);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(mymap);

        const office = [{{$schedule->office->latitude}}, {{$schedule->office->longitude}}];
        const radius = {{$schedule->office->radius}};
        const circle = L.circle(office, {
            color: 'blue',
            fillColor: '#3182ce',
            fillOpacity: 0.5,
            radius: radius
        }).addTo(mymap);

        const marker = L.marker(office).addTo(mymap);

// fungsi untuk jika presensi otomatis mendapatkan koordinat lokasi
        function tagLocation(){
            if(navigator.geolocation){
                navigator.geolocation.getCurrentPosition((position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    const userLocation = [lat, lng];
                    marker.setLatLng(userLocation);
                    mymap.setView(userLocation, 18);

                    const distance = marker.getLatLng().distanceTo(circle.getLatLng());
                    if(distance <= radius){
                        alert('Presensi berhasil');
                    } else {
                        alert('Anda diluar jangkauan kantor');
                    }
                });
            } else {
                alert('Geolocation is not available');
            }
        }
    </script>
</div>
