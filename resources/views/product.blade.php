@extends('app.layout')

@section('content')
<!-- Add Product Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addModalLabel">Add Product</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="add-form">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="categoryId" class="form-label">Category</label>
                        <select class="form-select" id="categoryId" name="category_id" required>
                            <option value="">Select Category</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="addProductName">Product Name</label>
                        <input type="text" class="form-control" id="addProductName" name="name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editModalLabel">Edit Product</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="edit-form">
                <input type="hidden" id="editId" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="editCategoryId" class="form-label">Category</label>
                        <select class="form-select" id="editCategoryId" name="category_id" required>
                            <option value="">Select Category</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="editProductName">Product Name</label>
                        <input type="text" class="form-control" id="editProductName" name="name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="container mt-5">
    <div class="d-flex justify-content-between">
        <h1>Product</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            Add
        </button>
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody id="productTable">
            <!-- product data will be populated here -->
        </tbody>
    </table>
</div>

<script>
    function fetchProducts() {
        axios.get('{{ route('getproducts') }}')
            .then(response => {
                const data = response.data;
                const productTable = document.getElementById('productTable');
                productTable.innerHTML = '';

                if (!data.products || data.products.length === 0) {
                    productTable.innerHTML = `
                        <tr><td colspan="3" class="text-center text-muted">No products found.</td></tr>
                    `;
                    return;
                }

                data.products.forEach((product, index) => {
                    const safeName = product.name.replace(/'/g, "\\'");
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <th scope="row">${index + 1}</th>
                        <td>${product.category}</td>
                        <td>${product.name}</td>
                        <td>
                            <button class="btn btn-danger btn-sm" onclick="deleteProduct(${product.id})">Delete</button>
                            <button class="btn btn-warning btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#editModal"
                                onclick="editProduct(${product.id}, '${safeName}', ${product.category_id})">
                                Edit
                            </button>
                        </td>
                    `;
                    productTable.appendChild(row);
                });
            })
            .catch(error => {
                console.error('Error fetching products:', error);
            });
    }

    fetchProducts();

    document.getElementById('addModal').addEventListener('show.bs.modal', function () {
        populateCategorySelect('categoryId');
    });

    document.getElementById('add-form').addEventListener('submit', function (e) {
        e.preventDefault();
        const name = document.getElementById('addProductName').value;
        const category_id = document.getElementById('categoryId').value;

        axios.post('{{ route('product.store') }}', { name, category_id }, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            const data = response.data;

            if (!data.status) {
                let messages = data.errors ? Object.values(data.errors).flat().join('\n') : 'Unknown error';
                Swal.fire({ icon: 'error', title: 'Error', text: messages });
                return;
            }

            fetchProducts();
            document.getElementById('add-form').reset();
            bootstrap.Modal.getInstance(document.getElementById('addModal')).hide();
            Swal.fire({
                icon: 'success',
                title: 'Product Added',
                text: 'Product has been added successfully!',
                timer: 2000,
                showConfirmButton: false
            });
        })
        .catch(error => {
            console.error('Error adding Product:', error);
            Swal.fire({ icon: 'error', title: 'Something Went Wrong', text: error.message });
        });
    });

    function editProduct(id, name, categoryId) {
        document.getElementById('editId').value = id;
        document.getElementById('editProductName').value = name;
        populateCategorySelect('editCategoryId', categoryId);
    }

    document.getElementById('edit-form').addEventListener('submit', function (e) {
        e.preventDefault();

        const id = document.getElementById('editId').value;
        const name = document.getElementById('editProductName').value;
        const category_id = document.getElementById('editCategoryId').value;

        axios.put(`{{ route('product.update', ':id') }}`.replace(':id', id), { name, category_id }, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            const data = response.data;

            if (!data.status) {
                let messages = data.errors ? Object.values(data.errors).flat().join('\n') : 'Update failed';
                Swal.fire({ icon: 'error', title: 'Update Failed', text: messages });
                return;
            }

            fetchProducts();
            document.getElementById('edit-form').reset();
            bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
            Swal.fire({
                icon: 'success',
                title: 'Product Updated',
                text: 'Product has been updated successfully!',
                timer: 2000,
                showConfirmButton: false
            });
        })
        .catch(error => {
            console.error('Unexpected error:', error);
            Swal.fire({ icon: 'error', title: 'Unexpected Error', text: error.message });
        });
    });

    function deleteProduct(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'This Product will be deleted permanently!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
        }).then(result => {
            if (result.isConfirmed) {
                axios.delete(`{{ route('product.destroy', ':id') }}`.replace(':id', id), {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Product Deleted',
                        text: 'Product has been deleted successfully!',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    fetchProducts();
                })
                .catch(error => {
                    console.error('Error deleting Product:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Something Went Wrong',
                        text: error.message || 'Error deleting Product',
                        timer: 2000,
                        showConfirmButton: false
                    });
                });
            }
        });
    }

    function populateCategorySelect(selectId, selectedValue = null) {
        axios.get('{{ route('getcategories') }}')
            .then(response => {
                const categories = response.data.categories || [];
                const select = document.getElementById(selectId);
                select.innerHTML = `<option value="">Select Category</option>`;

                categories.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category.id;
                    option.textContent = category.name;
                    if (selectedValue && selectedValue == category.id) {
                        option.selected = true;
                    }
                    select.appendChild(option);
                });
            })
            .catch(error => {
                console.error(`Error loading categories for #${selectId}:`, error);
            });
    }

</script>
@endsection
