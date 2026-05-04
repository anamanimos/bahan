<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>📷 Kamera Companion</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        :root {
            --primary: #3B82F6;
            --primary-dark: #2563EB;
            --success: #10B981;
            --danger: #EF4444;
            --warning: #F59E0B;
            --bg: #0F172A;
            --surface: #1E293B;
            --surface-2: #334155;
            --text: #F1F5F9;
            --text-muted: #94A3B8;
            --border: #475569;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg);
            color: var(--text);
            height: 100vh;
            height: 100dvh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
        }

        /* Header */
        .header {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .header-title {
            font-size: 15px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-badge.connected {
            background: rgba(16, 185, 129, 0.15);
            color: var(--success);
        }

        .status-badge.disconnected {
            background: rgba(239, 68, 68, 0.15);
            color: var(--danger);
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: currentColor;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* Camera Area */
        .camera-container {
            flex: 1;
            min-height: 0;
            position: relative;
            background: #000;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        #camera-video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        #camera-canvas {
            display: none;
        }

        .camera-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            pointer-events: none;
        }

        /* Corner guides */
        .camera-overlay::before {
            content: '';
            position: absolute;
            top: 15%; left: 10%; right: 10%; bottom: 15%;
            border: 2px solid rgba(255,255,255,0.25);
            border-radius: 12px;
        }

        .camera-hint {
            position: absolute;
            top: 8%;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(10px);
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12px;
            color: rgba(255,255,255,0.8);
            white-space: nowrap;
        }

        /* Preview overlay */
        .preview-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.9);
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 10;
            padding: 20px;
        }

        .preview-overlay.active {
            display: flex;
        }

        .preview-overlay img {
            max-width: 100%;
            max-height: 60vh;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
        }

        .preview-actions {
            display: flex;
            gap: 16px;
            margin-top: 24px;
        }

        .preview-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 28px;
            border-radius: 50px;
            border: none;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .preview-btn.cancel {
            background: var(--surface-2);
            color: var(--text);
        }

        .preview-btn.confirm {
            background: var(--success);
            color: white;
        }

        .preview-btn:active {
            transform: scale(0.95);
        }

        /* Bottom Controls */
        .controls {
            background: var(--surface);
            border-top: 1px solid var(--border);
            padding: 12px 16px;
            padding-bottom: max(12px, env(safe-area-inset-bottom));
            flex-shrink: 0;
        }

        .capture-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 24px;
        }

        .capture-btn {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            border: 4px solid white;
            background: transparent;
            cursor: pointer;
            position: relative;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .capture-btn::after {
            content: '';
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: white;
            transition: all 0.15s;
        }

        .capture-btn:active::after {
            transform: scale(0.85);
            background: #ddd;
        }

        .capture-btn:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }

        .flip-btn {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: none;
            background: var(--surface-2);
            color: var(--text);
            font-size: 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .flip-btn:active {
            transform: scale(0.9);
        }

        /* Status log */
        .status-log {
            text-align: center;
            margin-top: 8px;
            font-size: 12px;
            color: var(--text-muted);
            min-height: 18px;
        }

        .status-log.success {
            color: var(--success);
        }

        .status-log.error {
            color: var(--danger);
        }

        .status-log.uploading {
            color: var(--warning);
        }

        /* Loading spinner */
        .spinner {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: currentColor;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            vertical-align: middle;
            margin-right: 6px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Flash effect */
        .flash {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: white;
            opacity: 0;
            pointer-events: none;
            z-index: 5;
        }

        .flash.active {
            animation: flash-anim 0.3s ease-out;
        }

        @keyframes flash-anim {
            0% { opacity: 0.8; }
            100% { opacity: 0; }
        }

        /* Error state */
        .camera-error {
            text-align: center;
            padding: 40px 24px;
        }

        .camera-error .icon {
            font-size: 48px;
            margin-bottom: 16px;
        }

        .camera-error h3 {
            font-size: 18px;
            margin-bottom: 8px;
        }

        .camera-error p {
            color: var(--text-muted);
            font-size: 14px;
            line-height: 1.6;
        }

        .retry-btn {
            margin-top: 20px;
            padding: 10px 24px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-title">
            📷 Kamera Companion
        </div>
        <div class="status-badge connected" id="connection-status">
            <span class="status-dot"></span>
            <span id="status-text">Terhubung</span>
        </div>
    </div>

    <!-- Camera Area -->
    <div class="camera-container">
        <video id="camera-video" autoplay playsinline muted></video>
        <canvas id="camera-canvas"></canvas>
        
        <div class="camera-overlay">
            <div class="camera-hint" id="camera-hint">Arahkan ke nota / surat jalan</div>
        </div>

        <div class="flash" id="camera-flash"></div>

        <!-- Preview overlay -->
        <div class="preview-overlay" id="preview-overlay">
            <img src="" id="preview-image" alt="Preview" />
            <div class="preview-actions">
                <button class="preview-btn cancel" id="btn-retake">
                    🔄 Ulangi
                </button>
                <button class="preview-btn confirm" id="btn-send">
                    ✅ Kirim
                </button>
            </div>
        </div>

        <!-- Error state (hidden by default) -->
        <div class="camera-error" id="camera-error" style="display:none;">
            <div class="icon">📷</div>
            <h3>Kamera Tidak Tersedia</h3>
            <p>Pastikan Anda telah mengizinkan akses kamera pada browser ini.</p>
            <button class="retry-btn" id="btn-retry">Coba Lagi</button>
        </div>
    </div>

    <!-- Bottom Controls -->
    <div class="controls">
        <div class="capture-row">
            <button class="flip-btn" id="btn-flip" title="Ganti Kamera">🔄</button>
            <button class="capture-btn" id="btn-capture" title="Ambil Foto"></button>
            <div style="width:44px;"></div> <!-- Spacer for centering -->
        </div>
        <div class="status-log" id="status-log">Siap mengambil foto</div>
    </div>

    <script>
        const TOKEN = @json($token);
        const BASE_URL = @json(url('/'));
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

        let stream = null;
        let facingMode = 'environment'; // Start with back camera
        let capturedImageData = null;

        const video = document.getElementById('camera-video');
        const canvas = document.getElementById('camera-canvas');
        const ctx = canvas.getContext('2d');
        const flash = document.getElementById('camera-flash');
        const previewOverlay = document.getElementById('preview-overlay');
        const previewImage = document.getElementById('preview-image');
        const statusLog = document.getElementById('status-log');
        const captureBtn = document.getElementById('btn-capture');
        const cameraError = document.getElementById('camera-error');

        // --- Camera Functions ---
        async function startCamera() {
            try {
                // Stop any existing stream
                if (stream) {
                    stream.getTracks().forEach(t => t.stop());
                }

                cameraError.style.display = 'none';
                video.style.display = 'block';

                stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: facingMode,
                        width: { ideal: 1920 },
                        height: { ideal: 1080 }
                    },
                    audio: false
                });

                video.srcObject = stream;
                captureBtn.disabled = false;
                setStatus('Siap mengambil foto', '');
            } catch (err) {
                console.error('Camera error:', err);
                video.style.display = 'none';
                cameraError.style.display = 'block';
                captureBtn.disabled = true;
                setStatus('Gagal mengakses kamera', 'error');
            }
        }

        function capturePhoto() {
            if (!stream) return;

            // Flash effect
            flash.classList.add('active');
            setTimeout(() => flash.classList.remove('active'), 300);

            // Crop to 3:4 aspect ratio (portrait, center crop)
            const vw = video.videoWidth;
            const vh = video.videoHeight;
            const targetRatio = 3 / 4;
            let sx, sy, sw, sh;

            if (vw / vh > targetRatio) {
                // Video is wider than 4:3 → crop sides
                sh = vh;
                sw = Math.round(vh * targetRatio);
                sx = Math.round((vw - sw) / 2);
                sy = 0;
            } else {
                // Video is taller than 4:3 → crop top/bottom
                sw = vw;
                sh = Math.round(vw / targetRatio);
                sx = 0;
                sy = Math.round((vh - sh) / 2);
            }

            canvas.width = sw;
            canvas.height = sh;
            ctx.drawImage(video, sx, sy, sw, sh, 0, 0, sw, sh);

            // Compress to JPEG (quality 0.85)
            capturedImageData = canvas.toDataURL('image/jpeg', 0.85);

            // Show preview
            previewImage.src = capturedImageData;
            previewOverlay.classList.add('active');
            setStatus('Preview foto — kirim atau ulangi?', '');
        }

        function retakePhoto() {
            capturedImageData = null;
            previewOverlay.classList.remove('active');
            setStatus('Siap mengambil foto', '');
        }

        async function sendPhoto() {
            if (!capturedImageData) return;

            setStatus('<span class="spinner"></span> Mengirim foto...', 'uploading');
            document.getElementById('btn-send').disabled = true;

            try {
                const response = await fetch(BASE_URL + '/companion/upload', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        token: TOKEN,
                        photo: capturedImageData
                    })
                });

                const data = await response.json();

                if (data.success) {
                    previewOverlay.classList.remove('active');
                    capturedImageData = null;
                    setStatus('✅ Foto berhasil dikirim ke PC!', 'success');
                    document.getElementById('camera-hint').textContent = 'Foto sudah terkirim';

                    // Re-enable after 5s in case PC requests new photo
                    setTimeout(() => {
                        captureBtn.disabled = false;
                        document.getElementById('camera-hint').textContent = 'Arahkan ke nota / surat jalan';
                        setStatus('Siap mengambil foto baru', '');
                    }, 5000);
                } else {
                    setStatus('❌ ' + (data.message || 'Gagal mengirim foto'), 'error');
                }
            } catch (err) {
                console.error('Upload error:', err);
                setStatus('❌ Gagal mengirim — periksa koneksi internet', 'error');
            }

            document.getElementById('btn-send').disabled = false;
        }

        function flipCamera() {
            facingMode = facingMode === 'environment' ? 'user' : 'environment';
            startCamera();
        }

        function setStatus(text, type) {
            statusLog.innerHTML = text;
            statusLog.className = 'status-log' + (type ? ' ' + type : '');
        }

        // --- Heartbeat (every 15s for fast PC detection) ---
        setInterval(async () => {
            try {
                const res = await fetch(BASE_URL + '/companion/heartbeat/' + TOKEN, {
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN }
                });
                if (res.ok) {
                    document.getElementById('connection-status').className = 'status-badge connected';
                    document.getElementById('status-text').textContent = 'Terhubung';
                } else {
                    document.getElementById('connection-status').className = 'status-badge disconnected';
                    document.getElementById('status-text').textContent = 'Sesi Expired';
                    captureBtn.disabled = true;
                }
            } catch (e) {
                // Silently fail
            }
        }, 15000); // Every 15 seconds

        // --- Event Listeners ---
        captureBtn.addEventListener('click', capturePhoto);
        document.getElementById('btn-retake').addEventListener('click', retakePhoto);
        document.getElementById('btn-send').addEventListener('click', sendPhoto);
        document.getElementById('btn-flip').addEventListener('click', flipCamera);
        document.getElementById('btn-retry').addEventListener('click', startCamera);

        // --- Init ---
        startCamera();
    </script>
</body>
</html>
