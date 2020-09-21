<?php

	include 'fonksiyonlar/bagla.php';

	if ($girdi == '1') { header("Location:http://kurtsozluk.net/index.php"); }

	//DEĞİŞKENLER

	if (isset($_POST['aktif'])) {
		
		$telefon = yollaf($_POST['telefon']);

		$kc = $db->query("SELECT * FROM uyeler WHERE telefon = '{$telefon}'")->fetch(PDO::FETCH_ASSOC);

		$code = cekf($kc['aktivasyonkodu']);

		$aktivasyonkodu = yollaf($_POST['aktivasyonkodu']);

		if ($aktivasyonkodu == $code) {

			$query = $db->prepare("UPDATE uyeler SET uye_aktiflik = ? WHERE telefon = ?"); 

			$guncelle = $query->execute(array('1',$telefon));

			header("Location: http://kurtsozluk.net/giris.php?aktif");

			exit();

		}else{

			$hata = '<br/><div class="alert alert-danger" role="alert">Hatalı kod.</div>';

		}

	}

//TEKRAR KOD YOLLAMA KODU

	if (isset($_POST['kodyolla'])) {

		$telefon = yollaf($_POST['telefon']);

		$kodsaniye = time();

		$kontrol = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6Le8D0MUAAAAAEWgBIRP9AzLCXItVC0Ix33FkIRb&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR']);

		if (isset($_POST['g-recaptcha-response'])) {
		   
		    $captcha = $_POST['g-recaptcha-response'];
		
		}
		
		if (!$captcha) {
		
		    $hata = '<br/><div class="alert alert-danger" role="alert">Sen insan olamazsın robotsun sen roboooot.</div>';
		
		}elseif ($kontrol.success == false) {
		
		    $hata = '<br/><div class="alert alert-danger" role="alert">Spamını gördük aslaan.</div>';
		
		}elseif (empty($telefon) === true) {
			
			$hata = '<br/><div class="alert alert-danger" role="alert">Niye bir telefon numarası yazmadan göndere basıyorsun güzel kardeşim.</div>';

		}elseif (no_var_mi($telefon) == '0') {
			
			$hata = '<br/><div class="alert alert-danger" role="alert">Bu numarayla daha önce hiç  kayıt olunmamış ki kardeşim.</div>';

		}elseif(strlen($telefon) != 10){

			$hata = '<br/><div class="alert alert-danger" role="alert">Girdiğin numara 10 haneli değil ki aga.</div>';

		}elseif (telefonaktif($telefon) == '1') {

			$hata = '<br/><div class="alert alert-danger" role="alert">Aşık mısın olum bu hesap zaten daha önce aktifleştirilmiş. Giriş yap hadi.</div>';
			
		}elseif(kodsaniyekontrol($kodsaniye) == '0'){

			$hata = '<br/><div class="alert alert-danger" role="alert">O mesajlar bedavaya gelmiyor kardeş öyle zırt pırt kod isteyemezsin bir saat sonra tekrar dene.</div>';

		}else{

		$query = $db->prepare("UPDATE uyeler SET kodsaniye = ? WHERE telefon = ?"); 

		$guncelle = $query->execute(array($kodsaniye,$telefon));

		$koducek = $db->query("SELECT * FROM uyeler WHERE telefon = '{$telefon}'")->fetch(PDO::FETCH_ASSOC);

		$aktivasyonkodu = cekf($koducek['aktivasyonkodu']);

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
   	        		 <text>Aktivasyon kodunuz : {$aktivasyonkodu}</text>
   	        		 <receipents>
   	            		 <number>{$telefon}</number>
   	        		 </receipents>
   	    		 </message>
   			 </order>
   		 </request>
EOS;


$result = sendRequest('http://api.iletimerkezi.com/v1/send-sms',$xml,array('Content-Type: text/xml'));
//Donen xml degerini sisteminizde parse etmek icin
//http://www.lalit.org/lab/convert-xml-to-array-in-php-xml2array/
//adresindeki kutuphaneyi oneririz		
		header("Location: http://kurtsozluk.net/uye_ol.php?telefon=".$telefon."&tgakbg");

		exit();

	}

	}

//uye haydetme kodları

	if (isset($_POST['uye_ol']) === true && empty($_POST['uye_ol']) === false) {
		
		$uye_adi = yollaf($_POST['uye_adi']);

		$ad_patlat = explodeEachChar($uye_adi);

		$uygun = 1;

		foreach($ad_patlat as $key => $value)
		{
			$alfabedizi = "abcçdefgğhıijklmnoöqprsştuüvwxyz0123456789 ";

			if (!preg_match("/[".$value."]/i", $alfabedizi)) {

				$uygun = 0;
			}
		}

		$uye_adi = strtolower($uye_adi);

		$uye_adi = bosluk_sil($uye_adi);

		$search = array('Ç','Ğ','Ö','Ş','Ü','İ');
			
		$replace = array('ç','ğ','ö','ş','ü','i');
		
		$uye_adi = str_replace($search,$replace,$uye_adi);

		$sifre = yollaf($_POST['sifre']);

		$sifre_tekrar = yollaf($_POST['sifre_tekrar']);

		$sifre_patlat = explodeEachChar($sifre);

		$sifreuygun = 1;

		foreach($sifre_patlat as $key => $value)
		{
			$alfabedizi = "abcçdefgğhıijklmnoöqprsştuüvwxyzABCÇDEFGĞHIİJKLMNOÖQPRSŞTUÜVWXYZ0123456789";

			if (!preg_match("/[".$value."]/i", $alfabedizi)) {

				$sifreuygun = 0;
			}
		}

		$telefon = yollaf($_POST['telefon']);

		$numarapatlat = str_split($telefon);

		$telilknumara = $numarapatlat[0];

		$aktivasyonkodu = substr(str_shuffle("1234567890"), 0, 6);

		//$uye_mail_kod = md5($uye_adi + microtime());		

		$kontrol = file_get_contents("http://www.google.com/recaptcha/api/siteverify?secret=6LflDpgUAAAAAEKs1KSfdHraqGZhZMSpftVmdEct&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR']);

		if (isset($_POST['g-recaptcha-response'])) {
		   
		    $captcha = $_POST['g-recaptcha-response'];
		
		}
		
		if (!$captcha) {
		
		    $hata = '<br/><div class="alert alert-danger" role="alert">Sen insan olamazsın robotsun sen roboooot.</div>';
		
		}elseif ($kontrol.success == false) {
		
		    $hata = '<br/><div class="alert alert-danger" role="alert">Spamını gördük aslaan.</div>';
		
		}elseif(empty($uye_adi) === true || ctype_space($uye_adi) === true){

			$hata = '<br/><div class="alert alert-danger" role="alert">Kullanıcı adı kısmını boş bıraktınız.</div>';

		}elseif(is_numeric($telefon) === false){

			$hata = '<br/><div class="alert alert-danger" role="alert">Telefon numaranızda bir hata var lütfen kontrol ediniz.</div>';

		}elseif($telilknumara != '5'){

			$hata = '<br/><div class="alert alert-danger" role="alert">Telefon numaranızda bir hata var lütfen kontrol ediniz.</div>';

		}elseif (strlen($uye_adi) > 32 && strlen($uye_adi) < 6) {

			$hata = '<br/><div class="alert alert-danger" role="alert">Kullanıcı adı 32 karakterden fazla 6 karakterden az olmasın.</div>';

		}elseif($uygun == 0){

			$hata = '<br/><div class="alert alert-danger" role="alert">Alfabe, sayı ve boşluk karakterlerinden başka karakter kullanmayınız.</div>';

		}elseif($sifreuygun == 0){

			$hata = '<br/><div class="alert alert-danger" role="alert">Şifrede alfabe ve sayı karakterlerinden başka bir karakter kullanmayınız.</div>';

		}elseif (empty($sifre) === true || ctype_space($sifre) === true) {

			$hata = '<br/><div class="alert alert-danger" role="alert">Şifre kısmını boş bıraktınız.</div>';

		}elseif (empty($sifre_tekrar) === true || ctype_space($sifre_tekrar) === true) {

			$hata = '<br/><div class="alert alert-danger" role="alert">Şifre tekrarı kısmını boş bıraktınız.</div>';

		}elseif (uye_adi_var_mi($uye_adi) === '1') {

			$hata = '<br/><div class="alert alert-danger" role="alert">Bu kullanıcı adı başka bir üyemize aittir. Lütfen başka bir kullanıcı adı seçiniz.</div>';
			
		}elseif (no_var_mi($telefon) === '1') {

			$hata = '<br/><div class="alert alert-danger" role="alert">Bu telefon numarasıyla zaten bir üyelik var.<br/>

						<form action="" method="POST">
	
							<input name="telefon" type="hidden" class="form-control" id="inputEmail3" value="'.$telefon.'"><br/>

							<button type="submit" name="kodyolla" value=" " class="btn btn-danger">Yeniden Kod Yollamak İçin Buraya Tıkla</button>

						</form>

					</div>';
			
		}elseif(strlen($sifre) < 6){

			$hata = '<br/><div class="alert alert-danger" role="alert">Şifre en az altı karakter olmalıdır.</div>';

		}elseif ($sifre !== $sifre_tekrar) {

			$hata = '<br/><div class="alert alert-danger" role="alert">Şifre ile şifre tekrarı birbiriyle eşleşmiyor. Lütfen kontrol ediniz.</div>';
			
		}elseif(strlen($telefon) != 10){

			$hata = '<br/><div class="alert alert-danger" role="alert">Telefon numarasını 10 haneli olarak giriniz.</div>';

		}else{

			$sifre = md5($sifre);

			$kodsaniye = time();

			$devre = "19/4";

			$query = $db->prepare("INSERT INTO uyeler SET uye_adi = ?, uye_sifre = ?, uye_tipi = ?, uye_aktiflik = ?, arti_puan = ?, eksi_puan = ?, unuttum_kodu = ?, msjlasilan = ?, uye_motto = ?, son_giris_tarihi = ?, uyelik_tarihi = ?, engelliler = ?, cezalar = ?, girdi_sayisi = ?, foto = ?, uye_duyuru = ?, kaldigiduyuru = ?, uye_devre = ?, ceza_sayisi = ?, takip = ?, ad_soyad = ?, iban = ?, gosterim = ?, arsiv = ?, siralama = ?, uye_renk = ?, aktivasyonkodu = ?, kodsaniye = ?, telefon = ?, yenitelefon = ?, odeme = ?, odeduy = ?");

			$uyeyi_kaydet = $query->execute(array($uye_adi,$sifre,$sifir, $sifir, $sifir, $sifir, $sifir, $sifir, $sifir, $now, $now, $sifir, $sifir, $sifir, $sifir, $sifir, $sifir, $devre, $sifir, $sifir, $sifir, $sifir, $sifir, $sifir, $sifir, $sifir, $aktivasyonkodu,$su_an, $telefon, $telefon, $sifir, $sifir));

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
   	        		 <text>Aktivasyon kodunuz : {$aktivasyonkodu}</text>
   	        		 <receipents>
   	            		 <number>{$telefon}</number>
   	        		 </receipents>
   	    		 </message>
   			 </order>
   		 </request>
EOS;


$result = sendRequest('http://api.iletimerkezi.com/v1/send-sms',$xml,array('Content-Type: text/xml'));
//Donen xml degerini sisteminizde parse etmek icin
//http://www.lalit.org/lab/convert-xml-to-array-in-php-xml2array/
//adresindeki kutuphaneyi oneririz

			header("Location: http://kurtsozluk.net/uye_ol.php?telefon=".$telefon."&tgakbg");

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

				<div class="col-md-5">

					<div class="div2">

						<?php echo $hata; ?>
						
						<br/>

						<?php if(isset($_GET['tgakbg'])){ ?> 

							<div class="alert alert-info" role="alert">

								<p class="text-justify">

									Telefonunuza gönderdiğimiz aktivasyon kodunu buraya giriniz.

								</p>

							</div>

						<?php }else{ ?> 

							<div class="alert alert-info" role="alert">

								<p class="text-justify">

									Kurt Sözlük, üyelerin her konuda başlıklar altında paylaşım yaptıkları bir internet sitesidir. Kurt Sözlük reklam gelirini

									aktifliklerine göreüyeleriyle paylaşır. Böylece bir sözlüğün esas unsuru olan yazarlar da emeklerinin karşılığını alabilirler.

								</p>

							</div>

						<?php } ?>

						<?php if(isset($_GET['telefon']) === true && empty($_GET['telefon']) === false){ $telefon = $_GET['telefon']; ?>

							
								<h4><strong>Aktivasyon</strong></h4>
								<form action="" method="POST" class="form-horizontal">
							  		<div class="form-group">
									  <div class="col-md-10">
									      <input name="aktivasyonkodu" type="text" class="form-control" id="inputEmail3" placeholder="Aktivasyon Kodu">
									      <input type="hidden" name="telefon" value="<?php echo $telefon; ?>">
									  </div>
							  		</div>
							  		<div class="form-group">
								    	<div class="col-md-10">
									    	<button type="submit" name="aktif" value=" " class="btn btn-primary">Gönder</button>
									  	</div>
							  		</div>
								</form>

						<?php }else{ ?>

							<h4><strong>Üye Ol</strong></h4>
							<form action="" method="POST" class="form-horizontal">
							  <div class="form-group">
								  <div class="col-md-10">
								      <input name="uye_adi" type="text" class="form-control" id="inputEmail3" placeholder="Kullanıcı Adı">
								  </div>
							  </div>
							  <div class="form-group">
							      <div class="col-md-10">
							      <input name="sifre" type="password" class="form-control" id="inputPassword3" placeholder="Şifre">
							  	</div>
							  </div>
							  <div class="form-group">
							      <div class="col-md-10">
							      <input name="sifre_tekrar" type="password" class="form-control" id="inputPassword3" placeholder="Şifre Tekrarı">
							 	 </div>
							  </div>
							  <div class="form-group">
							      <div class="col-md-10">
							      <input name="telefon" type="tel" class="form-control" id="inputEmail3" placeholder="Telefon (Başında Sıfır Olmadan - 5xxxxxxxxx)">
							 	 </div>
							  </div>
								<div class="form-group">
									<div class="col-md-10">
										<div class="g-recaptcha" data-sitekey="6LflDpgUAAAAANmY1V9Q8M6a4wCdIv90VZMN0Zpg"></div>
									</div>
								</div>
							 
							  <div class="form-group">
							    	<div class="col-md-10" style="text-align: center;">
							    		<button type="submit" name="uye_ol" value=" " class="btn btn-default btn-lg btn-block">Üye Ol</button>
							  		</div>
							  </div>
							   <div class="alert alert-danger" role="alert">

								<p class="text-justify">
							      	<small>Telefonuna bir kod gelecek. Onu bir sonraki sayfada yazarak üye olabilirsin. Bir numarayla yalnızca bir kere üye olabilirsin. Mesaj maalesef site adıyla değil de şahıs adıyla gelecek aşağıdaki nottan sebebini öğrenebilirsin. <br/> NOT: BTK'nın 13.08.2018 tarihli ve 98966759-153.99-E.60186 sayılı kararı ile internet sitesi adreslerini, SMS başlığı olarak kullanmak yasaklanmıştır.</small>
							      </p>
							  </div>
							</form>

						<?php } ?>

					</div>
					
				</div>

				<div class="col-md-4">

					<div class="div2">

						<br/>

						<div class="alert alert-info" role="alert">

							<p class="text-justify">

								 Aktivasyon kodunuzu kaybettiyseniz tekrar aktivasyon kodu almak için aşağıya numaranızı girip göndere basınız.

							</p>

						</div>
						
						<form action="" method="POST" class="form-horizontal">
						  
						  <div class="form-group">
						      <div class="col-md-12">
						      <input name="telefon" type="tel" class="form-control" id="inputEmail3" placeholder="Telefon (Başında Sıfır Olmadan - 5xxxxxxxxx)">
						 	 </div>
						  </div>
						  <div class="form-group">
								<div class="col-md-10">
									<div class="g-recaptcha" data-sitekey="6LflDpgUAAAAANmY1V9Q8M6a4wCdIv90VZMN0Zpg"></div>
								</div>
							</div>
						  <div class="form-group">
						    	<div class="col-md-12" style="text-align: center;">
						    		<button type="submit" name="kodyolla" value=" " class="btn btn-default btn-lg btn-block">Gönder</button>
						  		</div>
						  </div>
						  <div class="alert alert-danger" role="alert">
										<p class="text-justify">
											<small>Telefonuna bir kod gelecek. Onu bir sonraki sayfada kullanacaksın. Mesaj maalesef site adıyla değil de şahıs adıyla gelecek aşağıdaki nottan sebebini öğrenebilirsin. <br/> NOT: BTK'nın 13.08.2018 tarihli ve 98966759-153.99-E.60186 sayılı kararı ile internet sitesi adreslerini, SMS başlığı olarak kullanmak yasaklanmıştır.</small>
										</p>
									</div>
						</form>

					</div>

				</div>

			</div>

		</div>

		<?php include 'template/jscss.html'; ?>

	</body>

</html>