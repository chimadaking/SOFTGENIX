<?php require BASE_PATH . '/app/views/layouts/header.php'; ?>
<h1 class="mb-4">Manage Products</h1>

<a href="<?php echo BASE_DIR; ?>/product/create" class="btn btn-success mb-3">
    <i class="bi bi-plus-circle"></i> Add New Product
</a>

<div class="card shadow-sm">
    <div class="card-body">
        <?php if (!empty($data['products'])): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['products'] as $product): ?>
                            <tr>
                                <td><?php echo $product->id; ?></td>
                                <td><?php echo h($product->name); ?></td>
                                <td><?php echo h($product->category_name); ?></td>
                                <td>$<?php echo number_format($product->price, 2); ?></td>
                                <td><?php echo $product->stock; ?></td>
                                <td><span class="badge bg-<?php 
                                    $statusColors = [
                                        'active' => 'success',
                                        'inactive' => 'secondary',
                                        'out_of_stock' => 'danger'
                                    ];
                                    echo $statusColors[$product->status] ?? 'secondary';
                                ?>"><?php echo ucfirst($product->status); ?></span></td>
                                <td>
                                    <a href="<?php echo BASE_DIR; ?>/product/edit/<?php echo $product->id; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <a href="<?php echo BASE_DIR; ?>/product/delete/<?php echo $product->id; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-center mb-0">No products found. <a href="<?php echo BASE_DIR; ?>/product/create">Add your first product</a>.</p>
        <?php endif; ?>
    </div>
</div>

<?php require BASE_PATH . '/app/views/layouts/footer.php'; ?>
