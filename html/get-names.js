$(function() {
    $.ajax({
        type: 'GET',
        url: '../php/get_names.php'
    }).done(function(response) {
        var names = JSON.parse(response);
        names.forEach(function(elem) {
            $("#names-list").append("<option value=\"" + elem + "\">\n");
        });
    }).fail(function(data) {
        
    });
});