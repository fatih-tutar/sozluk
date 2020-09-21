<?php

	include 'fonksiyonlar/bagla.php';

	if ($girdi == '1') { header("Location:http://kurtsozluk.net/index.php"); }

	if (isset($_GET['basarili'])) {

		$hata = '<br/><div class="alert alert-success" role="alert">Giriş yapabilirsin.</div>';

	}

	if (isset($_GET['aktif'])) {

		$hata = '<br/><div class="alert alert-success" role="alert">Hesabınız başarıyla aktif edildi. Giriş yapabilirsiniz.</div>';

	}

	if (isset($_POST['giris']) === true && empty($_POST['giris']) === false) {
		
		$k_adi = yollaf($_POST['k_adi']);

		$sifre = yollaf($_POST['sifre']);

		$sifreli = md5($sifre);



		$utc = $db->query("SELECT * FROM uyeler WHERE uye_adi = '{$k_adi}' AND uye_sifre = '{$sifreli}'")->fetch(PDO::FETCH_ASSOC);

		$u_tipi = cekf($utc['uye_tipi']);



		$cek = $db->query("SELECT * FROM ceza WHERE cezali_adi = '{$k_adi}' AND ceza_saniye > '{$su_an}' ORDER BY ceza_id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
		
		

		if($cek){



			$ceza_sebebi = cekf($cek['ceza_sebebi']);

			$ceza_saniye = cekf($cek['ceza_saniye']);

			$ceza_saniye = $ceza_saniye + 3600;

			$ceza_bitis =  date("d-m-Y H:i:s",$ceza_saniye);

			$giriid = cekf($cek['girdi_id']);

			$girimetnicek = $db->query("SELECT * FROM giriler WHERE giriid = '{$giriid}'")->fetch(PDO::FETCH_ASSOC);

			$girimetin = $girimetnicek['girimetin'];

			$sorgu = $db->prepare("SELECT COUNT(*) FROM ceza WHERE cezali_adi = '{$k_adi}' AND ceza_saniye > '{$su_an}' ORDER BY ceza_id DESC LIMIT 1");
			
			$sorgu->execute();
			
			$ceza_sayi = $sorgu->fetchColumn();
			
		}else{ 

			$ceza_sayi = 0;

		}

		if ($u_tipi == '4') {

			$hata = '<br/><div class="alert alert-danger" role="alert">Hesabın süresiz silindi.</div>';

		}elseif ($ceza_sayi != '0') {

			$hata = '<br/><div class="alert alert-danger" role="alert">Uzaklaştırmaya Sebep Olan Giri<p style="color:black;">'.$girimetin.'</p><hr/>Uzaklaştırma Sebebi<p style="color:black;">'.$ceza_sebebi.'</p><hr/>Uzaklaştırma bitiş zamanı<p style="color:black;">'.$ceza_bitis.'</p></div>';

		}elseif (empty($k_adi) === true) {

			$hata = '<br/><div class="alert alert-danger" role="alert">Kullanıcı adı kısmını boş bıraktınız.</div>';

		}elseif(empty($sifre) === true){

			$hata = '<br/><div class="alert alert-danger" role="alert">Şifre kısmını boş bıraktınız.</div>';
		}

		elseif(giris($k_adi,$sifreli) === false){

			$hata = '<br/><div class="alert alert-danger" role="alert">Kullanıcı adını veya şifreyi yanlış girdiniz.</div>';
		}

		elseif (aktif_mi($k_adi) === false) {

			$hata = '<br/><div class="alert alert-danger" role="alert">Üye olma sayfasından size yolladığımız aktivasyon koduyla üyeliğinizi aktifleştiriniz.</div>';
			
		}else{

			if (is_numeric(giris($k_adi,$sifreli)) === true) {
				
				$_SESSION['uye_id'] = giris($k_adi,$sifreli);

				setcookie("gska",$k_adi,time()+259200);

				setcookie("gss",$sifreli,time()+259200);

				$query = $db->prepare("UPDATE uyeler SET son_giris_tarihi = ? WHERE uye_adi = ?"); 

				$guncelle = $query->execute(array($now,$k_adi));

				header("Location: http://kurtsozluk.net/index.php");

				exit();

			}
			
		}

	}

	if (isset($_GET['d']) === true) {

		$hata = '<br/><div class="alert alert-info" role="alert">Şifreniz başarıyla değiştirildi.<br>Yeni şifrenizle giriş yapabilirsiniz.</div>';

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

							<div class="div2" style="text-align: center;">

								<?php echo $hata; ?>

								<h4><strong>&nbsp;Giriş</strong></h4>

								<form action="" method="POST" class="form-horizontal">
								  <div class="form-group">
								    <div class="col-md-12">
								      <input name="k_adi" type="text" class="form-control" id="inputEmail3" placeholder="Kullanıcı Adı">
								    </div>
								  </div>
								  <div class="form-group">
								    <div class="col-md-12">
								      <input name="sifre" type="password" class="form-control" id="inputPassword3" placeholder="Şifre">
								    </div>
								  </div>
								  <div class="form-group">
								    <div class="col-md-12">
								          <a href="http://kurtsozluk.net/uye_ol.php">&nbsp;Üye değilsen hemen tıkla.</a>
								    </div>
								  </div>
								  <div class="form-group">
								    <div class="col-md-12">
								          <a href="http://kurtsozluk.net/unuttum.php">&nbsp;Şifremi unuttum.</a>
								    </div>
								  </div>
								  <div class="form-group">
								    <div class="col-md-12">
								      <button type="submit" name="giris" value=" " class="btn btn-default btn-block">Giriş</button>
								    </div>
								  </div>
								</form>
							</div>

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