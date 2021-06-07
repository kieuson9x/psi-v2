<?php
require_once 'php_action/db_connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
	header('location:  ' . URLROOT . '/dashboard.php');
}

$errors = array();

if ($_POST) {
	$username = $_POST['username'];
	$password = $_POST['password'];

	if (empty($username) || empty($password)) {
		if ($username == "") {
			$errors[] = "Tên đăng nhập bắt buộc";
		}

		if ($password == "") {
			$errors[] = "Mật khẩu bắt buộc";
		}
	} else {
		$sql = 'SELECT * FROM employees WHERE user_name = :username';
		$statement = $connect->prepare($sql);
		$statement->execute(['username' => $username]);

		if ($statement->rowCount() === 1) {
			$row = $statement->fetch(PDO::FETCH_ASSOC);
			$hashedPassword = $row['password'];

			if (password_verify($password, $hashedPassword)) {
				$_SESSION['user_id'] = $row['id'];

				$_SESSION['full_name'] = $row['full_name'];

				// Get employee level
				if (!$_SESSION['employee_level']) {
					$_SESSION['employee_level'] = getEmployeeLevel($connect, $row['level_id']);
				}

				header('location: ' . URLROOT . '/dashboard.php');
			} else {
				$errors[] = "Sai tên đăng nhập hoặc mật khẩu";
			}
		} else {
			$errors[] = "Tên đăng nhập không tồn tại";
		} // /else
	} // /else not empty username // password

} // /if $_POST

function getEmployeeLevel($connect, $levelId)
{
	$sql = 'SELECT id, name FROM employee_levels WHERE id = :levelId';
	$statement = $connect->prepare($sql);
	$statement->execute(['levelId' => $levelId]);
	$row = $statement->fetch(PDO::FETCH_ASSOC);

	return $row['name'] ?? null;
}

?>

<!DOCTYPE html>
<html>

<head>
	<title>PSI</title>

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>PSI</title>

	<!-- Bootstrap -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<link rel="stylesheet" href="//cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css" type="text/css" />
	<link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.18.2/dist/bootstrap-table.min.css" />
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/Talv/x-editable@develop/dist/bootstrap4-editable/css/bootstrap-editable.css">

	<!-- Bootstrap select css -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />

	<!-- font css -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto&display=swap">

	<!-- Toastr css -->
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" type="text/css" />

	<!-- Select 2 css -->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />

	<!-- custom css -->
	<link rel="stylesheet" href="custom/css/custom.css">
</head>

<body style="height: 100vh;" class="d-flex align-items-center justify-content-center">
	<div class="container">
		<div class="d-flex align-items-center justify-content-center">
			<div class="col-md-6">
				<div class="panel panel-info">
					<div class="panel-heading">
						<h3 class="panel-title">Đăng nhập </h3>
					</div>
					<div class="panel-body">

						<div class="messages">
							<?php if ($errors) {
								foreach ($errors as $key => $value) {
									echo '<div class="alert alert-warning" role="alert">
										<i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
									' . $value . '</div>';
								}
							} ?>
						</div>

						<form class="form-horizontal" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" id="loginForm">
							<fieldset>
								<div class="form-group">
									<label for="username" class="col-sm-4 control-label">Tên đăng nhập</label>
									<div class="col-sm-8">
										<input type="text" class="form-control" id="username" name="username" placeholder="Username" autocomplete="off" />
									</div>
								</div>
								<div class="form-group">
									<label for="password" class="col-sm-4 control-label">Mật khẩu</label>
									<div class="col-sm-8">
										<input type="password" class="form-control" id="password" name="password" placeholder="Password" autocomplete="off" />
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-offset-2 col-sm-10">
										<button type="submit" class="btn btn-default"> <i class="fa fa-sign-in" aria-hidden="true"></i> Đăng nhập</button>
									</div>
								</div>
							</fieldset>
						</form>
					</div>
					<!-- panel-body -->
				</div>
				<!-- /panel -->
			</div>
		</div>
		<!-- /row -->
	</div>
	<!-- container -->

	<!-- jQuery -->

	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

	<!-- jquery datatable -->
	<script src="//cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>

	<!-- bootstrap -->
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

	<!-- bootstrap table -->
	<script src="https://unpkg.com/bootstrap-table@1.18.2/dist/bootstrap-table.min.js"></script>
	<script src="https://unpkg.com/bootstrap-table@1.18.2/dist/extensions/editable/bootstrap-table-editable.min.js">
	</script>

	<!-- bootstrap select -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>

	<!-- dayjs -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/dayjs/1.10.4/dayjs.min.js"></script>

	<!-- Select2 -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

	<!-- Toast -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

	<!-- Custom app js -->
	<link rel="stylesheet" href="/custom/js/app.js">
</body>

</html>