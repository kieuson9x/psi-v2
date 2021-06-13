var table;
$(document).ready(function () {
    // top nav bar
    $('#nav-link-employee-sales').addClass('active');

    bindingEmployeeSales(new Date().getFullYear());

    $('#btnFilterEmployeeSales')
        .unbind('click')
        .bind('click', function () {
            var currentYear = $('#year-selection').val();
            bindingEmployeeSales(currentYear);
        });

    $('#product-selection').select2({
        placeholder: 'Chọn sản phẩm',
        ajax: {
            url: '/php_action/productSearch.php',
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

    $('#add_agency_sale').on('click', function (e) {
        e.preventDefault();

        //Fetch form to apply custom Bootstrap validation
        var form = $('form[name=add_agency_sales]');
        var formData = form.serialize();
        var data = $.deparam(formData);

        var { months } = data || {};

        if (form[0].checkValidity() === false) {
            e.stopPropagation();
        }

        if (!months || _.isEmpty(months)) {
            $('#month-selection input').each(function (idx, obj) {
                obj.setCustomValidity('Months should be selected');
            });
            e.stopPropagation();
        }

        form.addClass('was-validated');

        if (form[0].checkValidity() && !_.isEmpty(months)) {
            $.ajax({
                url: '/php_action/employeeSaleCreate.php',
                type: 'POST',
                data: data,
                success: function (response) {
                    console.log('resp', response);
                    var response = JSON.parse(response);
                    if (response.success) {
                        location.reload();
                        toastr.success('Cập nhật thành công!');
                        $('form[name=add_agency_sales]').trigger('reset');
                    } else {
                        toastr.error('Cập nhật không thành công!');
                    }
                }
            });
        }
    });

    $('#cancel_add_agency_sale').on('click', function (e) {
        e.preventDefault();
        $('form[name=add_agency_sales]').trigger('reset');
    });
});

function bindingEmployeeSales(year) {
    $.ajax({
        url: '/php_action/employeeSaleFetch.php',
        type: 'get',
        data: {
            year: year
        },
        dataType: 'json',
        success: function (response) {
            var { agency_sales, year, agencyOptions } = response || {};

            if (year) {
                $('#year-selection option, #year-selection_create option').each(function () {
                    if ($(this).val() == year) {
                        $(this).attr('selected', 'selected');
                    }
                });
            }

            var table = $('#table_agency_sales tbody');
            table.empty();

            if (!_.isEmpty(agency_sales)) {
                $.each(agency_sales, function (idx, sale) {
                    var td = ``;

                    for (var i = 0; i < 12; i++) {
                        td += `
                            <td data-type="text" data-state="sale" data-agency-id="${_.get(
                                sale,
                                '0.agency_id'
                            )}" data-name="${i + 1}" data-pk="${_.get(sale, '0.product_id')}">
                                ${
                                    _.get(
                                        _.find(sale, (o) => parseInt(o.month) === i + 1),
                                        'number_of_sale_goods'
                                    ) || 0
                                }
                            </td>

                    `;
                    }

                    table.append(`
                        <tr>
                            <td class="not-editable">${_.get(sale, '0.product_id', '')} </th>
                            <td class="not-editable">${_.get(sale, '0.name') || 0} </th>
                            <td class="not-editable">${_.get(
                                _.find(agencyOptions, (o) => o.value === _.get(sale, '0.agency_id')),
                                'title',
                                ''
                            )} </th>
                            ${td}
                        </tr>
              `);
                });

                $('#table_agency_sales').DataTable({
                    destroy: true,
                    responsive: true,
                    ordering: false
                });

                $('#table_agency_sales tbody tr td:not(.not-editable)').editable({
                    send: 'always',
                    type: 'text',
                    url: '/php_action/employeeSaleUpdate.php',
                    params: function (params) {
                        var state = $(this).attr('data-state');
                        var agencyId = $(this).attr('data-agency-id');
                        params.year = 2021;
                        params.state = state;
                        params.agency_id = agencyId;

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

                $('#agency-selection').select2({
                    placeholder: 'Chọn đại lý',
                    ajax: {
                        url: '/php_action/agencySearch.php',
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
            }
        } // /success function
    });
}
