<?php include 'app/views/layouts/header.php'; ?>

<div class="container">
    <?php include 'app/views/layouts/sidebar.php'; ?>

    <div class="content">
        <h1>Add New Product</h1>
        <p>Add a product, set its category and price, and upload its picture.</p>

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
                        required
                        placeholder="e.g., Popsicle Stick"
                    >
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <input
                        name="description"
                        placeholder="e.g., Wooden food product"
                    >
                </div>

                <div class="form-group">
                    <label>Product Category</label>
                    <select name="category" required>
                        <option value="">-- Select Category --</option>
                        <option value="Wooden Utensils">Wooden Utensils</option>
                        <option value="Wooden Sticks">Wooden Sticks</option>
                        <option value="Food Accessories">Food Accessories</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Unit Measurement</label>
                    <input
                        name="unit"
                        required
                        placeholder="e.g., box"
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
                            required
                            placeholder="0.00"
                        >
                    </div>

                    <div class="form-group">
                        <label>Reorder Level</label>
                        <input
                            name="reorder_level"
                            type="number"
                            min="0"
                            step="1"
                            required
                            placeholder="10"
                        >
                    </div>

                </div>

                <div class="form-group">
                    <label>Product Image (JPG, PNG, or WEBP; max 2 MB)</label>
                    <input
                        type="file"
                        name="image"
                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                    >
                </div>

                <button
                    type="submit"
                    class="btn-login"
                    style="margin-top: 10px;"
                >
                    Save Product
                </button>

            </form>
        </div>
    </div>
</div>

<?php include 'app/views/layouts/footer.php'; ?>
