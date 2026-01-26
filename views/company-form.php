<?php
/**
 * Company Form Template
 */
?>

<div class="container-narrow">
    <h2><?php echo $is_edit ? 'Edit Company' : 'Add New Company'; ?></h2>

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
        <label>Company Name <span class="required">*</span></label>
        <input 
            type="text" 
            name="company_name" 
            placeholder="Enter Company Name" 
            value="<?php echo htmlspecialchars($_POST['company_name'] ?? ($company['company_name'] ?? '')); ?>" 
            required>

        <label>Status <span class="required">*</span></label>
        <select name="status" required>
            <option value="1" <?php echo ($_POST['status'] ?? ($company['status'] ?? 1)) == 1 ? 'selected' : ''; ?>>Active</option>
            <option value="0" <?php echo ($_POST['status'] ?? ($company['status'] ?? 1)) == 0 ? 'selected' : ''; ?>>Inactive</option>
        </select>

        <button type="submit" class="btn" style="margin-top: 15px;
    width: 100%;"><?php echo $is_edit ? 'Update Company' : 'Save Company'; ?></button>
    </form>

    <a href="index.php" class="back">← Back to Companies</a>
</div>
