$(function() {

    function populateTable(tableData, bodyToReplace) {
        var tbody = document.createElement("tbody");
        tbody.setAttribute("id", "result-body");

        var tData = tableData.tdata;
        debugger;
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

    var form = $('#filter-form');

    $(form).submit(function(event) {
        event.preventDefault();

        var formData = $(form).serialize();

        $.ajax({
            type: 'GET',
            url: $(form).attr('action'),
            data: formData
        }).done(function(response) {
            debugger;
            var tData = JSON.parse(response);
            populateTable(tData, $("#result-body"));
        }).fail(function(data) {
            debugger;
        });
    });

    // var tData = [
    //     {
    //         "id": "12345",
    //         "date": "1-1-1990",
    //         "name": "Lastname, Firstname",
    //         "username": "Lfirst",
    //         "department": "a",
    //         "assignment": "b",
    //         "groupSize": "1",
    //         "communityService": "no",
    //         "in": "9:00",
    //         "out": "5:00",
    //         "hours": "8"
    //     },
    //     {
    //         "id": "123456",
    //         "date": "2-2-1990",
    //         "name": "Lastname, asdfasdfdd",
    //         "username": "asdfasdfsdf",
    //         "groupSize": "1",
    //         "communityService": "no",
    //         "in": "90",
    //         "out": "50",
    //         "hours": "8",
    //         "assignment": "asdfsdf"
    //     }
    // ];

    // populateTable(tData, $("#result-body"));
});