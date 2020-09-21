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

							<?php

					if (isset($_POST['basliklarda_arama_formu'])) {

						echo '<div class="row div2"><b>En Yakın Sonuçlar</b></div>';

						$aranan_baslik = yollaf($_POST['aranan_baslik']);

						$hic_baslik_yok = 0;

						$i = 0;

						$query = $db->query("SELECT * FROM basliklar WHERE baslik LIKE '%$aranan_baslik%' AND silindi = '0' ORDER BY songiritarih DESC LIMIT 50", PDO::FETCH_ASSOC);
						
						if ( $query->rowCount() ){
						
							foreach( $query as $satir ){

								$i++;

								$hic_baslik_yok = 1;
								
								$konu_id = cekf($satir['baslikid']);

								$baslikurl = cekf($satir['baslikurl']);
													
								$konuyu_acan_id = cekf($satir['baslikacanid']);

								$row = $db->query("SELECT * FROM uyeler WHERE uye_id = '{$konuyu_acan_id}'")->fetch(PDO::FETCH_ASSOC);

								$konuyu_acanin_adi = cekf($row['uye_adi']);

								$konu_baslik = strtolower(cekf($satir['baslik']));

								$cevap_adedi = cekf($satir['giriadedi']);

								$saniye = cekf($satir['basliksaniye']);

								$son_cevap_tarihi = cekf($satir['songiritarih']);

								$son_saniye = cekf($satir['songirisaniye']);

								if($son_saniye == 0){

									$ne_kadar_once = ne_kadar_once($su_an,$saniye);

								}else{

									$ne_kadar_once = ne_kadar_once($su_an,$son_saniye);

								}

				?>

								<a name="<?php echo $a; ?>">

									<div class="row div2">

										<a href="http://kurtsozluk.net/baslik.php?<?php echo $baslikurl; ?>_<?php echo $konu_id; ?>">

											<strong><?php echo $konu_baslik; ?> </strong> <small>(<?php echo $cevap_adedi; ?>)</small>

										</a>

									</div>

								</a>

				<?php

							}
						}

						if ($hic_baslik_yok == 0) {

							$urle_baslik = str_replace(" ", "_", $aranan_baslik);
							
							header("Location:http://kurtsozluk.net/baslikac.php?baslik=".$urle_baslik);

							exit();

						}

					}else{

						header("Location :http://kurtsozluk.net/index.php");

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