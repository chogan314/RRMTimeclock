// yyyy-mm-dd -> mm-dd-yyyy
function convertToMDY(dateString, delimiter) {
    if (typeof delimiter === 'undefined') delimiter = "-";
    var year = dateString.slice(0, 4);
    var month = dateString.slice(5, 7);
    var day = dateString.slice(8, 10);
    return month + delimiter + day + delimiter + year;
}

// mm-dd-yyyy -> yyyy-mm-dd
function convertToYMD(dateString, delimiter) {
    if (typeof delimiter === 'undefined') delimiter = "-";
    var month = dateString.slice(0, 2);
    var day = dateString.slice(3, 5);
    var year = dateString.slice(6, 10);
    return year + delimiter + month + delimiter + day;
}

// 2:00 PM -> 14:00
function convertTo24Hour(timeString) {
    var split = timeString.split(" ");
    var pm = split[1].charAt(0) == "P" || split[1].charAt(0) == "p";
    var hoursMinutes = split[0].split(":");
    var hours = parseInt(hoursMinutes[0]);
    if (pm) hours += 12;
    return hours.toString() + ":" + hoursMinutes[1];
}

$(function() {
    var filterForm = $('#filter-form');

    function submitFilterForm() {
        var formData = $(filterForm).serialize();

        $.ajax({
            type: 'GET',
            url: $(filterForm).attr('action'),
            data: formData
        }).done(function(response) {
            debugger;
            var tData = JSON.parse(response);
            populateTable(tData, $("#result-body"));
        }).fail(function(data) {
            debugger;
            // todo
        });
    }

    $(filterForm).submit(function(event) {
        event.preventDefault();
        submitFilterForm();
    });

    var popup = $('#popup');
    var createButton = $('#open-create-popup');
    var popupCancelButton = $('#popup-cancel-button');
    
    createButton.click(function() {
        $('#popup-create-header').css('display', 'block');
        $('#popup-edit-header').css('display', 'none');
        $('#popup-create-button').css('display', 'block');
        $('#popup-edit-button').css('display', 'none');
        popup.css('display', 'block');
    });

    function clearPopup() {
        $('#popup-record-id').val("");
        $('#popup-date-input').val("");
        $('#popup-name-input').val("");
        $('#popup-username-input').val("");
        $('#popup-cs-checkbox').prop('checked', false);
        $('#popup-group-size-input').val("");
        $('#popup-punch-type-select').val("default");
        $('#popup-time-input').val("");
        $('#popup-department-select').val("default");
        $('#popup-assignment-select').val("default");
    }

    popupCancelButton.click(function() {
        popup.css('display', 'none');
        clearPopup();
    });

    var popupCreateButton = $('#popup-create-button');
    var popupEditButton = $('#popup-edit-button');
    var popupForm = $('#popup-form');

    popupCreateButton.click(function() {
        var formData = $(popupForm).serialize();
        $.ajax({
            type: 'POST',
            url: "hours-cru-create.php",
            data: formData
        }).done(function(response) {
            debugger;
            // submitFilterForm();
        }).fail(function(data) {
            debugger;
            // todo
        });
    });

    popupEditButton.click(function() {
        var formData = $(popupForm).serialize();
        $.ajax({
            type: 'POST',
            url: "hours-cru-update.php",
            data: formData
        }).done(function(response) {
            submitFilterForm();
        }).fail(function(data) {
            // todo
        });
    });

    function populateTable(tableData, bodyToReplace) {
        var tbody = document.createElement("tbody");
        tbody.setAttribute("id", "result-body");

        var rowCount = 0;
        for (key in tableData) {
            var row = tableData[key];
            var tr = document.createElement("tr");
            tbody.appendChild(tr);
            Object.keys(row).forEach(function(key) {
                var val = row[key];
                var td = document.createElement("td");
                tr.appendChild(td);

                td.setAttribute("id", "row-" + rowCount + "-" + key);
                td.innerHTML = val;
            });
            var td = document.createElement("td");
            tr.appendChild(td);
            td.setAttribute("id", "row-" + rowCount + "-edit");
            var buttonDiv = document.createElement("div");
            td.appendChild(buttonDiv);
            buttonDiv.className += "input-button";
            buttonDiv.innerHTML = "Edit";

            (function(index) {
                buttonDiv.addEventListener("click", function() {
                    $('#popup-create-header').css('display', 'none');
                    $('#popup-edit-header').css('display', 'block');
                    $('#popup-create-button').css('display', 'none');
                    $('#popup-edit-button').css('display', 'block');

                    $('#popup-record-id').val($('#row-' + index + '-id').text());
                    $('#popup-date-input').val(convertToYMD($('#row-' + index + '-date').text()));
                    $('#popup-name-input').val($('#row-' + index + '-name').text());
                    $('#popup-username-input').val($('#row-' + index + '-username').text());
                    $('#popup-cs-checkbox').prop('checked', $('#row-' + index + '-community-service').text().toUpperCase() == "YES");
                    $('#popup-group-size-input').val($('#row-' + index + '-group-size').text());
                    $('#popup-punch-type-select').val($('#row-' + index + '-punch-type').text());
                    $('#popup-time-input').val(convertTo24Hour($('#row-' + index + '-time').text()));
                    $('#popup-department-select').val(departmentIds[$('#row-' + index + '-department').text()]);
                    $('#popup-assignment-select').val(assignmentIds[$('#row-' + index + '-assignment').text()]);

                    popup.css('display', 'block');
                });
            })(rowCount);

            rowCount++;
        }
        bodyToReplace.replaceWith($(tbody));
    }

    // var tData = [
    //     {
    //         "id": "12345",
    //         "date": "06-22-1995",
    //         "name": "Lastname, Firstname",
    //         "username": "Lastfirst",
    //         "group-size": "1",
    //         "community-service": "No",
    //         "punch-type": "In",
    //         "time": "4:30 PM",
    //         "department": "asdf",
    //         "assignment": "Testing"
    //     },
    //     {
    //         "id": "12345",
    //         "date": "06-22-1995",
    //         "name": "Lastname, Firstname",
    //         "username": "Lastfirst",
    //         "group-size": "1",
    //         "community-service": "Yes",
    //         "punch-type": "In",
    //         "time": "4:30 PM",
    //         "department": "asdf",
    //         "assignment": "Testing"
    //     }
    // ];

    // populateTable(tData, $("#result-body"));
});