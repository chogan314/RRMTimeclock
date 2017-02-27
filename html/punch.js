$(function() {
    var form = $('#punch-form');
    var confirmDiv = $('#punch-confirm');

    $(form).submit(function(event) {
        event.preventDefault();

        var formData = $(form).serialize();

        $.ajax({
            type: 'POST',
            url: $(form).attr('action'),
            data: formData
        }).done(function(response) {
            $(confirmDiv).text(response);
            $('#username').val('');
            $('#password').val('');
            $('#punch-in').prop('checked', false);
            $('#punch-out').prop('checked', false);
        }).fail(function(data) {
            if (data.responseText !== '') {
                $(confirmDiv).text(data.responseText);
            } else {
                $(confirmDiv).text('Oops! An error occured and your message could not be sent.');
            }
        });
    });
});