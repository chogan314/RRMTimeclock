$(function() {
    function resetFilterRange() {
        var today = moment().format("YYYY-MM-DD");
        var thirtyDaysAgo = moment().subtract(30, 'days').format("YYYY-MM-DD");
        $('#stop-date').val(today);
        $('#start-date').val(thirtyDaysAgo);
    }

    var filterForm = $('#date-selection');

    function submitFilterForm() {
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
            rowCount++;
        }

        var tr = document.createElement("tr");
        tr.setAttribute("id", "total-hours");
        tbody.appendChild(tr);
        var td = document.createElement("td");
        td.innerHTML = "Total Hours:";
        td.colSpan = 4;
        tr.appendChild(td);
        td = document.createElement("td");
        td.innerHTML = "100";
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

    resetFilterRange();

    var tData = [
        {
            "event": "Punch Out",
            "assignment": "Testing",
            "date": "07-22-2002",
            "time": "8:00 PM",
            "hours": "4",
        },
        {
            "event": "Punch In",
            "assignment": "Testing",
            "date": "07-22-2002",
            "time": "4:00 PM",
            "hours": "",
        }
    ];

    populateTable(tData, $('#result-body'));

});