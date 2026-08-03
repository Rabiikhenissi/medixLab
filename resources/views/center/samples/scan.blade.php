@extends('layouts.center')

@section('title', 'Scanner Échantillon - Medix eSanté')

@section('content')
<div class="max-w-2xl mx-auto space-y-6 select-none">
    <div>
        <h1 class="text-3xl font-bold text-[#1e293b]">Scanner un Échantillon</h1>
        <p class="text-sm text-[#64748b] mt-2">Scannez le code-barres pour retrouver l'échantillon.</p>
    </div>

    <div class="bg-white border border-[#e2e8f0] rounded-2xl p-8 text-center">
        <div class="max-w-md mx-auto">
            <div class="mb-6">
                <svg class="w-16 h-16 mx-auto text-[#7C3AED]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5z"/>
                </svg>
            </div>

            <div class="flex justify-center gap-2 mb-6">
                <button type="button" id="tabManual" class="tab-btn px-4 py-2 rounded-xl text-xs font-bold transition bg-[#7C3AED] text-white">Saisie manuelle</button>
                <button type="button" id="tabCamera" class="tab-btn px-4 py-2 rounded-xl text-xs font-bold transition border border-[#e2e8f0] text-[#64748b]">Scanner caméra</button>
            </div>

            <form id="scanForm" class="space-y-4">
                @csrf
                <div>
                    <label class="text-[10px] font-bold text-[#64748b] uppercase tracking-wider">Code-barres</label>
                    <input type="text" id="barcodeInput" name="code" placeholder="Scannez ou saisissez le code..." autofocus
                        class="mt-1 w-full border-2 border-[#7C3AED] rounded-xl px-5 py-4 text-lg text-center font-mono font-bold focus:border-[#5B21B6] outline-none"
                        style="font-size: 24px; letter-spacing: 3px;">
                </div>
                <p class="text-xs text-[#94a3b8]">Scannez le code-barres ou saisissez-le manuellement.</p>
            </form>

            <div id="cameraView" class="hidden mt-6">
                <div id="cameraReader" class="mx-auto overflow-hidden rounded-2xl border-2 border-dashed border-[#e2e8f0]"></div>
                <p id="cameraStatus" class="text-xs text-[#94a3b8] mt-3">Placez le code-barres devant la caméra. La recherche se lance automatiquement.</p>
                <button type="button" id="cameraStopBtn" class="mt-3 border border-[#e2e8f0] hover:bg-[#f8fafc] text-[#64748b] font-bold px-4 py-2 rounded-xl text-xs transition">Arrêter la caméra</button>
            </div>

            <div id="scanResult" class="hidden mt-6 p-6 rounded-2xl border"></div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    const input = document.getElementById('barcodeInput');
    const result = document.getElementById('scanResult');
    const cameraView = document.getElementById('cameraView');
    const cameraStatus = document.getElementById('cameraStatus');
    let timeout = null;
    let html5Qr = null;
    let cameraRunning = false;
    let pendingCameraScan = false;
    let lastDecoded = null;
    let lastDecodedAt = 0;

    function renderError(message) {
        result.className = 'mt-6 p-6 rounded-2xl border border-red-200 bg-red-50 text-left';
        result.innerHTML = `<p class="text-sm font-bold text-red-600">${message}</p>`;
    }

    function renderResult(data) {
        result.className = 'mt-6 p-6 rounded-2xl border border-emerald-200 bg-emerald-50';
        result.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="text-left">
                    <p class="text-xs text-[#64748b] uppercase font-bold tracking-wider">Échantillon trouvé</p>
                    <p class="text-xl font-bold text-[#1e293b] mt-1">${data.sample_code}</p>
                    <p class="text-sm text-[#64748b]">${data.patient_name}</p>
                    <p class="text-sm text-[#64748b]">${data.exam} · ${data.material_type || ''}</p>
                    <p class="text-sm">Statut: <span class="font-bold">${data.status}</span></p>
                    <p class="text-sm">Emplacement: ${data.storage_location || '-'}</p>
                </div>
                <a href="${data.show_url}" class="bg-[#7C3AED] hover:bg-[#6D28D9] text-white font-bold px-4 py-2 rounded-xl text-xs transition">Voir détail</a>
            </div>
        `;
    }

    function performLookup(code) {
        fetch('{{ route("center.samples.lookup") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('[name=_token]').value },
            body: JSON.stringify({ code: code })
        })
        .then(r => r.json())
        .then(data => {
            const wasCamera = pendingCameraScan;
            pendingCameraScan = false;

            if (data.error) {
                renderError(data.error);
                if (wasCamera) cameraStatus.textContent = 'Scannez à nouveau (code non trouvé).';
                return;
            }

            renderResult(data);
            if (wasCamera) {
                stopCamera();
                switchTab('manual');
            } else {
                input.value = '';
            }
        })
        .catch(() => {
            pendingCameraScan = false;
            renderError('Erreur de recherche. Réessayez.');
        });
    }

    input.addEventListener('input', function() {
        clearTimeout(timeout);
        if (this.value.length < 3) return;
        // skip lookups triggered by the camera filling the input (camera path already ran)
        if (pendingCameraScan && this.value === lastDecoded) return;

        timeout = setTimeout(() => {
            pendingCameraScan = false;
            performLookup(this.value.trim());
        }, 300);
    });

    function onDecoded(decodedText) {
        const code = (decodedText || '').trim();
        if (!code) return;

        // html5-qrcode calls this on every frame while the barcode is in view:
        // ignore repeats of the same code within a short window.
        const now = Date.now();
        if (code === lastDecoded && now - lastDecodedAt < 2000) return;
        lastDecoded = code;
        lastDecodedAt = now;

        pendingCameraScan = true;
        input.value = code;
        cameraStatus.textContent = 'Code lu: ' + code + ' — recherche…';
        performLookup(code);
    }

    async function stopCamera() {
        cameraRunning = false;
        if (html5Qr) {
            try { await html5Qr.stop(); } catch (e) {}
        }
        cameraView.classList.add('hidden');
        cameraStatus.textContent = 'Placez le code-barres devant la caméra. La recherche se lance automatiquement.';
    }

    async function startCamera() {
        if (cameraRunning) return;
        if (!window.Html5Qrcode) {
            renderError('La bibliothèque de scan caméra n\'a pas pu être chargée.');
            return;
        }

        // dispose any previous instance so the scanner can restart without reloading
        if (html5Qr) {
            try { await html5Qr.stop(); } catch (e) {}
            try { await html5Qr.clear(); } catch (e) {}
            html5Qr = null;
        }
        document.getElementById('cameraReader').innerHTML = '';

        cameraView.classList.remove('hidden');
        cameraStatus.textContent = 'Connexion à la caméra…';

        try {
            html5Qr = new Html5Qrcode('cameraReader', { formatsToSupport: [Html5QrcodeSupportedFormats.CODE_128] });
            await html5Qr.start(
                { facingMode: 'environment' },
                { fps: 10, qrbox: { width: 250, height: 120 } },
                onDecoded,
                () => {}
            );
            cameraRunning = true;
            cameraStatus.textContent = '';
        } catch (err) {
            cameraRunning = false;
            cameraStatus.textContent = 'Impossible d\'accéder à la caméra: ' + err + ' — réessayez ou utilisez la saisie manuelle.';
            result.className = 'mt-6 p-6 rounded-2xl border border-red-200 bg-red-50 text-left';
            result.innerHTML = `<p class="text-sm font-bold text-red-600">Impossible d\'accéder à la caméra: ${err}</p>`;
            cameraView.classList.add('hidden');
        }
    }

    function switchTab(tab) {
        const isManual = tab === 'manual';
        document.getElementById('tabManual').className = 'tab-btn px-4 py-2 rounded-xl text-xs font-bold transition ' + (isManual ? 'bg-[#7C3AED] text-white' : 'border border-[#e2e8f0] text-[#64748b]');
        document.getElementById('tabCamera').className = 'tab-btn px-4 py-2 rounded-xl text-xs font-bold transition ' + (!isManual ? 'bg-[#7C3AED] text-white' : 'border border-[#e2e8f0] text-[#64748b]');
        document.getElementById('scanForm').classList.toggle('hidden', !isManual);
        cameraView.classList.toggle('hidden', isManual);

        if (isManual) {
            stopCamera();
            input.focus();
        } else {
            startCamera();
        }
    }

    document.getElementById('tabManual').addEventListener('click', () => switchTab('manual'));
    document.getElementById('tabCamera').addEventListener('click', () => switchTab('camera'));
    document.getElementById('cameraStopBtn').addEventListener('click', () => switchTab('manual'));

    window.addEventListener('beforeunload', () => stopCamera());
</script>
@endsection
