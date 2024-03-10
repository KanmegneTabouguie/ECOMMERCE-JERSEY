<?php
    // Start the session
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Product Management</h2>
        <!-- Add Product Button -->
        <button class="btn btn-success mb-3" data-toggle="modal" data-target="#addProductModal">Add Product</button>

        <!-- Product Table -->
        <table class="table">
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Stock Quantity</th>
                    <th>Photo Data</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="productTableBody">
                <!-- Product details will be populated here using JavaScript -->
            </tbody>
        </table>

        <!-- Add Product Modal -->
        <div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addProductModalLabel">Add Product</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Form to add a new product -->
                        <form id="addProductForm">
                            <div class="form-group">
                                <label for="productName">Product Name:</label>
                                <input type="text" class="form-control" id="productName" required>
                            </div>
                            <div class="form-group">
                                <label for="description">Description:</label>
                                <textarea class="form-control" id="description" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="price">Price:</label>
                                <input type="number" class="form-control" id="price" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label for="category">Category:</label>
                                <select class="form-control" id="category" required>
                                    <option value="Premier League">Premier League</option>
                                    <option value="La Liga">La Liga</option>
                                    <option value="Bundesliga">Bundesliga</option>
                                    <option value="Serie A">Serie A</option>
                                    <option value="Ligue 1">Ligue 1</option>
                                    <option value="Liga NOS">Liga NOS</option>
                                    <option value="Eredivisie">Eredivisie</option>
                                    <option value="Pays">Pays</option>

                                </select>
                            </div>
                            <div class="form-group">
                                <label for="stockQuantity">Stock Quantity:</label>
                                <input type="number" class="form-control" id="stockQuantity" required>
                            </div>
                            <div class="form-group">
                                <label for="photoData">Photo Data URL:</label>
                                <input type="url" class="form-control" id="photoData" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Add Product</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Product Modal -->
        <div class="modal fade" id="viewProductModal" tabindex="-1" role="dialog" aria-labelledby="viewProductModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewProductModalLabel">View Product</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Product details will be populated here using JavaScript -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Update Product Modal -->
        <div class="modal fade" id="updateProductModal" tabindex="-1" role="dialog" aria-labelledby="updateProductModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateProductModalLabel">Update Product</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Form to update a product -->
                        <form id="updateProductForm">
                            <input type="hidden" id="updateProductId" value="">
                            <div class="form-group">
                                <label for="updateProductName">Product Name:</label>
                                <input type="text" class="form-control" id="updateProductName" required>
                            </div>
                            <div class="form-group">
                                <label for="updateDescription">Description:</label>
                                <textarea class="form-control" id="updateDescription" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="updatePrice">Price:</label>
                                <input type="number" class="form-control" id="updatePrice" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label for="updateCategory">Category:</label>
                                <input type="text" class="form-control" id="updateCategory" required>
                            </div>
                            <div class="form-group">
                                <label for="updateStockQuantity">Stock Quantity:</label>
                                <input type="number" class="form-control" id="updateStockQuantity" required>
                            </div>
                            <div class="form-group">
                                <label for="updatePhotoData">Photo Data URL:</label>
                                <input type="url" class="form-control" id="updatePhotoData" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Product</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Your custom JavaScript file to handle API requests and update the UI -->
    <script src="./productapi.js"></script>
</body>
</html>
