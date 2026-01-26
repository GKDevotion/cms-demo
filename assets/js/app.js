/**
 * Client Management Application - JavaScript
 * Handles all client operations and UI interactions
 */

/* ================= LOAD CLIENTS ================= */
function loadClients(page = 1) {
    $.post('clients_ajax.php', {
        action: 'fetch',
        page: page
    }, function(data) {
        $('#clientTable').html(data);
    });
}

/* ================= DELETE ================= */
function deleteClient(id) { 
    $.post('clients_ajax.php', {
        action: 'delete',
        id: id
    }, function(res) {
        if (res === 'success') {
            alert('Are you sure you want to delete this client?');
            loadClients();
        } else {
            alert('Error deleting client');
        }
    });
}

/* ================= TOGGLE STATUS ================= */
function toggleStatus(id) {
    $.post('clients_ajax.php', {
        action: 'status',
        id: id
    }, function(res) {
        if (res === 'success') {
            loadClients();
        } else {
            alert('Error updating status');
        }
    });
}

/* ================= SEARCH ================= */
function searchClients(query) {
    if (query.trim() === '') {
        loadClients();
        return;
    }

    $.post('clients_ajax.php', {
        action: 'search',
        query: query
    }, function(data) {
        $('#clientTable').html(data);
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

        $.post('clients_ajax.php', {
            action: 'search',
            query: searchValue
        }, function(data) {
            $('#clientTable').html(data);
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
