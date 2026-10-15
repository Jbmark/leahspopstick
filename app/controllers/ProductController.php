
<?php

require_once 'config/database.php';
require_once 'app/models/Product.php';

$product = new Product($pdo);
$action = $_GET['action'] ?? 'index';

//////////////////////////////////////////////////////////
/* Find the project root folder */
$projectRoot = __DIR__;

while (
    !(
        is_file($projectRoot . '/app/models/Product.php') &&
        is_dir($projectRoot . '/public')
    )
) {
    $parent = dirname($projectRoot);

    if ($parent === $projectRoot) {
        die('ERROR: Cannot find the project root folder.');
    }

    $projectRoot = $parent;
}

/* Set the correct image upload folder */
$imageFolder = $projectRoot . '/public/shop/images/';

/* Create the folder if it does not exist */
if (!is_dir($imageFolder)) {
    if (!mkdir($imageFolder, 0755, true) && !is_dir($imageFolder)) {
        die('ERROR: Could not create image folder: ' . $imageFolder);
    }
}

/* Check if PHP can write to the folder */
if (!is_writable($imageFolder)) {
    die('ERROR: Image folder is not writable: ' . $imageFolder);
}

///////////////////////////////////////////////////////////////

// Handle image upload

function uploadProductImage($imageFolder)
{
    if (
        !isset($_FILES['image']) ||
        $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE
    ) {
        return null;
    }

    $file = $_FILES['image'];

    // Check the upload error
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception(
            'PHP upload error code: ' . $file['error']
        );
    }

    // Check file size
    if ($file['size'] > 2 * 1024 * 1024) {
        throw new Exception('Image must not exceed 2 MB.');
    }

    // Check that the temporary file exists
    if (!is_uploaded_file($file['tmp_name'])) {
        throw new Exception('The uploaded temporary file is invalid.');
    }

    // Check the actual image type
    $imageInfo = getimagesize($file['tmp_name']);

    if ($imageInfo === false) {
        throw new Exception('Please upload a valid image.');
    }

    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp'
    ];

    $mimeType = $imageInfo['mime'];

    if (!isset($allowedTypes[$mimeType])) {
        throw new Exception(
            'Only JPG, PNG, and WEBP images are allowed.'
        );
    }

    // Generate a unique filename
    $filename = bin2hex(random_bytes(16))
        . '.'
        . $allowedTypes[$mimeType];

    // Build the absolute destination path
    $destination = $imageFolder . $filename;

    // Save debug information before moving the file
    file_put_contents(
        __DIR__ . '/upload-debug.txt',
        date('Y-m-d H:i:s') .
        "\nDestination: " . $destination .
        "\nFolder exists: " . (is_dir($imageFolder) ? 'YES' : 'NO') .
        "\nFolder writable: " . (is_writable($imageFolder) ? 'YES' : 'NO') .
        "\nTemporary file: " . $file['tmp_name'] .
        "\nTemporary file exists: " .
        (file_exists($file['tmp_name']) ? 'YES' : 'NO') .
        "\n",
        FILE_APPEND
    );

    // Move the uploaded file into public/shop/images
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        $lastError = error_get_last();

        file_put_contents(
            __DIR__ . '/upload-debug.txt',
            "MOVE FAILED\n" .
            "PHP error: " . ($lastError['message'] ?? 'No PHP error reported') .
            "\nDestination: " . $destination . "\n\n",
            FILE_APPEND
        );

        throw new Exception(
            'Could not save the image. Check upload-debug.txt for the destination and PHP error.'
        );
    }

    // Confirm the file was saved
    if (!is_file($destination)) {
        throw new Exception(
            'The image move completed, but the destination file cannot be found.'
        );
    }

    // Return the relative path stored in the database
    return 'shop/images/' . $filename;
}

///////////////////////////////////////////////////////////////

switch ($action) {

    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = $_POST;
                $data['image'] = uploadProductImage($imageFolder);

                $product->create($data);

                header('Location: products.php');
                exit;

            } catch (Exception $e) {
                $error_message = $e->getMessage();
            }
        }

        include 'app/views/products/create.php';
        break;

    case 'edit':
        $id = (int) ($_GET['id'] ?? 0);
        $item = $product->getById($id);

        if (!$item) {
            header('Location: products.php');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = $_POST;

                // Keep the current image if no new image is uploaded
                $newImage = uploadProductImage($imageFolder);

                $data['image'] = $newImage ?? $item['image'];

                $product->update($id, $data);

////////////////////////////////////////////////
                // Delete the previous image only after a successful update
                
if (
    $newImage &&
    !empty($item['image']) &&
    strpos($item['image'], 'shop/images/') === 0
) {
    $oldImagePath = $projectRoot . '/public/' . $item['image'];

    if (is_file($oldImagePath)) {
        unlink($oldImagePath);
    }
}

///////////////////////////////////////////////////

                header('Location: products.php');
                exit;

            } catch (Exception $e) {
                $error_message = $e->getMessage();
            }
        }

        include 'app/views/products/edit.php';
        break;

    case 'delete':
        $product->deactivate((int) ($_GET['id'] ?? 0));
        header('Location: products.php');
        exit;

//////////////////////////////////////////////    
default:
    $category = $_GET['category'] ?? '';

    $allowedCategories = [
        'Wooden Utensils',
        'Wooden Sticks',
        'Food Accessories',
        'Uncategorized'
    ];

    if (!in_array($category, $allowedCategories, true)) {
        $category = '';
    }

    $products = $product->getAll($category);

    include 'app/views/products/index.php';
    break;

    ///////////////////////////////////////////////

}
