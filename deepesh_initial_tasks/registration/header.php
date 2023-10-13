<?php
    $title = "";
    $url_array =  explode('/', $_SERVER['REQUEST_URI']) ;
    $url = end($url_array);

    if (strpos($url, "view_reg_records.php") !== false) {
        $url = "view_reg_records.php";
    }
    
    switch($url) {
        case "registration.php":
            $title = "Registration";
            break;
        case "view_reg_records.php":
            $title = "View Records";
            break;
        default:
            $title = "My Application";
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<title><?php echo $title; ?></title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>

<body>
	<div class="p-5 bg-primary text-white text-center">
		<h1><?php echo $title; ?></h1>
	</div>

	<nav class="navbar navbar-expand-sm bg-dark navbar-dark">
		<div class="container-fluid">
			<ul class="navbar-nav">
				<li class="nav-item">
					<a class="nav-link" href="<?php echo "registration.php"; ?>">Registration</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="<?php echo "view_reg_records.php?view_reg_records=true"; ?>">View Reg Records</a>
				</li>
				<!-- <li class="nav-item">
					<a class="nav-link" href="#">Link</a>
				</li>
				<li class="nav-item">
					<a class="nav-link disabled" href="#">Disabled</a>
				</li> -->
			</ul>
		</div>
	</nav>