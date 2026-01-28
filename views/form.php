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
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">
            <?php echo $is_edit ? 'Edit Client' : 'Add New Client'; ?>
        </h2>

        <div class="d-flex align-items-center gap-3">
            <a href="index.php"
                class="btn"
                style="background-color:#b88a2e; border:1px solid #b88a2e; color:#fff;">
                ← Back to Clients
            </a>

            <button type="submit"
                class="btn btn-primary"
                style="background-color:#b88a2e; border:1px solid #b88a2e;">
                <?php echo $is_edit ? 'Update Client' : 'Save Client'; ?>
            </button>
        </div>
    </div>



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

        <div class="row g-4">

            <!-- LEFT COLUMN (col-6) -->
            <div class="col-md-4">

                <div class="card custom-card">

                    <div class="row mb-2">
                        <div class=" col-md-6">
                            <label>First Name <span class="required">*</span></label>
                            <input type="text" name="first_name" placeholder="Enter Your First Name"
                                value="<?php echo htmlspecialchars($_POST['first_name'] ?? ($client['first_name'] ?? '')); ?>"
                                required>
                        </div>

                        <div class=" col-md-6 ">
                            <label>Second Name</label>
                            <input type="text" name="second_name" placeholder="Enter Your Second Name"
                                value="<?php echo htmlspecialchars($_POST['second_name'] ?? ($client['second_name'] ?? '')); ?>">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label>Last Name <span class="required">*</span></label>
                        <input type="text" name="last_name" placeholder="Enter Your Last Name"
                            value="<?php echo htmlspecialchars($_POST['last_name'] ?? ($client['last_name'] ?? '')); ?>"
                            required>
                    </div>

                    <div class="mb-2">
                        <label>Email <span class="required">*</span></label>
                        <input type="email" name="email" placeholder="example@email.com"
                            value="<?php echo htmlspecialchars($_POST['email'] ?? ($client['email'] ?? '')); ?>"
                            required>
                    </div>

                    <div class="mb-2">
                        <label>Mobile Number <span class="required">*</span></label>
                        <input type="text" name="mobile_number" placeholder="+91 1000010000"
                            value="<?php echo htmlspecialchars($_POST['mobile_number'] ?? ($client['mobile_number'] ?? '')); ?>"
                            required>
                    </div>

                    <div class="mb-2">
                        <label>Designation <span class="required">*</span></label>
                        <input type="text" name="designation" placeholder="e.g., Manager, Director"
                            value="<?php echo htmlspecialchars($_POST['designation'] ?? ($client['designation'] ?? '')); ?>"
                            required>
                    </div>

                    <div class="mb-2">
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


                    <div class="" style="  margin-bottom: 15px;">
                        <label>Status <span class="required">*</span></label>
                        <select name="status" required class="form-control">
                            <option value="1" <?php echo ($_POST['status'] ?? ($client['status'] ?? 1)) == 1 ? 'selected' : ''; ?>>Active</option>
                            <option value="0" <?php echo ($_POST['status'] ?? ($client['status'] ?? 1)) == 0 ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary  " style="width:100%; background-color: #b88a2e; border: 1px solid #b88a2e;">
                        <?php echo $is_edit ? 'Update Client' : 'Save Client'; ?>
                    </button>
                    <a href="index.php" class="back mt-2">← Back to Clients</a>

                </div>

            </div>

            <!-- RIGHT COLUMN (col-6) -->
            <div class="col-md-8">
                <div class="card custom-card">
                    <label class="mb-2 fw-bold">Services <span class="required">*</span></label>

                    <div class="accordion" id="servicesAccordion">

                        <?php if (!empty($company_services)): ?>
                            <?php foreach ($company_services as $index => $parent): ?>

                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading<?= $parent['id']; ?>">
                                        <button class="accordion-button collapsed fw-bold"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#collapse<?= $parent['id']; ?>"
                                            aria-expanded="false"
                                            aria-controls="collapse<?= $parent['id']; ?>">
                                            <i class="bi bi-arrow-down-right-circle me-2 fs-4"></i>
                                            <?= htmlspecialchars($parent['name']); ?>
                                        </button>
                                    </h2>

                                    <div id="collapse<?= $parent['id']; ?>"
                                        class="accordion-collapse collapse"
                                        aria-labelledby="heading<?= $parent['id']; ?>"
                                        data-bs-parent="#servicesAccordion">

                                        <div class="accordion-body">
                                            <div class="row">
                                                <?php foreach ($parent['children'] as $child): ?>
                                                    <div class="col-md-3 col-sm-6 mb-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input"
                                                                type="checkbox"
                                                                name="company_services[]"
                                                                value="<?= $child['id']; ?>"
                                                                id="service<?= $child['id']; ?>"
                                                                <?= in_array($child['id'], $client_services) ? 'checked' : ''; ?>>

                                                            <label class="form-check-label" for="service<?= $child['id']; ?>" style="margin-top: 0px; margin-left:12px; font-size: 14px;">
                                                                <?= htmlspecialchars($child['name']); ?>
                                                            </label>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No services available.</p>
                        <?php endif; ?>

                    </div>

                </div>


            </div>

        </div>

    </form>


</div>
<style>
    /* Make sure cards fill their columns */
    .custom-card {
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .05);
        border: 1px solid #e5e5e5;
        padding: 30px;

        margin-bottom: 20px;
    }

    /* Change checkbox color */
    .form-check-input {
        width: 14px;
        height: 14px;
        border: 2px solid #b88a2e;
    }

    .form-check-input:checked {
        background-color: #b88a2e;
        border-color: #b88a2e;
    }



    /* Ensure the form row columns work properly */
    .row {
        display: flex;
        flex-wrap: wrap;
        margin-right: -15px;
        margin-left: -15px;
    }

    .col-md-6 {
        flex: 0 0 50%;
        max-width: 50%;
        padding-right: 15px;
        padding-left: 15px;
    }

    /* Fix for mobile responsiveness */
    @media (max-width: 768px) {
        .col-md-6 {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }

    /* Remove outer accordion border */
    .accordion {
        border: none;
    }

    /* Remove each item border */
    .accordion-item {
        border: none;
    }

    /* Remove button borders & focus outline */
    .accordion-button {
        border: none;
        box-shadow: none;
    }

    /* Accordion button – normal */
    .accordion-button {
        background-color: #fff;
        color: #000;
    }

    /* When accordion is OPEN */
    .accordion-button:not(.collapsed) {
        background-color: #b88a2e;
        /* change this */
        color: #fff;
        box-shadow: none;
    }

    /* Remove blue focus outline */
    .accordion-button:focus {
        box-shadow: none;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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