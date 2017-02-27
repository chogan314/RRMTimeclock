$(function() {
    var form = $('#view-form');

    $(form).submit(function(event) {
        event.preventDefault();

        var formData = $(form).serialize();

        $.ajax({
            type: 'GET',
            url: $(form).attr('action'),
            data: formData
        }).done(function(response) {
            $("#results-table-dynamic").empty();
            var timeData = JSON.parse(response);
            timeData.forEach(function(elem) {
                $("#results-table-dynamic").append("<tr><td>" + elem.name + "</td><td>" + elem.clockedTime + "</td></tr>");
            });
        }).fail(function(data) {

        });
    });
});