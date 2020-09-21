<?php

	include 'fonksiyonlar/bagla.php';

// YÖNLENDİRMELER

	if ($girdi == 1) {

//duyuru yolla

		if (isset($_POST['yolla'])) {
			
			$duyuru_metin = yollaf($_POST['duyuru_metin']);

			$duyuru_yazan = yollaf($_POST['duyuru_yazan']);

			$query = $db->prepare("INSERT INTO duyurular SET duyuru_yazan = ?, duyuru_metin = ?, duyuru_tarih = ?");

			$duyuru_ekle = $query->execute(array($duyuru_yazan, $duyuru_metin, $now));

			$query = $db->prepare("UPDATE uyeler SET uye_duyuru = ?"); 

			$duyuru_guncelle = $query->execute(array('1'));

			header("Location: http://kurtsozluk.net/duyurular.php");

			exit();

		}

		$query = $db->prepare("UPDATE uyeler SET uye_duyuru = ? WHERE uye_id = ?"); 

		$uye_duyuru_guncelle = $query->execute(array('0',$uye_id));

		$uye_duyuru = 0;

//DUYURU GÜNCELLEME

		if (isset($_POST['duzenle'])) {
			
			$duyuru_metin = yollaf($_POST['duyurumetni']);

			$duyuru_id = yollaf($_POST['duyuruid']);

			$query = $db->prepare("UPDATE duyurular SET duyuru_metin = ? WHERE duyuru_id = ?"); 

			$guncelle = $query->execute(array($duyuru_metin,$duyuru_id));

			header("Location:http://kurtsozluk.net/duyurular.php");

			exit();

		}
		
	}else{

		header("Location: http://kurtsozluk.net/index.php");

		exit();

	}

//GUNCELLEMELER

	$sd = $db->query("SELECT * FROM duyurular ORDER BY duyuru_id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);

	$sonduyuruid = cekf($sd['duyuru_id']);

	$query = $db->prepare("UPDATE uyeler SET kaldigiduyuru = ? WHERE uye_id = ?"); 

	$guncelle = $query->execute(array($sonduyuruid,$uye_id));

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

							<?php if($uye_id == '1' && $girdi == '1'){ ?>

							<div class="row div2">

								<form action="" method="POST">
									
									<div class="form-group">
										
										<textarea class="form-control" name="duyuru_metin"></textarea>

										<input type="hidden" name="duyuru_yazan" value="<?php echo $uye_id; ?>">

									</div>

									<div class="form-group">
										
										<input type="submit" name="yolla">

									</div>

								</form>

							</div>

						<?php } ?>		

							<div class="row div2">

								<div class="col-md-12" style="text-align: center;"><b>DUYURULAR</b></div>

							</div>	
						
						<?php

							$i = 0;

							$query = $db->query("SELECT * FROM duyurular ORDER BY duyuru_tarih DESC LIMIT 20", PDO::FETCH_ASSOC);

							if ( $query->rowCount() ){

								foreach( $query as $row ){

									$i++;

									$duyuru_id = cekf($row['duyuru_id']);

									if ($duyuru_id == $uye_kaldigiduyuru) {
										
										include 'template/masthead.php';
									}

									$duyuru_metin = cekf($row['duyuru_metin']);

									$duyuru_metin = bkz($duyuru_metin);

									$duyuru_metin = res($duyuru_metin);

									$duyuru_metin = url($duyuru_metin);

									$duyuru_metin = you($duyuru_metin);

									$duyuru_metin = dai($duyuru_metin);

									$duyuru_metin = nl2br($duyuru_metin);

									$duyuru_tarih = cekf($row['duyuru_tarih']);

									$timestamp = strtotime($duyuru_tarih);

									$duyuru_tarih = date("d m Y", $timestamp);

									$duyuru_yazan = cekf($row['duyuru_yazan']);

									$dyac = $db->query("SELECT * FROM uyeler WHERE uye_id = '{$duyuru_yazan}'")->fetch(PDO::FETCH_ASSOC);

									$duyuru_yazan_adi = cekf($dyac['uye_adi']);

						?>

						<?php  	if ($duyuru_id > $uye_kaldigiduyuru) { ?>

								<div class="row div2" style="background-color: <?php echo $uye_renk; ?>;">
									
									<p style="color: white;"><?php echo $duyuru_metin; ?><br/><?php echo $duyuru_tarih; ?></p>

									<?php if($uye_id == '1' && $girdi == '1'){ ?>

										<br/>

										<a href="#" title="Düzenle" onclick="return false" onmousedown="javascript:ackapa('ddd_<?php echo $duyuru_id; ?>');"><button class="btn btn-default">Düzenle</button></a>

										<div id="ddd_<?php echo $duyuru_id; ?>" style="display: none;">

											<form action="" method="POST">

												<textarea name="duyurumetni" class="form-control"><?php echo $duyuru_metin; ?></textarea>

												<input type="hidden" name="duyuruid" value="<?php echo $duyuru_id; ?>">
												
												<button type="submit" name="duzenle" class="btn btn-default">Düzenle</button>

											</form>

										</div>

									<?php } ?>

								</div>

						<?php	}else{ ?>

								<div class="row div2">
									
									<p><?php echo $duyuru_metin; ?><br/><?php echo $duyuru_tarih; ?></p>

									<?php if($uye_id == '1' && $girdi == '1'){ ?>

										<a href="#" title="Düzenle" onclick="return false" onmousedown="javascript:ackapa('ddd_<?php echo $duyuru_id; ?>');"><button class="btn btn-default">Düzenle</button></a>

										<div id="ddd_<?php echo $duyuru_id; ?>" style="display: none;">

											<form action="" method="POST">

												<textarea name="duyurumetni" class="form-control"><?php echo $duyuru_metin; ?></textarea>

												<input type="hidden" name="duyuruid" value="<?php echo $duyuru_id; ?>">
												
												<button type="submit" name="duzenle" class="btn btn-default">Düzenle</button>

											</form>

										</div>

									<?php } ?>

								</div>

						<?php 	}  ?>

						

					<?php	}

							} ?>

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