<?php

	include 'fonksiyonlar/bagla.php';

	if (isset($_GET['b']) === true) {

		$hata = '<br/><div class="alert alert-success" role="alert">Şifre değiştirme kodunuzu sms yoluyla size gönderdik.</div>';
		
	}

	if (isset($_GET['dsd'])) {
		
		$hata = '<br/><div class="alert alert-danger" role="alert">Bu sistemde geçici bir bakım vardır. Lütfen daha sonra tekrar deneyiniz.</div>';

	}

	if (isset($_POST['degistir'])) {
		
		$telefon = yollaf($_POST['telefon']);

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

		$degistirme_kodu = yollaf($_POST['degistirme_kodu']);

		$degistirme_kodu = trim($degistirme_kodu);

		$satir = $db->query("SELECT * FROM uyeler WHERE telefon = '{$telefon}'")->fetch(PDO::FETCH_ASSOC);
			
		$unuttum_kodu = $satir['unuttum_kodu'];

		$kontrol = file_get_contents("http://www.google.com/recaptcha/api/siteverify?secret=6LflDpgUAAAAAEKs1KSfdHraqGZhZMSpftVmdEct&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR']);

		if (isset($_POST['g-recaptcha-response'])) {
		   
		    $captcha = $_POST['g-recaptcha-response'];
		
		}
		
		if (!$captcha) {
		
		    $hata = '<br/><div class="alert alert-danger" role="alert">Sen insan olamazsın robotsun sen roboooot.</div>';
		
		}elseif ($kontrol.success == false) {
		
		    $hata = '<br/><div class="alert alert-danger" role="alert">Spamını gördük aslaan.</div>';
		
		}elseif (empty($telefon) === true) {

			$hata = '<br/><div class="alert alert-danger" role="alert">Telefon kısmını boş bıraktın sadıç</div>';
			
		}elseif(strlen($telefon) != 10){

			$hata = '<br/><div class="alert alert-danger" role="alert">Telefon numarasını 10 haneli olarak giriniz.</div>';

		}elseif (empty($sifre) === true) {

			$hata = '<br/><div class="alert alert-danger" role="alert">Şifre kısmını boş bıraktınız.</div>';

		}elseif (empty($sifre_tekrar) === true) {

			$hata = '<br/><div class="alert alert-danger" role="alert">Şifre tekrarı kısmını boş bıraktınız.</div>';

		}elseif(strlen($sifre) < 6){

			$hata = '<br/><div class="alert alert-danger" role="alert">Şifre en az altı karakter olmalıdır.</div>';

		}elseif($sifreuygun == 0){

			$hata = '<br/><div class="alert alert-danger" role="alert">Şifrede alfabe ve sayı karakterlerinden başka bir karakter kullanmayınız.</div>';

		}elseif (empty($degistirme_kodu) === true) {

			$hata = '<br/><div class="alert alert-danger" role="alert">Değiştirme kodu kısmını boş bıraktınız.</div>';

		}elseif ($sifre !== $sifre_tekrar) {

			$hata = '<br/><div class="alert alert-danger" role="alert">Şifre ile şifre tekrarı birbirini tutmuyor.</div>';
			
		}elseif($degistirme_kodu !== $unuttum_kodu){

			$hata = '<br/><div class="alert alert-danger" role="alert">Değiştirme kodunuz hatalı, lütfen kontrol ediniz.</div>';

		}else{

			$sifre = md5($sifre);

			$query = $db->prepare("UPDATE uyeler SET uye_sifre = ? WHERE telefon = ?"); 

			$sifreyi_guncelle = $query->execute(array($sifre,$telefon));

			header("Location: http://kurtsozluk.net/giris.php?d");

		}

	}

	if (isset($_POST['unuttum'])) {
		
		$unuttum_kodu = substr(md5(rand(999,999999)), 0, 8);

		$telefon = yollaf($_POST['telefon']);

		$kodsaniye = time();

		$kontrol = file_get_contents("http://www.google.com/recaptcha/api/siteverify?secret=6LflDpgUAAAAAEKs1KSfdHraqGZhZMSpftVmdEct&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR']);

		if (isset($_POST['g-recaptcha-response'])) {
		   
		    $captcha = $_POST['g-recaptcha-response'];
		
		}
		
		if (!$captcha) {
		
		    $hata = '<br/><div class="alert alert-danger" role="alert">Sen insan olamazsın robotsun sen roboooot.</div>';
		
		}elseif ($kontrol.success == false) {
		
		    $hata = '<br/><div class="alert alert-danger" role="alert">Spamını gördük aslaan.</div>';
		
		}elseif (empty($telefon) === true) {

			$hata = '<br/><div class="alert alert-danger" role="alert">Telefonunu yazsana sadıç.</div>';

		}elseif(strlen($telefon) != 10){

			$hata = '<br/><div class="alert alert-danger" role="alert">Telefon numarasını 10 haneli olarak giriniz.</div>';

		}else{

			$query = $db->prepare("UPDATE uyeler SET unuttum_kodu = ?, kodsaniye = ? WHERE telefon = ?"); 

			$unuttum = $query->execute(array($unuttum_kodu,$kodsaniye,$telefon));

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
   	        		 <text>Sifre degistirme kodunuz : {$unuttum_kodu}</text>
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

			header("Location: http://kurtsozluk.net/unuttum.php?b");

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

							<div class="row div2">

								<div class="col-md-12">

							<?php  

								echo $hata;

								if (isset($_GET['b']) === true) {
									
							?>

									<h4><strong>Şifreni Değiştir</strong></h4>

									<form action="" method="POST" class="form-horizontal">
									  <div class="form-group">
									    
									      <input name="telefon" type="tel" class="form-control" placeholder="Telefon (Başında Sıfır Olmadan - 5xxxxxxxxx)">
									    
									  </div>
									  <div class="form-group">
									    
									      <input name="sifre" type="password" class="form-control" id="inputPassword3" placeholder="Yeni şifrenizi giriniz.">
									    
									  </div>
									  <div class="form-group">
									    
									      <input name="sifre_tekrar" type="password" class="form-control" id="inputPassword3" placeholder="Yeni şifrenizi tekrar giriniz.">
									    
									  </div>
									  <div class="form-group">
									    
									      <input name="degistirme_kodu" type="text" class="form-control" id="inputEmail3" placeholder="Değiştirme kodunu giriniz.">
									    
									  </div>
									  <div class="form-group">
												
													<div class="g-recaptcha" data-sitekey="6LflDpgUAAAAANmY1V9Q8M6a4wCdIv90VZMN0Zpg"></div>
												
											</div>
									  <div class="form-group" style="text-align: center;">
									    
									      <button type="submit" name="degistir" value=" " class="btn btn-dark btn-block">Değiştir</button>
									    
									  </div>
									</form>

							<?php

								}else{

							?>

									<h4><strong>Şifremi Unuttum</strong></h4>

									<form action="" method="POST" class="form-horizontal">
									  <div class="form-group">
									      <input name="telefon" type="tel" class="form-control" id="inputEmail3" placeholder="Telefon (Başında Sıfır Olmadan - 5xxxxxxxxx)">
									    
									  </div>
									  <div class="form-group">
													<div class="g-recaptcha" data-sitekey="6LflDpgUAAAAANmY1V9Q8M6a4wCdIv90VZMN0Zpg"></div>
											</div>
									  <div class="form-group" style="text-align: center;">
									      <button type="submit" name="unuttum" value=" " class="btn btn-dark btn-block">Gönder</button>
									  </div>

									  <div class="alert alert-danger" role="alert">
										<p class="text-justify">
											<small>Telefonuna bir kod gelecek. Onu bir sonraki sayfada kullanacaksın. Mesaj maalesef site adıyla değil de şahıs adıyla gelecek aşağıdaki nottan sebebini öğrenebilirsin. <br/> NOT: BTK'nın 13.08.2018 tarihli ve 98966759-153.99-E.60186 sayılı kararı ile internet sitesi adreslerini, SMS başlığı olarak kullanmak yasaklanmıştır.</small>
										</p>
									</div>
									</form>

							<?php

								}

							?>

								</div>

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