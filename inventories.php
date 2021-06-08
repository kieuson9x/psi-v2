<?php require_once 'includes/header.php'; ?>

<div class="col main pt-5 mt-3 offset-md-3 offset-lg-2">
    <div class="row">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header">
                    Bảng nhập tồn kho
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label for="year" class="col-xs-2 col-form-label mr-2">Năm</label>
                        <div class="col-xs-4 mr-2">
                            <select id="year-selection" class="form-control" id="year" name="year">
                                <?php for ($i = date('Y') - 1; $i <= date('Y') + 2; $i++) : ?>
                                    <option value="<?php echo $i ?>"><?php echo $i ?></option>
                                <?php endfor ?>
                            </select>
                        </div>
                        <button id="btnFilterInventories" type="button" class="btn btn-primary mr-1 w-40  d-flex align-items-center justify-content">
                            <i class="material-icons">filter_alt</i>
                            Lọc
                        </button>

                        <div class="col-xs-4">
                            <a href="#addProductPlanModal" class="btn btn-success d-flex align-items-center justify-content " data-toggle="modal"><i class="material-icons">&#xE147;</i> <span>Thêm/Cập nhật số nhập
                                    mới</span></a>
                        </div>
                    </div>

                    <table class="table table-striped" id="table_inventories">
                        <thead>
                            <tr>
                                <td rowspan="2">ID</td>
                                <td rowspan="2">Tên vật tư</td>
                                <?php for ($i = 1; $i <= 12; $i++) : ?>
                                    <th data-editable="true" colspan="3"><?php echo "Tháng {$i}"; ?></th>

                                <?php endfor ?>
                            </tr>

                            <tr>
                                <?php for ($i = 1; $i <= 12; $i++) : ?>
                                    <th data-editable="true">P</th>
                                    <th>S</th>
                                    <th data-editable="true">I</th>
                                <?php endfor ?>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                    <!-- /table -->

                </div> <!-- /panel-body -->
            </div> <!-- /panel -->
        </div> <!-- /col-md-12 -->
    </div> <!-- /row -->
</div>

<!-- add product plan -->
<div id="addProductPlanModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <form name="add_inventory" method="POST" action="/php_action/inventoryCreate.php" class="form-horizontal">
                <div class="modal-header">
                    <h4 class="modal-title">Thêm/cập nhật số lượng nhập</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label>Sản phẩm</label>
                        <select id="product-selection" class="form-control" required name="product_id" data-live-search="true" style="width: 100%"></select>
                        <div class="invalid-feedback">
                            Trường này bắt buộc nhập!
                        </div>
                    </div>

                    <div class="form-group row">
                        <label>Năm</label>
                        <select id="year-selection_create" class="form-control" id="year" name="year" required>
                            <?php for ($i = date('Y') - 1; $i <= date('Y') + 2; $i++) : ?>
                                <option value="<?php echo $i ?>"><?php echo $i ?></option>
                            <?php endfor ?>
                        </select>
                        <div class="invalid-feedback">
                            Trường này bắt buộc nhập!
                        </div>
                    </div>

                    <div class="form-group row">
                        <label>Tháng</label>
                        <div class="form-group">
                            <?php for ($i = 1; $i <= 12; $i++) : ?>
                                <div id="month-selection" class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="months[]" value="<?php echo $i ?>">
                                    <label class="form-check-label" for="month_<?php echo $i ?>">Tháng <?php echo $i ?></label>
                                </div>
                            <?php endfor ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label>Số lượng nhập</label>
                        <input type="text" class="form-control" name="number_of_imported_goods" required>
                        <div class="invalid-feedback">
                            Trường này bắt buộc nhập!
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" id="add_inventory" class="btn btn-success" data-dismiss="modal">Thêm</button>
                    <button type="button" id="cancel_add_inventory" class="btn btn-secondary mr-1" data-dismiss="modal">Huỷ</button>
                </div>
            </form>
        </div>
    </div>

    <script src="custom/js/inventories.js"></script>

    <?php require_once 'includes/footer.php'; ?>