<div class="relative overflow-x-auto shadow-md rounded-lg border border-gray-200">
    <div class="flex justify-between items-center px-6 pt-5 bg-white">
        <div class="text-xl font-semibold text-gray-900">
            Registro de Direcciones
            <p class="mt-1 text-sm font-normal text-gray-500">En esta sección puedes registrar tus direcciones</p>
        </div>
    </div>

    <form id="form-direccion" method="POST" action="direccion_guardar.php" class="bg-white p-6 pb-5 rounded-lg shadow-md">
        <div class="mb-2">
            <label for="nombre_direccion" class="block text-gray-700 font-medium mb-1">Nombre de la Dirección:</label>
            <input type="text" id="nombre_direccion" name="nombre_direccion" placeholder="Ej: Mi Casa" required class="border border-gray-300 rounded-lg px-4 py-2 w-full focus:outline-none focus:ring focus:border-blue-300">
        </div>

        <div class="mb-2 relative">
            <label for="direccion_aprox" class="block text-gray-700 font-medium mb-1">Dirección Aproximada:</label>
            <input type="text" id="direccion_aprox" name="direccion_aprox" placeholder="Ingresa la dirección" required class="border border-gray-300 rounded-lg px-4 py-2 w-full focus:outline-none focus:ring focus:border-blue-300">
            <div id="autocomplete-results" class="autocomplete-suggestions"></div>
        </div>

        <input type="hidden" id="latitud" name="latitud">
        <input type="hidden" id="longitud" name="longitud">
        <input type="hidden" id="id_cliente" name="id_cliente" value="<?php echo $idUsuario; ?>"> 

        <div id="map" class="my-6"></div>
        <button type="submit" class="text-sm border border-blue-800 text-white bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg transition duration-300 flex items-center">
            <span class="material-icons text-base mr-2">add</span>
            Agregar Dirección
        </button>
    </form> 
</div>

<script>
window.onload = function() {
    // Configura el token de acceso de Mapbox
    mapboxgl.accessToken = ''// Reemplaza con tu token de Mapbox

    const autocompleteInput = document.getElementById("direccion_aprox");
    const autocompleteResults = document.getElementById("autocomplete-results");

    // Inicializa el mapa de Mapbox
    const map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/outdoors-v11',
        center: [-66.9167, 10.5000],
        zoom: 12
    });

    // Añadir controles de zoom y rotación al mapa
    map.addControl(new mapboxgl.NavigationControl());

    // Variable para el marcador
    let marker;

    // Función para actualizar el marcador en el mapa
    function updateMarker(lng, lat) {
        if (marker) {
            marker.setLngLat([lng, lat]);
        } else {
            marker = new mapboxgl.Marker({ draggable: true })
                .setLngLat([lng, lat])
                .addTo(map);
        }
    }

    // Escucha el evento de movimiento del marcador
    function addMarkerDragEndListener() {
        marker.on('dragend', () => {
            const lngLat = marker.getLngLat();
            document.getElementById("latitud").value = lngLat.lat;
            document.getElementById("longitud").value = lngLat.lng;
        });
    }

    // Autocompletado para direcciones
    autocompleteInput.addEventListener("input", function() {
        const query = autocompleteInput.value;
        if (query.length > 2) {
            fetch(`https://api.mapbox.com/geocoding/v5/mapbox.places/${encodeURIComponent(query)}.json?access_token=${mapboxgl.accessToken}&autocomplete=true&limit=5`)
                .then(response => response.json())
                .then(data => {
                    autocompleteResults.innerHTML = '';
                    
                    data.features.forEach(feature => {
                        const suggestion = document.createElement("div");
                        suggestion.classList.add("autocomplete-suggestion");
                        suggestion.textContent = feature.place_name;

                        suggestion.addEventListener("click", function() {
                            const lng = feature.geometry.coordinates[0];
                            const lat = feature.geometry.coordinates[1];

                            autocompleteInput.value = feature.place_name;
                            document.getElementById("latitud").value = lat;
                            document.getElementById("longitud").value = lng;
                            autocompleteResults.innerHTML = '';

                            map.flyTo({ center: [lng, lat], zoom: 15 });
                            updateMarker(lng, lat);
                            addMarkerDragEndListener();  // Habilita arrastrar el marcador para elegir coordenadas exactas
                        });

                        autocompleteResults.appendChild(suggestion);
                    });
                })
                .catch(error => console.error('Error al obtener las sugerencias:', error));
        } else {
            autocompleteResults.innerHTML = '';
        }
    });

    document.addEventListener("click", function(e) {
        if (!autocompleteInput.contains(e.target) && !autocompleteResults.contains(e.target)) {
            autocompleteResults.innerHTML = '';
        }
    });
};
</script>