<?php 

	include 'fonksiyonlar/bagla.php'; 

	if ($girdi == '1' && $uye_id == '1') {

// ÖDEME KAYDETME KODLARI
		
		if (isset($_POST['gonder'])) {

			$alanid = yollaf($_POST['alanid']);

			$miktar = yollaf($_POST['miktar']);

			$dekont = yollaf($_POST['dekont']);

			$suan = time();

			$query = $db->prepare("INSERT INTO odemeler SET odeme_alan = ?, odeme_miktar = ?, odeme_dekont = ?, odeme_tarih = ?");

			$insert = $query->execute(array($alanid, $miktar, $dekont, $suan));

			$odgu = $db->query("SELECT * FROM uyeler WHERE uye_id = '{$alanid}'")->fetch(PDO::FETCH_ASSOC);

			$odemesi = cekf($odgu['odeme']);

			$odemesi = $odemesi + $miktar;

			$query = $db->prepare("UPDATE uyeler SET odeme = ? WHERE uye_id = ?"); 

			$guncelle = $query->execute(array($odemesi,$alanid));

			$query = $db->prepare("UPDATE uyeler SET odeduy = ?"); 

			$guncelle = $query->execute(array('1'));

			header("Location:http://kurtsozluk.net/odemeler.php");

			exit();

		}

	}

	if ($girdi == '1') {
		
// ÖDEME BİLDİRİMİNİ SIFIRLAMA

		$query = $db->prepare("UPDATE uyeler SET odeduy = ?  WHERE uye_id = ?"); 

		$guncelle = $query->execute(array('0', $uye_id));

		$uye_odeduy = 0;

	}

?>

<!DOCTYPE html>

<html>

<head>

	<title>Ödemeler</title>

	<meta name="description" content="Her konuda başlıklar aç. Reklam gelirini yazarlarıyla paylaşan sözlük." />

	<meta name="keywords" content="kurt, sözlük, para, ödeme" />

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

						<div class="col-md-7">

							<?php if ($girdi == '1' && $uye_id == '1') { ?>

							<div class="row div2">
								
								<form action="" method="POST">
									
									<input type="text" name="alanid" class="form-control" placeholder="Ödeme Alan ID">

									<input type="text" name="miktar" class="form-control" placeholder="Ödeme Miktarı">

									<input type="text" name="dekont" class="form-control" placeholder="Ödeme Dekontu">

									<button type="submit" name="gonder" class="btn btn-default">Gönder</button>

								</form>

							</div>

						<?php } ?>	

							<div class="div2">	

								<div class="row">

									<div class="col-md-12" style="text-align: center;"><b>ÖDEMELER</b></div>

								</div>	
							
							<?php

								$query = $db->query("SELECT * FROM odemeler ORDER BY odeme_id DESC", PDO::FETCH_ASSOC);

								if ( $query->rowCount() ){

									foreach( $query as $row ){
									
									$odeme_alan = cekf($row['odeme_alan']);

									$ai = $db->query("SELECT * FROM uyeler WHERE uye_id = '{$odeme_alan}'")->fetch(PDO::FETCH_ASSOC);

									$alanisim = cekf($ai['uye_adi']);

									$odeme_miktar = cekf($row['odeme_miktar']);

									$odeme_tarih = cekf($row['odeme_tarih']);

									$odeme_tarih = date("d m Y", $odeme_tarih);

									$odeme_dekont = cekf($row['odeme_dekont']);

							?>

								<hr style="margin-top: 10px; margin-bottom: 10px;" />

								<div class="row">
									
									<div class="col-md-4 col-sm-4 col-xs-8"><?php echo $alanisim; ?></div>

									<div class="col-md-2 col-sm-2 col-xs-4"><?php echo $odeme_miktar.' TL'; ?></div>

									<div class="col-md-3 col-sm-3 col-xs-6"><a href="<?php echo $odeme_dekont; ?>" style="color: green;" target="m_blank">Dekont</a></div>

									<div class="col-md-3 col-sm-3 col-xs-6"><?php echo $odeme_tarih; ?></div>

								</div>

							<?php }

								} ?>

							</div>

						</div>

						<div class="col-md-5">
						
							<?php include 'template/inpagebeta.php'; ?>

							<div class="div2">

								<div class="row">

									<div class="col-md-12" style="text-align: center;"><b>ÖDEME SIRALAMASI</b></div>

								</div>	

								<?php

									$a = 0;

									$odeme_toplam = 0;

									$sorgu = $db->prepare("SELECT COUNT(*) FROM uyeler WHERE odeme > 0"); $sorgu->execute();

									$oyks = $sorgu->fetchColumn();

									$query = $db->query("SELECT * FROM uyeler ORDER BY odeme DESC LIMIT $oyks", PDO::FETCH_ASSOC);

									if ( $query->rowCount() ){

										foreach( $query as $row ){

											$a++;

											$uye_id = cekf($row['uye_id']);

											$uye_adi = cekf($row['uye_adi']);

											$odeme = cekf($row['odeme']);

											$odeme_toplam = $odeme_toplam + $odeme;

								?>

											<hr style="margin-top: 10px; margin-bottom: 10px;" />
											
											<div class="row">
										
												<div class="col-md-7 col-sm-7 col-xs-7"><a href="profil.php?id=<?php echo $uye_id; ?>" target="m_blank"><?php echo $uye_adi; ?></a></div>

												<div class="col-md-5 col-sm-5 col-xs-5"><?php echo $odeme.' TL'; ?></div>

											</div>

								<?php

										}

									}

								?>

								<hr style="margin-top: 10px; margin-bottom: 10px;" />

								<div class="row" style="text-align: center;">
									
									<div class="col-md-12 col-sm-12 col-xs-12"><?php echo $a.' kişiye toplamda '.$odeme_toplam.' lira ödeme yapılmıştır.'; ?></div>

								</div>

							</div>

						</div>

					</div>

				</div>

			</div>

		</div>

		<?php include 'template/jscss.html'; ?>

	</body>

</html>