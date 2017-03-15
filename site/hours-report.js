$(function() {

    function populateTable(tableData, bodyToReplace) {
        var tbody = document.createElement("tbody");
        tbody.setAttribute("id", "result-body");

        var tData = tableData.tdata;
        var rowCount = 0;
        for (var key in tData) {
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
        bodyToReplace.replaceWith($(tbody));

        $('#total-volunteers').text(tableData.totalVolunteers);
        $('#unique-volunteers').text(tableData.uniqueVolunteers);
        $('#cs-hours').text(tableData.communityServiceHours);
        $('#non-cs-hours').text(tableData.nonCommunityServiceHours);
        $('#total-volunteer-hours').text(tableData.totalHours);
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
            },
            {
                input: $("#names-input"),
                validateFunc: validateNameOrUsername,
                allowEmpty: true,
                extraCharacters: ["*"]
            },
            {
                input: $("#department-select"),
                validateFunc: validateSelect
            }
        ];
        return validateInputs(inputElements, "input-item-error");
    }

    var form = $('#filter-form');

    $(form).submit(function(event) {
        event.preventDefault();
        if (!validateFilter()) {
            return;
        }

        var formData = $(form).serialize();

        $.ajax({
            type: 'GET',
            url: $(form).attr('action'),
            data: formData
        }).done(function(response) {
            var tData = JSON.parse(response);
            populateTable(tData, $("#result-body"));
        }).fail(function(data) {
        });
    });

    function getVolunteerUsernames() {
        $.ajax({
            type: 'GET',
            url: "get-volunteer-usernames.php"
        }).done(function(response) {
            var namesList = JSON.parse(response);
            for (var key in namesList) {
                var name = namesList[key];
                $("#names-list").append("<option value=':" + name + "'>");
            }
        }).fail(function(data) {
            // todo
        });
    }

    function getVolunteerNames() {
        $.ajax({
            type: 'GET',
            url: "get-volunteer-names.php"
        }).done(function(response) {
            $("#names-list").empty();
            var namesList = JSON.parse(response);
            for (var key in namesList) {
                var name = namesList[key];
                $("#names-list").append("<option value='" + name + "'>");
            }
            getVolunteerUsernames();
        }).fail(function(data) {
            // todo
        });
    }

    getVolunteerNames();
});