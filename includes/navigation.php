<nav class="navbar fixed-top navbar-expand-md navbar-dark bg-primary mb-3">
    <div class="flex-row d-flex">
        <button type="button" class="navbar-toggler mr-2 " data-toggle="offcanvas" title="Toggle responsive left sidebar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand" href="#" title="Free Bootstrap 4 Admin Template">Kangaroo - PSI</a>
    </div>

    <div class="navbar-collapse collapse" id="collapsingNavbar">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <span class="nav-link">Xin chào <?php echo $_SESSION['full_name'] ?? ''; ?></span>
            </li>
            <li class="nav-item">
                <span class="nav-link"> | </span>
            </li>
            <li class="nav-item" id="topNavLogout"><a href="logout.php" class="nav-link"> <i class="glyphicon glyphicon-log-out"></i> Đăng xuất</a></li>
            </li>
        </ul>
    </div>
</nav>