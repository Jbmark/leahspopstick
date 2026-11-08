
<?php
if (!isset($batch) || !is_array($batch)) {
    header("Location: inventory.php");
    exit;
}

if (!isset($batchHistory) || !is_array($batchHistory)) {
    $batchHistory = [];
}

function qrEscape($value) {
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
}

// Encode the database batch ID, not a stock-changing action.
$qrPayload = json_encode([
    'type' => 'leahs_popstick_batch',
    'batch_id' => (int)$batch['batch_id'],
    'batch_number' => $batch['batch_number']
]);

include 'app/views/layouts/header.php';
?>

<div class="container" style="max-width:1000px;margin:30px auto;padding:20px;">

    <!-- Batch details and QR -->
    <div style="background:#fff;padding:25px;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.08);">

        <h2>Batch QR Code</h2>
        <p>View the batch details and its inventory transaction history.</p>

        <hr>

        <div style="text-align:center;margin:25px 0;">
            <div id="batch-qr"
                 style="display:inline-block;padding:15px;background:#fff;">
            </div>

            <h3 style="margin-top:15px;">
                <?= qrEscape($batch['batch_number']) ?>
            </h3>

            <p><?= qrEscape($batch['product_name']) ?></p>
        </div>

        <div style="line-height:1.9;">
            <p><strong>Batch ID:</strong>
                <?= (int)$batch['batch_id'] ?></p>

            <p><strong>Quantity Received:</strong>
                <?= (int)$batch['quantity_received'] ?>
                <?= qrEscape($batch['unit']) ?></p>

            <p><strong>Remaining Quantity:</strong>
                <?= (int)$batch['quantity_remaining'] ?>
                <?= qrEscape($batch['unit']) ?></p>

            <p><strong>Date Received:</strong>
                <?= qrEscape($batch['date_received']) ?></p>

            <p><strong>Status:</strong>
                <?= qrEscape($batch['status']) ?></p>
        </div>

        <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:25px;">
            <button type="button" onclick="window.print()"
                    class="btn btn-primary">
                Print QR Label
            </button>

            <a href="inventory.php" class="btn btn-secondary">
                Back to Inventory
            </a>
        </div>
    </div>

    <!-- Batch transaction history -->
    <div id="batch-history"
         style="background:#fff;padding:25px;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.08);margin-top:25px;">

        <h2>Batch History</h2>

        <p style="color:#666;">
            Records of stock received, deducted, returned, or adjusted
            for this batch.
        </p>

        <?php if (empty($batchHistory)): ?>

            <p style="padding:15px;background:#f8f9fa;border-radius:8px;">
                No transaction history found for this batch.
            </p>

        <?php else: ?>

            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;min-width:650px;">

                    <thead>
                        <tr style="background:#f3f4f6;text-align:left;">
                            <th style="padding:12px;border-bottom:1px solid #ddd;">Date</th>
                            <th style="padding:12px;border-bottom:1px solid #ddd;">Transaction</th>
                            <th style="padding:12px;border-bottom:1px solid #ddd;">Quantity</th>
                            <th style="padding:12px;border-bottom:1px solid #ddd;">Order Reference</th>
                            <th style="padding:12px;border-bottom:1px solid #ddd;">Remarks</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($batchHistory as $history): ?>
                            <tr>
                                <td style="padding:12px;border-bottom:1px solid #eee;">
                                    <?= qrEscape($history['transaction_date']) ?>
                                </td>

                                <td style="padding:12px;border-bottom:1px solid #eee;">
                                    <?php
                                    $type = $history['transaction_type'];

                                    $badgeColors = [
                                        'Stock In' => '#dcfce7',
                                        'Stock Out' => '#fee2e2',
                                        'Return' => '#dbeafe',
                                        'Adjustment' => '#fef3c7'
                                    ];

                                    $badgeColor = $badgeColors[$type] ?? '#e5e7eb';
                                    ?>
                                    <span style="display:inline-block;padding:5px 9px;border-radius:6px;background:<?= $badgeColor ?>;">
                                        <?= qrEscape($type) ?>
                                    </span>
                                </td>

                                <td style="padding:12px;border-bottom:1px solid #eee;">
                                    <?= (int)$history['quantity'] ?>
                                    <?= qrEscape($batch['unit']) ?>
                                </td>

                                <td style="padding:12px;border-bottom:1px solid #eee;">
                                    <?php if ($history['reference_id'] !== null): ?>
                                        Order #<?= (int)$history['reference_id'] ?>
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>

                                <td style="padding:12px;border-bottom:1px solid #eee;">
                                    <?= qrEscape($history['remarks'] ?? '') ?: '—' ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>

            <p style="margin-top:12px;color:#666;font-size:13px;">
                Showing <?= count($batchHistory) ?> transaction record(s).
                Newest transactions appear first.
            </p>

        <?php endif; ?>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
const qrPayload = <?= json_encode($qrPayload) ?>;
const qrContainer = document.getElementById('batch-qr');

if (typeof QRCode !== 'undefined') {
    new QRCode(qrContainer, {
        text: qrPayload,
        width: 220,
        height: 220,
        correctLevel: QRCode.CorrectLevel.M
    });
} else {
    qrContainer.textContent =
        'QR library unavailable. Check your internet connection.';
}
</script>

<style>
@media print {
    body * {
        visibility: hidden;
    }

    #batch-qr,
    #batch-qr *,
    #batch-qr ~ h3 {
        visibility: visible;
    }

    #batch-qr {
        position: relative;
    }

    button, a {
        display: none !important;
    }
}
</style>

</body>
</html>
