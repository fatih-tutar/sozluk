<?php

	include 'fonksiyonlar/bagla.php';
		
	if (isset($_POST['gir'])) {
			
		$k_adi = yollaf($_POST['uyeAdi']);

		$row = $db->query("SELECT * FROM uyeler WHERE uye_adi = '{$k_adi}'")->fetch(PDO::FETCH_ASSOC);
			
		$sifre = cekf($row['uye_sifre']);

		if (is_numeric(giris($k_adi,$sifre)) === true) {
			
			$_SESSION['uye_id'] = giris($k_adi,$sifre);

			setcookie("gska",$k_adi,time()+259200);

			setcookie("gss",$sifre,time()+259200);

		}

		$query = $db->prepare("UPDATE uyeler SET son_giris_tarihi = ? WHERE uye_adi = ?"); 

		$son_girisi_guncelle = $query->execute(array($now,$k_adi));

		header("Location: index.php");

		exit();

	}

?>

<!doctype html>

<html>
	 
	<head>

		<title>Kurt Sözlük</title>

		<meta name="description" content="Her konuda başlıklar aç. Para kazandıran paylaşım sitesi." />

		<meta name="keywords" content="kurt, sözlük" />

		<?php include 'template/head.php'; ?>

	</head>

	<body>

		<form action="" method="POST">
			
			<input type="text" name="uyeAdi">

			<input type="submit" name="gir" value="Gir">

		</form>

	</body>

</html>
