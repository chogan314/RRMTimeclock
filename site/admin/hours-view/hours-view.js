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
    var popup = $('#popup');
    var createButton = $('#open-create-popup');
    var popupCancelButton = $('#popup-cancel-button');
    var popupCreateButton = $('#popup-create-button');
    var popupEditButton = $('#popup-edit-button');
    var popupForm = $('#popup-form');

    function validateFilter() {
        var inputElements = [
            {
                input: $("#start-date"),
                validateFunc: validateDate
            },
            {
                input: $("#stop-date"),
                validateFunc: validateDate
            },
            {
                input: $("#names-input"),
                validateFunc: validateSplitName,
                allowEmpty: true,
                extraCharacters: ["*"]
            }
        ];
        return validateInputs(inputElements, "input-item-error");
    }

    function validateCreate() {
        var inputElements = [
            {
                input: $("#popup-date-input"),
                validateFunc: validateDate
            },
            {
                input: $("#popup-name-input"),
                validateFunc: validateSplitName
            },
            {
                input: $("#popup-username-input"),
                validateFunc: validateName
            },
            {
                input: $("#popup-cs-select"),
                validateFunc: validateSelect,
            },
            {
                input: $("#popup-group-size-input"),
                validateFunc: validateNumber
            },
            {
                input: $("#popup-punch-type-select"),
                validateFunc: validateSelect
            },
            {
                input: $("#popup-time-input"),
                validateFunc: validateTime
            },
            {
                input: $("#popup-department-select"),
                validateFunc: validateSelect
            },
            {
                input: $("#popup-assignment-select"),
                validateFunc: validateSelect
            }
        ];
        return validateInputs(inputElements, "input-item-error");
    }

    function validateUpdate() {
        var inputElements = [
            {
                input: $("#popup-date-input"),
                validateFunc: validateDate
            },
            {
                input: $("#popup-name-input"),
                validateFunc: validateSplitName,
                allowEmpty: true
            },
            {
                input: $("#popup-username-input"),
                validateFunc: validateName,
                allowEmpty: true
            },
            {
                input: $("#popup-cs-select"),
                validateFunc: validateSelect,
            },
            {
                input: $("#popup-group-size-input"),
                validateFunc: validateNumber,
                allowEmpty: true
            },
            {
                input: $("#popup-punch-type-select"),
                validateFunc: validateSelect
            },
            {
                input: $("#popup-time-input"),
                validateFunc: validateTime
            },
            {
                input: $("#popup-department-select"),
                validateFunc: validateSelect
            },
            {
                input: $("#popup-assignment-select"),
                validateFunc: validateSelect
            }
        ];
        return validateInputs(inputElements, "input-item-error");
    }

    function submitFilterForm() {
        if (!validateFilter()) {
            return;
        }
        var formData = $(filterForm).serialize();
        $.ajax({
            type: 'GET',
            url: $(filterForm).attr('action'),
            data: formData
        }).done(function(response) {
            debugger;
            var data = JSON.parse(response);
            populateTable(data.tdata, $("#result-body"));
        }).fail(function(data) {
            // todo
        });
    }

    $(filterForm).submit(function(event) {
        event.preventDefault();
        submitFilterForm();
    });    
    
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
        $('#popup-cs-select').val("default");
        $('#popup-group-size-input').val("");
        $('#popup-punch-type-select').val("default");
        $('#popup-time-input').val("");
        $('#popup-department-select').val("default");
        $('#popup-assignment-select').val("default");

        $('#popup-record-id').removeClass("input-item-error");
        $('#popup-date-input').removeClass("input-item-error");
        $('#popup-name-input').removeClass("input-item-error");
        $('#popup-username-input').removeClass("input-item-error");
        $('#popup-cs-select').removeClass("input-item-error");
        $('#popup-group-size-input').removeClass("input-item-error");
        $('#popup-punch-type-select').removeClass("input-item-error");
        $('#popup-time-input').removeClass("input-item-error");
        $('#popup-department-select').removeClass("input-item-error");
        $('#popup-assignment-select').removeClass("input-item-error");
    }

    popupCancelButton.click(function() {
        popup.css('display', 'none');
        clearPopup();
    });

    popupCreateButton.click(function() {
        if (!validateCreate()) {
            return;
        }
        var formData = $(popupForm).serialize();
        $.ajax({
            type: 'POST',
            url: "hours-view-create.php",
            data: formData
        }).done(function(response) {
            popup.css('display', 'none');
            clearPopup();
        }).fail(function(data) {
            // todo
        });
    });

    popupEditButton.click(function() {
        if (!validateUpdate()) {
            return;
        }
        var formData = $(popupForm).serialize();
        $.ajax({
            type: 'POST',
            url: "hours-view-update.php",
            data: formData
        }).done(function(response) {
            popup.css('display', 'none');
            clearPopup();
        }).fail(function(data) {
            // todo
        });
    });

    function populateTable(tableData, bodyToReplace) {
        var tbody = document.createElement("tbody");
        tbody.setAttribute("id", "result-body");

        var rowCount = 0;
        for (var key in tableData) {
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
                    $('#popup-cs-select').val($('#row-' + index + '-community-service').text().toLowerCase());
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

    function getVolunteerNames() {
        $.ajax({
            type: 'GET',
            url: "get-volunteer-names.php"
        }).done(function(response) {
            $("#names-list").empty();
            $("#popup-names-list").empty();
            var namesList = JSON.parse(response);
            for (var key in namesList) {
                var name = namesList[key];
                $("#names-list").append("<option value='" + name + "'>");
                $("#popup-names-list").append("<option value='" + name + "'>");
            }
        }).fail(function(data) {
            // todo
        });
    }

    function getVolunteerUsernames() {
        $.ajax({
            type: 'GET',
            url: "get-volunteer-usernames.php"
        }).done(function(response) {
            $("#popup-usernames-list").empty();
            var namesList = JSON.parse(response);
            for (var key in namesList) {
                var name = namesList[key];
                $("#popup-usernames-list").append("<option value='" + name + "'>");
            }
        }).fail(function(data) {
            // todo
        });
    }

    function getVolunteerData() {
        getVolunteerNames();
        getVolunteerUsernames();
    }

    getVolunteerData();
});