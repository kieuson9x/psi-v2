var table;
$(document).ready(function () {
    // top nav bar
    $('#nav-link-inventory').addClass('active');

    bindingInventories(new Date().getFullYear());

    $('#btnFilterInventories')
        .unbind('click')
        .bind('click', function () {
            var currentYear = $('#year-selection').val();
            bindingInventories(currentYear);
        });

    $('#product-selection').select2({
        placeholder: 'Chọn sản phẩm',
        ajax: {
            url: '/php_action/searchProducts.php',
            dataType: 'json',
            delay: 250,
            type: 'POST',
            data: function (data) {
                return {
                    query: data.term // search term
                };
            },
            processResults: function (response) {
                return {
                    results: response
                };
            }
        }
    });

    $('#add_inventory').on('click', function (e) {
        e.preventDefault();

        //Fetch form to apply custom Bootstrap validation
        var form = $('form[name=add_inventory]');
        var formData = form.serialize();
        var data = $.deparam(formData);

        var { months } = data || {};

        if (form[0].checkValidity() === false) {
            e.stopPropagation();
        }

        if (_.isEmpty(months)) {
            $('#month-selection input').each(function (idx, obj) {
                obj.setCustomValidity('Months should be selected');
            });
            e.stopPropagation();
            // .setCustomValidity("Passwords must match");
        }

        form.addClass('was-validated');

        if (form[0].checkValidity() && !_.isEmpty(months)) {
            $.ajax({
                url: '/php_action/inventoryCreate.php',
                type: 'POST',
                data: data,
                success: function (response) {
                    console.log('resp', response);
                    var response = JSON.parse(response);
                    if (response.success) {
                        location.reload();
                        toastr.success('Cập nhật thành công!');
                        $('form[name=add_inventory]').trigger('reset');
                    } else {
                        toastr.error('Cập nhật không thành công!');
                    }
                }
            });
        }
    });

    $('#cancel_add_inventory').on('click', function (e) {
        e.preventDefault();
        $('form[name=add_inventory]').trigger('reset');
    });
});

function bindingInventories(year) {
    $.ajax({
        url: '/php_action/inventoryFetch.php',
        type: 'get',
        data: {
            year: year
        },
        dataType: 'json',
        success: function (response) {
            var { inventories, year } = response || {};

            if (year) {
                $('#year-selection option, #year-selection_create option').each(function () {
                    if ($(this).val() == year) {
                        $(this).attr('selected', 'selected');
                    }
                });
            }

            if (inventories) {
                var table = $('#table_inventories tbody');
                table.empty();

                $.each(inventories, function (idx, elem) {
                    var td = ``;

                    for (var i = 0; i < 12; i++) {
                        td += `
                    <td data-type="text" data-state="purchase" data-name="${i + 1}" data-pk="${_.get(
                            elem,
                            `${i + 1}.product_id`
                        )}">
                        ${
                            _.get(
                                _.find(elem, function (o) {
                                    return parseInt(o.month) === i + 1;
                                }),
                                'number_of_imported_goods'
                            ) || 0
                        }
                    </td>

                    <td class="not-editable" data-state="sale">
                        ${
                            _.get(
                                _.find(elem, function (o) {
                                    return parseInt(o.month) === i + 1;
                                }),
                                'number_of_sale_goods'
                            ) || 0
                        }
                    </td>

                    <td data-type="text" data-state="inventory" data-name="${i + 1}" data-pk="${_.get(
                            elem,
                            `${i + 1}.product_id`
                        )}">
                        ${
                            _.get(
                                _.find(elem, function (o) {
                                    return parseInt(o.month) === i + 1;
                                }),
                                'number_of_remaining_goods'
                            ) || 0
                        }
                    </td>
                    `;
                    }

                    table.append(`
                <tr>
                    <td class="not-editable">${_.get(elem, '0.product_id', '')} </th>
                    <td class="not-editable">${_.get(elem, '0.name') || 0} </th>
                    ${td}
                </tr>
              `);
                });

                $('#table_inventories').DataTable({
                    responsive: true,
                    ordering: false
                });

                $('#table_inventories tbody tr td:not(.not-editable)').editable({
                    send: 'always',
                    type: 'text',
                    url: '/php_action/inventoryUpdate.php',
                    params: function (params) {
                        var state = $(this).attr('data-state');
                        params.year = 2021;
                        params.state = state;

                        return params;
                    },
                    validate: function (value) {
                        if (!Number.isInteger(parseFloat(value))) {
                            return 'Chỉ nhập số nguyên';
                        }
                    },
                    success: function (response, newValue) {
                        if (response && response.success) {
                            toastr.success('Cập nhật thành công!');
                        } else {
                            toastr.error('Cập nhật không thành công!');
                        }
                    },
                    ajaxOptions: {
                        type: 'POST',
                        dataType: 'json'
                    }
                });
            }
        } // /success function
    });
}
