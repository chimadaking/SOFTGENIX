<?php require BASE_PATH . '/app/views/layouts/header.php'; ?>
<h1 class="mb-4">Add API Provider</h1>

<a href="<?php echo BASE_DIR; ?>/api" class="btn btn-secondary mb-3">&larr; Back to API Management</a>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="<?php echo BASE_DIR; ?>/api/create" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            
            <div class="mb-3">
                <label for="name" class="form-label">Provider Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="base_url" class="form-label">Base URL</label>
                <input type="url" name="base_url" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="api_key" class="form-label">API Key</label>
                <input type="text" name="api_key" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="api_secret" class="form-label">API Secret</label>
                <input type="password" name="api_secret" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success">Add Provider</button>
        </form>
    </div>
</div>

<?php require BASE_PATH . '/app/views/layouts/footer.php'; ?>
