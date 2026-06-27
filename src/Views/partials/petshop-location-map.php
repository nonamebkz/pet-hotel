<?php

declare(strict_types=1);

$mapLat = old('petshop_lat', $petshop_lat ?? null);
$mapLng = old('petshop_lng', $petshop_lng ?? null);
$defaultLat = (float) ($petshop_lat ?? app_settings('petshop_lat'));
$defaultLng = (float) ($petshop_lng ?? app_settings('petshop_lng'));
$hasCoords = $mapLat !== null && $mapLat !== '' && $mapLng !== null && $mapLng !== '';
?>
<div class="space-y-2">
    <p class="text-xs text-gray-500">Klik peta atau geser penanda untuk menentukan lokasi petshop.</p>

    <div id="petshop-location-map" class="w-full h-72 rounded-lg border border-gray-300 z-0"></div>

    <input type="hidden" id="petshop_lat" name="petshop_lat" value="<?= e((string) ($mapLat ?? '')) ?>">
    <input type="hidden" id="petshop_lng" name="petshop_lng" value="<?= e((string) ($mapLng ?? '')) ?>">

    <p id="petshop-coords-display" class="text-sm text-gray-500">
        <?php if ($hasCoords): ?>
            Koordinat: <?= e((string) $mapLat) ?>, <?= e((string) $mapLng) ?>
        <?php else: ?>
            Belum ada lokasi dipilih.
        <?php endif; ?>
    </p>

    <?php if (!empty($errors['petshop_lat']) || !empty($errors['petshop_lng'])): ?>
        <p class="text-red-600 text-xs"><?= e($errors['petshop_lat'] ?? $errors['petshop_lng'] ?? '') ?></p>
    <?php endif; ?>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
(function () {
    const fallbackLat = <?= json_encode((float) $defaultLat) ?>;
    const fallbackLng = <?= json_encode((float) $defaultLng) ?>;
    const initialLat = <?= json_encode($hasCoords ? (float) $mapLat : null) ?>;
    const initialLng = <?= json_encode($hasCoords ? (float) $mapLng : null) ?>;

    const latInput = document.getElementById('petshop_lat');
    const lngInput = document.getElementById('petshop_lng');
    const coordsDisplay = document.getElementById('petshop-coords-display');

    const startLat = initialLat !== null ? initialLat : fallbackLat;
    const startLng = initialLng !== null ? initialLng : fallbackLng;

    const map = L.map('petshop-location-map').setView([startLat, startLng], initialLat !== null ? 16 : 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    let marker = L.marker([startLat, startLng], { draggable: true }).addTo(map);

    function setLocation(lat, lng) {
        latInput.value = lat.toFixed(8);
        lngInput.value = lng.toFixed(8);
        coordsDisplay.textContent = 'Koordinat: ' + lat.toFixed(6) + ', ' + lng.toFixed(6);
        marker.setLatLng([lat, lng]);
        map.setView([lat, lng], Math.max(map.getZoom(), 16));
    }

    marker.on('dragend', function (e) {
        const pos = e.target.getLatLng();
        setLocation(pos.lat, pos.lng);
    });

    map.on('click', function (e) {
        setLocation(e.latlng.lat, e.latlng.lng);
    });

    if (initialLat === null) {
        setLocation(fallbackLat, fallbackLng);
    }

    setTimeout(function () { map.invalidateSize(); }, 100);
})();
</script>
