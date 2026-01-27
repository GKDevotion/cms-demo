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

    // Get parent services for dropdown
    $parentServices = [];
    $result = $conn->query("SELECT id, name FROM  company_services WHERE parent_id IS NULL OR parent_id = 0 ORDER BY name ASC");
    if ($result) {
        $parentServices = $result->fetch_all(MYSQLI_ASSOC);
    }

    // Process form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $errors = [];

        // Sanitize inputs
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : 0;
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

        // Check if slug already exists
        $stmt = $conn->prepare("SELECT id FROM  company_services WHERE slug = ?");
        $stmt->bind_param("s", $slug);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Slug already exists";
        }
        $stmt->close();

        // If no errors, insert the record
        if (empty($errors)) {
            $stmt = $conn->prepare("
            INSERT INTO  company_services 
            (name, slug, parent_id,  status, sort_order, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, NOW(), NOW())
        ");

            $stmt->bind_param("ssiii", $name, $slug, $parent_id, $status, $sort_order);

            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Service created successfully!";
                header('Location: index.php');
                exit;
            } else {
                $errors[] = "Failed to create service: " . $conn->error;
            }
            $stmt->close();
        }
    }
    ?>

 <!DOCTYPE html>
 <html lang="en">

 <head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Create Service</title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">

     <style>
         .required::after {
             content: " *";
             color: red;
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
                         <h4 class="mb-0">Create New Service</h4>
                         <a href="index.php" class="btn btn-sm btn-outline-secondary" style="color: white; border: 1px solid white">
                             <i class="bi bi-arrow-left"></i> Back to List
                         </a>
                     </div>

                     <div class="card-body">
                         <?php if (!empty($errors)): ?>
                             <div class="alert alert-danger">
                                 <ul class="mb-0">
                                     <?php foreach ($errors as $error): ?>
                                         <li><?php echo htmlspecialchars($error); ?></li>
                                     <?php endforeach; ?>
                                 </ul>
                             </div>
                         <?php endif; ?>

                         <form method="POST" action="">
                             <div class="row">
                                 <div class="col-md-6 mb-3">
                                     <label for="name" class="form-label required">Service Name</label>
                                     <input type="text" class="form-control" id="name" name="name"
                                         value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                                         required autofocus>
                                     <div class="form-text">Enter the name of the service</div>
                                 </div>

                                 <div class="col-md-6 mb-3">
                                     <label for="slug" class="form-label">Slug</label>
                                     <input type="text" class="form-control" id="slug" name="slug"
                                         value="<?php echo htmlspecialchars($_POST['slug'] ?? ''); ?>">
                                     <div class="form-text">URL-friendly version (auto-generated if empty)</div>
                                 </div>

                                 <div class="col-md-6 mb-3">
                                     <label for="parent_id" class="form-label">Parent Service</label>
                                     <select class="form-select" id="parent_id" name="parent_id">
                                         <option value="">-- Select Service --</option>
                                         <?php foreach ($parentServices as $parent): ?>
                                             <option value="<?php echo $parent['id']; ?>"
                                                 <?php echo (($_POST['parent_id'] ?? '') == $parent['id']) ? 'selected' : ''; ?>>
                                                 <?php echo htmlspecialchars($parent['name']); ?>
                                             </option>
                                         <?php endforeach; ?>
                                     </select>
                                     <div class="form-text">Select parent service if this is a sub-service</div>
                                 </div>

                                 <div class="col-md-6 mb-3">
                                     <label for="sort_order" class="form-label">Sort Order</label>
                                     <input type="number" class="form-control" id="sort_order" name="sort_order"
                                         value="<?php echo htmlspecialchars($_POST['sort_order'] ?? 0); ?>"
                                         min="0" step="1">
                                     <div class="form-text">Lower numbers appear first</div>
                                 </div>

                                 <div class="col-md-6 mb-3 d-flex align-items-end">
                                     <div class="form-check form-switch">
                                         <input class="form-check-input" type="checkbox" id="status" name="status"
                                             value="1" <?php echo isset($_POST['status']) ? 'checked' : 'checked'; ?>>
                                         <label class="form-check-label" for="status">Active Status</label>
                                     </div>
                                 </div>
                             </div>

                             <div class="mt-4">
                                 <button type="submit" class="btn border-0 btn-primary" style="background-color: #b88a2e;">
                                     <i class="bi bi-plus-circle"></i> Create Service
                                 </button>
                                 <button type="reset" class="btn border-0 btn-secondary" style="background-color: #b88a2e; border-radius: 20px; font-size: large; ">
                                     <i class="bi bi-arrow-clockwise"></i> Reset
                                 </button>
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
             if (!slugField.dataset.manual) {
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
     </script>
 </body>

 </html>