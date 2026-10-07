<?php

$success = $_SESSION['user_success'] ?? '';
$error = $_SESSION['user_error'] ?? '';

unset($_SESSION['user_success']);
unset($_SESSION['user_error']);

?>

<div class="page-header">
    <div>
        <h1>User Management</h1>
        <p>Manage system users and their assigned roles.</p>
    </div>

    <a href="users.php?action=create" class="btn btn-primary">
        Add User
    </a>
</div>


<?php if ($success): ?>

    <div class="alert alert-success">
        <?= htmlspecialchars($success) ?>
    </div>

<?php endif; ?>


<?php if ($error): ?>

    <div class="alert alert-danger">
        <?= htmlspecialchars($error) ?>
    </div>

<?php endif; ?>


<div class="card">

    <div class="card-header">
        <h2>System Users</h2>
    </div>

    <div class="table-responsive">

        <table class="table">

            <thead>
                <tr>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                <?php if (empty($users)): ?>

                    <tr>
                        <td colspan="6" style="text-align: center;">
                            No users found.
                        </td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($users as $user): ?>

                        <tr>

                            <td>
                                <?= htmlspecialchars($user['full_name']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($user['username']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($user['email'] ?? '') ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($user['role_name']) ?>
                            </td>

                            <td>

                                <?php if ($user['status'] === 'Active'): ?>

                                    <span style="
                                        color: #2E7D32;
                                        background: #E8F5E9;
                                        padding: 4px 8px;
                                        border-radius: 4px;
                                        font-size: 12px;
                                        font-weight: 600;
                                    ">
                                        Active
                                    </span>

                                <?php else: ?>

                                    <span style="
                                        color: #C62828;
                                        background: #FFEBEE;
                                        padding: 4px 8px;
                                        border-radius: 4px;
                                        font-size: 12px;
                                        font-weight: 600;
                                    ">
                                        Inactive
                                    </span>

                                <?php endif; ?>

                            </td>

                            <td>

                                <a
                                    href="users.php?action=edit&id=<?= $user['user_id'] ?>"
                                    class="btn btn-secondary"
                                >
                                    Edit
                                </a>

                                <?php
                                $currentUserId =
                                    $_SESSION['user']['user_id'] ?? 0;
                                ?>

                                <?php if ($user['user_id'] != $currentUserId): ?>

                                    <?php if ($user['status'] === 'Active'): ?>

                                        <a
                                            href="users.php?action=toggle_status&id=<?= $user['user_id'] ?>"
                                            class="btn btn-danger"
                                            onclick="return confirm('Deactivate this user?');"
                                        >
                                            Deactivate
                                        </a>

                                    <?php else: ?>

                                        <a
                                            href="users.php?action=toggle_status&id=<?= $user['user_id'] ?>"
                                            class="btn btn-success"
                                            onclick="return confirm('Activate this user?');"
                                        >
                                            Activate
                                        </a>

                                    <?php endif; ?>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>