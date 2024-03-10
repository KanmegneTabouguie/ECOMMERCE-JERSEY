<?php

require_once 'database.php';

// Function to fetch all products with associated photos
function getAllProducts($conn) {
    $query = "SELECT * FROM product";  // Assuming your table is named 'products'
    $result = mysqli_query($conn, $query);

    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $productId = $row['product_id'];

        // Remove all double quotes from photo_data
        $photoData = str_replace('"', '', $row['photo_data']);

        // Add product details with one set of quotes around photo_data
        $productDetails = [
            'product_id' => $row['product_id'],
            'product_name' => $row['product_name'],
            'description' => $row['description'],
            'price' => $row['price'],
            'category' => $row['category'],
            'stock_quantity' => $row['stock_quantity'],
            'photo_data' => $photoData,  // Remove extra quotes from photo_data
        ];

        $products[] = $productDetails;
    }

    return $products;
}





// Function to fetch product by ID with associated photos
function getProductById($conn, $productId) {
    $query = "SELECT * FROM product WHERE product_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $productId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $product = null;
    if ($row = mysqli_fetch_assoc($result)) {
        // Add product details
        $product = [
            'product_id' => $row['product_id'],
            'product_name' => $row['product_name'],
            'description' => $row['description'],
            'price' => $row['price'],
            'category' => $row['category'],
            'stock_quantity' => $row['stock_quantity'],
            'photo_data' => json_decode($row['photo_data'], true),  // Assuming 'photo_data' is in JSON format
        ];
    }

    return $product;
}

// Function to add a new product
function addProduct($conn, $productName, $description, $price, $category, $stockQuantity, $photoData, $userId = null) {
    // Ensure photo_data is an array and convert it to a JSON string
    $photoDataJson = !empty($photoData) ? json_encode($photoData) : null;

    $query = "INSERT INTO product (product_name, description, price, category, stock_quantity, photo_data) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sssiss", $productName, $description, $price, $category, $stockQuantity, $photoDataJson);
    $success = mysqli_stmt_execute($stmt);

    if ($success) {
        return "Product added successfully";
    } else {
        return "Failed to add product: " . mysqli_error($conn);
    }
}

// Function to edit an existing product
// Function to edit an existing product
function editProduct($conn, $productId, $productName = null, $description = null, $price = null, $category = null, $stockQuantity = null, $photoData = null) {
    // Build the SET clause for the SQL query
    $setClause = "";
    $params = array();

    if ($productName !== null) {
        $setClause .= "product_name = ?, ";
        $params[] = $productName;
    }
    if ($description !== null) {
        $setClause .= "description = ?, ";
        $params[] = $description;
    }
    if ($price !== null) {
        $setClause .= "price = ?, ";
        $params[] = $price;
    }
    if ($category !== null) {
        $setClause .= "category = ?, ";
        $params[] = $category;
    }
    if ($stockQuantity !== null) {
        $setClause .= "stock_quantity = ?, ";
        $params[] = $stockQuantity;
    }
    if ($photoData !== null) {
        $setClause .= "photo_data = ?, ";
        $params[] = $photoData;
    }

    // Remove the trailing comma and space
    $setClause = rtrim($setClause, ', ');

    // Prepare and execute the SQL query
    $query = "UPDATE product SET $setClause WHERE product_id = ?";
    $params[] = $productId;

    $paramTypes = str_repeat('s', count($params));
    array_unshift($params, $paramTypes); // Add param types at the beginning

    $stmt = mysqli_prepare($conn, $query);
    call_user_func_array('mysqli_stmt_bind_param', array_merge(array($stmt), $params));

    $success = mysqli_stmt_execute($stmt);

    if ($success) {
        return "Product updated successfully";
    } else {
        return "Failed to update product: " . mysqli_error($conn);
    }
}



// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if product_id is provided in the URL
if (isset($_GET['product_id'])) {
    $productId = intval($_GET['product_id']);

    // Fetch product by ID
    $singleProduct = getProductById($conn, $productId);

    // Output JSON response for a single product
    header('Content-Type: application/json');
    echo json_encode($singleProduct);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if it's a POST request (for adding a new product)
    $requestData = json_decode(file_get_contents("php://input"), true);

    // Extract data from the decoded JSON
    $productName = $requestData['product_name'];
    $description = $requestData['description'];
    $price = $requestData['price'];
    $category = $requestData['category'];
    $stockQuantity = $requestData['stock_quantity'];
    $photoData = $requestData['photo_data'];

    // Add a new product
    $addProductResult = addProduct($conn, $productName, $description, $price, $category, $stockQuantity, $photoData);

    // Output JSON response for the result of adding a product
    header('Content-Type: application/json');
    echo json_encode(['message' => $addProductResult]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Check if it's a PUT request (for editing an existing product)
    $requestData = json_decode(file_get_contents("php://input"), true);

    // Extract data from the decoded JSON
    $productId = $requestData['product_id'];
    $productName = $requestData['product_name'];
    $description = $requestData['description'];
    $price = $requestData['price'];
    $category = $requestData['category'];
    $stockQuantity = $requestData['stock_quantity'];
    $photoData = $requestData['photo_data'];

    // Edit an existing product
    $editProductResult = editProduct($conn, $productId, $productName, $description, $price, $category, $stockQuantity, $photoData);

    // Output JSON response for the result of editing a product
    header('Content-Type: application/json');
    echo json_encode(['message' => $editProductResult]);
} else {
    // Fetch all products
    $allProducts = getAllProducts($conn);

    // Output JSON response for all products
    header('Content-Type: application/json');
    echo json_encode($allProducts);
}

// Close the database connection
$conn->close();

?>
