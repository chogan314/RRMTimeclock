$(function() {
    var popup = $('#popup');
    var popupForm = $('#popup-form');

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
                    $('#popup-record-id').val($('#row-' + index + '-id').text());
                    $('#popup-assignment-name-input').val($('#row-' + index + '-assignment-name').text());

                    clearPopup();
                    popup.css('display', 'block');
                });
            })(rowCount);

            rowCount++;
        }
        bodyToReplace.replaceWith($(tbody));
    }

    function refreshTable() {   
        $.ajax({
            type: 'GET',
            url: "assignments-view-get.php"
        }).done(function(response) {
            var tData = JSON.parse(response);
            populateTable(tData, $("#result-body"));
        }).fail(function(data) {
            // todo
        });
    }

    function createRecord() {
        var formData = $("#create-form").serialize();

        $.ajax({
            type: 'POST',
            url: $("#create-form").attr('action'),
            data: formData
        }).done(function(response) {
            refreshTable();
        }).fail(function(data) {
            // todo
        });
    }

    function clearPopup() {
        $('#popup-record-id').val();
        $('#popup-assignment-name-input').val();
    }

    $(popupForm).submit(function(event) {
        event.preventDefault();
        var formData = $(popupForm).serialize();

        $.ajax({
            type: 'POST',
            url: $(popupForm).attr('action'),
            data: formData
        }).done(function(response) {
            popup.css('display', 'none');
            clearPopup();
            refreshTable();
        }).fail(function(data) {
            // todo
        });
    });

    $("#create-form").submit(function(event) {
        event.preventDefault();
        createRecord();
    });

    $("#popup-cancel-button").click(function() {
        popup.css('display', 'none');
        clearPopup();
    });

    refreshTable();
});