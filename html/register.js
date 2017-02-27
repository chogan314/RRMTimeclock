$(function() {
    var form = $('#register-form');
    var confirmDiv = $('#register-confirm');

    $(form).submit(function(event) {
        event.preventDefault();

        var formData = $(form).serialize();

        $.ajax({
            type: 'POST',
            url: $(form).attr('action'),
            data: formData
        }).done(function(response) {
            $(confirmDiv).text(response);
            $('#first-name').val('');
            $('#last-name').val('');
            $('#create-username').val('');
            $('#create-password').val('');
            $('#confirm-password').val('');
        }).fail(function(data) {
            if (data.responseText !== '') {
                $(confirmDiv).text(data.responseText);
            } else {
                $(confirmDiv).text('Oops! An error occured and your message could not be sent.');
            }
        });
    });
});