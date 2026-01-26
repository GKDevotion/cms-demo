 
    function loadCompanies(page = 1) {
        $.post('companies_ajax.php', {
            action: 'fetch',
            page
        }, function(res) {
            $('#companyTable').html(res);
        });
    }

    function deleteCompany(id) {
        if (!confirm('Delete company?')) return;
        $.post('companies_ajax.php', {
            action: 'delete',
            id
        }, function(res) {
            if (res === 'success') loadCompanies();
        });
    }

    function toggleStatus(id) {
        $.post('companies_ajax.php', {
            action: 'status',
            id
        }, function(res) {
            if (res === 'success') loadCompanies();
        });
    }

    /* ================= ACTION MENU ================= */
    function toggleMenu(btn) {
        // Hide all menus
        document.querySelectorAll('.action-menu').forEach(menu => menu.style.display = 'none');
        // Show the clicked menu
        btn.nextElementSibling.style.display = 'block';
    }

    // Hide menu when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.action-wrapper')) {
            document.querySelectorAll('.action-menu').forEach(menu => menu.style.display = 'none');
        }
    });

    /* ================= SEARCH ================= */
    $(document).ready(function() {
        // Load clients on page load
        loadClients();

        // Search functionality
        $('#search').on('keyup', function() {
            let searchValue = $(this).val().toLowerCase();

            if (searchValue === '') {
                loadClients();
                return;
            }

            $.post('companies_ajax.php', {
                action: 'search',
                query: searchValue
            }, function(data) {
                $('#companyTable').html(data);
            });
        });

        // Handle success message if present
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('success') === '1') {
            showSuccessMessage('Operation completed successfully!');
            // Clear the URL
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    });

    /* ================= UTILITY FUNCTIONS ================= */
    function showSuccessMessage(message) {
        const msg = $('<div class="success-box">')
            .append($('<strong>').text('Success!'))
            .append($('<p>').text(message));

        $('.container').prepend(msg);

        setTimeout(function() {
            msg.fadeOut(function() {
                $(this).remove();
            });
        }, 3000);
    }

    function showErrorMessage(message) {
        const msg = $('<div class="error-box">')
            .append($('<strong>').text('Error!'))
            .append($('<p>').text(message));

        $('.container').prepend(msg);
    }

    /* ================= SEARCH ================= */
    function searchCompanies(query) {
        if (query.trim() === '') {
            loadClients();
            return;
        }

        $.post('companies_ajax.php', {
            action: 'search',
            query: query
        }, function(data) {
            $('#companyTable').html(data);
        });
    }
    $(document).ready(loadCompanies);
 