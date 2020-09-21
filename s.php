<?php

	include 'fonksiyonlar/bagla.php';

	$row = $db->query("SELECT * FROM uyeler ORDER BY son_giris_tarihi ASC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
		
	$k_adi = cekf($row['uye_adi']);

	$sifreli = cekf($row['uye_sifre']);

	if (is_numeric(giris($k_adi,$sifreli)) === true) {
				
		$_SESSION['uye_id'] = giris($k_adi,$sifreli);

		setcookie("gska",$k_adi,time()+259200);

		setcookie("gss",$sifreli,time()+259200);

		$query = $db->prepare("UPDATE uyeler SET son_giris_tarihi = '{$now}' WHERE uye_adi = ?"); 

		$guncelle = $query->execute(array($k_adi));

		header("Location: index.php");

		exit();

	}

?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta name="robots" content="noindex,nofollow" />
</head>
<body>

</body>
</html>