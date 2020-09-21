<?php

	include 'fonksiyonlar/bagla.php';

	if ($girdi != 1) {
		
		header("Location:cikis.php");

		exit();

	}

?>
<!DOCTYPE html>

<html>

<head>

	<title></title>

	<meta name="keywords" content="kurt, sözlük" />

	<?php include 'template/head.php'; ?>

</head>

<body>

	<?php include 'template/banner.php'; ?>

	<div class="container-fluid"> 
		
		<div class="row">
			
			<div class="col-md-4">
				
				<div class="div2">
					
					<form action="" method="POST">
						
						<textarea type="text" name="yapilacakmetin" class="form-control" placeholder="Yapılacak işi giriniz."></textarea><br/>

						<button type="submit" name="yapilacakyolla" class="btn btn-block" style="background-color: <?php echo $uye_renk; ?>"><b style="color: white;">Yolla</b></button>

					</form>

				</div>

			</div>

		</div> 

	</div>

	<?php include 'template/js.php'; ?>

</body>

</html>