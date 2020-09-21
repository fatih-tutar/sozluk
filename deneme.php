<?php

	include 'fonksiyonlar/bagla.php'; 	

?>

<!doctype html>

<html>
	
	<head>

		<title>Kurt Sözlük</title>

		<meta name="description" content="Her konuda başlıklar aç. Reklam gelirini yazarlarıyla paylaşan sözlük." />

		<meta name="keywords" content="kurt, sözlük" />

		<?php include 'template/head.php'; ?>

	</head>

	<body>

		<?php include 'template/banner.php'; ?>

		<?php

		$degisken = "m,u,s,t,a,f,a,tanrıverdicoderler"; 

		while (strstr($degisken, "m") === false) {
			
			echo "string";

		}

		?>

		<?php include 'template/jscss.html'; ?>

	</body>

</html>