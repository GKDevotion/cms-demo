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
        <div class="form-row">
            <div>
                <label>First Name <span class="required">*</span></label>
                <input
                    type="text"
                    name="first_name"
                    placeholder="Enter Your First Name"
                    value="<?php echo htmlspecialchars($_POST['first_name'] ?? ($client['first_name'] ?? '')); ?>"
                    required>
            </div>
            <div>
                <label>Second Name</label>
                <input
                    type="text"
                    name="second_name"
                    placeholder="Enter Your Second Name"
                    value="<?php echo htmlspecialchars($_POST['second_name'] ?? ($client['second_name'] ?? '')); ?>">
            </div>
        </div>

        <label>Last Name <span class="required">*</span></label>
        <input
            type="text"
            name="last_name"
            placeholder="Enter Your Last Name"
            value="<?php echo htmlspecialchars($_POST['last_name'] ?? ($client['last_name'] ?? '')); ?>"
            required>

        <label>Email <span class="required">*</span></label>
        <input
            type="email"
            name="email"
            placeholder="example@email.com"
            value="<?php echo htmlspecialchars($_POST['email'] ?? ($client['email'] ?? '')); ?>"
            required>

        <label>Mobile Number <span class="required">*</span></label>
        <input
            type="text"
            name="mobile_number"
            placeholder="+1-234-567-8900"
            value="<?php echo htmlspecialchars($_POST['mobile_number'] ?? ($client['mobile_number'] ?? '')); ?>"
            required>

        <label>Designation <span class="required">*</span></label>
        <input
            type="text"
            name="designation"
            placeholder="e.g., Manager, Director"
            value="<?php echo htmlspecialchars($_POST['designation'] ?? ($client['designation'] ?? '')); ?>"
            required>

        <label>Companies</label>
        <select name="companies[]" multiple>
            <?php if (!empty($companies)): ?>
                <?php foreach ($companies as $company): ?>
                    <option
                        value="<?php echo htmlspecialchars($company['id']); ?>"
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
        <div class="form-hint">Hold Ctrl/Cmd to select multiple companies</div>

        <label>Services</label>
        <select name="company_services[]" class="form-select" multiple>
            <?php foreach ($company_services as $service): ?>
                <option
                    value="<?= $service['id'] ?>"
                    <?= in_array($service['id'], $client_services) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($service['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>


        <div class="form-hint">Hold Ctrl/Cmd to select multiple services</div>


        <label>Status <span class="required">*</span></label>
        <select name="status" required>
            <option value="1" <?php echo ($_POST['status'] ?? ($client['status'] ?? 1)) == 1 ? 'selected' : ''; ?>>Active</option>
            <option value="0" <?php echo ($_POST['status'] ?? ($client['status'] ?? 1)) == 0 ? 'selected' : ''; ?>>Inactive</option>
        </select>

        <button type="submit" class="btn" style="    margin-top: 20px;
    width: 100%;"><?php echo $is_edit ? 'Update Client' : 'Save Client'; ?></button>
    </form>

    <a href="index.php" class="back ">← Back to Clients</a>
</div>