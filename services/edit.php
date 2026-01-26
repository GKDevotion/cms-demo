 <?php
    session_start();
    require_once '../config/Database.php';
    require_once '../classes/Service.php';

    // Check if user is logged in (add your auth logic)
    // if (!isset($_SESSION['user_id'])) {
    //     header('Location: login.php');
    //     exit;
    // }

    $conn = Database::getInstance();
    $service = new Service($conn);

    // Get service ID from URL
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id <= 0) {
        $_SESSION['error_message'] = "Invalid service ID!";
        header('Location: index.php');
        exit;
    }

    // Fetch service data
    $serviceData = $service->getById($id);
    if (!$serviceData) {
        $_SESSION['error_message'] = "Service not found!";
        header('Location: index.php');
        exit;
    }

    // Get parent services for dropdown (exclude current service and its children)
    $parentServices = [];
    $sql = "SELECT id, name FROM  company_services WHERE id != ? AND (parent_id IS NULL OR parent_id = 0) ORDER BY name ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $parentServices = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Process form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $errors = [];

        // Sanitize inputs
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
        $status = isset($_POST['status']) ? 1 : 0;
        $sort_order = (int)($_POST['sort_order'] ?? 0);

        // Validation
        if (empty($name)) {
            $errors[] = "Name is required";
        }

        if (empty($slug)) {
            // Auto-generate slug from name if empty
            $slug = strtolower(str_replace(' ', '-', $name));
        } else {
            $slug = strtolower(str_replace(' ', '-', $slug));
        }

        // Check if slug already exists (excluding current record)
        $stmt = $conn->prepare("SELECT id FROM  company_services WHERE slug = ? AND id != ?");
        $stmt->bind_param("si", $slug, $id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Slug already exists for another service";
        }
        $stmt->close();

        // Prevent circular reference - service can't be its own parent
        if ($parent_id == $id) {
            $errors[] = "A service cannot be its own parent";
        }

        // Check for parent-child circular reference
        if ($parent_id) {
            // Get all children of current service
            $children = [];
            $childStmt = $conn->prepare("SELECT id FROM  company_services WHERE parent_id = ?");
            $childStmt->bind_param("i", $id);
            $childStmt->execute();
            $childResult = $childStmt->get_result();
            while ($child = $childResult->fetch_assoc()) {
                $children[] = $child['id'];
            }
            $childStmt->close();

            // Check if selected parent is a child of current service
            if (in_array($parent_id, $children)) {
                $errors[] = "Cannot select a child service as parent";
            }
        }

        // If no errors, update the record
        if (empty($errors)) {
            $stmt = $conn->prepare("
            UPDATE  company_services 
            SET name = ?, slug = ?, parent_id = ?,  status = ?, sort_order = ?, updated_at = NOW()
            WHERE id = ?
        ");

            $stmt->bind_param("ssiiii", $name, $slug, $parent_id,   $status, $sort_order, $id);

            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Service updated successfully!";
                header('Location: index.php');
                exit;
            } else {
                $errors[] = "Failed to update service: " . $conn->error;
            }
            $stmt->close();
        }
    } else {
        // Pre-fill form with existing data if not submitted
        $name = $serviceData['name'];
        $slug = $serviceData['slug'];
        $parent_id = $serviceData['parent_id'];
        $status = $serviceData['status'];
        $sort_order = $serviceData['sort_order'];
    }
    ?>

 <!DOCTYPE html>
 <html lang="en">

 <head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Edit Service</title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
     <style>
         .required::after {
             content: " *";
             color: red;
         }

         .service-info {
             background-color: #f8f9fa;
             border-radius: 5px;
             padding: 15px;
             margin-bottom: 20px;
         }

         .info-label {
             font-weight: bold;
             color: #6c757d;
         }

         :root {
             --brand: #b88a2e;
             --brand-dark: #9e7426;
             --brand-light: #f3ead7;
         }

         body {
             background-color: #f6f7fb;
         }

         .required::after {
             content: " *";
             color: red;
         }

         /* Card */
         .card {
             border: none;
             border-radius: 14px;
             box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
         }

         .card-header {
             background: linear-gradient(135deg, var(--brand), var(--brand-dark));
             color: #fff;
             border-radius: 14px 14px 0 0;
         }

         .card-header h4 {
             font-weight: 600;
         }

         /* Service info box */
         .service-info {
             background: var(--brand-light);
             border-left: 5px solid var(--brand);
             border-radius: 10px;
             padding: 15px;
         }

         .info-label {
             font-weight: 600;
             color: #6c757d;
         }

         /* Buttons */
         .btn-primary {
             background-color: var(--brand);
             border-color: var(--brand);
             border-radius: 30px;
             padding: 8px 22px;
         }

         .btn-primary:hover {
             background-color: var(--brand-dark);
             border-color: var(--brand-dark);
         }

         .btn-warning {
             background-color: var(--brand);
             border-color: var(--brand);
             color: #fff;
         }

         .btn-warning:hover {
             background-color: var(--brand-dark);
         }

         .btn-outline-secondary:hover {
             background-color: var(--brand);
             color: #fff;
             border-color: var(--brand);
         }

         /* Form */
         .form-control,
         .form-select {
             border-radius: 10px;
             padding: 10px 14px;
         }

         .form-control:focus,
         .form-select:focus {
             border-color: var(--brand);
             box-shadow: 0 0 0 0.2rem rgba(184, 138, 46, 0.25);
         }

         /* Switch */
         .form-check-input:checked {
             background-color: var(--brand);
             border-color: var(--brand);
         }

         /* Alerts */
         .alert-success {
             border-left: 4px solid var(--brand);
         }

         /* Dropdown */
         .dropdown-menu {
             border-radius: 12px;
         }

         /* Icons */
         .bi {
             vertical-align: -2px;
         }
     </style>

 </head>

 <body>
     <div class="container mt-5">
         <div class="row justify-content-center">
             <div class="col-md-8">
                 <div class="card">
                     <div class="card-header d-flex justify-content-between align-items-center">
                         <h4 class="mb-0">
                             <i class="bi bi-pencil-square"></i> Edit Service
                         </h4>
                         <div>
                             <a href="index.php" class="btn btn-sm " style="color: white; border: 1px solid white">
                                 <i class="bi bi-arrow-left"></i> Back to List
                             </a>
                         </div>
                     </div>

                     <div class="card-body">
                         <!-- Service Information -->
                         <div class="service-info mb-4">
                             <div class="row">
                                 <div class="col-md-6">
                                     <div class="mb-2">
                                         <span class="info-label">Created:</span>
                                         <span><?php echo date('Y-m-d H:i', strtotime($serviceData['created_at'] ?? 'N/A')); ?></span>
                                     </div>
                                 </div>
                                 <div class="col-md-6">
                                     <div class="mb-2">
                                         <span class="info-label">Last Updated:</span>
                                         <span><?php echo date('Y-m-d H:i', strtotime($serviceData['updated_at'] ?? 'N/A')); ?></span>
                                     </div>
                                 </div>
                             </div>
                         </div>

                         <?php if (!empty($errors)): ?>
                             <div class="alert alert-danger">
                                 <h5><i class="bi bi-exclamation-triangle"></i> Please fix the following errors:</h5>
                                 <ul class="mb-0">
                                     <?php foreach ($errors as $error): ?>
                                         <li><?php echo htmlspecialchars($error); ?></li>
                                     <?php endforeach; ?>
                                 </ul>
                             </div>
                         <?php endif; ?>

                         <?php if (isset($_SESSION['success_message'])): ?>
                             <div class="alert alert-success">
                                 <?php echo htmlspecialchars($_SESSION['success_message']); ?>
                                 <?php unset($_SESSION['success_message']); ?>
                             </div>
                         <?php endif; ?>

                         <form method="POST" action="">
                             <div class="row">
                                 <div class="col-md-6 mb-3">
                                     <label for="name" class="form-label required">Service Name</label>
                                     <input type="text" class="form-control" id="name" name="name"
                                         value="<?php echo htmlspecialchars($name ?? ''); ?>"
                                         required>
                                     <div class="form-text">Enter the name of the service</div>
                                 </div>

                                 <div class="col-md-6 mb-3">
                                     <label for="slug" class="form-label">Slug</label>
                                     <input type="text" class="form-control" id="slug" name="slug"
                                         value="<?php echo htmlspecialchars($slug ?? ''); ?>">
                                     <div class="form-text">URL-friendly version</div>
                                 </div>

                                 <div class="col-md-6 mb-3">
                                     <label for="parent_id" class="form-label">Parent Service</label>
                                     <select class="form-select" id="parent_id" name="parent_id">
                                         <option value="">-- None (Main Service) --</option>
                                         <?php foreach ($parentServices as $parent): ?>
                                             <option value="<?php echo $parent['id']; ?>"
                                                 <?php echo (($parent_id ?? '') == $parent['id']) ? 'selected' : ''; ?>>
                                                 <?php echo htmlspecialchars($parent['name']); ?>
                                             </option>
                                         <?php endforeach; ?>
                                     </select>
                                     <div class="form-text">
                                         <?php if ($parent_id): ?>
                                             Current Parent: <?php echo htmlspecialchars($serviceData['parent_name'] ?? 'Unknown'); ?>
                                         <?php else: ?>
                                             This is a top-level service
                                         <?php endif; ?>
                                     </div>
                                 </div>

                                 <div class="col-md-6 mb-3">
                                     <label for="sort_order" class="form-label">Sort Order</label>
                                     <input type="number" class="form-control" id="sort_order" name="sort_order"
                                         value="<?php echo htmlspecialchars($sort_order ?? 0); ?>"
                                         min="0" step="1">
                                     <div class="form-text">Lower numbers appear first (current: <?php echo $sort_order; ?>)</div>
                                 </div>

                                 <div class="col-md-6 mb-3 d-flex align-items-end">
                                     <div class="form-check form-switch">
                                         <input class="form-check-input" type="checkbox" id="status" name="status"
                                             value="1" <?php echo ($status ?? 0) == 1 ? 'checked' : ''; ?>>
                                         <label class="form-check-label" for="status">
                                             Status
                                         </label>
                                     </div>
                                 </div>
                             </div>

                             <div class="mt-4 pt-3 border-top">
                                 <button type="submit" class="btn btn-primary">
                                     <i class="bi bi-check-circle"></i> Update Service
                                 </button>
                                 <button type="reset" class="btn btn-secondary border-0" style="background-color: #b88a2e; border-radius: 20px; font-size: large; ">
                                     <i class="bi bi-arrow-clockwise"></i> Reset Changes
                                 </button>
                                 <a href="index.php" class="btn btn-outline-danger border-0 text-white" style="background-color: #b88a2e;  border-radius: 20px; font-size: large; ">
                                     <i class="bi bi-x-circle"></i> Cancel
                                 </a>

                                 <!-- Quick Actions -->
                                 <div class="btn-group float-end">
                                     <button type="button" class="btn  text-white dropdown-toggle" data-bs-toggle="dropdown" style="background-color: #b88a2e; border-radius: 20px; font-size: large; ">
                                         <i class="bi bi-lightning"></i> Quick Actions
                                     </button>
                                     <ul class="dropdown-menu">
                                         <li><a class="dropdown-item" href="create.php">
                                                 <i class="bi bi-plus-circle"></i> Create New Service
                                             </a></li>
                                         <li>
                                             <hr class="dropdown-divider">
                                         </li>
                                         <li><a class="dropdown-item text-danger" href="javascript:void(0)"
                                                 onclick="if(confirm('Are you sure you want to delete this service?')) { window.location.href='index.php?delete=<?php echo $id; ?>'; }">
                                                 <i class="bi bi-trash"></i> Delete This Service
                                             </a></li>
                                     </ul>
                                 </div>
                             </div>
                         </form>
                     </div>
                 </div>
             </div>
         </div>
     </div>

     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
     <script>
         // Auto-generate slug from name
         document.getElementById('name').addEventListener('input', function() {
             const nameField = this;
             const slugField = document.getElementById('slug');

             // Only auto-fill if slug is empty or hasn't been manually modified
             if (!slugField.dataset.manual && slugField.value === '<?php echo addslashes($serviceData['slug'] ?? ''); ?>') {
                 const slug = nameField.value
                     .toLowerCase()
                     .replace(/[^a-z0-9\s-]/g, '') // Remove special chars
                     .replace(/\s+/g, '-') // Replace spaces with hyphens
                     .replace(/-+/g, '-') // Remove multiple hyphens
                     .trim();
                 slugField.value = slug;
             }
         });

         // Mark slug as manually modified
         document.getElementById('slug').addEventListener('input', function() {
             this.dataset.manual = true;
         });

         // Confirm before leaving if changes were made
         let formChanged = false;
         const form = document.querySelector('form');
         const initialFormData = new FormData(form);

         form.addEventListener('change', () => {
             formChanged = true;
         });

         form.addEventListener('submit', () => {
             formChanged = false;
         });

         window.addEventListener('beforeunload', (e) => {
             if (formChanged) {
                 e.preventDefault();
                 e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
             }
         });

         // Status toggle preview
         document.getElementById('status').addEventListener('change', function() {
             const badge = document.querySelector('#status + label .badge');
             if (this.checked) {
                 badge.textContent = 'ACTIVE';
                 badge.className = 'badge bg-success ms-2';
             } else {
                 badge.textContent = 'INACTIVE';
                 badge.className = 'badge bg-danger ms-2';
             }
         });
     </script>
 </body>

 </html>