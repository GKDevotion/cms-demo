function loadServices(page = 1) {
    $.post('services_ajax.php', {
        action: 'fetch',
        page: page
    }, function(data) {
        $('#serviceTable').html(data);
    });
}

function deleteService(id) {
 

    $.post('services_ajax.php', {
        action: 'delete',
        id: id
    }, function(res) {
        if (res === 'success') {
            alert('Are you sure you want to delete this service ?');
            loadServices();
        } else {
            alert('Error deleting service');
        }
    });
}

function toggleStatus(id) {
    $.post('services_ajax.php', {
        action: 'status',
        id: id
    }, function(res) {
        if (res === 'success') {
            loadServices();
        } else {
            alert('Error updating status');
        }
    });
}

function toggleMenu(btn) {
    document.querySelectorAll('.action-menu').forEach(menu => menu.style.display = 'none');
    btn.nextElementSibling.style.display = 'block';
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('.action-wrapper')) {
        document.querySelectorAll('.action-menu').forEach(menu => menu.style.display = 'none');
    }
});

$(document).ready(function() {
    loadServices();

    $('#search').on('keyup', function() {
        let searchValue = $(this).val().trim();

        if (searchValue === '') {
            loadServices();
            return;
        }

        $.post('services_ajax.php', {
            action: 'search',
            query: searchValue
        }, function(data) {
            $('#serviceTable').html(data);
        });
    });
});
