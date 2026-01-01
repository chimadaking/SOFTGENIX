<?php require BASE_PATH . '/app/views/layouts/header.php'; ?>
<h1 class="mb-4">API Management</h1>

<a href="<?php echo BASE_DIR; ?>/api/create" class="btn btn-success mb-3">
    <i class="bi bi-plus-circle"></i> Add Provider
</a>

<div class="card shadow-sm">
    <div class="card-body">
        <?php if (!empty($data['providers'])): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Base URL</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['providers'] as $provider): ?>
                            <tr>
                                <td><?php echo $provider->id; ?></td>
                                <td><?php echo h($provider->name); ?></td>
                                <td><?php echo h($provider->base_url); ?></td>
                                <td>$<?php echo number_format($provider->balance, 2); ?></td>
                                <td><span class="badge bg-<?php echo ($provider->status == 'active') ? 'success' : 'secondary'; ?>"><?php echo ucfirst($provider->status); ?></span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-info test-api" data-id="<?php echo $provider->id; ?>">Test</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-center mb-0">No API providers found. <a href="<?php echo BASE_DIR; ?>/api/create">Add your first provider</a>.</p>
        <?php endif; ?>
    </div>
</div>

<script>
document.querySelectorAll('.test-api').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        fetch('<?php echo BASE_DIR; ?>/api/test/' + id)
            .then(response => response.json())
            .then(data => {
                alert(data.success ? 'API connection successful!' : 'API connection failed!');
            })
            .catch(error => alert('Error testing API connection'));
    });
});
</script>

<?php require BASE_PATH . '/app/views/layouts/footer.php'; ?>
