<?php

session_start();

require_once 'config/database.php';

// Only Owner can access User Management
if (
    !isset($_SESSION['user']) ||
    $_SESSION['user']['role_name'] !== 'Owner'
) {
    header("Location: index.php");
    exit;
}

$message = "";
$error = "";


// =====================================================
// CREATE USER
// =====================================================

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['action_create_user'])
) {

    $full_name = trim($_POST['full_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $role_id = intval($_POST['role_id'] ?? 0);

    if (
        $full_name === '' ||
        $username === '' ||
        $password === '' ||
        $role_id <= 0
    ) {

        $error = "Full name, username, password, and role are required.";

    } else {

        try {

            // Check duplicate username
            $checkDup = $pdo->prepare("
                SELECT user_id
                FROM users
                WHERE username = ?
            ");

            $checkDup->execute([$username]);

            if ($checkDup->fetch()) {

                $error = "The username '" .
                    htmlspecialchars($username) .
                    "' is already taken.";

            } else {

                // Hash password
                $hashed_password = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                $insStmt = $pdo->prepare("
                    INSERT INTO users
                    (
                        full_name,
                        username,
                        password,
                        email,
                        role_id,
                        status
                    )
                    VALUES (?, ?, ?, ?, ?, 'Active')
                ");

                $insStmt->execute([
                    $full_name,
                    $username,
                    $hashed_password,
                    $email,
                    $role_id
                ]);

                $message = "User account created successfully.";
            }

        } catch (Exception $e) {

            $error = "Database error: " . $e->getMessage();
        }
    }
}


// =====================================================
// EDIT USER
// =====================================================

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['action_edit_user'])
) {

    $user_id = intval($_POST['user_id'] ?? 0);
    $full_name = trim($_POST['full_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role_id = intval($_POST['role_id'] ?? 0);

    if (
        $user_id <= 0 ||
        $full_name === '' ||
        $username === '' ||
        $role_id <= 0
    ) {

        $error = "Full name, username, and role are required.";

    } else {

        try {

            // Check if username is already used by another user
            $checkDup = $pdo->prepare("
                SELECT user_id
                FROM users
                WHERE username = ?
                AND user_id != ?
            ");

            $checkDup->execute([
                $username,
                $user_id
            ]);

            if ($checkDup->fetch()) {

                $error = "The username '" .
                    htmlspecialchars($username) .
                    "' is already taken.";

            } else {

                $updateStmt = $pdo->prepare("
                    UPDATE users
                    SET
                        full_name = ?,
                        username = ?,
                        email = ?,
                        role_id = ?
                    WHERE user_id = ?
                ");

                $updateStmt->execute([
                    $full_name,
                    $username,
                    $email,
                    $role_id,
                    $user_id
                ]);

                $message = "User account updated successfully.";
            }

        } catch (Exception $e) {

            $error = "Database error: " . $e->getMessage();
        }
    }
}


// =====================================================
// ACTIVATE / DEACTIVATE USER
// =====================================================

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['action_toggle_status'])
) {

    $user_id = intval($_POST['user_id'] ?? 0);

    $currentUserId =
        $_SESSION['user']['user_id'] ?? 0;

    // Prevent current Owner from deactivating own account
    if ($user_id == $currentUserId) {

        $error = "You cannot deactivate your own account.";

    } else {

        try {

            $getUser = $pdo->prepare("
                SELECT status
                FROM users
                WHERE user_id = ?
            ");

            $getUser->execute([$user_id]);

            $targetUser = $getUser->fetch(PDO::FETCH_ASSOC);

            if (!$targetUser) {

                $error = "User account not found.";

            } else {

                if ($targetUser['status'] === 'Active') {
                    $newStatus = 'Inactive';
                } else {
                    $newStatus = 'Active';
                }

                $updateStatus = $pdo->prepare("
                    UPDATE users
                    SET status = ?
                    WHERE user_id = ?
                ");

                $updateStatus->execute([
                    $newStatus,
                    $user_id
                ]);

                $message = "User status updated successfully.";
            }

        } catch (Exception $e) {

            $error = "Database error: " . $e->getMessage();
        }
    }
}


// =====================================================
// GET USERS
// =====================================================

try {

    $usersList = $pdo->query("
        SELECT
            u.user_id,
            u.full_name,
            u.username,
            u.email,
            u.status,
            u.role_id,
            r.role_name
        FROM users u
        JOIN roles r
            ON u.role_id = r.role_id
        ORDER BY
            r.role_name ASC,
            u.full_name ASC
    ")->fetchAll(PDO::FETCH_ASSOC);


    // Get available roles
    $rolesDropdown = $pdo->query("
        SELECT *
        FROM roles
        ORDER BY role_name ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {

    die(
        "Database error: " .
        htmlspecialchars($e->getMessage())
    );
}


include 'app/views/layouts/header.php';

?>

<div class="container" style="display: flex;">

    <?php include 'app/views/layouts/sidebar.php'; ?>

    <div
        class="content"
        style="
            flex: 1;
            padding: 30px;
            background: #FAFAFA;
        "
    >

        <!-- PAGE HEADER -->

        <div
            style="
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 25px;
            "
        >

            <div>

                <h1
                    style="
                        color: #1B5E20;
                        margin: 0;
                        font-weight: 700;
                    "
                >
                    User Management
                </h1>

                <p
                    style="
                        color: #6B7280;
                        margin: 4px 0 0 0;
                        font-size: 14px;
                    "
                >
                    Manage system users and their assigned roles.
                </p>

            </div>

        </div>


        <!-- SUCCESS MESSAGE -->

        <?php if ($message): ?>

            <div
                style="
                    padding: 12px;
                    background: #E8F5E9;
                    color: #2E7D32;
                    border-radius: 8px;
                    font-weight: 600;
                    margin-bottom: 20px;
                    border-left: 4px solid #2E7D32;
                "
            >
                <?= htmlspecialchars($message) ?>
            </div>

        <?php endif; ?>


        <!-- ERROR MESSAGE -->

        <?php if ($error): ?>

            <div
                style="
                    padding: 12px;
                    background: #FFEBEE;
                    color: #C62828;
                    border-radius: 8px;
                    font-weight: 600;
                    margin-bottom: 20px;
                    border-left: 4px solid #D32F2F;
                "
            >
                <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>


        <!-- MAIN CONTENT -->

        <div
            style="
                display: grid;
                grid-template-columns: 1fr 2fr;
                gap: 30px;
                align-items: flex-start;
            "
        >


            <!-- =========================================
                 ADD USER
            ========================================== -->

            <div
                style="
                    background: white;
                    border: 1px solid #E5E9F0;
                    border-radius: 12px;
                    padding: 25px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.01);
                "
            >

                <h3
                    style="
                        color: #11142D;
                        margin: 0 0 15px 0;
                        font-size: 16px;
                        font-weight: 700;
                        border-bottom: 1px solid #E5E9F0;
                        padding-bottom: 10px;
                    "
                >
                    Add New User
                </h3>


                <form
                    method="POST"
                    style="
                        display: flex;
                        flex-direction: column;
                        gap: 14px;
                    "
                >

                    <div>

                        <label
                            style="
                                display: block;
                                font-size: 12px;
                                font-weight: 600;
                                color: #4A5568;
                                margin-bottom: 5px;
                            "
                        >
                            Full Name
                        </label>

                        <input
                            type="text"
                            name="full_name"
                            required
                            placeholder="e.g. Maria Santos"
                            style="
                                width: 100%;
                                padding: 10px 12px;
                                border: 1px solid #CBD5E1;
                                border-radius: 6px;
                                font-size: 14px;
                            "
                        >

                    </div>


                    <div>

                        <label
                            style="
                                display: block;
                                font-size: 12px;
                                font-weight: 600;
                                color: #4A5568;
                                margin-bottom: 5px;
                            "
                        >
                            Username
                        </label>

                        <input
                            type="text"
                            name="username"
                            required
                            placeholder="e.g. msantos"
                            style="
                                width: 100%;
                                padding: 10px 12px;
                                border: 1px solid #CBD5E1;
                                border-radius: 6px;
                                font-size: 14px;
                            "
                        >

                    </div>


                    <div>

                        <label
                            style="
                                display: block;
                                font-size: 12px;
                                font-weight: 600;
                                color: #4A5568;
                                margin-bottom: 5px;
                            "
                        >
                            Password
                        </label>

                        <input
                            type="password"
                            name="password"
                            required
                            placeholder="Enter password"
                            style="
                                width: 100%;
                                padding: 10px 12px;
                                border: 1px solid #CBD5E1;
                                border-radius: 6px;
                                font-size: 14px;
                            "
                        >

                    </div>


                    <div>

                        <label
                            style="
                                display: block;
                                font-size: 12px;
                                font-weight: 600;
                                color: #4A5568;
                                margin-bottom: 5px;
                            "
                        >
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            placeholder="e.g. user@email.com"
                            style="
                                width: 100%;
                                padding: 10px 12px;
                                border: 1px solid #CBD5E1;
                                border-radius: 6px;
                                font-size: 14px;
                            "
                        >

                    </div>


                    <div>

                        <label
                            style="
                                display: block;
                                font-size: 12px;
                                font-weight: 600;
                                color: #4A5568;
                                margin-bottom: 5px;
                            "
                        >
                            Role
                        </label>

                        <select
                            name="role_id"
                            required
                            style="
                                width: 100%;
                                padding: 10px 12px;
                                border: 1px solid #CBD5E1;
                                border-radius: 6px;
                                font-size: 14px;
                                background: white;
                            "
                        >

                            <option value="">
                                -- Select Role --
                            </option>

                            <?php foreach ($rolesDropdown as $rl): ?>

                                <option
                                    value="<?= $rl['role_id'] ?>"
                                >
                                    <?= htmlspecialchars($rl['role_name']) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <button
                        type="submit"
                        name="action_create_user"
                        style="
                            padding: 12px;
                            background: #1B5E20;
                            color: white;
                            border: none;
                            border-radius: 6px;
                            font-weight: 600;
                            font-size: 14px;
                            cursor: pointer;
                        "
                    >
                        Add User
                    </button>

                </form>

            </div>


            <!-- =========================================
                 USER LIST
            ========================================== -->

            <div
                style="
                    background: white;
                    border: 1px solid #E5E9F0;
                    border-radius: 12px;
                    padding: 25px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.01);
                "
            >

                <h3
                    style="
                        color: #11142D;
                        margin: 0 0 15px 0;
                        font-size: 16px;
                        font-weight: 700;
                        border-bottom: 1px solid #E5E9F0;
                        padding-bottom: 10px;
                    "
                >
                    System Users
                </h3>


                <div style="overflow-x: auto;">

                    <table
                        style="
                            width: 100%;
                            border-collapse: collapse;
                            text-align: left;
                            font-size: 14px;
                        "
                    >

                        <thead>

                            <tr
                                style="
                                    background: #FAF8F5;
                                    border-bottom: 1px solid #E5E9F0;
                                "
                            >

                                <th style="padding: 12px;">
                                    Full Name
                                </th>

                                <th style="padding: 12px;">
                                    Username
                                </th>

                                <th style="padding: 12px;">
                                    Role
                                </th>

                                <th style="padding: 12px;">
                                    Status
                                </th>

                                <th style="padding: 12px;">
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <?php if (empty($usersList)): ?>

                                <tr>

                                    <td
                                        colspan="5"
                                        style="
                                            padding: 20px;
                                            text-align: center;
                                        "
                                    >
                                        No users found.
                                    </td>

                                </tr>

                            <?php else: ?>


                                <?php foreach ($usersList as $usr): ?>

                                    <tr
                                        style="
                                            border-bottom: 1px solid #F1F5F9;
                                        "
                                    >

                                        <td style="padding: 12px;">

                                            <strong>
                                                <?= htmlspecialchars(
                                                    $usr['full_name']
                                                ) ?>
                                            </strong>

                                            <?php if (!empty($usr['email'])): ?>

                                                <div
                                                    style="
                                                        font-size: 12px;
                                                        color: #6B7280;
                                                        margin-top: 3px;
                                                    "
                                                >
                                                    <?= htmlspecialchars(
                                                        $usr['email']
                                                    ) ?>
                                                </div>

                                            <?php endif; ?>

                                        </td>


                                        <td
                                            style="
                                                padding: 12px;
                                                font-family: monospace;
                                            "
                                        >

                                            <?= htmlspecialchars(
                                                $usr['username']
                                            ) ?>

                                        </td>


                                        <td style="padding: 12px;">

                                            <?= htmlspecialchars(
                                                $usr['role_name']
                                            ) ?>

                                        </td>


                                        <td style="padding: 12px;">

                                            <?php if (
                                                $usr['status'] === 'Active'
                                            ): ?>

                                                <span
                                                    style="
                                                        background: #E8F5E9;
                                                        color: #1B5E20;
                                                        padding: 4px 8px;
                                                        border-radius: 12px;
                                                        font-size: 11px;
                                                        font-weight: 700;
                                                    "
                                                >
                                                    Active
                                                </span>

                                            <?php else: ?>

                                                <span
                                                    style="
                                                        background: #FFEBEE;
                                                        color: #C62828;
                                                        padding: 4px 8px;
                                                        border-radius: 12px;
                                                        font-size: 11px;
                                                        font-weight: 700;
                                                    "
                                                >
                                                    Inactive
                                                </span>

                                            <?php endif; ?>

                                        </td>


                                        <td style="padding: 12px;">

                                            <!-- EDIT -->

                                            <button
                                                type="button"
                                                onclick="openEditUser(
                                                    <?= htmlspecialchars(
                                                        json_encode($usr),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                )"
                                                style="
                                                    padding: 7px 10px;
                                                    background: #E8F5E9;
                                                    color: #1B5E20;
                                                    border: none;
                                                    border-radius: 5px;
                                                    cursor: pointer;
                                                    font-weight: 600;
                                                "
                                            >
                                                Edit
                                            </button>


                                            <?php
                                            $currentUserId =
                                                $_SESSION['user']['user_id']
                                                ?? 0;
                                            ?>


                                            <?php if (
                                                $usr['user_id']
                                                != $currentUserId
                                            ): ?>

                                                <form
                                                    method="POST"
                                                    style="
                                                        display: inline;
                                                    "
                                                >

                                                    <input
                                                        type="hidden"
                                                        name="user_id"
                                                        value="<?= $usr['user_id'] ?>"
                                                    >

                                                    <button
                                                        type="submit"
                                                        name="action_toggle_status"
                                                        onclick="
                                                            return confirm(
                                                                'Are you sure you want to change this user status?'
                                                            );
                                                        "
                                                        style="
                                                            padding: 7px 10px;
                                                            background:
                                                                <?= $usr['status'] === 'Active'
                                                                    ? '#FFEBEE'
                                                                    : '#E8F5E9' ?>;
                                                            color:
                                                                <?= $usr['status'] === 'Active'
                                                                    ? '#C62828'
                                                                    : '#1B5E20' ?>;
                                                            border: none;
                                                            border-radius: 5px;
                                                            cursor: pointer;
                                                            font-weight: 600;
                                                        "
                                                    >

                                                        <?= $usr['status'] === 'Active'
                                                            ? 'Deactivate'
                                                            : 'Activate' ?>

                                                    </button>

                                                </form>

                                            <?php endif; ?>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>


                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>


<!-- =====================================================
     EDIT USER MODAL
====================================================== -->

<div
    id="editUserModal"
    style="
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.45);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 20px;
    "
>

    <div
        style="
            background: white;
            width: 100%;
            max-width: 500px;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        "
    >

        <div
            style="
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 20px;
            "
        >

            <h2
                style="
                    margin: 0;
                    color: #1B5E20;
                "
            >
                Edit User
            </h2>

            <button
                type="button"
                onclick="closeEditUser()"
                style="
                    border: none;
                    background: none;
                    font-size: 24px;
                    cursor: pointer;
                "
            >
                &times;
            </button>

        </div>


        <form method="POST">

            <input
                type="hidden"
                name="user_id"
                id="edit_user_id"
            >


            <div style="margin-bottom: 15px;">

                <label
                    style="
                        display: block;
                        font-weight: 600;
                        margin-bottom: 5px;
                    "
                >
                    Full Name
                </label>

                <input
                    type="text"
                    name="full_name"
                    id="edit_full_name"
                    required
                    style="
                        width: 100%;
                        padding: 10px;
                        border: 1px solid #CBD5E1;
                        border-radius: 6px;
                    "
                >

            </div>


            <div style="margin-bottom: 15px;">

                <label
                    style="
                        display: block;
                        font-weight: 600;
                        margin-bottom: 5px;
                    "
                >
                    Username
                </label>

                <input
                    type="text"
                    name="username"
                    id="edit_username"
                    required
                    style="
                        width: 100%;
                        padding: 10px;
                        border: 1px solid #CBD5E1;
                        border-radius: 6px;
                    "
                >

            </div>


            <div style="margin-bottom: 15px;">

                <label
                    style="
                        display: block;
                        font-weight: 600;
                        margin-bottom: 5px;
                    "
                >
                    Email
                </label>

                <input
                    type="email"
                    name="email"
                    id="edit_email"
                    style="
                        width: 100%;
                        padding: 10px;
                        border: 1px solid #CBD5E1;
                        border-radius: 6px;
                    "
                >

            </div>


            <div style="margin-bottom: 20px;">

                <label
                    style="
                        display: block;
                        font-weight: 600;
                        margin-bottom: 5px;
                    "
                >
                    Role
                </label>

                <select
                    name="role_id"
                    id="edit_role_id"
                    required
                    style="
                        width: 100%;
                        padding: 10px;
                        border: 1px solid #CBD5E1;
                        border-radius: 6px;
                        background: white;
                    "
                >

                    <?php foreach ($rolesDropdown as $rl): ?>

                        <option
                            value="<?= $rl['role_id'] ?>"
                        >
                            <?= htmlspecialchars(
                                $rl['role_name']
                            ) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <div
                style="
                    display: flex;
                    justify-content: flex-end;
                    gap: 10px;
                "
            >

                <button
                    type="button"
                    onclick="closeEditUser()"
                    style="
                        padding: 10px 15px;
                        border: 1px solid #CBD5E1;
                        background: white;
                        border-radius: 6px;
                        cursor: pointer;
                    "
                >
                    Cancel
                </button>


                <button
                    type="submit"
                    name="action_edit_user"
                    style="
                        padding: 10px 15px;
                        background: #1B5E20;
                        color: white;
                        border: none;
                        border-radius: 6px;
                        cursor: pointer;
                        font-weight: 600;
                    "
                >
                    Save Changes
                </button>

            </div>

        </form>

    </div>

</div>


<script>

function openEditUser(user) {

    document.getElementById('edit_user_id').value =
        user.user_id;

    document.getElementById('edit_full_name').value =
        user.full_name;

    document.getElementById('edit_username').value =
        user.username;

    document.getElementById('edit_email').value =
        user.email || '';

    document.getElementById('edit_role_id').value =
        user.role_id;

    document.getElementById('editUserModal').style.display =
        'flex';
}


function closeEditUser() {

    document.getElementById('editUserModal').style.display =
        'none';
}

</script>