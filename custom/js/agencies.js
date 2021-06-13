var table;
$(document).ready(function () {
    bindingAgenciesTable();
});

function bindingAgenciesTable() {
    $.ajax({
        url: '/php_action/agencyFetch.php',
        type: 'get',
        dataType: 'json',
        success: function (response) {
            var { agencies } = response || {};

            if (agencies) {
                var table = $('#table_agencies tbody');
                table.empty();

                $.each(agencies, function (idx, elem) {
                    var td = ``;

                    table.append(`
                        <tr>
                            <td>${_.get(elem, 'id', '')} </th>
                            <td>${_.get(elem, 'name') || ''} </th>
                            <td>${_.get(elem, 'province') || ''} </th>
                        </tr>
                    `);
                });

                $('#table_agencies').DataTable({
                    destroy: true,
                    responsive: true,
                    ordering: false
                });
            }
        } // /success function
    });
}
