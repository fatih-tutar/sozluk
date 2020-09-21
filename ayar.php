<?php

	include 'fonksiyonlar/bagla.php';

	if ($girdi = 0) {
		
		header("Location: http://kurtsozluk.net/index.php");

	}elseif($girdi = 1){

// MOTTO YOLLAMA

		if (isset($_POST['ayar_yolla'])) {

			$kullaniciadi = yollaf($_POST['kullaniciadi']);

			$kullaniciadi = strtolower($kullaniciadi);

			$konu_baslik = bosluk_sil($kullaniciadi);

			$search = array('Ç','Ğ','Ö','Ş','Ü','İ');
				
			$replace = array('ç','ğ','ö','ş','ü','i');
			
			$kullaniciadi = str_replace($search,$replace,$kullaniciadi);

			$uye_renk = yollaf($_POST['uye_renk']);

			$foto = yollaf($_POST['foto']);
			
			$motto = yollaf($_POST['motto']);

			$ad_soyad = yollaf($_POST['ad_soyad']);

			$iban = yollaf($_POST['iban']);

			$suankisifre = yollaf($_POST['suankisifre']);

			$yenisifre = yollaf($_POST['yenisifre']);

			$yenisifretekrar = yollaf($_POST['yenisifretekrar']);

			if($uye_sifre != md5($suankisifre) && empty($suankisifre) === false){

				echo "<script>alert('Şu anki şifreni yanlış girdin sadıç.');</script>";

			}elseif(karakterkntrl($kullaniciadi) == 0){

				echo "<script>alert('Alfabe, sayı ve boşluk karakterlerinden başka karakter kullanmayınız.');</script>";

			}elseif(empty($kullaniciadi) === true || ctype_space($kullaniciadi) === true){

				echo "<script>alert('Kullanıcı adı kısmını boş bıraktınız.');</script>";

			}elseif (strlen($kullaniciadi) > 32 && strlen($kullaniciadi) < 6) {

				echo "<script>alert('Kullanıcı adı 32 karakterden fazla 6 karakterden az olmasın.');</script>";

			}elseif ($yenisifre != $yenisifretekrar && empty($yenisifretekrar) === false && empty($yenisifre) === false) {

				echo "<script>alert('Yeni şifre ile tekrarı birbirini tutmuyor.');</script>";

			}elseif (strlen($yenisifre) < 6 && empty($yenisifre) === false) {

				echo "<script>alert('Şifre en az altı karakter olmalı sadıç.');</script>";

			}elseif(karakterkntrl($yenisifre) == 0){

				echo "<script>alert('Şifrede alfabe ve sayı karakterlerinden başka bir karakter kullanmayınız.');</script>";

			}elseif (strlen($motto) > 255 && empty($motto) === false) {

				echo "<script>alert('3-4 satırı geçmesin dedik ya sadıç.');</script>";

			}elseif(strlen($iban) < 24 && empty($iban) === false){

				echo "<script>alert('IBAN numarasını ya boş bırak ya da düzgün bir IBAN gir kardeş.');</script>";

			}elseif (uye_adi_var_mi($kullaniciadi) === '1' && $uye_adi != $kullaniciadi) {

				echo "<script>alert('Bu kullanıcı adı başka bir üyemize aittir. Lütfen başka bir kullanıcı adı seçiniz.');</script>";
				
			}elseif(karakterkntrl($motto) == 0){

				echo "<script>alert('Mottoda alfabe ve sayı karakterlerinden başka bir karakter kullanmayınız.');</script>";

			}elseif(karakterkntrl($ad_soyad) == 0){

				echo "<script>alert('Ad soyad kısmında alfabe ve sayı karakterlerinden başka bir karakter kullanmayınız.');</script>";

			}elseif(karakterkntrl($iban) == 0){

				echo "<script>alert('İban kısmında alfabe ve sayı karakterlerinden başka bir karakter kullanmayınız.');</script>";

			}else{

				if (empty($suankisifre) === false && empty($yenisifre) === false && empty($yenisifretekrar) === false) {
					
					$uye_sifre = md5($yenisifre);

					setcookie("gss",$uye_sifre,time()+259200);

				}

				setcookie("gska",$kullaniciadi,time()+259200);

				$query = $db->prepare("UPDATE uyeler SET uye_adi = ?, uye_sifre = ?, uye_renk = ?, ad_soyad = ?, iban = ?, foto = ?,  uye_motto = ? WHERE uye_id = ?"); 

				$query->execute(array($kullaniciadi,$uye_sifre,$uye_renk,$ad_soyad,$iban,$foto,$motto,$uye_id));

				header("Location: http://kurtsozluk.net/ayar.php?bbg");

				exit();

			}

		}

		if (isset($_POST['kodyolla'])) {
			
			$sifre = yollaf($_POST['sifre']);

			$yenitelefon = yollaf($_POST['yenitelefon']);

			$numarapatlat = str_split($yenitelefon);

			$telilknumara = $numarapatlat[0];

			$md5lisifre = md5($sifre);

			$kodsaniye = time();

			$aktivasyonkodu = substr(str_shuffle("1234567890"), 0, 6);

			if ($uye_sifre != $md5lisifre) {
				
				echo "<script>alert('Şifreni yanlış girdin güzel kardeşim.');</script>";

			}elseif(is_numeric($yenitelefon) === false){

				echo "<script>alert('Böyle telefon numarası olmaz kardeş bizi mi koparıyorsun?');</script>";

			}elseif($telilknumara != '5'){

				echo "<script>alert('Böyle telefon numarası olmaz kardeş bizi mi koparıyorsun?');</script>";

			}elseif(strlen($yenitelefon) != 10){

				echo "<script>alert('Telefon numarasını 10 haneli olarak gir dedik ya güzel kardeşim.');</script>";

			}elseif (no_var_mi($yenitelefon) === '1') {

				echo "<script>alert('Bu telefonla zaten bir üyelik var güzel kardeşim.');</script>";

			}else{

				$query = $db->prepare("UPDATE uyeler SET kodsaniye = ?, aktivasyonkodu = ?, yenitelefon = ? WHERE uye_id = ?"); 

				$query->execute(array($kodsaniye,$aktivasyonkodu,$yenitelefon,$uye_id));

				function sendRequest($site_name,$send_xml,$header_type) {

    	//die('SITENAME:'.$site_name.'SEND XML:'.$send_xml.'HEADER TYPE '.var_export($header_type,true));
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL,$site_name);
    	curl_setopt($ch, CURLOPT_POST, 1);
    	curl_setopt($ch, CURLOPT_POSTFIELDS,$send_xml);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,1);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    	curl_setopt($ch, CURLOPT_HTTPHEADER,$header_type);
    	curl_setopt($ch, CURLOPT_HEADER, 0);
    	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    	curl_setopt($ch, CURLOPT_TIMEOUT, 120);

    	$result = curl_exec($ch);

    	return $result;
}

$username   = '5315426368';
$password   = 'sifre10';
$orgin_name = 'FATIH TUTAR';

$xml = <<<EOS
   		 <request>
   			 <authentication>
   				 <username>{$username}</username>
   				 <password>{$password}</password>
   			 </authentication>

   			 <order>
   	    		 <sender>{$orgin_name}</sender>
   	    		 <sendDateTime>01/05/2013 18:00</sendDateTime>
   	    		 <message>
   	        		 <text>Telefon numarası degistirme kodunuz : {$aktivasyonkodu}</text>
   	        		 <receipents>
   	            		 <number>{$yenitelefon}</number>
   	        		 </receipents>
   	    		 </message>
   			 </order>
   		 </request>
EOS;


$result = sendRequest('http://api.iletimerkezi.com/v1/send-sms',$xml,array('Content-Type: text/xml'));
//Donen xml degerini sisteminizde parse etmek icin
//http://www.lalit.org/lab/convert-xml-to-array-in-php-xml2array/
//adresindeki kutuphaneyi oneririz
				header("Location: http://kurtsozluk.net/ayar.php?s=tgkgs");

				exit();

			}

		}

		if (isset($_POST['dogrulama'])) {

			$aktivasyonkodu = yollaf($_POST['aktivasyonkodu']);

			$koducek = $db->query("SELECT * FROM uyeler WHERE uye_id = '{$uye_id}'")->fetch(PDO::FETCH_ASSOC);

			$gelenkod = cekf($koducek['aktivasyonkodu']);

			if ($aktivasyonkodu != $gelenkod) {
				
				echo "<script>alert('Kodunuz hatalıdır. Yeni bir kod ile işlem yapabilmek için bir saat sonra tekrar deneyebilirsiniz.');</script>";

			}elseif(is_numeric($aktivasyonkodu) === false){

				echo "<script>alert('Biz böyle bir kod göndermeyiz kardeşim.');</script>";

			}elseif(strlen($aktivasyonkodu) != 6){

				echo "<script>alert('Kod yanlış altı haneli olması lazım.');</script>";

			}else{

				$query = $db->prepare("UPDATE uyeler SET telefon = ? WHERE uye_id = ?"); 

				$query->execute(array($yenitelefon,$uye_id));

				header("Location:http://kurtsozluk.net/ayar.php?s=tnbg");

				exit();

			}

		}

		if (isset($_GET['bbg'])) {

			echo "<script>alert('Bilgileriniz başarıyla güncellendi.');</script>";

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

							<div class="row div2">

								<br/>

								<form action="" method="POST">

									<div class="form-group"><b>Kullanıcı Adı</b></div>

									<div class="form-group"><input type="text" name="kullaniciadi" class="form-control" value="<?php echo $uye_adi; ?>"></div>

									<hr/>

									<div class="form-group"><b>Profil Fotoğrafı</b> (buraya resim linki koy)</div>

									<div class="form-group"><img src="<?php echo $uye_foto; ?>" class="img-responsive img-thumbnail" alt="<?php echo $profilindeki_uyenin_adi; ?>" width="100" height="auto"></div>

									<div class="form-group"><input type="text" name="foto" class="form-control" value="<?php echo $uye_foto; ?>"></div>

									<hr/>
										
									<div class="form-group">
										
										<b>Motto</b>

									</div>

									<div class="form-group">
										
										<textarea class="form-control" cols="3" name="motto"><?php echo $uye_motto; ?></textarea>

									</div>

									<hr/>

									<div class="form-group">
										
										<b>Kendi Rengini Seç </b>

									</div>

									<div class="form-group">
										
										<input type="color" name="uye_renk" value="<?php echo $uye_renk; ?>" style="width: 50%; height: 50px;"/>

									</div>

									<hr/>

									<div class="form-group">
										
										<b>Şifre Değiştirme</b>

									</div>

									<div class="form-group">

										<input type="text" name="suankisifre" class="form-control" placeholder="Şu anki şifre">

									</div>

									<div class="form-group">

										<input type="text" name="yenisifre" class="form-control" placeholder="Yeni şifre">

									</div>

									<div class="form-group">

										<input type="text" name="yenisifretekrar" class="form-control" placeholder="Yeni şifre tekrar">

									</div>

									<hr/>

									<div class="form-group">
										
										<b>Ödeme Alabilmeniz İçin Gerekli Bilgiler</b><br/>

									</div>

									<div class="form-group">
										
										<b>Ad Soyad</b>

									</div>

									<div class="form-group">
										
										<input type="text" name="ad_soyad" class="form-control" value="<?php echo $uye_ad_soyad; ?>">

									</div>
									
									<div class="form-group">
										
										<b>IBAN no</b>

									</div>

									<div class="form-group">
										
										<input type="text" name="iban" class="form-control" value="<?php echo $uye_iban; ?>">

									</div>

									<div class="form-group" style="text-align: center;">
										
										<button type="submit" name="ayar_yolla" value=" " class="btn btn-dark btn-block">Güncelle</button>

									</div>

								</form>

							</div>

							<br/><br/>

							<div class="row div2">

								<?php if (isset($_GET['s']) === true && empty($_GET['s']) === false && $_GET['s'] == 'tgkgs') { ?>

								<form action="" method="POST">
									
									<div class="form-group">
												
										<b>Kod Girişi</b>

									</div>

									<div class="form-group">
									
										<input name="aktivasyonkodu" type="text" class="form-control" id="inputEmail3" placeholder="Kodunuzu Giriniz">
									
									</div>

									<div class="form-group" style="text-align: center;">
										
										<button type="submit" name="dogrulama" value=" " class="btn btn-dark btn-block">Gönder</button>
									
									</div>

								</form>

								<?php }else{ ?>

								<form action="" method="POST">

									<?php 

										if (isset($_GET['s']) === true && empty($_GET['s']) === false && $_GET['s'] == 'tnbg') { 

											echo '<div class="alert alert-success" role="alert">

													<p class="text-justify">Telefon numaranızı başarıyla güncellediniz. Hadi hayırlı olsun.</p>

												</div>';

									 	} 

									?>

									<div class="form-group">
											
										<b>Telefon Numarası Güncelleme</b>

									</div>
									
									<div class="form-group">
										<input name="sifre" type="password" class="form-control" placeholder="Şifre">
									</div>

									<div class="form-group">
										<input name="yenitelefon" type="tel" class="form-control" placeholder="Yeni Telefon No (Başında Sıfır Olmadan - 5xxxxxxxxx)">
									</div>
									
									<div class="alert alert-danger" role="alert">
										<p class="text-justify">
											<small>Telefonuna bir kod gelecek. Onu bir sonraki sayfada kullanacaksın. Mesaj maalesef site adıyla değil de şahıs adıyla gelecek aşağıdaki nottan sebebini öğrenebilirsin. <br/> NOT: BTK'nın 13.08.2018 tarihli ve 98966759-153.99-E.60186 sayılı kararı ile internet sitesi adreslerini, SMS başlığı olarak kullanmak yasaklanmıştır.</small>
										</p>
									</div>

									<div class="form-group" style="text-align: center;">
										<button type="submit" name="kodyolla" value=" " class="btn btn-dark btn-block">Kod Yolla</button>
									</div>

								</form>

								<?php } ?>

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