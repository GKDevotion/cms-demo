<?php

/**
 * Client Form View - Form for creating/editing clients
 * 
 * Expected variables:
 * - $page_title: Page title
 * - $client: Client data (null for create, array for edit)
 * - $errors: Array of validation errors
 * - $companies: Array of available companies
 * - $is_edit: Boolean flag indicating if this is an edit form
 */

?>

<div class="container-narrow">
    <h2><?php echo $is_edit ? 'Edit Client' : 'Add New Client'; ?></h2>

    <?php if (!empty($errors)): ?>
        <div class="error-box">
            <strong>Please fix the following errors:</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST">

        <div class="row">
            <!-- LEFT COLUMN (col-6) -->
            <div class="col-md-6">

                <div class="form-row mb-3">
                    <label>First Name <span class="required">*</span></label>
                    <input type="text" name="first_name" placeholder="Enter Your First Name"
                        value="<?php echo htmlspecialchars($_POST['first_name'] ?? ($client['first_name'] ?? '')); ?>"
                        required>
                </div>

                <div class="form-row mb-3">
                    <label>Second Name</label>
                    <input type="text" name="second_name" placeholder="Enter Your Second Name"
                        value="<?php echo htmlspecialchars($_POST['second_name'] ?? ($client['second_name'] ?? '')); ?>">
                </div>

                <div class="form-row mb-3">
                    <label>Last Name <span class="required">*</span></label>
                    <input type="text" name="last_name" placeholder="Enter Your Last Name"
                        value="<?php echo htmlspecialchars($_POST['last_name'] ?? ($client['last_name'] ?? '')); ?>"
                        required>
                </div>

                <div class="form-row mb-3">
                    <label>Email <span class="required">*</span></label>
                    <input type="email" name="email" placeholder="example@email.com"
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ($client['email'] ?? '')); ?>"
                        required>
                </div>

                <div class="form-row mb-3">
                    <label>Mobile Number <span class="required">*</span></label>
                    <input type="text" name="mobile_number" placeholder="+91 1000010000"
                        value="<?php echo htmlspecialchars($_POST['mobile_number'] ?? ($client['mobile_number'] ?? '')); ?>"
                        required>
                </div>

                <div class="form-row mb-3">
                    <label>Designation <span class="required">*</span></label>
                    <input type="text" name="designation" placeholder="e.g., Manager, Director"
                        value="<?php echo htmlspecialchars($_POST['designation'] ?? ($client['designation'] ?? '')); ?>"
                        required>
                </div>

                <div class="form-row mb-3">
                    <label>Companies<span class="required">*</span></label>
                    <select name="companies[]" multiple class="form-control">
                        <?php if (!empty($companies)): ?>
                            <?php foreach ($companies as $company): ?>
                                <option value="<?php echo htmlspecialchars($company['id']); ?>"
                                    <?php
                                    $selected_companies = $_POST['companies'] ?? ($client_companies ?? []);
                                    echo in_array($company['id'], $selected_companies) ? 'selected' : '';
                                    ?>>
                                    <?php echo htmlspecialchars($company['company_name']); ?>
                                </option>

                            <?php endforeach; ?>

                        <?php else: ?>
                            <option disabled>No companies available</option>
                        <?php endif; ?>
                    </select>


                </div>
                <div class="form-hint ">Hold Ctrl/Cmd to select multiple companies</div>
            </div>


            <!-- RIGHT COLUMN (col-6) -->
            <div class="col-md-6">

                <label>Services<span class="required">*</span></label>
                <div class="services-hierarchy form-row">
                    <?php if (!empty($company_services)): ?>
                        <?php foreach ($company_services as $parent): ?>
                            <div class="service-parent mb-2">

                                <label class="parent-service d-block fw-bold" style="color: #b88a2e;">
                                    <i class="bi bi-arrow-down-right-circle icon-space"></i><?= htmlspecialchars($parent['name']); ?>
                                </label>
                                <style>
                                    .child-checkbox {
                                        width: 5%;
                                        padding: 10px;
                                        margin-top: 5px;
                                        border-radius: 4px;
                                        border: 1px solid #ddd;
                                        font-size: 14px;
                                        font-family: inherit;
                                    }

                                    .services-hierarchy {
                                        display: grid;
                                        grid-template-columns: repeat(4, 1fr);
                                        gap: 15px;
                                    }

                                    .icon-space {
                                        margin-right: 8px;
                                    }
                                </style>
                                <?php if (!empty($parent['children'])): ?>
                                    <div class="service-children ms-4 mt-1">
                                        <?php foreach ($parent['children'] as $child): ?>
                                            <label class="child-service d-block">
                                                <input type="checkbox"
                                                    class="child-checkbox"
                                                    name="company_services[]"
                                                    value="<?= $child['id']; ?>"
                                                    data-parent="<?= $parent['id']; ?>"
                                                    <?= in_array($child['id'], $client_services) ? 'checked' : ''; ?>>
                                                <?= htmlspecialchars($child['name']); ?>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No services available.</p>
                    <?php endif; ?>
                </div>

                <div class="form-row mt-4" style="    margin-top: 15px; margin-bottom: 15px;">
                    <label>Status <span class="required">*</span></label>
                    <select name="status" required class="form-control">
                        <option value="1" <?php echo ($_POST['status'] ?? ($client['status'] ?? 1)) == 1 ? 'selected' : ''; ?>>Active</option>
                        <option value="0" <?php echo ($_POST['status'] ?? ($client['status'] ?? 1)) == 0 ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>

            </div>

        </div>

        <button type="submit" class="btn btn-primary mt-3" style="width:100%;">
            <?php echo $is_edit ? 'Update Client' : 'Save Client'; ?>
        </button>

    </form>

    <a href="index.php" class="back ">← Back to Clients</a>

    <script>
        document.querySelectorAll('.child-checkbox').forEach(child => {
            child.addEventListener('change', function() {
                const parent = document.querySelector(
                    '.parent-checkbox[value="' + this.dataset.parent + '"]'
                );
                if (this.checked) parent.checked = true;
            });
        });
    </script>

</div>