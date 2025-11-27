<?php
require '../includes/config.php';
require '../includes/functions.php';
require '../includes/auth.php'; // <-- loads redirectIfNotAdmin()
redirectIfNotAdmin();

$success = null;

// Handle add or edit bread type
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $name = trim($_POST['name']);
    $photo_path = null;

    if (!empty($_FILES['photo']['name'])) {
        $target_dir = "../uploads/bread_types/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $photo_name = uniqid() . "_" . basename($_FILES['photo']['name']);
        $target_file = $target_dir . $photo_name;
        move_uploaded_file($_FILES['photo']['tmp_name'], $target_file);
        $photo_path = "uploads/bread_types/" . $photo_name;
    }

    if ($action === 'add') {
        $stmt = $pdo->prepare("INSERT INTO bread_types (name, image_path, is_active) VALUES (?, ?, 1)");
        $stmt->execute([$name, $photo_path]);
        $success = "✅ New bread type added!";
    } elseif ($action === 'edit') {
        $type_id = intval($_POST['type_id']);
        if ($photo_path) {
            $stmt = $pdo->prepare("UPDATE bread_types SET name = ?, image_path = ? WHERE id = ?");
            $stmt->execute([$name, $photo_path, $type_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE bread_types SET name = ? WHERE id = ?");
            $stmt->execute([$name, $type_id]);
        }
        $success = "✅ Bread type updated!";
    }
}

// Handle toggle activate/deactivate
if (isset($_POST['toggle_id'])) {
    $type_id = intval($_POST['toggle_id']);
    $stmt = $pdo->prepare("SELECT is_active FROM bread_types WHERE id = ?");
    $stmt->execute([$type_id]);
    $type = $stmt->fetch();
    if ($type) {
        $new_status = $type['is_active'] ? 0 : 1;
        $stmt = $pdo->prepare("UPDATE bread_types SET is_active = ? WHERE id = ?");
        $stmt->execute([$new_status, $type_id]);
        echo json_encode(['success' => true, 'status' => $new_status]);
        exit;
    }
    echo json_encode(['success' => false]);
    exit;
}

// Get all bread types
$bread_types = $pdo->query("SELECT * FROM bread_types ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Manage Bread Types - Brede Yuru Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(90deg, #73f879ff, rgba(78, 75, 250, 1));
            padding: 20px;
            color: #343a40;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h2 {
            color: #ffc107;
            font-size: 2.4em;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .success {
            text-align: center;
            background: #d4edda;
            color: #155724;
            padding: 10px 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .bread-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
        }
        .bread-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            padding: 15px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .bread-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.2);
        }
        .bread-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 10px;
        }
        .bread-card h3 {
            color: #ffc107;
            margin-bottom: 5px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 12px;
            margin-bottom: 10px;
            font-size: 0.9em;
            color: #fff;
        }
        .active { background: #28a745; }
        .inactive { background: #dc3545; }
        form input, form button, .toggle-btn {
            width: 100%;
            margin: 5px 0;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1em;
        }
        .toggle-btn {
            background: #ffc107;
            color: #fff;
            font-weight: bold;
        }
        .toggle-btn:hover {
            background: #ff9800;
        }
        .save-btn {
            background: #ff6f61;
            color: #fff;
            font-weight: bold;
        }
        .save-btn:hover {
            background: #e85d4f;
        }

        .floating-btn {
            position: fixed;
            bottom: auto;
            top: 20px;
            right: auto;
            left: 20px;
            width: 50px;
            height: 50px;
            background: #ff6f61;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3em; /* icon size */
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.25);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .floating-btn:hover {
            background: #e85d4f;
            transform: translateY(-3px) scale(1.05);
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Manage Bread Types 🍞</h2>
    </div>

    <div class="container">
        <?php if ($success): ?>
            <div class="success"><?= $success ?></div>
        <?php endif; ?>

        <h3>Add New Bread Type</h3>
        <div class="bread-card">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                <input type="text" name="name" placeholder="Bread Type Name" required>

                <input type="file" name="photo" accept="image/*" onchange="previewImage(event, 'editPreview<?= $bread['id'] ?>')">
                <img id="addPreview" src="../assets/img/placeholder.png" alt="Preview">
                <button type="submit" class="save-btn">Add Bread Type</button>
            </form>
        </div>

        <h3>Edit Existing Bread Types</h3>
        <div class="bread-grid">
            <?php foreach ($bread_types as $type): ?>
            <div class="bread-card">
                <h3><?= htmlspecialchars($type['name']) ?></h3>
                <span class="status-badge <?= $type['is_active'] ? 'active' : 'inactive' ?>">
                    <?= $type['is_active'] ? 'Active' : 'Inactive' ?>
                </span>
                <img src="../<?= $type['image_path'] ?: 'assets/img/placeholder.png' ?>" alt="Bread Type">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="type_id" value="<?= $type['id'] ?>">
                    <input type="text" name="name" value="<?= htmlspecialchars($type['name']) ?>" required>
                    <input type="file" name="photo" accept="image/*" onchange="previewImage(event, 'editPreview<?= $type['id'] ?>')">
                    <button type="submit" class="save-btn">Save Changes</button>
                    <button type="button" class="toggle-btn" onclick="toggleStatus(<?= $type['id'] ?>)">
                        <?= $type['is_active'] ? 'Deactivate' : 'Activate' ?>
                    </button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
        <p>
            <a href="dashboard.php" class="floating-btn" title="Back to Dashboard">
                <i class="fas fa-arrow-left"></i>
            </a>
        </p>
    </div>

    <script>
    function toggleStatus(typeId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You are about to toggle this bread type's status.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#007b5e',
            cancelButtonColor: '#dc3545',
            confirmButtonText: 'Yes, toggle it!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'toggle_id=' + typeId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) location.reload();
                    else Swal.fire('Error', 'Could not update status.', 'error');
                });
            }
        });
    }
    function previewImage(event, previewId) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById(previewId);
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
    </script>
</body>
</html>
