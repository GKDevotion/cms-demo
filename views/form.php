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
            <div class="col-md-12">

                <div class="card custom-card">

                    <div class="row mb-2">

                        <div class=" col-md-3">
                            <label>Title <span class="required">*</span></label>
                            <select name="title" class="form-select " required style="padding: 10px;">
                                <option value="">Select Title</option>
                                <option value="0" <?= isset($client['title']) && $client['title'] == 0 ? 'selected' : '' ?>>Mr</option>
                                <option value="1" <?= isset($client['title']) && $client['title'] == 1 ? 'selected' : '' ?>>Mrs</option>
                                <option value="2" <?= isset($client['title']) && $client['title'] == 2 ? 'selected' : '' ?>>Miss</option>
                                <option value="3" <?= isset($client['title']) && $client['title'] == 3 ? 'selected' : '' ?>>Master</option>
                            </select>
                        </div>

                        <div class=" col-md-3">
                            <label>First Name <span class="required">*</span></label>
                            <input type="text" name="first_name" placeholder="Enter Your First Name"
                                value="<?php echo htmlspecialchars($_POST['first_name'] ?? ($client['first_name'] ?? '')); ?>">
                        </div>

                        <div class=" col-md-3 ">
                            <label>Second Name</label>
                            <input type="text" name="second_name" placeholder="Enter Your Second Name"
                                value="<?php echo htmlspecialchars($_POST['second_name'] ?? ($client['second_name'] ?? '')); ?>">
                        </div>

                        <div class="col-md-3">
                            <label>Last Name <span class="required">*</span></label>
                            <input type="text" name="last_name" placeholder="Enter Your Last Name"
                                value="<?php echo htmlspecialchars($_POST['last_name'] ?? ($client['last_name'] ?? '')); ?>"
                                required>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-3">
                            <label>Mobile 1 <span class="required">*</span></label>
                            <input type="text" name="mobile1" placeholder="+91 1000010000"
                                value="<?php echo htmlspecialchars($_POST['mobile1'] ?? ($client['mobile1'] ?? '')); ?>"
                                required>
                        </div>

                        <div class="col-md-3">
                            <label>Mobile 2</label>
                            <input type="text" name="mobile2" placeholder="+91 1000010000"
                                value="<?php echo htmlspecialchars($_POST['mobile2'] ?? ($client['mobile2'] ?? '')); ?>">
                        </div>

                        <div class="col-md-3">
                            <label>Landline</label>
                            <input type="text" name="landline" placeholder="+91 1000010000"
                                value="<?php echo htmlspecialchars($_POST['landline'] ?? ($client['landline'] ?? '')); ?>">
                        </div>

                    </div>

                    <div class="row">

                        <!-- Permanent Address -->
                        <div class="address-block mt-2">
                            <h6>Permanent Address</h6>
                            <div class="row">
                                <div class="col-md-3 mb-2">
                                    <input type="text" name="address[permanent][address]" placeholder="Address" value="<?php echo htmlspecialchars($permanent['address'] ?? ''); ?>">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="address[permanent][city]" placeholder="City" value="<?php echo htmlspecialchars($permanent['city'] ?? ''); ?>">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="address[permanent][state]" placeholder="State" value="<?php echo htmlspecialchars($current['state'] ?? ''); ?>">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="address[permanent][country]" placeholder="Country" value="<?php echo htmlspecialchars($current['country'] ?? ''); ?>">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="address[permanent][pincode]" placeholder="Pincode" value="<?php echo htmlspecialchars($current['pincode'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Current Address -->
                        <div class="address-block mt-2">
                            <h6>Current Address</h6>

                            <div class="col-md-12 mb-2">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="copyCurrentFromPermanent">
                                    <label class="form-check-label " for="copyCurrentFromPermanent" style="margin-top: 0%; margin-left: 10px;">
                                        Same as Permanent Address
                                    </label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 mb-2">
                                    <input type="text" name="address[current][address]" id="current_address" placeholder="Address" value="<?php echo htmlspecialchars($current['address'] ?? ''); ?>"> 
                                </div>

                                <div class="col-md-3">
                                    <input type="text" name="address[current][city]" placeholder="City" value="<?php echo htmlspecialchars($current['city'] ?? ''); ?>">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="address[current][state]" placeholder="State" value="<?php echo htmlspecialchars($current['state'] ?? ''); ?>">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="address[current][country]" placeholder="Country" value="<?php echo htmlspecialchars($current['country'] ?? ''); ?>">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="address[current][pincode]" placeholder="Pincode" value="<?php echo htmlspecialchars($current['pincode'] ?? ''); ?>">
                                </div>

                            </div>
                        </div>

                        <div class="col-md-3">
                            <label>Company Name</label>
                            <input type="text" name="company_name" placeholder="Enter Company Name"
                                value="<?php echo htmlspecialchars($_POST['company_name'] ?? ($client['company_name'] ?? '')); ?>">
                        </div>

                        <div class="col-md-3">
                            <label>Company Type</label>
                            <input type="text" name="company_type" placeholder="Enter company Type"
                                value="<?php echo htmlspecialchars($_POST['company_type'] ?? ($client['company_type'] ?? '')); ?>">
                        </div>

                        <div class="col-md-3">
                            <label>Company Website</label>
                            <input type="text" name="company_website" placeholder="Enter Company Website"
                                value="<?php echo htmlspecialchars($_POST['company_website'] ?? ($client['company_website'] ?? '')); ?>">
                        </div>

                        <!-- Company Address -->
                        <div class="address-block mt-2">
                            <h6>Company Address</h6>
                            <div class="row">
                                <div class="col-md-3 mb-2">
                                    <input type="text" name="address[company][address]" placeholder="Address" value="<?php echo htmlspecialchars($company['address'] ?? ''); ?>">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="address[company][city]" placeholder="City" value="<?php echo htmlspecialchars($company['city'] ?? ''); ?>">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="address[company][state]" placeholder="State" value="<?php echo htmlspecialchars($company['state'] ?? ''); ?>">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="address[company][country]" placeholder="Country" value="<?php echo htmlspecialchars($company['country'] ?? ''); ?>">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="address[company][pincode]" placeholder="Pincode" value="<?php echo htmlspecialchars($company['pincode'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label>TRN Number</label>
                            <input type="text" name="trn_no" placeholder="Enter TRN Number"
                                value="<?php echo htmlspecialchars($_POST['trn_no'] ?? ($client['trn_no'] ?? '')); ?>">
                        </div>

                        <div class="col-md-3">
                            <label>TAX Number</label>
                            <input type="text" name="tax_no" placeholder="Enter TAX Number"
                                value="<?php echo htmlspecialchars($_POST['tax_no'] ?? ($client['tax_no'] ?? '')); ?>">
                        </div>

                        <div class="col-md-3">
                            <label>SMS Notification</label>
                            <select name="sms_notification" class="form-control form-select">
                                <option value="1" <?= (($_POST['sms_notification'] ?? $client['sms_notification'] ?? '') == 1) ? 'selected' : '' ?>>Yes</option>
                                <option value="0" <?= (($_POST['sms_notification'] ?? $client['sms_notification'] ?? '') == 0) ? 'selected' : '' ?>>No</option>
                            </select>
                        </div>


                        <div class="col-md-3">
                            <label>Email Notification</label>
                            <select name="email_notification" class="form-control form-select">
                                <option value="1" <?= (($_POST['email_notification'] ?? $client['email_notification'] ?? '') == 1) ? 'selected' : '' ?>>Yes</option>
                                <option value="0" <?= (($_POST['email_notification'] ?? $client['email_notification'] ?? '') == 0) ? 'selected' : '' ?>>No</option>
                            </select>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-4">
                            <label>Email <span class="required">*</span></label>
                            <input type="email" name="email" placeholder="example@email.com"
                                value="<?php echo htmlspecialchars($_POST['email'] ?? ($client['email'] ?? '')); ?>"
                                required>
                        </div>

                        <div class="col-md-4">
                            <label>Designation <span class="required">*</span></label>
                            <input type="text" name="designation" placeholder="e.g., Manager, Director"
                                value="<?php echo htmlspecialchars($_POST['designation'] ?? ($client['designation'] ?? '')); ?>"
                                required>
                        </div>

                        <div class="col-md-4">
                            <label>Birth Date <span class="required">*</span></label>
                            <input type="date" name="birth_date" placeholder="e.g., 1990-01-01"
                                value="<?php echo htmlspecialchars($_POST['birth_date'] ?? ($client['birth_date'] ?? '')); ?>"
                                required>
                        </div>

                    </div>


                    <div class="mb-4">
                        <label>Services <span class="required">*</span></label>
                        <div class="d-flex flex-wrap gap-2  me-4">
                            <?php if (!empty($companies)): ?>
                                <?php
                                $selected_companies = $_POST['companies'] ?? ($client_companies ?? []);
                                foreach ($companies as $company):
                                ?>
                                    <div class="form-check mt-3 d-flex  me-4 gap-3">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            name="companies[]"
                                            value="<?php echo htmlspecialchars($company['id']); ?>"
                                            id="company_<?php echo $company['id']; ?>"
                                            <?php echo in_array($company['id'], $selected_companies) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="company_<?php echo $company['id']; ?>" style="margin-top: 1%; margin-left:5px;">
                                            <?php echo htmlspecialchars($company['company_name']); ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div>No companies available</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary  " style="width:100%; background-color: #b88a2e; border: 1px solid #b88a2e;">
                        <?php echo $is_edit ? 'Update Client' : 'Save Client'; ?>
                    </button>
                    <a href="index.php" class="back mt-2">← Back to Clients</a>

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
<script>
    document.getElementById('address_type').addEventListener('change', function() {
        document.querySelectorAll('.address-block').forEach(el => el.classList.add('d-none'));

        if (this.value) {
            document.getElementById(this.value + '_address').classList.remove('d-none');
        }
    });
</script>

<script>
document.getElementById('copyCurrentFromPermanent').addEventListener('change', function() {
    const checked = this.checked;

    // List of address field keys
    const fields = ['address', , 'city', 'state', 'country', 'pincode'];

    fields.forEach(function(field) {
        const permanentField = document.querySelector(`input[name="address[permanent][${field}]"]`);
        const currentField = document.querySelector(`input[name="address[current][${field}]"]`);
        if (permanentField && currentField) {
            if (checked) {
                currentField.value = permanentField.value;
            } else {
                currentField.value = '';
            }
        }
    });
});
</script>
