$(function() {

    $('#user-welcome').text("Welcome " + volunteerFirstName + " " + volunteerLastName);

    function selectForm() {
        if (punchedIn) {
            $.ajax({
                type: 'GET',
                url: 'user-form-departments-assignments.php'
            }).done(function(response) {
                var data = JSON.parse(response);
                $('#current-department').text("Current department: " + data.department);
                $('#current-assignment').text("Current assignment: " + data.assignment);
            }).fail(function(data) {
                // todo
            });

            $('#punch-in-container').hide();
            $('#punch-out-container').show();
        } else {
            $('#punch-in-container').show();
            $('#punch-out-container').hide();
        }
    }

    function validateFilter() {
        var inputElements = [
            {
                input: $("#start-date"),
                validateFunc: validateDate
            },
            {
                input: $("#stop-date"),
                validateFunc: validateDate
            }
        ];
        return validateInputs(inputElements, "input-item-error");
    }

    function validatePunchIn() {
        var inputElements = [
            {
                input: $("#department-select"),
                validateFunc: validateSelect
            },
            {
                input: $("#assignment-select"),
                validateFunc: validateSelect
            },
            {
                input: $("#group-size-text"),
                validateFunc: validateNumber
            }
        ];
        return validateInputs(inputElements, "input-item-error");
    }

    selectForm();

    function resetFilterRange() {
        var today = moment().format("YYYY-MM-DD");
        // var thirtyDaysAgo = moment().subtract(30, 'days').format("YYYY-MM-DD");
        $('#stop-date').val(today);
        $('#start-date').val(today);
    }

    var filterForm = $('#date-selection');

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

    function populateTable(tableData, bodyToReplace) {
        var tbody = document.createElement("tbody");
        tbody.setAttribute("id", "result-body");

        var tData = tableData.tData;
        var rowCount = 0;
        for (key in tData) {
            var row = tData[key];
            var tr = document.createElement("tr");
            tbody.appendChild(tr);
            Object.keys(row).forEach(function(key) {
                var val = row[key];
                var td = document.createElement("td");
                tr.appendChild(td);

                td.setAttribute("id", "row-" + rowCount + "-" + key);
                td.innerHTML = val;
            });
            rowCount++;
        }

        var tr = document.createElement("tr");
        tr.setAttribute("id", "total-hours");
        tbody.appendChild(tr);
        var td = document.createElement("td");
        td.innerHTML = "Total Hours:";
        td.colSpan = 5;
        tr.appendChild(td);
        td = document.createElement("td");
        td.innerHTML = tableData.totalHours;
        tr.appendChild(td);

        bodyToReplace.replaceWith($(tbody));
    }

    $('#logout').click(function() {
        $.ajax({
            type: 'POST',
            url: "signout.php"
        }).done(function(response) {
            window.location.replace("signin.html");
        }).fail(function(data) {
            // todo
        });
    });

    var punchInForm = $('#punch-in-form');
    $(punchInForm).submit(function(event) {
        event.preventDefault();
        if (!validatePunchIn()) {
            return;
        }
        var formData = $(punchInForm).serialize();

        $.ajax({
            type: 'POST',
            url: $(punchInForm).attr('action'),
            data: formData
        }).done(function(response) {
            punchedIn = true;
            selectForm();
            resetFilterRange();
            submitFilterForm();
        }).fail(function(data) {
            // todo
        });
    });

    var punchOutForm = $('#punch-out-form');
    $(punchOutForm).submit(function(event) {
        event.preventDefault();
        var formData = $(punchOutForm).serialize();

        $.ajax({
            type: 'POST',
            url: $(punchOutForm).attr('action'),
            data: formData
        }).done(function(response) {
            punchedIn = false;
            selectForm();
            resetFilterRange();
            submitFilterForm();
        }).fail(function(data) {
            // todo
        });
    });

    resetFilterRange();
    submitFilterForm();
});