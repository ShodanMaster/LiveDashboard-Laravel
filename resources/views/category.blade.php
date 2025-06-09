@extends('app.layout')

@section('content')
<!-- Add Category Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addModalLabel">Add Category</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="add-form">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="addCategoryName">Category Name</label>
                        <input type="text" class="form-control" id="addCategoryName" name="name">
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

<!-- Edit Category Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editModalLabel">Edit Category</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="edit-form">
                <input type="hidden" id="editId" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="editCategoryName">Category Name</label>
                        <input type="text" class="form-control" id="editCategoryName" name="name">
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
        <h1>Category</h1>
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
        <tbody id="categoryTable">
            <!-- category data will be populated here -->
        </tbody>
    </table>
</div>

<script>
    function fetchCategories() {
        axios.get('{{ route('getcategories') }}')
            .then(response => {
                const data = response.data;
                const categoryTable = document.getElementById('categoryTable');
                categoryTable.innerHTML = '';

                if (!data.categories || data.categories.length === 0) {
                    categoryTable.innerHTML = `
                        <tr><td colspan="5" class="text-center text-muted">No categories found.</td></tr>
                    `;
                    return;
                }

                data.categories.forEach((category, index) => {
                    const safeName = category.name.replace(/'/g, "\\'");
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <th scope="row">${index + 1}</th>
                        <td>${category.name}</td>
                        <td>
                            <button class="btn btn-danger btn-sm" onclick="deleteCategory(${category.id})">Delete</button>
                            <button class="btn btn-warning btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#editModal"
                                onclick="editCategory(${category.id}, '${safeName}')">
                                Edit
                            </button>
                        </td>
                    `;
                    categoryTable.appendChild(row);
                });
            })
            .catch(error => {
                console.error('Error fetching categories:', error);
            });
    }

    fetchCategories();

    document.getElementById('add-form').addEventListener('submit', function (e) {
        e.preventDefault();
        const name = document.getElementById('addCategoryName').value;

        axios.post('{{ route('category.store') }}', { name }, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            const data = response.data;

            if (!data.status) {
                let messages = data.categories ? Object.values(data.categories).flat().join('\n') : 'Unknown error';
                Swal.fire({ icon: 'error', title: 'Error', text: messages });
                return;
            }

            fetchCategories();
            document.getElementById('add-form').reset();
            bootstrap.Modal.getInstance(document.getElementById('addModal')).hide();
            Swal.fire({
                icon: 'success',
                title: 'Category Added',
                text: 'Category has been added successfully!',
                timer: 2000,
                showConfirmButton: false
            });
        })
        .catch(error => {
            console.error('Error adding Category:', error);
            Swal.fire({ icon: 'error', title: 'Something Went Wrong', text: error.message });
        });
    });

    function editCategory(id, name) {
        document.getElementById('editId').value = id;
        document.getElementById('editCategoryName').value = name;
    }

    document.getElementById('edit-form').addEventListener('submit', function (e) {
        e.preventDefault();

        const id = document.getElementById('editId').value;
        const name = document.getElementById('editCategoryName').value;

        axios.put(`{{ route('category.update', ':id') }}`.replace(':id', id), { name }, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            const data = response.data;

            if (!data.status) {
                let messages = data.categories ? Object.values(data.categories).flat().join('\n') : 'Update failed';
                Swal.fire({ icon: 'error', title: 'Update Failed', text: messages });
                return;
            }

            fetchCategories();
            document.getElementById('edit-form').reset();
            bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
            Swal.fire({
                icon: 'success',
                title: 'Category Updated',
                text: 'Category has been updated successfully!',
                timer: 2000,
                showConfirmButton: false
            });
        })
        .catch(error => {
            console.error('Unexpected error:', error);
            Swal.fire({ icon: 'error', title: 'Unexpected Error', text: error.message });
        });
    });

    function deleteCategory(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'This category will be deleted permanently!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
        }).then(result => {
            if (result.isConfirmed) {
                axios.delete(`{{ route('category.destroy', ':id') }}`.replace(':id', id), {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Category Deleted',
                        text: 'Category has been deleted successfully!',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    fetchCategories();
                })
                .catch(error => {
                    console.error('Error deleting category:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Something Went Wrong',
                        text: error.message || 'Error deleting category',
                        timer: 2000,
                        showConfirmButton: false
                    });
                });
            }
        });
    }
</script>
@endsection
