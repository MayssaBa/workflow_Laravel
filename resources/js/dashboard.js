
document.addEventListener('DOMContentLoaded', function () {
    // Confirm that the dashboard JavaScript has loaded.
    console.log('Dashboard JavaScript loaded successfully.');

    // Automatically hide success and error alerts after 5 seconds.
    const alerts = document.querySelectorAll(
        '.alert-success, .alert-danger'
    );

    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';

            setTimeout(function () {
                alert.remove();
            }, 500);
        }, 5000);
    });

    // Add confirmation before deleting an item.
    const deleteForms = document.querySelectorAll(
        'form[data-confirm-delete]'
    );

    deleteForms.forEach(function (form) {
        form.addEventListener('submit', function (event) {
            const confirmed = confirm(
                'Are you sure you want to delete this item?'
            );

            if (!confirmed) {
                event.preventDefault();
            }
        });
    });
});
