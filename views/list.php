<?php
/**
 * Client List View - Displays clients in table format
 * 
 * Expected variables:
 * - $page_title: Page title
 */
$page_title = 'Client Listing';
require_once 'views/header.php';
require_once 'config/Database.php';
require_once 'classes/Client.php';

$client = new Client($conn);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$clientData = $client->getPaginated($page, 10);

$services = $client->getActiveCompanies();

?>

<div class="container">
    <div class="top-bar">
        <span style="font-size: 2rem;">Clients</span>
        <div class="search-box">
            <input type="text" id="search" placeholder="Search The Clients">
        </div>
        
        <a href="create.php" class="btn">+ Add Client</a>
      
    </div>

    <div class="accordion" id="accordionExample">
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    Generate Client Report
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <form action="generate-report.php" target="_blank" method="post" class="card shadow-sm border-0 mt-0">
                        <div class="card-body">
                            <div class="row g-3">

                            <!-- Mobile -->
                            <div class="col-md-3 mt-0">
                                <label class="form-label">Mobile Number</label>
                                <input
                                type="number"
                                name="mobile"
                                class="form-control"
                                placeholder="+91 90000 00000"
                                >
                            </div>

                            <!-- Birth Date -->
                            <div class="col-md-3 mt-0">
                                <label class="form-label">Birth Date</label>
                                <input
                                type="date"
                                name="birth_date"
                                class="form-control"
                                >
                            </div>

                            <!-- Service -->
                            <div class="col-md-6 mt-0">
                                <label class="form-label">Service</label>
                                <select name="service_id[]" id="service_id" class="form-select" multiple>
                                    <option value="">All Services</option>
                                    <?php foreach ($services as $service): ?>
                                        <option value="<?= $service['id'] ?>">
                                        <?= htmlspecialchars($service['company_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Continent country-->
                            <div class="col-md-3">
                                <label class="form-label">Country</label>
                                <input
                                type="text"
                                name="country"
                                class="form-control"
                                placeholder="Write country name here"
                                >
                            </div>

                            <!-- State -->
                            <div class="col-md-3">
                                <label class="form-label">State</label>
                                <input
                                type="text"
                                name="state"
                                class="form-control"
                                placeholder="Write state name here"
                                >
                            </div>

                            <!-- City -->
                            <div class="col-md-3">
                                <label class="form-label">City</label>
                                <input
                                type="text"
                                name="city"
                                class="form-control"
                                placeholder="Write city name here"
                                >
                            </div>

                            <!-- Pincode -->
                            <div class="col-md-3">
                                <label class="form-label">Pincode</label>
                                <input
                                type="text"
                                name="pincode"
                                class="form-control"
                                placeholder="Write pincode name here"
                                >
                            </div>

                            <!-- Created Date From -->
                            <div class="col-md-3">
                                <label class="form-label">Created From</label>
                                <input
                                type="date"
                                name="from_date"
                                class="form-control"
                                >
                            </div>

                            <!-- Created Date To -->
                            <div class="col-md-3">
                                <label class="form-label">Created To</label>
                                <input
                                type="date"
                                name="to_date"
                                class="form-control"
                                >
                            </div>

                            </div>
                        </div>

                        <div class="card-footer text-end bg-white">
                            <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-file-earmark-pdf me-1"></i> Generate Report
                            </button>
                            <a href="clients.php" class="btn btn-outline-secondary ms-2">
                            Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Sr</th> 
                <th>Name</th>
                <th>Email</th>
                <th>Mobile1</th>
                <th>Mobile2</th> 
                <th>Company Name</th>
                <th>Company Type</th>
                <th>Company Website</th>
                <th>Designation</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="clientTable">
            <!-- Loaded via AJAX -->
        </tbody>
    </table>
</div>

<?php require_once 'views/footer.php'; ?>
