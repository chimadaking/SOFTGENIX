<?php require BASE_PATH . '/app/views/layouts/header.php'; ?>
<h1 class="mb-4">API Logs</h1>

<div class="card shadow-sm">
    <div class="card-body">
        <?php if (!empty($data['logs'])): ?>
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Endpoint</th>
                            <th>Method</th>
                            <th>User</th>
                            <th>Response</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['logs'] as $log): ?>
                            <tr>
                                <td><?php echo date('M d, Y H:i:s', strtotime($log->created_at)); ?></td>
                                <td><?php echo h($log->endpoint); ?></td>
                                <td><span class="badge bg-secondary"><?php echo h($log->request_method); ?></span></td>
                                <td><?php echo $log->user_id ?: 'Guest'; ?></td>
                                <td><span class="badge bg-<?php echo ($log->response_code >= 200 && $log->response_code < 300) ? 'success' : 'danger'; ?>"><?php echo $log->response_code; ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-center mb-0">No API logs found.</p>
        <?php endif; ?>
    </div>
</div>

<?php require BASE_PATH . '/app/views/layouts/footer.php'; ?>
