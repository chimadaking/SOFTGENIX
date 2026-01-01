<?php require BASE_PATH . '/app/views/layouts/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Products</h1>
    <div class="input-group w-50">
        <input type="text" class="form-control" placeholder="Search products...">
        <button class="btn btn-outline-secondary" type="button"><i class="bi bi-search"></i></button>
    </div>
</div>

<div class="row">
    <?php if (!empty($data['products'])): ?>
        <?php foreach ($data['products'] as $product): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <?php if ($product->image): ?>
                        <img src="<?php echo BASE_DIR; ?>/uploads/products/<?php echo $product->image; ?>" class="card-img-top" alt="<?php echo h($product->name); ?>">
                    <?php else: ?>
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                        </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo h($product->name); ?></h5>
                        <p class="card-text text-muted small"><?php echo h($product->category_name); ?></p>
                        <?php if (!empty($product->short_description)): ?>
                        <p class="card-text text-muted"><?php echo h(substr($product->short_description, 0, 80)); ?>...</p>
                        <?php endif; ?>
                        <p class="card-text text-primary fw-bold fs-5">$<?php echo number_format($product->price, 2); ?></p>
                        <div class="d-grid gap-2">
                            <a href="<?php echo BASE_DIR; ?>/product/details/<?php echo $product->id; ?>" class="btn btn-primary">View Details</a>
                            <a href="<?php echo BASE_DIR; ?>/order/create/<?php echo $product->id; ?>" class="btn btn-success">
                                <i class="bi bi-cart-plus"></i> Order Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12 text-center">
            <p>No products found.</p>
        </div>
    <?php endif; ?>
</div>
<?php require BASE_PATH . '/app/views/layouts/footer.php'; ?>
