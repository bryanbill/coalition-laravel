<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Product Form</h1>
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10 col-sm-12">
                <form id="productForm" class="mb-4">
                    @csrf
                    <div class="mb-3">
                        <label for="product_name" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="product_name" name="product_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity in Stock</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" required>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Price per Item</label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Submit</button>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Datetime</th>
                                <th>Total Value</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="productTableBody">

                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4">Total</th>
                                <th id="totalSum">0</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        @csrf
                        <input type="hidden" id="editIndex" name="index">
                        <div class="mb-3">
                            <label for="editProductName" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="editProductName" name="product_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="editQuantity" class="form-label">Quantity in Stock</label>
                            <input type="number" class="form-control" id="editQuantity" name="quantity" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPrice" class="form-label">Price per Item</label>
                            <input type="number" step="0.01" class="form-control" id="editPrice" name="price" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveChangesBtn">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    $(document).ready(function() {
        loadProducts();

        // Form submission
        $('#productForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('submit') }}",
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    loadProducts();
                    $('#productForm')[0].reset(); // Clear the form
                }
            });
        });

        // Save changes in the modal
        $('#saveChangesBtn').on('click', function() {
            const index = $('#editIndex').val();
            const product_name = $('#editProductName').val();
            const quantity = $('#editQuantity').val();
            const price = $('#editPrice').val();

            $.ajax({
                url: `/edit/${index}`,
                method: 'POST',
                data: {
                    product_name: product_name,
                    quantity: quantity,
                    price: price,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    $('#editModal').modal('hide');
                    loadProducts();
                }
            });
        });
    });

    function loadProducts() {
        $.ajax({
            url: "{{ route('home') }}",
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                $('#productTableBody').empty();
                let totalSum = 0;

                response.forEach((product, index) => {
                    $('#productTableBody').append(`
                            <tr>
                                <td>${product.product_name}</td>
                                <td>${product.quantity}</td>
                                <td>${product.price}</td>
                                <td>${product.datetime}</td>
                                <td>${product.total_value}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="openEditModal(${index}, '${product.product_name}', ${product.quantity}, ${product.price})">Edit</button>
                                </td>
                            </tr>
                        `);
                    totalSum += parseFloat(product.total_value);
                });

                $('#totalSum').text(totalSum.toFixed(2));
            }
        });
    }

    function openEditModal(index, product_name, quantity, price) {
        $('#editIndex').val(index);
        $('#editProductName').val(product_name);
        $('#editQuantity').val(quantity);
        $('#editPrice').val(price);
        $('#editModal').modal('show');
    }
    </script>
</body>

</html>