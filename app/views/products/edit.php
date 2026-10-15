
<?php include 'app/views/layouts/header.php'; ?>

<div class="container">
    <?php include 'app/views/layouts/sidebar.php'; ?>

    <div class="content">
        <h1>Edit Product</h1>
        <p>Update product information, category, price, and image.</p>

        <div class="card" style="max-width: 600px;">

            <?php if (!empty($error_message)) { ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error_message); ?>
                </div>
            <?php } ?>

            <form method="POST" enctype="multipart/form-data">

                <div class="form-group">
                    <label>Product Name</label>
                    <input
                        name="product_name"
                        value="<?= htmlspecialchars($item['product_name']); ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <input
                        name="description"
                        value="<?= htmlspecialchars($item['description'] ?? ''); ?>"
                    >
                </div>

                <div class="form-group">
                    <label>Product Category</label>
                    <select name="category" required>
                        <option value="Wooden Utensils"
                            <?= ($item['category'] ?? '') === 'Wooden Utensils' ? 'selected' : ''; ?>>
                            Wooden Utensils
                        </option>

                        <option value="Wooden Sticks"
                            <?= ($item['category'] ?? '') === 'Wooden Sticks' ? 'selected' : ''; ?>>
                            Wooden Sticks
                        </option>

                        <option value="Food Accessories"
                            <?= ($item['category'] ?? '') === 'Food Accessories' ? 'selected' : ''; ?>>
                            Food Accessories
                        </option>

                        <option value="Uncategorized"
                            <?= ($item['category'] ?? '') === 'Uncategorized' ? 'selected' : ''; ?>>
                            Uncategorized
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Unit Measurement</label>
                    <input
                        name="unit"
                        value="<?= htmlspecialchars($item['unit']); ?>"
                        required
                    >
                </div>

                <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">

                    <div class="form-group">
                        <label>Selling Price (₱)</label>
                        <input
                            name="selling_price"
                            type="number"
                            min="0"
                            step="0.01"
                            value="<?= htmlspecialchars((string) $item['selling_price']); ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label>Reorder Level</label>
                        <input
                            name="reorder_level"
                            type="number"
                            min="0"
                            step="1"
                            value="<?= htmlspecialchars((string) $item['reorder_level']); ?>"
                            required
                        >
                    </div>

                </div>

                <div class="form-group">
                    <label>Current Product Image</label>

                    <?php if (!empty($item['image'])) { ?>
                        <div style="margin-bottom: 12px;">
                            
<img
    src="/leahs-popstick/public/<?= htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8'); ?>"
    alt="<?= htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8'); ?>"
    style="width: 180px; height: 140px; object-fit: cover; border-radius: 8px;"
>

                        </div>
                    <?php } else { ?>
                        <p>No image uploaded yet.</p>
                    <?php } ?>
                </div>

                <div class="form-group">
                    <label>Replace Product Image (optional)</label>
                    <input
                        type="file"
                        name="image"
                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                    >
                    <small>JPG, PNG, or WEBP. Maximum size: 2 MB.</small>
                </div>

                <button
                    type="submit"
                    class="btn-login"
                    style="margin-top: 10px;"
                >
                    Update Product
                </button>

            </form>
        </div>
    </div>
</div>

<?php include 'app/views/layouts/footer.php'; ?>
