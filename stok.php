<?php

	include 'fonksiyonlar/bagla.php';

	if (isset($_POST['giris_formu'])) {
		
		$kullanici_adi = $_POST['kullanici_adi'];

		$kullanici_sifre = $_POST['kullanici_sifre'];

		$md5li_sifre = md5($kullanici_sifre);

		if (empty($kullanici_adi) === true) {

			$hata = '<div class="alert alert-danger" role="alert">Kullanıcı adı kısmını boş bıraktınız.</div>';

		}elseif(empty($kullanici_sifre) === true){

			$hata = '<div class="alert alert-danger" role="alert">Şifre kısmını boş bıraktınız.</div>';
		}

		elseif(stokgiris($kullanici_adi,$md5li_sifre) === false){

			$hata = '<div class="alert alert-danger" role="alert">Kullanıcı veya şifreyi yanlış girdiniz.</div>';
		
		}else{

			if (is_numeric(stokgiris($kullanici_adi,$md5li_sifre)) === true) {
				
				$_SESSION['kullanici_id'] = giris($kullanici_adi,$md5li_sifre);

				header("Location: stok.php");

				exit();

			}
			
		}

	}

?>

<!DOCTYPE html>

<html>

<head>

	<title>Stok Programı</title>

	<?php include 'template/head.php'; ?>

</head>

<body>

	<div class="container-fluid" style="height: 100px;"></div>

	<div class="container">

		<div class="row">

			<div class="col-md-4"></div>

			<div class="col-md-4">

				<div class="div2" style="text-align: center;">

					<form action="" method="POST">

						<h3><b>Giriş</b></h3><br/>

						<input class="form-control" type="text" name="kullanici_adi" placeholder="Kullanıcı Adı"><br/>

						<input class="form-control" type="text" name="kullanici_sifre" placeholder="Şifre"><br/>

						<button class="btn btn-primary btn-block" name="giris_formu" type="submit">Giriş</button>

					</form>

				</div>

			</div>

			<div class="col-md-4"></div>

		</div>

	</div>

</body>

</html>