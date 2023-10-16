jQuery(document).ready(function($) {
    var adminUrl = window.location.origin;
    var addNewButton = $('.page-title-action');
    var customButton = $('<a href="' + adminUrl + '/wp-admin/admin.php?incident=1" class="page-title-action">Export to CSV</a>');
    addNewButton.after(customButton);
});
