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
    function validateFilter() {
        var inputElements = [
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
                input: $("#popup-lastname-input"),
                validateFunc: validateName
            },
            {
                input: $("#popup-firstname-input"),
                validateFunc: validateName
            },
            {
                input: $("#popup-username-input"),
                validateFunc: validateName
            },
            {
                input: $("#popup-password-input"),
                validateFunc: validatePassword
            }
        ];
        return validateInputs(inputElements, "input-item-error");
    }

    function validateUpdate() {
        var inputElements = [
            {
                input: $("#popup-lastname-input"),
                validateFunc: validateName,
                allowEmpty: true
            },
            {
                input: $("#popup-firstname-input"),
                validateFunc: validateName,
                allowEmpty: true
            },
            {
                input: $("#popup-username-input"),
                validateFunc: validateName,
                allowEmpty: true
            },
            {
                input: $("#popup-password-input"),
                validateFunc: validatePassword,
                allowEmpty: true
            }
        ];
        return validateInputs(inputElements, "input-item-error");
    }

    var filterForm = $('#filter-form');

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
            var tData = JSON.parse(response);
            populateTable(tData, $("#result-body"));
        }).fail(function(data) {
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
        $('#popup-lastname-input').val("");
        $('#popup-firstname-input').val("");
        $('#popup-community-service-cb').prop('checked', false);
        $('#popup-username-input').val("");
        $('#popup-password-input').val("");

        $('#popup-record-id').removeClass("input-item-error");
        $('#popup-lastname-input').removeClass("input-item-error");
        $('#popup-firstname-input').removeClass("input-item-error");
        $('#popup-username-input').removeClass("input-item-error");
        $('#popup-password-input').removeClass("input-item-error");
    }

    popupCancelButton.click(function() {
        popup.css('display', 'none');
        clearPopup();
    });

    var popupCreateButton = $('#popup-create-button');
    var popupEditButton = $('#popup-edit-button');
    var popupForm = $('#popup-form');

    popupCreateButton.click(function() {
        if (!validateCreate()) {
            return;
        }
        var formData = $(popupForm).serialize();     
        $.ajax({
            type: 'POST',
            url: "volunteer-view-create.php",
            data: formData
        }).done(function(response) {
            popup.css('display', 'none');
            clearPopup();
            getVolunteerNames();
            submitFilterForm();
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
            url: "volunteer-view-update.php",
            data: formData
        }).done(function(response) {
            popup.css('display', 'none');
            clearPopup();
            getVolunteerNames();
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
                    $('#popup-lastname-input').val($('#row-' + index + '-lastname').text());
                    $('#popup-firstname-input').val($('#row-' + index + '-firstname').text());
                    $('#popup-community-service-cb').prop('checked', $('#row-' + index + '-community-service').text().toUpperCase() == "YES");
                    $('#popup-username-input').val($('#row-' + index + '-username').text());

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
            url: "../../global/get-volunteer-names.php"
        }).done(function(response) {
            $("#names-list").empty();
            var namesList = JSON.parse(response);
            for (var key in namesList) {
                var name = namesList[key];
                $("#names-list").append("<option value='" + name + "'>");
            }
        }).fail(function(data) {
            // todo
        });
    }

    getVolunteerNames();
});
