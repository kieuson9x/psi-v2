<div class="col-md-3 col-lg-2 navbar-collapse offcanvas-collapse bg-light pl-0" id="sidebar" role="navigation">
    <ul class="nav flex-column sticky-top">
        <li class="nav-item navigation-item hidden">
            <span class="nav-link">Xin chào <?php echo $_SESSION['full_name'] ?? ''; ?></span>
        </li>

        <!-- <li class="nav-item">
            <a class="nav-link" href="<?php echo URLROOT; ?>">
                <i class="material-icons" style="font-size: 13px">
                    dashboard
                </i>
                Trang chủ
            </a>
        </li> -->

        <?php if (isset($_SESSION['user_id'])) : ?>
            <?php
            $employee_level = $_SESSION['employee_level'] ?? '';

            if ($employee_level == 'Quản lý khu vực' || $employee_level == 'Admin') :
            ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo URLROOT . "/agencies.php" ?>">
                        <i class="material-icons" style="font-size: 13px">business_center</i>
                        Danh sách đại lý
                    </a>
                </li>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['user_id'])) : ?>
            <?php
            $employee_level = $_SESSION['employee_level'] ?? '';

            if ($employee_level == 'Tài chính' || $employee_level == 'Admin') :
            ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo URLROOT . "/inventories.php" ?>" id="nav-link-inventory">
                        <i class="material-icons" style="font-size: 13px">inventory_2</i>
                        Bảng nhập tồn kho
                    </a>
                </li>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['user_id'])) : ?>
            <?php
            $employee_level = $_SESSION['employee_level'] ?? '';

            if ($employee_level == 'Quản lý khu vực' || $employee_level == 'Admin') :
            ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo URLROOT . "/employee-sales.php" ?>"" id=" nav-link-employee-sales">
                        <i class="material-icons" style="font-size: 13px">inventory_2</i>
                        Bảng nhập nhập số sales
                    </a>
                </li>
            <?php endif; ?>
        <?php endif; ?>

        <li class="nav-item navigation-item hidden" id="topNavLogout"><a href="logout.php" class="nav-link"> <i class="glyphicon glyphicon-log-out"></i> Đăng xuất</a></li>
        </li>
    </ul>
</div>