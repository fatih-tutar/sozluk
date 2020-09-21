<?php

	include 'fonksiyonlar/bagla.php';

//YÖNLENDİRMELER

	if ($girdi == '0') {
		
		header("Location: http://kurtsozluk.net/index.php");

	}

	if ($girdi == '1') {

		$sorgu = $db->prepare("SELECT COUNT(*) FROM ceza WHERE cezali_adi = '{$uye_adi}' AND ceza_saniye > '{$su_an}' ORDER BY ceza_id DESC LIMIT 1");
		
		$sorgu->execute();
		
		$ceza_sayi = $sorgu->fetchColumn();

		if ($ceza_sayi != '0') {

			header("Location: http://kurtsozluk.net/cikis.php");

			exit();

		}

		if (isset($_POST['bildirimsifirla'])) {

			$query = $db->prepare("UPDATE bildirimler SET bildirim_okundu = ? WHERE bildirimi_alan_id = ?"); 

			$guncelle = $query->execute(array('1',$uye_id));

			header("Location:http://kurtsozluk.net/bildirim.php");

			exit();

		}

	}

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

		<div class="container">
			
			<div class="row">
				
				<div class="col-md-3 hidden-sm hidden-xs">
					
					<?php include 'template/solframe.php'; ?>

				</div>

				<div class="col-md-9" style="padding: 0px;">

					<div class="row">

						<br/><?php include 'template/masthead.php'; ?>

					</div>

					<div class="row">

						<div class="col-md-6">

							<div style="text-align: right;">
								
								<form action="" method="POST">

									<button type="submit" name="bildirimsifirla" value=" " class="btn btn-default" style="margin-top: 5px;"><b>Bildirimleri Okundu Yap</b></button>

								</form>

							</div>

							<?php

								$query = $db->query("SELECT * FROM bildirimler WHERE bildirimi_alan_id = '{$uye_id}' AND bildirim_okundu = '0' ORDER BY bildirim_tarihi DESC LIMIT 13", PDO::FETCH_ASSOC);

								if ( $query->rowCount() ){

									foreach( $query as $satir ){

										$b_link = cekf($satir['b_link']);

										$b_link = str_replace('https://kurtsozluk', 'http://kurtsozluk', $b_link);

										$bildirim_id = cekf($satir['bildirim_id']);

										$numaras = cekf($satir['b_numara']);

										$bildir_okundu = cekf($satir['bildirim_okundu']);
										
										$bildirimi_yollayan_id = cekf($satir['bildirimi_yollayan_id']);

										$row = $db->query("SELECT * FROM uyeler WHERE uye_id = '{$bildirimi_yollayan_id}'")->fetch(PDO::FETCH_ASSOC);

										$bildirimi_yollayanin_adi = cekf($row['uye_adi']);

										$bildirim_turu = cekf($satir['bildirim_turu']);

										$bildirim_baslik_id = cekf($satir['bildirim_baslik_id']);

										$row = $db->query("SELECT * FROM basliklar WHERE baslikid = '{$bildirim_baslik_id}'")->fetch(PDO::FETCH_ASSOC);

										$bildirim_baslik_adi = cekf($row['baslik']);

							?>

							<div class="row div2" style="background-color: <?php echo $uye_renk; ?>;"><p style="color: white;">

							<?php //bildirim turlerine göre if else kısmı
								
									if ($bildirim_turu == 0) {
						
							?>
										
										<a style="text-decoration: underline; font-weight: bold; color: white;" href="http://kurtsozluk.net/profil.php?id=<?php echo $bildirimi_yollayan_id; ?>" >

											<?php echo $bildirimi_yollayanin_adi; ?>

										</a> 

										adlı üye

										<a style="text-decoration: underline; font-weight: bold; color: white;" href="<?php echo $b_link; ?>">

											<?php echo $bildirim_baslik_adi; ?>

										</a> 

										başlıklı konunuza yorum yaptı.

							<?php
									
									}
							
							?>

							<?php //bildirim turlerine göre if else kısmı
									
									if ($bildirim_turu == 1) {
							
							?>

										<a style="text-decoration: underline; font-weight: bold; color: white;" href="http://kurtsozluk.net/profil.php?id=<?php echo $bildirimi_yollayan_id; ?>"> 

											<?php echo $bildirimi_yollayanin_adi; ?>

										</a> 

										adlı üye

										<a style="text-decoration: underline; font-weight: bold; color: white;" href="<?php echo $b_link; ?>">

											<?php if(isset($bildirim_baslik_adi)){ echo $bildirim_baslik_adi;} ?>

										</a> 

										başlıklı konudaki yorumunuza cevap yazdı.

							<?php
									
									}
							
							?>

							<?php //bildirim turlerine göre if else kısmı
									
									if ($bildirim_turu == 2) {
							
							?>

										<a style="text-decoration: underline; font-weight: bold; color: white;" href="http://kurtsozluk.net/profil.php?id=<?php echo $bildirimi_yollayan_id; ?>"> 

											<?php echo $bildirimi_yollayanin_adi; ?>

										</a> 

										adlı üye

										<a style="text-decoration: underline; font-weight: bold; color: white;" href="<?php echo $b_link; ?>">

											<?php if(isset($bildirim_baslik_adi)){ echo $bildirim_baslik_adi;} ?>

										</a> 

										başlıklı konudaki cevabınıza yorum yaptı.

							<?php
									
									}
							
							?>

							</p></div>

							<?php

									}

								}
							
							?>

							<?php

								$query = $db->query("SELECT * FROM bildirimler WHERE bildirimi_alan_id = '{$uye_id}' AND bildirim_okundu = '1' ORDER BY bildirim_tarihi DESC LIMIT 50", PDO::FETCH_ASSOC);

								if ( $query->rowCount() ){

									foreach( $query as $satir ){

										$b_link = cekf($satir['b_link']);

										$b_link = str_replace('https://kurtsozluk', 'http://kurtsozluk', $b_link);

										$bildirim_id = filtrele($satir['bildirim_id']);

										$numaras = $satir['b_numara'];

										$bildir_okundu = $satir['bildirim_okundu'];
										
										$bildirimi_yollayan_id = filtrele(htmlentities($satir['bildirimi_yollayan_id']));

										$row = $db->query("SELECT * FROM uyeler WHERE uye_id = '{$bildirimi_yollayan_id}'")->fetch(PDO::FETCH_ASSOC);

										$bildirimi_yollayanin_adi = filtrele($row['uye_adi']);

										$bildirim_turu = filtrele(htmlentities($satir['bildirim_turu']));

										$bildirim_baslik_id = filtrele(htmlentities($satir['bildirim_baslik_id']));

										$row = $db->query("SELECT * FROM basliklar WHERE baslikid = '{$bildirim_baslik_id}'")->fetch(PDO::FETCH_ASSOC);

										$bildirim_baslik_adi = ($row['baslik']);

							?>

							<div class="row div2">

							<?php //bildirim turlerine göre if else kısmı
								
									if ($bildirim_turu == 0) {
						
							?>
										
										<a style="text-decoration: underline; font-weight: bold;" href="http://kurtsozluk.net/profil.php?id=<?php echo $bildirimi_yollayan_id; ?>" >

											<?php echo $bildirimi_yollayanin_adi; ?>

										</a> 

										adlı üye 

										<a style="text-decoration: underline; font-weight: bold;" href="<?php echo $b_link; ?>">

											<?php echo $bildirim_baslik_adi; ?>

										</a> 

										başlıklı konunuza yorum yaptı.

							<?php
									
									}
							
							?>

							<?php //bildirim turlerine göre if else kısmı
									
									if ($bildirim_turu == 1) {
							
							?>

										<a style="text-decoration: underline; font-weight: bold;" href="http://kurtsozluk.net/profil.php?id=<?php echo $bildirimi_yollayan_id; ?>"> 

											<?php echo $bildirimi_yollayanin_adi; ?>

										</a> 

										adlı üye

										<a style="text-decoration: underline; font-weight: bold;" href="<?php echo $b_link; ?>">

											<?php if(isset($bildirim_baslik_adi)){ echo $bildirim_baslik_adi;} ?>

										</a> 

										başlıklı konudaki yorumunuza cevap yazdı.

							<?php
									
									}
							
							?>

							<?php //bildirim turlerine göre if else kısmı
									
									if ($bildirim_turu == 2) {
							
							?>

										<a style="text-decoration: underline; font-weight: bold;" href="http://kurtsozluk.net/profil.php?id=<?php echo $bildirimi_yollayan_id; ?>"> 

											<?php echo $bildirimi_yollayanin_adi; ?>

										</a> 

										adlı üye

										<a style="text-decoration: underline; font-weight: bold;" href="<?php echo $b_link; ?>">

											<?php if(isset($bildirim_baslik_adi)){ echo $bildirim_baslik_adi;} ?>

										</a> 

										başlıklı konudaki cevabınıza yorum yaptı.

							<?php
									
									}
							
							?>

							</div>

							<?php

									}

								}
							
							?>

						</div>

						<div class="col-md-6 hidden-sm hidden-xs">
						
							<?php include 'template/inpagebeta.php'; ?>

						</div>

					</div>

				</div>

			</div>

		</div>

		<?php include 'template/jscss.html'; ?>

	</body>

</html>