<?php

	include 'fonksiyonlar/bagla.php';

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

	}

// bi takım hatalar

	if (isset($_GET['bos']) === true) {

		echo "<script>alert('Metin kısmını boş bıraktın hiç mi yazacak bir şeyin yok kardeş.');</script>";

	}

// BASLIKLARDA ARAMA KODLARI

	$arama_yapildi = 0;

	if (isset($_POST['basliklarda_arama_formu'])) {

		$arama_yapildi = 1;
	}

	// TASLAK KODLARI

	$t_var = 0;

	if(isset($_GET['t_kod']) === true && empty($_GET['t_kod']) === false){

		$t_kod = yollaf($_GET['t_kod']);

		$tslk = $db->query("SELECT * FROM taslak WHERE t_kod = '{$t_kod}' LIMIT 1")->fetch(PDO::FETCH_ASSOC);

		$t_baslik = trim(cekf($tslk['t_baslik']));

		$t_metin = cekf($tslk['t_metin']);

		$t_resim = cekf($tslk['t_resim']);

		$t_video = cekf($tslk['t_video']);

		if (empty($t_baslik) || ctype_space($t_baslik)) {

			echo "<script>alert('Başlık koymadın gardeşim.');</script>";

		}elseif(strlen($t_baslik) > 60){

			echo "<script>alert('Başlık 60 karakterden fazla olmasın gardeşim.');</script>";

		}elseif(strlen($t_metin) < 40){

			echo "<script>alert('Başlık metni 40 karakterden az olmasın güzel kardeşim.');</script>";

		}elseif (empty($t_metin) || ctype_space($t_metin)) {

			echo "<script>alert('Konu kısmına bir şey yazmadın gardeşim.');</script>";

		}elseif (uye_adi_var_mi($t_baslik) == '1' && $konu_baslik != $uye_adi) { 

			echo "<script>alert('Bu başlık birinin kullanıcı adı bu başlıkla konu açamazsın.');</script>";

		}

		$t_var = 1;

	}

	// YENİ KONU AÇMA

	if ($giris_yapti_mi == 1) {

		if (isset($_POST['yeni_konu_acma_formu']) === true && empty($_POST['yeni_konu_acma_formu']) === false) {

			$son_duyuru_id = '0';

			$konu_baslik = $_POST['konu_baslik'];

			$test = explodeEachChar($konu_baslik);
	 
			foreach($test as $key => $value)
			{
				$alfabedizi = "abcçdefgğhıijklmnoöqprsştuüvwxyzABCÇDEFGĞHIİJKLMNOÖQPRSŞTUÜVWXYZ0123456789.-? ";

				if (!preg_match("/[".$value."]/i", $alfabedizi)) {

					unset($test[$key]);
				}
			}

			foreach($test as $key => $value)
			{

				$baslikdizi = $baslikdizi."".$value;

			}

			$konu_baslik = strtolower(yollaf($baslikdizi));

			$konu_baslik = bosluk_sil($konu_baslik);

			$search = array('Ç','Ğ','Ö','Ş','Ü','İ');
				
			$replace = array('ç','ğ','ö','ş','ü','i');
			
			$konu_baslik = str_replace($search,$replace,$konu_baslik);

			$konu_baslik = kelimebol($konu_baslik,30);

			$ara = array('ç','ğ','ı','ö','ş','ü',' ');
				
			$degistir = array('c','g','i','o','s','u','-');
			
			$baslikurl = str_replace($ara,$degistir,$konu_baslik);

			$konu_metni = yollaf($_POST['konu_metni']);

			$taslak_kodu = substr(md5(rand(999,999999)), 0, 8);

			$su_an = time();

			if ((uye_adi_var_mi($konu_baslik) == '1' && $konu_baslik != $uye_adi) || (empty($konu_baslik) === true) || (strlen($konu_baslik) > 60) || (ctype_space($konu_baslik)) || (empty($konu_metni) === true) || (ctype_space($konu_metni))) {
				
				$query = $db->prepare("INSERT INTO taslak SET t_kod = ?, t_baslik = ?, t_metin = ?");

				$insert = $query->execute(array($taslak_kodu, $konu_baslik, $konu_metni));

				header("Location: http://kurtsozluk.net/baslikac.php?t_kod=".$taslak_kodu);

				exit();
			
			}else{

				if (baslik_var_mi($konu_baslik) === '1') {

					$sator = $db->query("SELECT * FROM basliklar WHERE baslik = '{$konu_baslik}' AND silindi = '0' LIMIT 1")->fetch(PDO::FETCH_ASSOC);

					$o_konu_id = cekf($sator['baslikid']);

					$o_konuyu_acan_id = cekf($sator['baslikacanid']);

					$query = $db->prepare("INSERT INTO giriler SET giribaslikid = ?, giriustid = ?, giriyazarid = ?, girimetin = ?, giritarih = ?, girisaniye = ?, giriarti = ?, girieksi = ?, giritipi = ?, artilayanlar = ?, eksileyenler = ?, silindi = ?, silen = ?");

					$cevabi_yukle = $query->execute(array($o_konu_id, $o_konu_id, $uye_id, $konu_metni, $now, $su_an, $sifir, $sifir, $cevap_turu, $sifir,$sifir,$sifir,$sifir));

					$satir = $db->query("SELECT * FROM basliklar WHERE baslikid = '{$o_konu_id}'")->fetch(PDO::FETCH_ASSOC);
						
					$cevap_adedi = $satir['giriadedi'];

					$cevap_adedi_artmis = $cevap_adedi + 1;

					$query = $db->prepare("UPDATE basliklar SET giriadedi = ?, songiritarih = ?, songirisaniye = ? WHERE baslikid = ?"); 

					$guncelle = $query->execute(array($cevap_adedi_artmis,$now, $su_an, $o_konu_id));	

					if ($uye_id !== $o_konuyu_acan_id) {

						$query = $db->prepare("INSERT INTO bildirimler SET bildirimi_yollayan_id = ?, bildirimi_alan_id = ?, bildirim_turu = ?, bildirim_baslik_id = ?, bildirim_okundu = ?, bildirim_tarihi = ?");

						$bildirim_ekle = $query->execute(array($uye_id,$konuyu_acan_id,'0',$o_konu_id,'0',$now));
					
					}

					header("Location: http://kurtsozluk.net/baslik.php?vardi&id=$o_konu_id");

					exit();

				}else{

					$query = $db->prepare("INSERT INTO basliklar SET baslik = ?, baslikacanid = ?, giriadedi = ?, silindi = ?, basliksaniye = ?, songiritarih = ?, songirisaniye = ?, gundem = ?, baslikurl = ?, gosterim = ?");

					$konuyu_ekle = $query->execute(array($konu_baslik,$uye_id,'1','0',$su_an,$now,$su_an,'0',$baslikurl,'0'));

					$satiro = $db->query("SELECT * FROM basliklar WHERE baslik = '{$konu_baslik}' AND silindi = '0' ORDER BY baslikid DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);

					$konunun_idsi = $satiro['baslikid'];

					$query = $db->prepare("INSERT INTO giriler SET giribaslikid = ?, giriustid = ?, giriyazarid = ?, girimetin = ?, giritarih = ?, girisaniye = ?, giriarti = ?, girieksi = ?, giritipi = ?, artilayanlar = ?, eksileyenler = ?, silindi = ?, silen = ?");

					$cevabi_yukle = $query->execute(array($konunun_idsi, $konunun_idsi, $uye_id, $konu_metni, $now, $su_an, $sifir, $sifir, $sifir, $sifir,$sifir,$sifir,$sifir));

					$query = $db->prepare("UPDATE uyeler SET gosterim = gosterim + 1 WHERE uye_id = ?"); 

					$attir = $query->execute(array($uye_id));

					header("Location: http://kurtsozluk.net/index.php");

					exit();

				}				

			}
		}

	}

	if (isset($_GET['baslik']) === true && empty($_GET['baslik']) === false) {
		
		$t_var = 1;

		$t_baslik = cekf($_GET['baslik']);

		$t_baslik = str_replace("_", " ", $t_baslik);

		echo "<script>alert('Böyle bir başlık yok istersen sen aç.');</script>";

	}

?>

<!doctype html>

<html>
	
	<head>

		<title>Kurt Sözlük</title>

		<meta name="description" content="Her konuda başlıklar aç. Reklam gelirini yazarlarıyla paylaşan sözlük." />

		<meta name="keywords" content="kurt, sözlük" />

		<?php include 'template/head.php'; ?>

		<script language="javascript" type="text/javascript">

		    function addbkz() {
		        var newtext = '(bkz.)';
		        document.baslikacmaformu.konu_metni.value += newtext;
		    }

		    function addurl() {
		        var newtext = '(url.)';
		        document.baslikacmaformu.konu_metni.value += newtext;
		    }

		    function addres() {
		        var newtext = '(res.)';
		        document.baslikacmaformu.konu_metni.value += newtext;
		    }

		    function addyou() {
		        var newtext = '(you.)';
		        document.baslikacmaformu.konu_metni.value += newtext;
		    }

		    function adddai() {
		        var newtext = '(dai.)';
		        document.baslikacmaformu.konu_metni.value += newtext;
		    }

		</script>

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

							<div class="div2">

								<form action="" method="POST" name="baslikacmaformu">

									<div class="form-group"><h5><strong>Başlık Aç</strong></h5></div>

									<div class="form-group"><?php if ($t_var == 1 && empty($t_baslik) === false) { ?> <input type="text" name="konu_baslik" value="<?php echo $t_baslik; ?>" class="form-control" /><?php }else{ ?> <input type="text" name="konu_baslik" placeholder="BAŞLIK (Başlık en fazla altmış karakter olsun.)" class="form-control" /><?php } ?></div>

									<div class="form-group" style="margin-top: 10px;"><?php if ($t_var == 1 && empty($t_metin) === false) { ?> <textarea name="konu_metni" class="form-control" rows="11"><?php echo $t_metin; ?></textarea><?php }else{ ?> <textarea name="konu_metni" class="form-control" rows="11" placeholder="İÇERİK (Ne konuda yazmak istiyorsan yaz, sahne senin.) Girini girerken kullanabileceğin fonksiyonlar : (bkz.başlık adı) / (url.herhangibir link) / (res.resim linki) / (you.youtube videosu linki) / (dai.dailymotion videosu linki)"></textarea><?php } ?></div>

									<div class="form-group" style="text-align: center;"><button type="submit" name="yeni_konu_acma_formu" value=" " class="btn btn-default">Gönder</button></div>

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