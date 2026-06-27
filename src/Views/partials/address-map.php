<?php

declare(strict_types=1);

$mapLat = old('latitude', $latitude ?? null);
$mapLng = old('longitude', $longitude ?? null);
$defaultLat = config('app')['petshop_lat'];
$defaultLng = config('app')['petshop_lng'];
$hasCoords = $mapLat !== null && $mapLat !== '' && $mapLng !== null && $mapLng !== '';
?>
<div class="space-y-2">
    <label class="block text-sm font-medium text-gray-700">Pilih Lokasi di Peta</label>
    <p class="text-xs text-gray-500">Ketik alamat di atas untuk mencari otomatis, klik peta untuk menandai, atau geser penanda.</p>

    <div id="address-map" class="w-full h-72 rounded-lg border border-gray-300 z-0"></div>

    <input type="hidden" id="latitude" name="latitude" value="<?= e((string) ($mapLat ?? '')) ?>">
    <input type="hidden" id="longitude" name="longitude" value="<?= e((string) ($mapLng ?? '')) ?>">

    <p id="coords-display" class="text-sm text-gray-500">
        <?php if ($hasCoords): ?>
            Koordinat: <?= e((string) $mapLat) ?>, <?= e((string) $mapLng) ?>
        <?php else: ?>
            Belum ada lokasi dipilih.
        <?php endif; ?>
    </p>

    <?php if (!empty($errors['latitude']) || !empty($errors['longitude']) || !empty($errors['location'])): ?>
        <p class="text-red-600 text-xs"><?= e($errors['latitude'] ?? $errors['longitude'] ?? $errors['location'] ?? '') ?></p>
    <?php endif; ?>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
(function () {
    const defaultLat = <?= json_encode((float) $defaultLat) ?>;
    const defaultLng = <?= json_encode((float) $defaultLng) ?>;
    const initialLat = <?= json_encode($hasCoords ? (float) $mapLat : null) ?>;
    const initialLng = <?= json_encode($hasCoords ? (float) $mapLng : null) ?>;

    const alamatInput = document.getElementById('alamat_lengkap');
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const coordsDisplay = document.getElementById('coords-display');

    const map = L.map('address-map').setView(
        initialLat !== null ? [initialLat, initialLng] : [defaultLat, defaultLng],
        initialLat !== null ? 16 : 13
    );

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    L.marker([defaultLat, defaultLng], {
        opacity: 0.8,
        title: 'Lokasi Petshop'
    }).addTo(map).bindPopup('Petshop');

    let marker = null;
    let geocodeTimer = null;
    let lastGeocodedQuery = '';
    let skipNextGeocode = false;

    function setLocation(lat, lng, options) {
        options = options || {};
        latInput.value = lat.toFixed(8);
        lngInput.value = lng.toFixed(8);
        coordsDisplay.textContent = 'Koordinat: ' + lat.toFixed(6) + ', ' + lng.toFixed(6);

        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng], { draggable: true }).addTo(map);
            marker.on('dragend', function (e) {
                skipNextGeocode = true;
                const pos = e.target.getLatLng();
                setLocation(pos.lat, pos.lng);
            });
        }

        if (options.flyTo !== false) {
            map.setView([lat, lng], Math.max(map.getZoom(), 16));
        }
    }

    function searchAddress(query) {
        const trimmed = query.trim();

        if (trimmed.length < 5) {
            return;
        }

        if (trimmed === lastGeocodedQuery) {
            return;
        }

        coordsDisplay.textContent = 'Mencari lokasi...';

        fetch('/profil/geocode?q=' + encodeURIComponent(trimmed), {
            headers: { 'Accept': 'application/json' }
        })
            .then(function (res) { return res.json().then(function (data) { return { ok: res.ok, data: data }; }); })
            .then(function (result) {
                if (!result.ok || !result.data.success) {
                    coordsDisplay.textContent = result.data.error || 'Lokasi tidak ditemukan.';
                    return;
                }

                lastGeocodedQuery = trimmed;
                setLocation(result.data.lat, result.data.lng);
            })
            .catch(function () {
                coordsDisplay.textContent = 'Gagal mencari lokasi. Coba lagi atau pilih manual di peta.';
            });
    }

    if (initialLat !== null) {
        setLocation(initialLat, initialLng, { flyTo: false });
    }

    map.on('click', function (e) {
        skipNextGeocode = true;
        setLocation(e.latlng.lat, e.latlng.lng);
    });

    if (alamatInput) {
        alamatInput.addEventListener('input', function () {
            if (skipNextGeocode) {
                skipNextGeocode = false;
                return;
            }

            clearTimeout(geocodeTimer);
            geocodeTimer = setTimeout(function () {
                searchAddress(alamatInput.value);
            }, 800);
        });
    }

    setTimeout(function () { map.invalidateSize(); }, 100);
})();
</script>
