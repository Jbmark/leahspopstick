
<?php include 'app/views/layouts/header.php'; ?>

<div class="container">

    <?php include 'app/views/layouts/sidebar.php'; ?>

    <div class="content">

        <style>
            .scanner-page {
                max-width: 900px;
                margin: 0 auto;
            }

            .scanner-heading {
                margin-bottom: 24px;
            }

            .scanner-heading h1 {
                margin: 0 0 8px;
                font-size: 28px;
            }

            .scanner-heading p {
                margin: 0;
                color: #64748b;
                line-height: 1.6;
            }

            .scanner-card {
                background: #fff;
                border: 1px solid #e2e8f0;
                border-radius: 14px;
                padding: 24px;
                box-shadow: 0 4px 15px rgba(0,0,0,.04);
            }

            .scanner-instructions {
                margin-bottom: 20px;
                padding: 14px;
                background: #f8fafc;
                border-radius: 10px;
                color: #475569;
                line-height: 1.6;
            }

            #reader {
                width: 100%;
                max-width: 480px;
                margin: 0 auto 20px;
                overflow: hidden;
                border-radius: 12px;
            }

            #reader video {
                border-radius: 10px;
            }

            #reader img {
                max-width: 100%;
            }

            .scanner-status {
                padding: 14px 16px;
                margin: 16px 0;
                background: #f1f5f9;
                border-radius: 9px;
                color: #334155;
                line-height: 1.5;
                text-align: center;
                overflow-wrap: anywhere;
            }

            .scanner-actions {
                display: flex;
                justify-content: center;
                align-items: center;
                flex-wrap: wrap;
                gap: 12px;
                margin-top: 20px;
            }

            .scanner-actions button,
            .scanner-actions a {
                display: inline-flex;
                justify-content: center;
                align-items: center;
                padding: 12px 20px;
                min-height: 44px;
                border: none;
                border-radius: 8px;
                text-decoration: none;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
            }

            .scanner-start {
                background: #166534;
                color: #fff;
            }

            .scanner-stop {
                background: #e2e8f0;
                color: #334155;
            }

            .scanner-back {
                background: #fff;
                color: #334155;
                border: 1px solid #cbd5e1 !important;
            }

            .scanner-actions button:disabled {
                opacity: .55;
                cursor: not-allowed;
            }

            @media (max-width: 600px) {
                .scanner-card {
                    padding: 16px;
                }

                .scanner-heading h1 {
                    font-size: 23px;
                }

                .scanner-actions {
                    flex-direction: column;
                    align-items: stretch;
                }

                .scanner-actions button,
                .scanner-actions a {
                    width: 100%;
                    box-sizing: border-box;
                }
            }
        </style>

        <div class="scanner-page">

            <div class="scanner-heading">
                <h1>Scan Batch QR</h1>
                <p>
                    Scan a saved inventory batch to view its details.
                </p>
            </div>

            <div class="scanner-card">

                <div class="scanner-instructions">
                    <strong>How to scan</strong>
                    <p style="margin:6px 0 0;">
                        Allow camera access, then position the batch QR
                        code inside the camera frame. Use your phone's
                        rear camera when available.
                    </p>
                </div>

                <div id="reader"></div>

                <div id="scan-status"
                     class="scanner-status"
                     role="status"
                     aria-live="polite">
                    Scanner is stopped. Click Start Scanner to begin.
                </div>

                <div class="scanner-actions">
                    <button type="button"
                            id="start-scan"
                            class="scanner-start">
                        Start Scanner
                    </button>

                    <button type="button"
                            id="stop-scan"
                            class="scanner-stop"
                            disabled>
                        Stop Scanner
                    </button>

                    <a href="inventory.php" class="scanner-back">
                        Back to Inventory
                    </a>
                </div>

            </div>
        </div>

    </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
let scanner = null;
let isRunning = false;
let isOpeningBatch = false;

const statusEl = document.getElementById('scan-status');
const startBtn = document.getElementById('start-scan');
const stopBtn = document.getElementById('stop-scan');

async function startScanner() {
    if (isRunning || isOpeningBatch) return;

    if (typeof Html5Qrcode === 'undefined') {
        statusEl.textContent =
            'Unable to load the scanner. Check your internet connection and refresh the page.';
        return;
    }

    startBtn.disabled = true;
    statusEl.textContent = 'Starting camera...';

    try {
        if (!scanner) {
            scanner = new Html5Qrcode('reader');
        }

        await scanner.start(
            { facingMode: 'environment' },
            {
                fps: 10,
                qrbox: { width: 230, height: 230 },
                aspectRatio: 1
            },
            async function(decodedText) {
                if (isOpeningBatch) return;

                let data;

                try {
                    data = JSON.parse(decodedText);
                } catch (error) {
                    statusEl.textContent =
                        'This QR is not a recognized inventory batch QR.';
                    return;
                }

                const batchId = Number(data.batch_id);

                if (
                    data.type !== 'leahs_popstick_batch' ||
                    !Number.isSafeInteger(batchId) ||
                    batchId <= 0
                ) {
                    statusEl.textContent =
                        'Invalid batch QR. Please scan a Leah’s Popstick batch label.';
                    return;
                }

                isOpeningBatch = true;
                statusEl.textContent =
                    'Batch QR recognized. Opening batch details...';

                try {
                    await stopScanner();

                    window.location.href =
                        'inventory.php?action=view_batch&id=' +
                        encodeURIComponent(batchId);
                } catch (error) {
                    isOpeningBatch = false;
                    statusEl.textContent =
                        'Could not stop the camera. Please try again.';
                    startBtn.disabled = false;
                }
            },
            function() {
                // Ignore frames where no QR is detected.
            }
        );

        isRunning = true;
        startBtn.disabled = true;
        stopBtn.disabled = false;

        statusEl.textContent =
            'Camera is active. Point it at a batch QR code.';

    } catch (error) {
        statusEl.textContent =
            'Camera could not start. Check camera permission and use HTTPS on your hosted website.';

        startBtn.disabled = false;
        console.error(error);
    }
}

async function stopScanner() {
    if (scanner && isRunning) {
        await scanner.stop();
        isRunning = false;
    }

    stopBtn.disabled = true;

    if (!isOpeningBatch) {
        startBtn.disabled = false;
        statusEl.textContent = 'Scanner stopped.';
    }
}

startBtn.addEventListener('click', startScanner);
stopBtn.addEventListener('click', stopScanner);
</script>
</body>
</html>
