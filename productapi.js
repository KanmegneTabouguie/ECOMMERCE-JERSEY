document.addEventListener('DOMContentLoaded', function () {
    // Fetch all products on page load
    fetchProducts();

    // Add Product Form
    const addProductForm = document.getElementById('addProductForm');
    addProductForm.addEventListener('submit', function (event) {
        event.preventDefault(); // Prevent the default form submission

        // Get form input values
        const productName = document.getElementById('productName').value;
        const description = document.getElementById('description').value;
        const price = document.getElementById('price').value;
        const category = document.getElementById('category').value;
        const stockQuantity = document.getElementById('stockQuantity').value;
        const photoData = document.getElementById('photoData').value;

        // Create a new product object
        const newProduct = {
            product_name: productName,
            description: description,
            price: price,
            category: category,
            stock_quantity: stockQuantity,
            photo_data: photoData
        };

        // Send a POST request to add the new product
        fetch('productapi.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(newProduct),
        })
        .then(response => response.json())
        .then(result => {
            console.log(result.message);
            // Fetch products again to update the table
            fetchProducts();
            // Close the modal (assuming you're using Bootstrap modal)
            $('#addProductModal').modal('hide');
            // Clear the form inputs
            addProductForm.reset();
        })
        .catch(error => console.error('Error adding product:', error));
    });

    // Update Product Form
    const updateProductForm = document.getElementById('updateProductForm');
    updateProductForm.addEventListener('submit', function (event) {
        event.preventDefault(); // Prevent the default form submission

        // Get form input values for updating a product
        const updateProductId = document.getElementById('updateProductId').value;
        const updateProductName = document.getElementById('updateProductName').value;
        const updateDescription = document.getElementById('updateDescription').value;
        const updatePrice = document.getElementById('updatePrice').value;
        const updateCategory = document.getElementById('updateCategory').value;
        const updateStockQuantity = document.getElementById('updateStockQuantity').value;
        const updatePhotoData = document.getElementById('updatePhotoData').value;

        // Create an object with updated product data
        const updatedProduct = {
            product_id: updateProductId,
            product_name: updateProductName,
            description: updateDescription,
            price: updatePrice,
            category: updateCategory,
            stock_quantity: updateStockQuantity,
            photo_data: updatePhotoData
        };

        // Send a PUT request to update the product
        fetch('productapi.php', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(updatedProduct),
        })
        .then(response => response.json())
        .then(result => {
            console.log(result.message);
            // Fetch products again to update the table
            fetchProducts();
            // Close the modal (assuming you're using Bootstrap modal)
            $('#updateProductModal').modal('hide');
            // Clear the form inputs
            updateProductForm.reset();
        })
        .catch(error => console.error('Error updating product:', error));
    });

    // Function to fetch all products
    function fetchProducts() {
        fetch('productapi.php')
            .then(response => response.json())
            .then(products => {
                displayProducts(products);
            })
            .catch(error => console.error('Error fetching products:', error));
    }

    // Function to display products in the table
    function displayProducts(products) {
        const tableBody = document.getElementById('productTableBody');
        tableBody.innerHTML = '';

        products.forEach(product => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${product.product_id}</td>
                <td>${product.product_name}</td>
                <td>${product.description}</td>
                <td>${product.price}</td>
                <td>${product.category}</td>
                <td>${product.stock_quantity}</td>
                <td>${product.photo_data}</td>
                <td>
                    <button class="btn btn-info btn-sm" onclick="viewProduct(${product.product_id})">View</button>
                    <button class="btn btn-warning btn-sm" onclick="updateProduct(${product.product_id})">Update</button>
                </td>
            `;

            tableBody.appendChild(row);
        });
    }

    // Function to handle product view
    window.viewProduct = function (productId) {
        // Fetch the details of the specific product
        fetch(`productapi.php?product_id=${productId}`)
            .then(response => response.json())
            .then(product => {
                // Display product details in a modal
                displayProductDetailsModal(product);
            })
            .catch(error => console.error('Error fetching product details:', error));
    };

    // Function to display product details in a modal
    function displayProductDetailsModal(product) {
        const viewProductModal = $('#viewProductModal');
        viewProductModal.find('.modal-title').text(`Product Details - ID: ${product.product_id}`);
        
        const modalBody = viewProductModal.find('.modal-body');
        modalBody.html(`
            <p><strong>Product Name:</strong> ${product.product_name}</p>
            <p><strong>Description:</strong> ${product.description}</p>
            <p><strong>Price:</strong> ${product.price}</p>
            <p><strong>Category:</strong> ${product.category}</p>
            <p><strong>Stock Quantity:</strong> ${product.stock_quantity}</p>
            <p><strong>Photo Data:</strong> ${product.photo_data}</p>
        `);

        viewProductModal.modal('show');
    }

    // Function to handle product update
    window.updateProduct = function (productId) {
        // Fetch the details of the specific product
        fetch(`productapi.php?product_id=${productId}`)
            .then(response => response.json())
            .then(product => {
                // Populate the update modal with existing data
                populateUpdateModal(product);
            })
            .catch(error => console.error('Error fetching product details:', error));
    };

    // Function to populate the update modal with existing data
    function populateUpdateModal(product) {
        const updateProductModal = $('#updateProductModal');
        updateProductModal.find('#updateProductId').val(product.product_id);
        updateProductModal.find('#updateProductName').val(product.product_name);
        updateProductModal.find('#updateDescription').val(product.description);
        updateProductModal.find('#updatePrice').val(product.price);
        updateProductModal.find('#updateCategory').val(product.category);
        updateProductModal.find('#updateStockQuantity').val(product.stock_quantity);
        updateProductModal.find('#updatePhotoData').val(product.photo_data);

        // Show the update modal
        updateProductModal.modal('show');
    }

   
});
