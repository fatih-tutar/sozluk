<?php 

	include 'fonksiyonlar/bagla.php';

	if ($girdi == '1') {

		$sorgu = $db->prepare("SELECT COUNT(*) FROM ceza WHERE cezali_adi = '{$uye_adi}' AND ceza_saniye > '{$su_an}' ORDER BY ceza_id DESC LIMIT 1");
		
		$sorgu->execute();
		
		$ceza_sayi = $sorgu->fetchColumn();

		if ($ceza_sayi != '0') {

			header("Location: cikis.php");

			exit();

		}

	}

//DEĞİŞKENLER

	if (strstr($site_adresi, "=")) {
		$idal = explode("=", $site_adresi);
	}

	if (strstr($site_adresi, "--")) {
		$idal = explode("--", $site_adresi);
	}

	if (strstr($site_adresi, "_")) {
		$idal = explode("_", $site_adresi);
	}

	$konu_id = $idal[1];

	if (isset($konu_id) === false || empty($konu_id) === true || is_numeric($konu_id) === false) {

		header("Location: http://kurtsozluk.net/index.php");

	}

	if (konu_id_var_mi($konu_id) == '0') {
		
		header("Location: http://kurtsozluk.net/index.php");
	}

	if (konu_silik_mi($konu_id) == '1') {
		
		header("Location: http://kurtsozluk.net/index.php");
	}

	$satir = $db->query("SELECT * FROM basliklar WHERE silindi = '0' AND baslikid = '{$konu_id}'")->fetch(PDO::FETCH_ASSOC);

	$konuyu_acan_id = cekf($satir['baslikacanid']);

	if ($girdi == 1) {

		$engellileri_patlat = explode(",", $uye_engellileri);

		foreach ($engellileri_patlat as $key => $value) {
			
			if ($value == $konuyu_acan_id) {
				
				header("Location: http://kurtsozluk.net/index.php");

			}

		}

		$kaelc = $db->query("SELECT * FROM uyeler WHERE uye_id = '{$konuyu_acan_id}' ORDER BY uye_id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);

		$kaelistesi = cekf($kaelc['engelliler']);

		$kaelpatlat = explode(",", $kaelistesi); 

		foreach ($kaelpatlat as $key => $value) {
			
			if ($uye_id == $value) {
				
				header("Location:index.php");

				exit();

			}

		}

	}

	$baslikurl = cekf($satir['baslikurl']);

	$konu_baslik = strtolower(cekf($satir['baslik']));

	$search = array('Ç','Ğ','Ö','Ş','Ü');

	$replace = array('ç','ğ','ö','ş','ü');
	
	$konu_baslik = str_replace($search,$replace,$konu_baslik);

	$konu_k_baslik = $konu_baslik;

	$k_cevap_adedi = cekf($satir['giriadedi']);

	$igc = $db->query("SELECT * FROM giriler WHERE giribaslikid = '{$konu_id}' ORDER BY giriid ASC LIMIT 1")->fetch(PDO::FETCH_ASSOC);

	$cevap_metni = cekf($igc['girimetin']);
				
	$myStr = nl2br($cevap_metni);

	$startStr = "(res.";

	$endStr = ")";

	$myArr = explode($endStr,$myStr);
	 
	foreach($myArr as $myVal)
	{
		$myVal = $myVal."[endOfString]";
		$returnArr[] = BetweenStr($myVal,$startStr,'[endOfString]');
	}
	
	$url_resim = $returnArr[0];

	if (empty($url_resim) === true) {
		
		$url_resim = "http://kurtsozluk.net/icon/kurtico.png";

	}

	$sorgu = $db->prepare("SELECT COUNT(*) FROM giriler WHERE giribaslikid = '{$konu_id}' AND giritipi = '0' AND silindi = '0'");
	
	$sorgu->execute();
	
	$bcs = $sorgu->fetchColumn();

	$ss = floor($bcs / 10);

	$ls = $ss + 1;

	if (isset($_GET['s']) === true && empty($_GET['s']) === false && is_numeric($_GET['s']) === true){

		$sayfa = cekf($_GET['s']);

		$cn = ($sayfa * 10) - 10;

	}else{

		$cn = 0;
	}

	// KONU BAŞLIĞINI PARÇALA

	$konu_keyword = str_replace(" ", ", ", $konu_baslik);

//GİRİŞ YAPMADAN YAPILAMAYACAK İŞLEMLER

	if ($girdi == '1') {

	// GUNDEM BELİRLEME

		$ydsaat = $su_an - 604800;

		$sorgu = $db->prepare("SELECT COUNT(*) FROM giriler WHERE giribaslikid = '{$konu_id}' AND girisaniye > '{$ydsaat}' AND silindi = '0'"); $sorgu->execute();

		$cevap_sayi = $sorgu->fetchColumn();

		$query = $db->prepare("UPDATE basliklar SET gundem = ? WHERE baslikid = ?"); 

		$query->execute(array($cevap_sayi,$konu_id));

	//GİRİ SAHİBİNİ TAKİP ETME KODU

		if (isset($_POST['takip']) === true) {

			$giriyazanid = yollaf($_POST['giriyazanid']);

			$numara = yollaf($_POST['numara']);
		
			if (empty($uye_takip) === true) {
				
				$y_takip = $giriyazanid;

			}else{

				$y_takip = "$uye_takip,$giriyazanid";

			}

			$query = $db->prepare("UPDATE uyeler SET takip = ? WHERE uye_id = ?"); 

			$query->execute(array($y_takip,$uye_id));

			if (isset($_GET['s']) === true && empty($_GET['s']) === false) {

				$link = "http://kurtsozluk.net/baslik.php?s=".$sayfa."&".$baslikurl."_".$konu_id."#".$numara;

			}else{

				$link = "http://kurtsozluk.net/baslik.php?".$baslikurl."_".$konu_id."#".$numara;
				
			}

			header("Location: ".$link);

			exit();

		}

	//GİRİ SAHİBİNİ TAKİBİ BIRAKMA KODU

		if (isset($_POST['takipbirak']) === true) {

			$giriyazanid = yollaf($_POST['giriyazanid']);

			$numara = yollaf($_POST['numara']);

			$takipciler = explode(",", $uye_takip);

			foreach ($takipciler as $key => $value) {
			
				if ($giriyazanid == $value) {
					
					unset($takipciler[$key]);

				}

			}

			$takipcileri_topla = implode(",", $takipciler);

			$query = $db->prepare("UPDATE uyeler SET takip = ? WHERE uye_id = ?"); 

			$query->execute(array($takipcileri_topla,$uye_id));

			if (isset($_GET['s']) === true && empty($_GET['s']) === false) {

				$link = "http://kurtsozluk.net/baslik.php?s=".$sayfa."&".$baslikurl."_".$konu_id."#".$numara;

			}else{

				$link = "http://kurtsozluk.net/baslik.php?".$baslikurl."_".$konu_id."#".$numara;
				
			}

			header("Location: ".$link);

			exit();

		}

	// CEVAP ARTILAMA

		if (isset($_POST['cevap_puani_arttir'])) {	

			$cevabi_yazan_id = yollaf($_POST['cevabi_yazan_id']);

			if ($cevabi_yazan_id == $uye_id) {

				echo "<script>alert('Kendi cevabına puan veremezsin güzel kardeşim.');</script>";

			}else{

				$c_artilamis = 0;

				$c_eksilemis = 0;

				$numara = $_POST['numara'];

				$c_id = $_POST['cevap_id'];

				$c_eksileyenler = $_POST['c_eksileyenler'];

				$c_artilayanlar = $_POST['c_artilayanlar'];

				$c_eksi_puan = $_POST['c_eksi_puan'];

				$c_arti_puan = $_POST['c_arti_puan'];

				$cevabi_yazanin_eksi_puani = $_POST['cevabi_yazanin_eksi_puani'];

				$cevabi_yazanin_arti_puani = $_POST['cevabi_yazanin_arti_puani'];

				$c_eksileyenler_p = explode(",", $c_eksileyenler);

				foreach ($c_eksileyenler_p as $key => $value) {
					
					if ($value == $uye_id) {
						
						$c_eksilemis = 1;

						unset($c_eksileyenler_p[$key]);

					}

				}

				$c_eksileyenler_y = implode(",", $c_eksileyenler_p);

				$c_artilayanlar_p = explode(",", $c_artilayanlar);

				foreach ($c_artilayanlar_p as $key => $value) {
					
					if ($value == $uye_id) {
						
						$c_artilamis = 1;

					}

				}

				if ($c_artilamis == 0) {

					$c_eksi_puan_y = $c_eksi_puan;

					$cevabi_yazanin_eksi_puani_y = $cevabi_yazanin_eksi_puani;

					if ($c_eksilemis == 1) {
						
						$c_eksi_puan_y = $c_eksi_puan - 1;

						$cevabi_yazanin_eksi_puani_y = $cevabi_yazanin_eksi_puani - 1;

					}

					if (empty($c_artilayanlar) === true) {
						
						$c_artilayanlar_y = $uye_id;

					}else{

						$c_artilayanlar_y = "$c_artilayanlar,$uye_id";

					}
					
					$c_arti_puan_y = $c_arti_puan + 1;

					$cevabi_yazanin_arti_puani_y = $cevabi_yazanin_arti_puani + 1;

					$query = $db->prepare("UPDATE giriler SET girieksi = ?, giriarti = ?, artilayanlar = ?, eksileyenler = ? WHERE giriid = ?"); 

					$guncelle = $query->execute(array($c_eksi_puan_y,$c_arti_puan_y,$c_artilayanlar_y,$c_eksileyenler_y,$c_id));

					$query = $db->prepare("UPDATE uyeler SET arti_puan = ?, eksi_puan = ? WHERE uye_id = ?"); 

					$guncelle = $query->execute(array($cevabi_yazanin_arti_puani_y,$cevabi_yazanin_eksi_puani_y,$cevabi_yazan_id));

					if (isset($_GET['s']) === true && empty($_GET['s']) === false) {

						$link = "http://kurtsozluk.net/baslik.php?s=".$sayfa."&".$baslikurl."_".$konu_id."#".$numara;

					}else{

						$link = "http://kurtsozluk.net/baslik.php?".$baslikurl."_".$konu_id."#".$numara;
						
					}

					$query = $db->prepare("UPDATE uyeler SET gosterim = gosterim + 1 WHERE uye_id = ?"); 

					$guncelle = $query->execute(array($cevabi_yazan_id));

					header("Location: ".$link);

					exit();

				}else{

					echo "<script>alert('Daha önce artı puan vermişsin.');</script>";

				}

			}

		}

	// CEVAP EKSİLEME

		if (isset($_POST['cevap_puani_azalt'])) {		

			$cevabi_yazan_id = yollaf($_POST['cevabi_yazan_id']);

			if ($cevabi_yazan_id == $uye_id) {

				echo "<script>alert('Kendi cevabına puan veremezsin güzel kardeşim.');</script>";

			}else{

				$c_artilamis = 0;

				$c_eksilemis = 0;

				$numara = $_POST['numara'];

				$c_id = $_POST['cevap_id'];

				$c_eksileyenler = $_POST['c_eksileyenler'];

				$c_artilayanlar = $_POST['c_artilayanlar'];

				$c_eksi_puan = $_POST['c_eksi_puan'];

				$c_arti_puan = $_POST['c_arti_puan'];

				$cevabi_yazanin_eksi_puani = $_POST['cevabi_yazanin_eksi_puani'];

				$cevabi_yazanin_arti_puani = $_POST['cevabi_yazanin_arti_puani'];

				$c_artilayanlar_p = explode(",", $c_artilayanlar);

				foreach ($c_artilayanlar_p as $key => $value) {
					
					if ($value == $uye_id) {
						
						$c_artilamis = 1;

						unset($c_artilayanlar_p[$key]);

					}

				}

				$c_artilayanlar_y = implode(",", $c_artilayanlar_p);

				$c_eksileyenler_p = explode(",", $c_eksileyenler);

				foreach ($c_eksileyenler_p as $key => $value) {
					
					if ($value == $uye_id) {
						
						$c_eksilemis = 1;

					}

				}

				if ($c_eksilemis == 0) {

					$c_arti_puan_y = $c_arti_puan;

					$cevabi_yazanin_arti_puani_y = $cevabi_yazanin_arti_puani;

					if ($c_artilamis == 1) {
						
						$c_arti_puan_y = $c_arti_puan - 1;

						$y_u_arti_puan = $u_arti_puan - 1;

						$cevabi_yazanin_arti_puani_y = $cevabi_yazanin_arti_puani - 1;

					}

					if (empty($c_eksileyenler) === true) {
						
						$c_eksileyenler_y = $uye_id;

					}else{

						$c_eksileyenler_y = "$c_eksileyenler,$uye_id";

					}
					
					$c_eksi_puan_y = $c_eksi_puan + 1;

					$cevabi_yazanin_eksi_puani_y = $cevabi_yazanin_eksi_puani + 1;

					$guncelle = $db->prepare("UPDATE giriler SET girieksi = ? ,  giriarti = ? , artilayanlar = ? ,  eksileyenler = ? WHERE giriid = ? ");  

					$konu_puanini_guncelle = $guncelle->execute(array($c_eksi_puan_y,$c_arti_puan_y,$c_artilayanlar_y,$c_eksileyenler_y,$c_id));

					$guncelle = $db->prepare("UPDATE uyeler SET arti_puan = ? ,  eksi_puan = ? WHERE uye_id = ? ");  

					$kisinin_puani_guncelle = $guncelle->execute(array($cevabi_yazanin_arti_puani_y,$cevabi_yazanin_eksi_puani_y,$cevabi_yazan_id));  

					if (isset($_GET['s']) === true && empty($_GET['s']) === false) {

						$link = "http://kurtsozluk.net/baslik.php?s=".$sayfa."&".$baslikurl."_".$konu_id."#".$numara;

					}else{

						$link = "http://kurtsozluk.net/baslik.php?".$baslikurl."_".$konu_id."#".$numara;
						
					}

					$query = $db->prepare("UPDATE uyeler SET gosterim = gosterim - 3 WHERE uye_id = ?"); 

					$guncelle = $query->execute(array($cevabi_yazan_id));

					header("Location: ".$link);

					exit();


				}else{

					echo "<script>alert('Daha önce eksi puan vermişsin.');</script>";

				}

			}

		}

		$query = $db->prepare("UPDATE bildirimler SET bildirim_okundu = ? WHERE bildirim_baslik_id = ? AND bildirimi_alan_id = ?"); 

		$insert = $query->execute(array("1",$konu_id,$uye_id));

	//GİRİ SİLME

		if (isset($_POST['cevabi_sil'])) {
			
			$silincek_id = yollaf($_POST['silincek_id']);

			if (girivarmi($silincek_id) == '1') {

				$cevap_turu = yollaf($_POST['cevap_turu']);

				$numara = yollaf($_POST['numara']);

				$numara = $numara - 1;

				$silme_adedi2 = 0;

				$silme_adedi3 = 0;

				if ($cevap_turu == '0') {

					$query = $db->query("SELECT * FROM giriler WHERE giriustid = '{$silincek_id}'", PDO::FETCH_ASSOC);

					if ( $query->rowCount() ){

						foreach( $query as $bul ){

							$ikincil = cekf($bul['giriid']);

							$sorgu = $db->prepare("SELECT COUNT(*) FROM giriler WHERE giriustid = '{$ikincil}'"); $sorgu->execute();

							$ucuncul_sayi = $sorgu->fetchColumn();

							$silme_adedi3 = $silme_adedi3 + $ucuncul_sayi;

							$query = $db->prepare("UPDATE giriler SET silindi = ?, silen = ? WHERE giriustid = ?"); 

							$cevapaltisil = $query->execute(array('1', $uye_id, $ikincil));

						}

					}

					$sorgu = $db->prepare("SELECT COUNT(*) FROM giriler WHERE giriustid = '{$silincek_id}'"); $sorgu->execute();

					$silme_adedi2 = $sorgu->fetchColumn();

					$query = $db->prepare("UPDATE giriler SET silindi = ?, silen = ? WHERE giriustid = ?"); 

					$cevapaltisil = $query->execute(array('1', $uye_id, $silincek_id));

				}if ($cevap_turu == '1') {

					$sorgu = $db->prepare("SELECT COUNT(*) FROM giriler WHERE giriustid = '{$silincek_id}'"); $sorgu->execute();

					$silme_adedi3 = $sorgu->fetchColumn();

					$query = $db->prepare("UPDATE giriler SET silindi = ?, silen = ? WHERE giriustid = ?"); 

					$cevapaltisil = $query->execute(array('1', $uye_id, $silincek_id));

				}

				$silme_adedi = $silme_adedi2 + $silme_adedi3 + 1;

				$query = $db->prepare("UPDATE giriler SET silindi = ?, silen = ? WHERE giriid = ?"); 

				$cevapaltisil = $query->execute(array('1', $uye_id, $silincek_id));

				$y_cevap_adedi = $k_cevap_adedi - $silme_adedi;

				$query = $db->query("SELECT * FROM giriler WHERE giriid = '{$giriid}' ORDER BY giritarih DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);

				$soncevap = cekf($query['giritarih']);

				if ($k_cevap_adedi == 1) {

					$query = $db->prepare("DELETE FROM basliklar WHERE baslikid = ?");

					$delete = $query->execute(array($konu_id));

					header("Location: http://kurtsozluk.net/index.php");

				}else{

					$query = $db->prepare("UPDATE basliklar SET giriadedi = ?, songiritarih = ? WHERE baslikid = ?"); 

					$guncelle = $query->execute(array($y_cevap_adedi, $soncevap, $konu_id));

					header("Location: http://kurtsozluk.net/baslik.php?".$baslikurl."_".$konu_id."#".$numara);
				
				}

				exit();

			}

		}

	//GİRİ GÖNDERME

		if (isset($_POST['cevap_formu']) === true && empty($_POST['cevap_formu']) === false) {

			if($giris_yapti_mi == 1){

				$cevaplamis = 0;

				$numarasi = yollaf($_POST['numara']);

				$cevabi_yazan_id = yollaf($_POST['cevabi_yazan_id']);

				$cevap_metni = trim(yollaf($_POST['cevap_metni']));

				$cevap_turu = yollaf($_POST['cevap_turu']);

				if (empty($cevap_metni) === true) {

					echo "<script>alert('İyi de bir şey yazmadın ki.');</script>";

				}elseif(strlen($cevap_metni) < 10){

					echo "<script>alert('Bu kadar kısa giri olmaz kardeş, sen duygularını artı eksi vererek anlat bence.');</script>";

				}elseif(strlen($cevap_metni) < 20 && $cevap_turu == '0'){

					echo "<script>alert('Birincil giriler 20 karakterden az olamaz.');</script>";

				}else{			

					$saniye = time();

					$yorumlayanlar_patlat = explode(",", $k_yorumlayanlar);

					foreach ($yorumlayanlar_patlat as $key => $value) {
						
						if ($uye_id == $value) {
							
							$yorumlanmis = 1;

						}

					}

					if ($yorumlanmis == 0) {

						if (empty($k_yorumlayanlar) === true) {
							
							$k_yorumlayanlar = $uye_id;

						}else{

							$k_yorumlayanlar = "$k_yorumlayanlar,$uye_id";

						}

					}

					$cevab_id = yollaf($_POST['cevab_id']);

					$query = $db->prepare("INSERT INTO giriler SET giribaslikid = ?, giriustid = ?, giriyazarid = ?, girimetin = ?, giritarih = ?, girisaniye = ?, giriarti = ?, girieksi = ?, giritipi = ?, artilayanlar = ?, eksileyenler = ?, silindi = ?, silen = ?");

					$cevabi_yukle = $query->execute(array($konu_id, $cevab_id, $uye_id, $cevap_metni, $now, $su_an, $sifir, $sifir, $cevap_turu, $sifir,$sifir,$sifir,$sifir));

					$guncelle = $db->prepare("UPDATE basliklar SET giriadedi = giriadedi + 1, songiritarih = ?, songirisaniye = ? WHERE baslikid = ? ");  

					$update = $guncelle->execute(array($now,$saniye, $konu_id));  

					$satir = $db->query("SELECT * FROM basliklar WHERE baslikid = '{$konu_id}'")->fetch(PDO::FETCH_ASSOC);
					
					if (isset($_GET['s']) === true && empty($_GET['s']) === false) {

						$link = "http://kurtsozluk.net/baslik.php?s=".$sayfa."&".$baslikurl."_".$konu_id."#".$numarasi;

					}else{

						$link = "http://kurtsozluk.net/baslik.php?".$baslikurl."_".$konu_id."#".$numarasi;

					}

					if ($cevap_turu == '0') {
						
						$link = "http://kurtsozluk.net/baslik.php?s=".$ls."&".$baslikurl."_".$konu_id."#".$numarasi;

					}

					if ($uye_id !== $konuyu_acan_id && $cevap_turu == '0') {

						$query = $db->prepare("INSERT INTO bildirimler SET bildirimi_yollayan_id = ?, bildirimi_alan_id = ?, bildirim_turu = ?, bildirim_baslik_id = ?, b_numara = ?, bildirim_okundu = ?, bildirim_tarihi = ?, bildirim_konu_id = ?, b_link = ?");

						$insert = $query->execute(array($uye_id,$konuyu_acan_id,'0',$konu_id,$numarasi,'0',$now,$konu_id,$link)); 

						$query = $db->prepare("UPDATE uyeler SET gosterim = gosterim + 1 WHERE uye_id = ?"); 

						$guncelle = $query->execute(array($konuyu_acan_id));

					}
					
					if ($uye_id !== $cevabi_yazan_id && $cevap_turu == '1') {

						$query = $db->prepare("INSERT INTO bildirimler SET bildirimi_yollayan_id = ?, bildirimi_alan_id = ?, bildirim_turu = ?, bildirim_baslik_id = ?, b_numara = ?, bildirim_okundu = ?, bildirim_tarihi = ?, bildirim_konu_id = ?, b_link = ?");

						$insert = $query->execute(array($uye_id,$cevabi_yazan_id,'1',$konu_id,$numarasi,'0',$now,$konu_id,$link));

						$query = $db->prepare("UPDATE uyeler SET gosterim = gosterim + 1 WHERE uye_id = ?"); 

						$guncelle = $query->execute(array($cevabi_yazan_id));

					}

					if ($uye_id !== $cevabi_yazan_id && $cevap_turu == '2') {

						$query = $db->prepare("INSERT INTO bildirimler SET bildirimi_yollayan_id = ?, bildirimi_alan_id = ?, bildirim_turu = ?, bildirim_baslik_id = ?, b_numara = ?, bildirim_okundu = ?, bildirim_tarihi = ?, bildirim_konu_id = ?, b_link = ?");

						$insert = $query->execute(array($uye_id,$cevabi_yazan_id,'2',$konu_id,$numarasi,'0',$now,$konu_id,$link));

						$query = $db->prepare("UPDATE uyeler SET gosterim = gosterim + 1 WHERE uye_id = ?"); 

						$guncelle = $query->execute(array($cevabi_yazan_id));

					}

					$query = $db->prepare("UPDATE uyeler SET gosterim = gosterim + 1 WHERE uye_id = ?"); 

					$guncelle = $query->execute(array($uye_id));

					if ($cevap_turu == '0') {

						header("Location: http://kurtsozluk.net/baslik.php?s=".$ls."&".$baslikurl."_".$konu_id."#".$numarasi);

					}elseif (isset($_GET['s']) === true && empty($_GET['s']) === false) {

						header("Location: http://kurtsozluk.net/baslik.php?s=".$sayfa."&".$baslikurl."_".$konu_id."#".$numarasi);

					}else{

						header("Location: http://kurtsozluk.net/baslik.php?".$baslikurl."_".$konu_id."#".$numarasi);

					}

					exit();

				}

			}elseif ($giris_yapti_mi == 0) {

				echo "<script>alert('Üye olmadan yazamazsın.');</script>";

			}

		}

	// cevap sikayet

		if (isset($_POST['cevap_sikayet_formu']) === true && empty($_POST['cevap_sikayet_formu']) === false) {

			$sikayet_kutusu = yollaf($_POST['cevapSikayetKutusu']);

			$cevabi_yazan_id = yollaf($_POST['cevabi_yazan_id']);

			$cevap_id = yollaf($_POST['cevap_id']);
			
			if (empty($sikayet_kutusu)) {

				echo "<script>alert('Şikayet kısmına bir şey yazmadın. Ha bu arada hatırlatalım şikayet sistemini boş yere meşgul etmek banlanma sebebidir.	Tabi sen öyle bir şey yapmassın biz eminiz.');</script>";

			}else{

				$sikayeti_ekle = $db->prepare("INSERT INTO sikayetler SET sikayet_eden_id = ?, sikayet_edilen_id = ?, sikayet_konu_id = ?, sikayet_raporu = ?, sikayet_tarihi = ?, silindi = ?");

				$insert = $sikayeti_ekle->execute(array($uye_id,$cevabi_yazan_id,$cevap_id,$sikayet_kutusu,$now,'0'));
				
				echo "<script>alert('Şikayetiniz iletilmiştir.');</script>";

			}

		}

	//cevap_duzenle

		if (isset($_POST['cevap_duzenle'])) {

			$numara = yollaf($_POST['numara']);
			
			$cevap_id = yollaf($_POST['cevap_id']);

			$cevap_metni = trim(yollaf($_POST['cevap_metni']));

			$giritipi = yollaf($_POST['giritipi']);

			if (empty($cevap_metni) === true) {
					
				echo "<script>alert('İyi de bir şey yazmadın ki.');</script>";

			}elseif(strlen($cevap_metni) < 10){

				echo "<script>alert('Bu kadar kısa giri olmaz kardeş, sen duygularını artı eksi vererek anlat bence.');</script>";

			}elseif(strlen($cevap_metni) < 20 && $giritipi == '0'){

					echo "<script>alert('Birincil giriler 40 karakterden az olamaz güzel kardeşim.');</script>";

			}else{

				$query = $db->prepare("UPDATE giriler SET girimetin = ? WHERE giriid = ?"); 

				$query->execute(array($cevap_metni,$cevap_id));

				header("Location: http://kurtsozluk.net/baslik.php?".$baslikurl."_".$konu_id."#".$numara);

				exit();

			}

		}

	//

		if (isset($_GET['vardi'])) {

			echo "<script>alert('Bu başlığa sahip bir konu zaten vardı. Biz de içeriğini bu konunun en sonuna cevap olarak ekledik. Başlığı açmadan önce başlık aramada

			ararsan daha iyi olur.');</script>";

		}

	//ARŞİVE EKLEME KODLARI

		if (isset($_POST['arsive_ekle'])) {
			
			if (empty($uye_arsiv)) {
				
				$uye_arsiv = $konu_id;

			}else{

				$uye_arsiv = "$uye_arsiv,$konu_id";

			}

			$guncelle = $db->prepare("UPDATE uyeler SET arsiv = ? WHERE uye_id = ?  ORDER BY uye_id DESC LIMIT 1");  

			$update = $guncelle->execute(array($uye_arsiv,$uye_id)); 

			header("Location:http://kurtsozluk.net/baslik.php?".$baslikurl."_".$konu_id);

			exit();

		}

	//ARŞİVDEN ÇIKARMA KODLARI

		if (isset($_POST['arsivdencikar'])) {
			
			$arsiv_patlat = explode(",", $uye_arsiv);

			foreach ($arsiv_patlat as $key => $value) {

				if ($konu_id == $value) {
					
					unset($arsiv_patlat[$key]);

				}

			}

			$arsivitopla = implode(",", $arsiv_patlat);

			$guncelle = $db->prepare("UPDATE uyeler SET arsiv = ? WHERE uye_id = ? ORDER BY uye_id DESC LIMIT 1");  

			$update = $guncelle->execute(array($arsivitopla,$uye_id)); 

			header("Location:http://kurtsozluk.net/baslik.php?".$baslikurl."_".$konu_id);

			exit();

		}

	//ARŞİVDE VAR MI KONTROL ETME KODLARI

		if (empty($uye_arsiv)) {

			$arsivdemi = 0;

		}else{

			$arsivdemi = 0;

			$arsiv_patlat = explode(",", $uye_arsiv);

			foreach ($arsiv_patlat as $key => $value) {

				if ($konu_id == $value) {

					$arsivdemi = 1;

				}

			}

		}

	//BANLA

		if (isset($_POST['banla'])) {
			
			$ceza_sebebi = yollaf($_POST['ceza_sebebi']);

			$kisi_id = yollaf($_POST['kisi_id']);

			$kac = $db->query("SELECT * FROM uyeler WHERE uye_id = '{$kisi_id}'")->fetch(PDO::FETCH_ASSOC);

			$kisi_adi = cekf($kac['uye_adi']);

			$p_ceza_sayisi = cekf($kac['ceza_sayisi']);

			$ban_tipi = yollaf($_POST['ban_tipi']);

			$girdi_id = yollaf($_POST['girdi_id']);

			if(empty($ceza_sebebi) === true){

				echo "<script>alert('Uzaklaştırma sebebini yazmadın.');</script>";

			}else{

				$ceza_sayisi = $p_ceza_sayisi + 1;

				$ceza_suresi = $ceza_sayisi * 86400;

				$sorgu = $db->prepare("SELECT COUNT(*) FROM ceza WHERE cezali_adi = '{$kisi_id}' AND ceza_saniye > '{$su_an}' ORDER BY ceza_id DESC LIMIT 1"); $sorgu->execute();

				$ceza_sayi = $sorgu->fetchColumn();

				if ($ceza_sayi != '0') {

					$ceko = $db->query("SELECT * FROM ceza WHERE cezali_adi = '{$kisi_id}' AND ceza_saniye > '{$su_an}' ORDER BY ceza_id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);

					$ceza_bitisi = cekf($ceko['ceza_saniye']);

					$ceza_bitis = $ceza_bitisi + $ceza_suresi;

				}else{

					$ceza_bitis = time() + $ceza_suresi;

				}

				$guncelle = $db->prepare("UPDATE uyeler SET ceza_sayisi = ? WHERE uye_id = ? ");  

				$update = $guncelle->execute(array($ceza_sayisi,$kisi_id));  

				$query = $db->prepare("INSERT INTO ceza SET cezali_adi = ?, ceza_sebebi = ?, ceza_saniye = ?, ceza_veren = ?, girdi_id = ?, ban_tipi = ?");

				$insert = $query->execute(array($kisi_adi, $ceza_sebebi, $ceza_bitis, $uye_id, $girdi_id, $ban_tipi));

				header("Location: http://kurtsozluk.net/baslik.php?".$baslikurl."_".$konu_id);

				exit();

			}

		}	

	//KONU delete

		if (isset($_POST['konudelete'])) {
			
			$baslikid = yollaf($_POST['baslikid']);

			$query = $db->prepare("DELETE FROM basliklar WHERE baslikid = ?");

			$delete = $query->execute(array($baslikid));

			$query = $db->prepare("DELETE FROM giriler WHERE giribaslikid = ?");

			$delete = $query->execute(array($baslikid));

			header("Location: http://kurtsozluk.net/index.php");

			exit();

		}

//KONU SİL

		if (isset($_POST['konusil'])) {
			
			$baslikid = yollaf($_POST['baslikid']);

			$guncelle = $db->prepare("UPDATE basliklar SET silindi = ? WHERE baslikid = ? ");  

			$update = $guncelle->execute(array('1',$baslikid));  

			$guncelle = $db->prepare("UPDATE giriler SET silindi = ? WHERE giribaslikid = ? ");  

			$update = $guncelle->execute(array('1',$baslikid));  

			header("Location: http://kurtsozluk.net/index.php");

			exit();

		}

// BAŞLIK DÜZENLE

		if (isset($_POST['baslikduzenle'])) {

			$duzenlenmisbaslik = $_POST['duzenlenmisbaslik'];

			$test = explodeEachChar($duzenlenmisbaslik);
	 
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

			$duzenlenmisbaslik = strtolower(yollaf($baslikdizi));

			$duzenlenmisbaslik = bosluk_sil($duzenlenmisbaslik);

			$search = array('Ç','Ğ','Ö','Ş','Ü','İ');
				
			$replace = array('ç','ğ','ö','ş','ü','i');
			
			$duzenlenmisbaslik = str_replace($search,$replace,$duzenlenmisbaslik);

			$duzenlenmisbaslik = kelimebol($duzenlenmisbaslik,30);

			$ara = array('ç','ğ','ı','ö','ş','ü',' ');
				
			$degistir = array('c','g','i','o','s','u','-');
			
			$duzenlenmisbaslikurl = str_replace($ara,$degistir,$duzenlenmisbaslik);

			if (empty($duzenlenmisbaslik) === true) {

				echo "<script>alert('Başlığı düzeltiyor musun siliyor musun belli değil sadıç ya.');</script>";

			}elseif(strlen($duzenlenmisbaslik) > 60){

				echo "<script>alert('Başlık 60 karakterden fazla olmasın dedik ya sadıç.');</script>";			

			}elseif (uye_adi_var_mi($duzenlenmisbaslik) == '1' && $duzenlenmisbaslik != $uye_adi) { 

				echo "<script>alert('Bu başlık birinin kullanıcı adı bu başlıkla konu açamazsın.');</script>";

			}else{

				$query = $db->prepare("UPDATE basliklar SET baslik = ? , baslikurl = ? WHERE baslikid = ?"); 

				$query->execute(array($duzenlenmisbaslik,$duzenlenmisbaslikurl,$konu_id));

				header("Location: http://kurtsozluk.net/baslik.php?".$baslikurl."_".$konu_id);

				exit();

			}

		}

	}

?>

<!doctype html>

<html>
	
	<head>

		<title><?php echo $konu_baslik; ?></title>

		<meta name="description" content='<?php echo $konu_baslik; ?>' />

		<meta name="keywords" content='<?php echo $konu_keyword.', kurt, sözlük'; ?>' />

		<meta property="og:title" content='<?php echo $konu_baslik; ?>' />
		<meta property="og:description" content='<?php echo $cevap_metni; ?>' />
		<meta property="og:url" content='http://kurtsozluk.net/baslik.php?<?php echo $baslikurl; ?>_<?php echo $konu_id; ?>' />
		<meta property="og:image" content='<?php echo $url_resim; ?>' />

		<meta name="twitter:domain" content="kurtsozluk.net" />
		<meta name="twitter:site" content="@tutimontana" />
		<meta name="twitter:title" content="<?php echo $konu_baslik; ?>" />
		<meta name="twitter:description" content="<?php echo $cevap_metni; ?>" />
		<meta name="twitter:creator" content="@tutimontana" />
		<meta name="twitter:card" content="summary_large_image" />
		<meta name="twitter:image" content="<?php echo $url_resim; ?>" />

		<?php include 'template/head.php'; ?>

	</head>

	<body>

		<?php include 'template/banner.php'; ?>

		<div class="container">

			<div class="row">

				<div class="col-md-3 hidden-sm hidden-xs">

					<?php include 'template/solframe.php'; ?>

				</div>

				<div class="col-md-8 col-sm-12 col-xs-12">

					<div class="row cerceve">

						<div class="col-md-10 col-sm-10 col-xs-10">

							<h4><strong><?php echo $konu_baslik; ?></strong><small>(<?php echo $k_cevap_adedi; ?>)</small></h4>

							<?php if($girdi == '1' && ($konuyu_acan_id == $uye_id || $uye_tipi == '1' || $uye_tipi == '2')){?>

							<div id="baslikduzenlemedivi" style="display:none;">

								<form action="" method="POST">

									<input type="text" name="duzenlenmisbaslik" value="<?php echo $konu_baslik; ?>" class="form-control">

									<button type="submit" name="baslikduzenle" value=" " class="btn btn-default" style="margin-top:5px;">Düzenle</button>

								</form>

							</div>

							<?php } ?>

						</div>
						<div class="col-md-2 col-sm-2 col-xs-2" style="padding: 6px;">
						<?php if($girdi == '1'){?>
							<div class="btn-group" role="group" aria-label="...">
								
								<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color: #FFFFFF; border-style: none;">

									<i class="fa fa-ellipsis-h fa-lg" style="color:#535A3B;"></i>

								</button>								

								<ul class="dropdown-menu dropdown-menu-right">
									<?php if($konuyu_acan_id == $uye_id || $uye_tipi == '1' || $uye_tipi == '2'){?>
									<li>

										<a href="#" title="Düzenle" onclick="return false" onmousedown="javascript:ackapa('baslikduzenlemedivi');">Düzenle</a>

									</li>
									<li style="padding-left: 15px;">

										<form action="" method="POST" style="margin-bottom: 0px;">
											<input type="hidden" name="baslikid" value="<?php echo $konu_id; ?>">
											<input type="submit" name="konusil" value="Sil" style="background-color: white; border-style: none;  width: 100%; text-align: left;" onclick="return window.confirm('Bu yolun dönüşü yok bak!')">
										</form>

									</li>
									<?php } ?>

									<li style="padding-left: 15px;">

										<?php if($arsivdemi == 0){?>

											<form action="" method="POST" style="margin-bottom: 0px;">

												<input type="submit" name="arsive_ekle" value="Arşive Ekle" style="background-color: white; border-style: none;">

											</form>

										<?php }else{ ?>

											<form action="" method="POST" style="margin-bottom: 0px;">

												<input type="submit" name="arsivdencikar" value="Arşivden Çıkar" style="background-color: white; border-style: none;">

											</form>

										<?php } ?>
										

									</li>
									<?php

										if ($uye_id == 1) {
									?>

									<li style="padding-left: 15px;">

										<form action="" method="POST" style="margin-bottom: 0px;">
											<input type="hidden" name="baslikid" value="<?php echo $konu_id; ?>">
											<input type="submit" name="konudelete" value="DELETE" style="background-color: white; border-style: none;  width: 100%; text-align: left;" onclick="return window.confirm('Bu yolun dönüşü yok bak!')">
										</form>

									</li>

									<?php
										}

									?>
								</ul>

							</div>
						<?php } ?>
						</div>
					</div>

					<div class="row"> <!-- GİRİLERİ DİVİ BAŞLANGIÇ -->

		<?php  

			$tp = 0;

			$query = $db->query("SELECT * FROM giriler WHERE giribaslikid = '{$konu_id}' AND giritipi = '0' AND silindi = '0' ORDER BY girisaniye ASC LIMIT $cn,10", PDO::FETCH_ASSOC);

			if ( $query->rowCount() ){

				foreach( $query as $satir ){

				$cevabi_yazan_id = cekf($satir['giriyazarid']);

				$engelli_bu = 0;

				if ($girdi == 1) {
					
					foreach ($engellileri_patlat as $key => $value) {
					
						if ($cevabi_yazan_id == $value) {
							
							$engelli_bu = 1;

						}

					}
					
				}

				if ($engelli_bu == 0) {

					$cn++;

					$tp++;

					$cevap_id = cekf($satir['giriid']);

					$cevap_metni = cekf($satir['girimetin']);
					
					$cevap_metni = nl2br($cevap_metni);

					$cevap_metni = bkz($cevap_metni);

					$cevap_metni = res($cevap_metni);

					$cevap_metni = url($cevap_metni);

					$cevap_metni = you($cevap_metni);

					$cevap_metni = dai($cevap_metni);

					$d_cevap_metni = cekf($satir['girimetin']);

					$row = $db->query("SELECT * FROM uyeler WHERE uye_id = '{$cevabi_yazan_id}'")->fetch(PDO::FETCH_ASSOC);
						
					$cevabi_yazanin_adi = cekf($row['uye_adi']);

					$cevabi_yazanin_arti_puani = cekf($row['arti_puan']);

					$cevabi_yazanin_eksi_puani = cekf($row['eksi_puan']);

					$cevabi_yazan_tipi = cekf($row['uye_tipi']);

					$c_arti_puan = cekf($satir['giriarti']);

					$c_eksi_puan = cekf($satir['girieksi']);

					$c_artilayanlar = cekf($satir['artilayanlar']);

					$c_eksileyenler = cekf($satir['eksileyenler']);

					$cevap_tarihi = cekf($satir['giritarih']);

					$saniye = cekf($satir['girisaniye']);

					$ne_kadar_once = ne_kadar_once($su_an,$saniye);

		?>

					

						<div class="col-md-12 col-sm-12 col-xs-12 cerceve" style="padding:10px 0px 0px 0px;">

							<div class="col-md-6 col-sm-6 col-xs-6"><a name="<?php echo $tp; ?>" href="http://kurtsozluk.net/profil.php?id=<?php echo $cevabi_yazan_id; ?>"><b><?php echo $cevabi_yazanin_adi; ?></b></a></div>

							<div class="col-md-6 col-sm-6 col-xs-6" style="text-align:right;"><a href="#" title="<?php echo $cevap_tarihi; ?>"><?php echo $ne_kadar_once; ?></a></div>

							<div class="col-md-12 col-sm-12 col-xs-12"><p style="margin:0px;"><?php echo $cevap_metni; ?></p></div>

						<?php if($girdi == 1){ if($cevabi_yazan_id == $uye_id || $uye_tipi == '1' || $uye_tipi == '2'){ ?>	

							<div class="col-md-1 col-sm-2 col-xs-3">
								
								<form action="" method="POST">

									<input type="hidden" name="c_artilayanlar" value="<?php echo $c_artilayanlar; ?>" />

									<input type="hidden" name="c_eksileyenler" value="<?php echo $c_eksileyenler; ?>" />

									<input type="hidden" name="numara" value="<?php echo $tp; ?>" />

									<input type="hidden" name="cevabi_yazanin_arti_puani" value="<?php echo $cevabi_yazanin_arti_puani; ?>" />

									<input type="hidden" name="cevabi_yazanin_eksi_puani" value="<?php echo $cevabi_yazanin_eksi_puani; ?>" />

									<input type="hidden" name="cevabi_yazan_id" value="<?php echo $cevabi_yazan_id; ?>" />

									<input type="hidden" name="cevap_id" value="<?php echo $cevap_id; ?>" />

									<input type="hidden" name="c_arti_puan" value="<?php echo $c_arti_puan; ?>" />

									<input type="hidden" name="c_eksi_puan" value="<?php echo $c_eksi_puan; ?>" />

									<button type="submit" name="cevap_puani_arttir" value=" " style="background-color:#FFFFFF; border-style:none; padding: 0px;"><span><i class="fa fa-angle-up fa-lg" style="color:#535A3B;"></i></span></button>

									&nbsp;<small><?php if($c_arti_puan != 0){ echo $c_arti_puan; } ?></small>

								</form>

							</div>

							<div class="col-md-1 col-sm-2 col-xs-3">
								
								<form action="" method="POST">

									<input type="hidden" name="c_artilayanlar" value="<?php echo $c_artilayanlar; ?>" />

									<input type="hidden" name="c_eksileyenler" value="<?php echo $c_eksileyenler; ?>" />

									<input type="hidden" name="numara" value="<?php echo $tp; ?>" />

									<input type="hidden" name="cevabi_yazanin_arti_puani" value="<?php echo $cevabi_yazanin_arti_puani; ?>" />

									<input type="hidden" name="cevabi_yazanin_eksi_puani" value="<?php echo $cevabi_yazanin_eksi_puani; ?>" />

									<input type="hidden" name="cevabi_yazan_id" value="<?php echo $cevabi_yazan_id; ?>" />

									<input type="hidden" name="cevap_id" value="<?php echo $cevap_id; ?>" />

									<input type="hidden" name="c_arti_puan" value="<?php echo $c_arti_puan; ?>" />

									<input type="hidden" name="c_eksi_puan" value="<?php echo $c_eksi_puan; ?>" />

									<button type="submit" name="cevap_puani_azalt" value=" " style="background-color:#FFFFFF; border-style:none; padding: 0px;"><span><i class="fa fa-angle-down fa-lg" style="color:#535A3B;"></i></span></button>

									&nbsp;<small><?php if($c_eksi_puan != 0){ echo $c_eksi_puan; } ?></small>

								</form>

							</div>
									
							<div class="col-md-1 col-sm-2 col-xs-3">

								<div class="btn-group" role="group" aria-label="...">
								
									<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color: #FFFFFF; border-style: none;">

										<i class="fa fa-ellipsis-h fa-lg" style="color:#535A3B;"></i>

									</button>

									<ul class="dropdown-menu dropdown-menu-left">

										<li><a href="#" onclick="return false" onmousedown="javascript:ackapa('cevaplamadivi<?php echo $cevap_id; ?>');">Altına Gir</a></li>

										<li><a href="#" title="Düzenle" onclick="return false" onmousedown="javascript:ackapa('duzenleme_divi_<?php echo $cevap_id; ?>');">Düzenle</a></li>
										
										<li style="padding-left: 15px;">

											<form action="" method="POST" style="margin-bottom: 0px;">
												<input type="hidden" name="silincek_id" value="<?php echo $cevap_id; ?>">
												<input type="hidden" name="sil_tip" value="2">
												<input type="hidden" name="numara" value="<?php echo $tp; ?>" >
												<input type="hidden" name="cevap_turu" value="0">
												<input type="submit" name="cevabi_sil" value="Sil" style="background-color: white; border-style: none;  width: 100%; text-align: left;" onclick="return window.confirm('Bu yolun dönüşü yok bak!')">
											</form>

										</li>

										<?php if($uye_tipi == '1' && $cevabi_yazan_tipi != '1'){?>

											<li><a href="#" title="Uzaklaştırma" onclick="return false" onmousedown="javascript:ackapa('ban_kutusu<?php echo $cevap_id; ?>');">Uzaklaştırma</a></li>

										<?php } ?>

									</ul>

								</div>

							</div>

							<div class="col-md-9 col-sm-6 col-xs-3" style="text-align: right;"> 

								<?php echo $cn; ?>
								
							</div>
								
						<?php }else{ ?>

							<div class="col-md-1 col-sm-2 col-xs-3">
								
								<form action="" method="POST">

									<input type="hidden" name="c_artilayanlar" value="<?php echo $c_artilayanlar; ?>" />

									<input type="hidden" name="c_eksileyenler" value="<?php echo $c_eksileyenler; ?>" />

									<input type="hidden" name="numara" value="<?php echo $tp; ?>" />

									<input type="hidden" name="cevabi_yazanin_arti_puani" value="<?php echo $cevabi_yazanin_arti_puani; ?>" />

									<input type="hidden" name="cevabi_yazanin_eksi_puani" value="<?php echo $cevabi_yazanin_eksi_puani; ?>" />

									<input type="hidden" name="cevabi_yazan_id" value="<?php echo $cevabi_yazan_id; ?>" />

									<input type="hidden" name="cevap_id" value="<?php echo $cevap_id; ?>" />

									<input type="hidden" name="c_arti_puan" value="<?php echo $c_arti_puan; ?>" />

									<input type="hidden" name="c_eksi_puan" value="<?php echo $c_eksi_puan; ?>" />

									<button type="submit" name="cevap_puani_arttir" value=" " style="background-color:#FFFFFF; border-style:none; padding: 0px;"><span><i class="fa fa-angle-up fa-lg" style="color:#535A3B;"></i></span></button>

									&nbsp;<small><?php if($c_arti_puan != 0){ echo $c_arti_puan; } ?></small>

								</form>

							</div>	

							<div class="col-md-1 col-sm-2 col-xs-3">
								
								<form action="" method="POST">

									<input type="hidden" name="c_artilayanlar" value="<?php echo $c_artilayanlar; ?>" />

									<input type="hidden" name="c_eksileyenler" value="<?php echo $c_eksileyenler; ?>" />

									<input type="hidden" name="numara" value="<?php echo $tp; ?>" />

									<input type="hidden" name="cevabi_yazanin_arti_puani" value="<?php echo $cevabi_yazanin_arti_puani; ?>" />

									<input type="hidden" name="cevabi_yazanin_eksi_puani" value="<?php echo $cevabi_yazanin_eksi_puani; ?>" />

									<input type="hidden" name="cevabi_yazan_id" value="<?php echo $cevabi_yazan_id; ?>" />

									<input type="hidden" name="cevap_id" value="<?php echo $cevap_id; ?>" />

									<input type="hidden" name="c_arti_puan" value="<?php echo $c_arti_puan; ?>" />

									<input type="hidden" name="c_eksi_puan" value="<?php echo $c_eksi_puan; ?>" />

									<button type="submit" name="cevap_puani_azalt" value=" " style="background-color:#FFFFFF; border-style:none; padding: 0px;"><span><i class="fa fa-angle-down fa-lg" style="color:#535A3B;"></i></span></button>

									&nbsp;<small><?php if($c_eksi_puan != 0){ echo $c_eksi_puan; } ?></small>

								</form>

							</div>	

							<div class="col-md-1 col-sm-2 col-xs-3">

								<div class="btn-group" role="group" aria-label="...">
								
									<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color: #FFFFFF; border-style: none;">

										<i class="fa fa-ellipsis-h fa-lg" style="color:#535A3B;"></i>

									</button>

									<ul class="dropdown-menu dropdown-menu-left">

										<li><a href="#" onclick="return false" onmousedown="javascript:ackapa('cevaplamadivi<?php echo $cevap_id; ?>');">Altına Gir</a></li>

										<li><a href="#" title="Rapor Et" onclick="return false" onmousedown="javascript:ackapa('sikayet_kutusu<?php echo $cevap_id; ?>');">Şikayet Et</a></li>
										
										<li style="padding-left: 15px;">

											<?php

												$takib = 0;

												$takipciler = explode(",", $uye_takip);

												foreach ($takipciler as $key => $value) {
													
													if ($value == $cevabi_yazan_id) {
														
														$takib = 1;

													}

												}

												if ($takib == 0) { ?>

													<form action="" method="POST" style="margin-bottom: 0px;">
														<input type="hidden" name="giriyazanid" value="<?php echo $cevabi_yazan_id; ?>">
														<input type="hidden" name="numara" value="<?php echo $tp; ?>" />
														<input type="submit" name="takip" value="Takip Et" style="background-color: white; border-style: none;  width: 100%; text-align: left;" onclick="return window.confirm('Olum emin misin lan?')">
													</form>

												<?php }elseif($takib == 1){ ?>

													<form action="" method="POST" style="margin-bottom: 0px;">
														<input type="hidden" name="giriyazanid" value="<?php echo $cevabi_yazan_id; ?>">
														<input type="hidden" name="numara" value="<?php echo $tp; ?>" />
														<input type="submit" name="takipbirak" value="Takibi Bırak" style="background-color: white; border-style: none;  width: 100%; text-align: left;" onclick="return window.confirm('Olum emin misin lan?')">
													</form>

												<?php }

											?>											

										</li>

									</ul>

								</div>

							</div>

							<div class="col-md-9 col-sm-6 col-xs-3" style="text-align: right;">

								<?php echo $cn; ?>
								
							</div>

						<?php 	} }else{ ?>

							<div class="row" style="padding: 10px;">

								<div class="col-md-1 col-sm-2 col-xs-3">
									
									<i class="fa fa-angle-up fa-lg" style="color:#535A3B;"></i></span>&nbsp;&nbsp;<small><?php echo $c_arti_puan; ?></small>

								</div>	

								<div class="col-md-1 col-sm-2 col-xs-3">
									
									<i class="fa fa-angle-down fa-lg" style="color:#535A3B;"></i></span>&nbsp;&nbsp;<small><?php echo $c_eksi_puan; ?></small>

								</div>	

								<div class="col-md-10 col-sm-8 col-xs-6" style="text-align: right;">

									<?php echo $cn; ?>
										
								</div>

							</div>

						<?php } ?>

							<?php if($girdi == '1'){

							if($cevabi_yazan_id == $uye_id || $uye_id == '1'){ ?>

							<div class="col-md-12" id="duzenleme_divi_<?php echo $cevap_id; ?>" style="display:none;">
														
								<form action="" method="POST">

									<div class="form-group"><textarea name="cevap_metni" class="form-control"><?php echo $d_cevap_metni; ?></textarea></div>
									<input type="hidden" name="cevap_id" value="<?php echo $cevap_id; ?>">
									<input type="hidden" name="numara" value="<?php echo $tp; ?>">
									<input type="hidden" name="giritipi" value="0">
									<div class="form-group"><button type="submit" name="cevap_duzenle" value=" " class="btn btn-default">Düzenle</button></div>
						
								</form>

							</div>

							<?php } ?>

							<div class="col-md-12" id="sikayet_kutusu<?php echo $cevap_id; ?>" style="display:none;">

								<form action="" method="POST">

									<div class="form-group"><textarea name="cevapSikayetKutusu" class="form-control" placeholder="Bu paylaşımı yapan kullanıcı hakkında şikayetiniz nedir?"></textarea></div>
									<input type="hidden" name="cevabi_yazan_id" value="<?php echo $cevabi_yazan_id; ?>">
									<input type="hidden" name="cevap_id" value="<?php echo $cevap_id; ?>">
									<div class="form-group"><button type="submit" name="cevap_sikayet_formu" value=" " class="btn btn-default">Şikayet Et</button></div>
								</form>

							</div>

							<div class="col-md-12" id="cevaplamadivi<?php echo $cevap_id; ?>" style="display:none; text-align:center;">
								<form action="" method="POST">
									<textarea name="cevap_metni" placeholder="Girini girerken kullanabileceğin fonksiyonlar : (bkz.başlık adı) / (url.herhangibir link) / (res.resim linki) / (you.youtube videosu linki) / (dai.dailymotion videosu linki)" class="form-control"></textarea>
									<input type="hidden" name="cevabi_yazan_id" value="<?php echo $cevabi_yazan_id; ?>">
									<input type="hidden" name="cevap_turu" value="1" />
									<input type="hidden" name="cevab_id" value="<?php echo $cevap_id; ?>" />
									<input type="hidden" name="numara" value="<?php echo $tp; ?>" />
									<button type="submit" name="cevap_formu" value=" " class="btn btn-default" style="margin-top:5px;">Gönder</button>
								</form>
							</div>

							<?php if ($uye_tipi == '1' && $cevabi_yazan_tipi != '1') { ?>

							<div class="col-md-12" id="ban_kutusu<?php echo $cevap_id; ?>" style="display:none;">

								<h4><strong>Uzaklaştırma Formu</strong></h4>

								<form action="" method="POST">
								
									<input type="text" name="ceza_sebebi" placeholder="ceza sebebi" class="form-control">

									<input type="hidden" name="girdi_id" value="<?php echo $cevap_id; ?>">

									<input type="hidden" name="ban_tipi" value="2">

									<input type="hidden" name="kisi_id" value="<?php echo $cevabi_yazan_id; ?>">

									<input type="submit" name="banla" value="Uzaklaştır">

								</form>

							</div>

						<?php } }?>						

						</div> <!-- BİRİNCİL GİRİLER DİV BİTİŞİ-->

		<?php

			$ccn = 0;

			$query = $db->query("SELECT * FROM giriler WHERE giriustid = '{$cevap_id}' AND giritipi = '1' AND silindi = '0' ORDER BY giriid ASC", PDO::FETCH_ASSOC);

			if ( $query->rowCount() ){

				foreach( $query as $satir ){
				
					$cevaba_cevap_yazan_id = cekf($satir['giriyazarid']);

					$engelli_bu = 0;

					foreach ($engellileri_patlat as $key => $value) {
						
						if ($cevaba_cevap_yazan_id == $value) {
							
							$engelli_bu = 1;

						}

					}

					if ($engelli_bu == 0) {

						$ccn++;

						$tp++;
						
						$cevabin_cevabi_id = cekf($satir['giriid']);
						
						$cevap_metni = cekf($satir['girimetin']);
						
						$cevap_metni = nl2br($cevap_metni);

						$cevap_metni = bkz($cevap_metni);

						$cevap_metni = res($cevap_metni);

						$cevap_metni = url($cevap_metni);

						$cevap_metni = you($cevap_metni);

						$cevap_metni = dai($cevap_metni);

						$d_cevap_metni = cekf($satir['girimetin']);

						$row = $db->query("SELECT * FROM uyeler WHERE uye_id = '{$cevaba_cevap_yazan_id}'")->fetch(PDO::FETCH_ASSOC);
							
						$ikincili_yazanin_adi = cekf($row['uye_adi']);

						$iki_yazanin_arti_puani = cekf($row['arti_puan']);

						$iki_yazanin_eksi_puani = cekf($row['eksi_puan']);

						$iki_yazanin_tipi = cekf($row['uye_tipi']);

						$cevap_tarihi = cekf($satir['giritarih']);

						$saniye = cekf($satir['girisaniye']);

						$ne_kadar_once = ne_kadar_once($su_an,$saniye);

						$cc_arti_puan = cekf($satir['giriarti']);

						$cc_eksi_puan = cekf($satir['girieksi']);

						$iki_artilayanlar = cekf($satir['artilayanlar']);

						$iki_eksileyenler = cekf($satir['eksileyenler']);

			?>

						

							<div class="col-md-11 col-md-offset-1 col-sm-11 col-sm-offset-1 col-xs-11 col-xs-offset-1  cerceve" style="padding:10px 0px 0px 0px;">

								<div class="col-md-6 col-sm-6 col-xs-6"><a name="<?php echo $tp; ?>" href="http://kurtsozluk.net/profil.php?id=<?php echo $cevaba_cevap_yazan_id; ?>"><b><?php echo $ikincili_yazanin_adi; ?></b></a></div>

								<div class="col-md-6 col-sm-6 col-xs-6" style="text-align:right;"><a href="#" title="<?php echo $cevap_tarihi; ?>"><?php echo $ne_kadar_once; ?></a></div>

								<div class="col-md-12 col-sm-12 col-xs-12"><p style="margin:0px;"><?php echo $cevap_metni; ?></p></div>

							<?php if($girdi == 1){ if($cevaba_cevap_yazan_id == $uye_id || $uye_tipi == '1' || $uye_tipi == '2'){ ?>	

								<div class="col-md-1 col-sm-2 col-xs-3">
									
									<form action="" method="POST">

										<input type="hidden" name="c_artilayanlar" value="<?php echo $iki_artilayanlar; ?>" />

										<input type="hidden" name="c_eksileyenler" value="<?php echo $iki_eksileyenler; ?>" />

										<input type="hidden" name="numara" value="<?php echo $tp; ?>" />

										<input type="hidden" name="cevabi_yazanin_arti_puani" value="<?php echo $iki_yazanin_arti_puani; ?>" />

										<input type="hidden" name="cevabi_yazanin_eksi_puani" value="<?php echo $iki_yazanin_eksi_puani; ?>" />

										<input type="hidden" name="cevabi_yazan_id" value="<?php echo $cevaba_cevap_yazan_id; ?>" />

										<input type="hidden" name="cevap_id" value="<?php echo $cevabin_cevabi_id; ?>" />

										<input type="hidden" name="c_arti_puan" value="<?php echo $cc_arti_puan; ?>" />

										<input type="hidden" name="c_eksi_puan" value="<?php echo $cc_eksi_puan; ?>" />

										<button type="submit" name="cevap_puani_arttir" value=" " style="background-color:#FFFFFF; border-style:none; padding: 0px;"><span><i class="fa fa-angle-up fa-lg" style="color:#535A3B;"></i></span></button>

										&nbsp;<small><?php if($cc_arti_puan != 0){ echo $cc_arti_puan; } ?></small>

									</form>

								</div>

								<div class="col-md-1 col-sm-2 col-xs-3">
									
									<form action="" method="POST">

										<input type="hidden" name="c_artilayanlar" value="<?php echo $iki_artilayanlar; ?>" />

										<input type="hidden" name="c_eksileyenler" value="<?php echo $iki_eksileyenler; ?>" />

										<input type="hidden" name="numara" value="<?php echo $tp; ?>" />

										<input type="hidden" name="cevabi_yazanin_arti_puani" value="<?php echo $iki_yazanin_arti_puani; ?>" />

										<input type="hidden" name="cevabi_yazanin_eksi_puani" value="<?php echo $iki_yazanin_eksi_puani; ?>" />

										<input type="hidden" name="cevabi_yazan_id" value="<?php echo $cevaba_cevap_yazan_id; ?>" />

										<input type="hidden" name="cevap_id" value="<?php echo $cevabin_cevabi_id; ?>" />

										<input type="hidden" name="c_arti_puan" value="<?php echo $cc_arti_puan; ?>" />

										<input type="hidden" name="c_eksi_puan" value="<?php echo $cc_eksi_puan; ?>" />

										<button type="submit" name="cevap_puani_azalt" value=" " style="background-color:#FFFFFF; border-style:none; padding: 0px;"><span><i class="fa fa-angle-down fa-lg" style="color:#535A3B;"></i></span></button>

										&nbsp;<small><?php if($cc_eksi_puan != 0){ echo $cc_eksi_puan; } ?></small>

									</form>

								</div>
										
								<div class="col-md-1 col-sm-2 col-xs-3">

									<div class="btn-group" role="group" aria-label="...">
									
										<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color: #FFFFFF; border-style: none;">

											<i class="fa fa-ellipsis-h fa-lg" style="color:#535A3B;"></i>

										</button>

										<ul class="dropdown-menu dropdown-menu-left">

											<li><a href="#" onclick="return false" onmousedown="javascript:ackapa('cevaplamadivi<?php echo $cevabin_cevabi_id; ?>');">Altına Gir</a></li>

											<li><a href="#" title="Düzenle" onclick="return false" onmousedown="javascript:ackapa('duzenleme_divi_<?php echo $cevabin_cevabi_id; ?>');">Düzenle</a></li>
											
											<li style="padding-left: 15px;">

												<form action="" method="POST" style="margin-bottom: 0px;">
													<input type="hidden" name="silincek_id" value="<?php echo $cevabin_cevabi_id; ?>">
													<input type="hidden" name="sil_tip" value="2">
													<input type="hidden" name="numara" value="<?php echo $tp; ?>" >
													<input type="hidden" name="cevap_turu" value="1">
													<input type="submit" name="cevabi_sil" value="Sil" style="background-color: white; border-style: none;  width: 100%; text-align: left;" onclick="return window.confirm('Bu yolun dönüşü yok bak!')">
												</form>

											</li>

											<?php if($uye_tipi == '1' && $iki_yazanin_tipi != '1'){?>

												<li><a href="#" title="Uzaklaştırma" onclick="return false" onmousedown="javascript:ackapa('ban_kutusu<?php echo $cevabin_cevabi_id; ?>');">Uzaklaştırma</a></li>

											<?php } ?>

										</ul>

									</div>

								</div>

								<div class="col-md-9 col-sm-6 col-xs-3" style="text-align: right;"> 

									<?php echo $cn.'.'.$ccn; ?>
									
								</div>
									
							<?php }else{ ?>

								<div class="col-md-1 col-sm-2 col-xs-3">
									
									<form action="" method="POST">

										<input type="hidden" name="c_artilayanlar" value="<?php echo $iki_artilayanlar; ?>" />

										<input type="hidden" name="c_eksileyenler" value="<?php echo $iki_eksileyenler; ?>" />

										<input type="hidden" name="numara" value="<?php echo $tp; ?>" />

										<input type="hidden" name="cevabi_yazanin_arti_puani" value="<?php echo $iki_yazanin_arti_puani; ?>" />

										<input type="hidden" name="cevabi_yazanin_eksi_puani" value="<?php echo $iki_yazanin_eksi_puani; ?>" />

										<input type="hidden" name="cevabi_yazan_id" value="<?php echo $cevaba_cevap_yazan_id; ?>" />

										<input type="hidden" name="cevap_id" value="<?php echo $cevabin_cevabi_id; ?>" />

										<input type="hidden" name="c_arti_puan" value="<?php echo $cc_arti_puan; ?>" />

										<input type="hidden" name="c_eksi_puan" value="<?php echo $cc_eksi_puan; ?>" />

										<button type="submit" name="cevap_puani_arttir" value=" " style="background-color:#FFFFFF; border-style:none; padding: 0px;"><span><i class="fa fa-angle-up fa-lg" style="color:#535A3B;"></i></span></button>

										&nbsp;<small><?php if($cc_arti_puan != 0){ echo $cc_arti_puan; } ?></small>

									</form>

								</div>	

								<div class="col-md-1 col-sm-2 col-xs-3">
									
									<form action="" method="POST">

										<input type="hidden" name="c_artilayanlar" value="<?php echo $iki_artilayanlar; ?>" />

										<input type="hidden" name="c_eksileyenler" value="<?php echo $iki_eksileyenler; ?>" />

										<input type="hidden" name="numara" value="<?php echo $tp; ?>" />

										<input type="hidden" name="cevabi_yazanin_arti_puani" value="<?php echo $iki_yazanin_arti_puani; ?>" />

										<input type="hidden" name="cevabi_yazanin_eksi_puani" value="<?php echo $iki_yazanin_eksi_puani; ?>" />

										<input type="hidden" name="cevabi_yazan_id" value="<?php echo $cevaba_cevap_yazan_id; ?>" />

										<input type="hidden" name="cevap_id" value="<?php echo $cevabin_cevabi_id; ?>" />

										<input type="hidden" name="c_arti_puan" value="<?php echo $cc_arti_puan; ?>" />

										<input type="hidden" name="c_eksi_puan" value="<?php echo $cc_eksi_puan; ?>" />

										<button type="submit" name="cevap_puani_azalt" value=" " style="background-color:#FFFFFF; border-style:none; padding: 0px;"><span><i class="fa fa-angle-down fa-lg" style="color:#535A3B;"></i></span></button>

										&nbsp;<small><?php if($cc_eksi_puan != 0){ echo $cc_eksi_puan; } ?></small>

									</form>

								</div>	

								<div class="col-md-1 col-sm-2 col-xs-3">

									<div class="btn-group" role="group" aria-label="...">
									
										<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color: #FFFFFF; border-style: none;">

											<i class="fa fa-ellipsis-h fa-lg" style="color:#535A3B;"></i>

										</button>

										<ul class="dropdown-menu dropdown-menu-left">

											<li><a href="#" onclick="return false" onmousedown="javascript:ackapa('cevaplamadivi<?php echo $cevabin_cevabi_id; ?>');">Altına Gir</a></li>

											<li><a href="#" title="Rapor Et" onclick="return false" onmousedown="javascript:ackapa('sikayet_kutusu<?php echo $cevabin_cevabi_id; ?>');">Şikayet Et</a></li>
											
											<li style="padding-left: 15px;">

												<?php

													$takib = 0;

													$takipciler = explode(",", $uye_takip);

													foreach ($takipciler as $key => $value) {
														
														if ($value == $cevaba_cevap_yazan_id) {
															
															$takib = 1;

														}

													}

													if ($takib == 0) { ?>

														<form action="" method="POST" style="margin-bottom: 0px;">
															<input type="hidden" name="giriyazanid" value="<?php echo $cevaba_cevap_yazan_id; ?>">
															<input type="hidden" name="numara" value="<?php echo $tp; ?>" />
															<input type="submit" name="takip" value="Takip Et" style="background-color: white; border-style: none;  width: 100%; text-align: left;" onclick="return window.confirm('Olum emin misin lan?')">
														</form>

													<?php }elseif($takib == 1){ ?>

														<form action="" method="POST" style="margin-bottom: 0px;">
															<input type="hidden" name="giriyazanid" value="<?php echo $cevaba_cevap_yazan_id; ?>">
															<input type="hidden" name="numara" value="<?php echo $tp; ?>" />
															<input type="submit" name="takipbirak" value="Takibi Bırak" style="background-color: white; border-style: none;  width: 100%; text-align: left;" onclick="return window.confirm('Olum emin misin lan?')">
														</form>

													<?php }

												?>		

											</li>

										</ul>

									</div>

								</div>

								<div class="col-md-9 col-sm-6 col-xs-3" style="text-align: right;">

									<?php echo $cn.'.'.$ccn; ?>
									
								</div>

							<?php 	} }else{ ?>

								<div class="row" style="padding: 10px;">

									<div class="col-md-1 col-sm-2 col-xs-3">
										
										<i class="fa fa-angle-up fa-lg" style="color:#535A3B;"></i></span>&nbsp;<small><?php echo $cc_arti_puan; ?></small>

									</div>	

									<div class="col-md-1 col-sm-2 col-xs-3">
										
										<i class="fa fa-angle-down fa-lg" style="color:#535A3B;"></i></span>&nbsp;<small><?php echo $cc_eksi_puan; ?></small>

									</div>	

									<div class="col-md-10 col-sm-8 col-xs-6" style="text-align: right;">

										<?php echo $cn.'.'.$ccn; ?>
											
									</div>

								</div>

							<?php } ?>

								<?php if($girdi == '1'){

								if($cevaba_cevap_yazan_id == $uye_id || $uye_tipi == '1' || $uye_tipi == '2'){ ?>

								<div class="col-md-12" id="duzenleme_divi_<?php echo $cevabin_cevabi_id; ?>" style="display:none;">
															
									<form action="" method="POST">

										<div class="form-group"><textarea name="cevap_metni" class="form-control" cols="5"><?php echo $d_cevap_metni; ?></textarea></div>
										<input type="hidden" name="cevap_id" value="<?php echo $cevabin_cevabi_id; ?>">
										<input type="hidden" name="numara" value="<?php echo $tp; ?>">
										<input type="hidden" name="giritipi" value="1">
										<div class="form-group"><button type="submit" name="cevap_duzenle" value=" " class="btn btn-default">Düzenle</button></div>
							
									</form>

								</div>

								<?php } ?>

								<div class="col-md-12" id="sikayet_kutusu<?php echo $cevabin_cevabi_id; ?>" style="display:none;">

									<form action="" method="POST">

										<div class="form-group"><textarea name="cevapSikayetKutusu" class="form-control" placeholder="Bu paylaşımı yapan kullanıcı hakkında şikayetiniz nedir?"></textarea></div>
										<input type="hidden" name="cevabi_yazan_id" value="<?php echo $cevaba_cevap_yazan_id; ?>">
										<input type="hidden" name="cevap_id" value="<?php echo $cevabin_cevabi_id; ?>">
										<div class="form-group"><button type="submit" name="cevap_sikayet_formu" value=" " class="btn btn-default">Şikayet Et</button></div>
									</form>

								</div>

								<div class="col-md-12" id="cevaplamadivi<?php echo $cevabin_cevabi_id; ?>" style="display:none; text-align:center;">
									<form action="" method="POST">
										<textarea name="cevap_metni" placeholder="Girini girerken kullanabileceğin fonksiyonlar : (bkz.başlık adı) / (url.herhangibir link) / (res.resim linki) / (you.youtube videosu linki) / (dai.dailymotion videosu linki)" class="form-control"></textarea>
										<input type="hidden" name="cevabi_yazan_id" value="<?php echo $cevaba_cevap_yazan_id; ?>">
										<input type="hidden" name="cevap_turu" value="2" />
										<input type="hidden" name="cevab_id" value="<?php echo $cevabin_cevabi_id; ?>" />
										<input type="hidden" name="numara" value="<?php echo $tp; ?>" />
										<button type="submit" name="cevap_formu" value=" " class="btn btn-default" style="margin-top:5px;">Gönder</button>
									</form>
								</div>

								<?php if ($uye_tipi == '1' && $iki_yazanin_tipi != '1') { ?>

								<div class="col-md-12" id="ban_kutusu<?php echo $cevabin_cevabi_id; ?>" style="display:none;">

									<h4><strong>Uzaklaştırma Formu</strong></h4>

									<form action="" method="POST">
									
										<input type="text" name="ceza_sebebi" placeholder="ceza sebebi" class="form-control">

										<input type="hidden" name="girdi_id" value="<?php echo $cevabin_cevabi_id; ?>">

										<input type="hidden" name="ban_tipi" value="2">

										<input type="hidden" name="kisi_id" value="<?php echo $cevaba_cevap_yazan_id; ?>">

										<input type="submit" name="banla" value="Uzaklaştır">

									</form>

								</div>

								<?php } }?>

							</div> <!-- İKİNCİL GİRİLER DİV BİTİŞİ-->

		<?php

			$un = 0; //ucuncu numara

			$query = $db->query("SELECT * FROM giriler WHERE giriustid = '{$cevabin_cevabi_id}' AND giritipi = '2' AND silindi = '0' ORDER BY giriid ASC", PDO::FETCH_ASSOC);

			if ( $query->rowCount() ){

				foreach( $query as $satir ){

					$ucuncuyu_yazan_id = cekf($satir['giriyazarid']);

					$engelli_bu = 0;

					foreach ($engellileri_patlat as $key => $value) {
						
						if ($ucuncuyu_yazan_id == $value) {
							
							$engelli_bu = 1;

						}

					}

					if ($engelli_bu == 0) {

						$un++;

						$tp++;

						$ucuncu_id = cekf($satir['giriid']);
						
						$ucuncu_metni = cekf($satir['girimetin']);
						
						$ucuncu_metni = nl2br($ucuncu_metni);

						$ucuncu_metni = bkz($ucuncu_metni);

						$ucuncu_metni = res($ucuncu_metni);

						$ucuncu_metni = url($ucuncu_metni);

						$ucuncu_metni = you($ucuncu_metni);

						$ucuncu_metni = dai($ucuncu_metni);

						$duzenlenen_ucuncu_metni = cekf($satir['girimetin']);

						$row = $db->query("SELECT * FROM uyeler WHERE uye_id = '{$ucuncuyu_yazan_id}'")->fetch(PDO::FETCH_ASSOC);

						$ucuncuyu_yazanin_adi = cekf($row['uye_adi']);

						$ucuncuyu_yazanin_arti_puani = cekf($row['arti_puan']);

						$ucuncuyu_yazanin_eksi_puani = cekf($row['eksi_puan']);

						$ucuncunun_tipi = cekf($row['uye_tipi']);

						$ucuncunun_tarihi = cekf($satir['giritarih']);

						$ucuncu_saniye = cekf($satir['girisaniye']);

						$ucuncu_ne_kadar_once = ne_kadar_once($su_an,$ucuncu_saniye);

						$ucuncu_arti_puan = cekf($satir['giriarti']);

						$ucuncu_eksi_puan = cekf($satir['girieksi']);

						$ucuncu_artilayanlar = cekf($satir['artilayanlar']);

						$ucuncu_eksileyenler = cekf($satir['eksileyenler']);

			?>

							<div class="col-md-10 col-md-offset-2 col-sm-10 col-sm-offset-2 col-xs-10 col-xs-offset-2 cerceve" style="padding:10px 0px 0px 0px;">

								<div class="col-md-6 col-sm-6 col-xs-6"><a name="<?php echo $tp; ?>" href="http://kurtsozluk.net/profil.php?id=<?php echo $ucuncuyu_yazan_id; ?>"><b><?php echo $ucuncuyu_yazanin_adi; ?></b></a></div>

								<div class="col-md-6 col-sm-6 col-xs-6" style="text-align:right;"><a href="#" title="<?php echo $ucuncunun_tarihi; ?>"><?php echo $ucuncu_ne_kadar_once; ?></a></div>

								<div class="col-md-12 col-sm-12 col-xs-12"><p style="margin:0px;"><?php echo $ucuncu_metni; ?></p></div>

							<?php if($girdi == 1){ if($ucuncuyu_yazan_id == $uye_id || $uye_tipi == '1' || $uye_tipi == '2'){ ?>	

								<div class="col-md-1 col-sm-2 col-xs-3">
									
									<form action="" method="POST">

										<input type="hidden" name="c_artilayanlar" value="<?php echo $ucuncu_artilayanlar; ?>" />

										<input type="hidden" name="c_eksileyenler" value="<?php echo $ucuncu_eksileyenler; ?>" />

										<input type="hidden" name="numara" value="<?php echo $tp; ?>" />

										<input type="hidden" name="cevabi_yazanin_arti_puani" value="<?php echo $ucuncuyu_yazanin_arti_puani; ?>" />

										<input type="hidden" name="cevabi_yazanin_eksi_puani" value="<?php echo $ucuncuyu_yazanin_eksi_puani; ?>" />

										<input type="hidden" name="cevabi_yazan_id" value="<?php echo $ucuncuyu_yazan_id; ?>" />

										<input type="hidden" name="cevap_id" value="<?php echo $ucuncu_id; ?>" />

										<input type="hidden" name="c_arti_puan" value="<?php echo $ucuncu_arti_puan; ?>" />

										<input type="hidden" name="c_eksi_puan" value="<?php echo $ucuncu_eksi_puan; ?>" />

										<button type="submit" name="cevap_puani_arttir" value=" " style="background-color:#FFFFFF; border-style:none; padding: 0px;"><span><i class="fa fa-angle-up fa-lg" style="color:#535A3B;"></i></span></button>

										&nbsp;<small><?php if($ucuncu_arti_puan != 0){ echo $ucuncu_arti_puan; } ?></small>

									</form>

								</div>

								<div class="col-md-1 col-sm-2 col-xs-3">
									
									<form action="" method="POST">

										<input type="hidden" name="c_artilayanlar" value="<?php echo $ucuncu_artilayanlar; ?>" />

										<input type="hidden" name="c_eksileyenler" value="<?php echo $ucuncu_eksileyenler; ?>" />

										<input type="hidden" name="numara" value="<?php echo $tp; ?>" />

										<input type="hidden" name="cevabi_yazanin_arti_puani" value="<?php echo $ucuncuyu_yazanin_arti_puani; ?>" />

										<input type="hidden" name="cevabi_yazanin_eksi_puani" value="<?php echo $ucuncuyu_yazanin_eksi_puani; ?>" />

										<input type="hidden" name="cevabi_yazan_id" value="<?php echo $ucuncuyu_yazan_id; ?>" />

										<input type="hidden" name="cevap_id" value="<?php echo $ucuncu_id; ?>" />

										<input type="hidden" name="c_arti_puan" value="<?php echo $ucuncu_arti_puan; ?>" />

										<input type="hidden" name="c_eksi_puan" value="<?php echo $ucuncu_eksi_puan; ?>" />

										<button type="submit" name="cevap_puani_azalt" value=" " style="background-color:#FFFFFF; border-style:none; padding: 0px;"><span><i class="fa fa-angle-down fa-lg" style="color:#535A3B;"></i></span></button>

										&nbsp;<small><?php if($ucuncu_eksi_puan != 0){ echo $ucuncu_eksi_puan; } ?></small>
									</form>

								</div>
										
								<div class="col-md-1 col-sm-2 col-xs-3">

									<div class="btn-group" role="group" aria-label="...">
									
										<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color: #FFFFFF; border-style: none;">

											<i class="fa fa-ellipsis-h fa-lg" style="color:#535A3B;"></i>

										</button>

										<ul class="dropdown-menu dropdown-menu-left">

											<li><a href="#" title="Düzenle" onclick="return false" onmousedown="javascript:ackapa('duzenleme_divi_<?php echo $ucuncu_id; ?>');">Düzenle</a></li>
											
											<li style="padding-left: 15px;">

												<form action="" method="POST" style="margin-bottom: 0px;">
													<input type="hidden" name="silincek_id" value="<?php echo $ucuncu_id; ?>">
													<input type="hidden" name="sil_tip" value="2">
													<input type="hidden" name="numara" value="<?php echo $tp; ?>" >
													<input type="hidden" name="cevap_turu" value="2">
													<input type="submit" name="cevabi_sil" value="Sil" style="background-color: white; border-style: none;  width: 100%; text-align: left;" onclick="return window.confirm('Bu yolun dönüşü yok bak!')">
												</form>

											</li>

											<?php if($uye_tipi == '1' && $ucuncunun_tipi != '1'){?>

												<li><a href="#" title="Uzaklaştırma" onclick="return false" onmousedown="javascript:ackapa('ban_kutusu<?php echo $ucuncu_id; ?>');">Uzaklaştırma</a></li>

											<?php } ?>

										</ul>

									</div>

								</div>

								<div class="col-md-9 col-sm-6 col-xs-3" style="text-align: right;"> 

									<?php echo $cn.'.'.$ccn.'.'.$un; ?>
									
								</div>
									
							<?php }else{ ?>

								<div class="col-md-1 col-sm-2 col-xs-3">
									
									<form action="" method="POST">

										<input type="hidden" name="c_artilayanlar" value="<?php echo $ucuncu_artilayanlar; ?>" />

										<input type="hidden" name="c_eksileyenler" value="<?php echo $ucuncu_eksileyenler; ?>" />

										<input type="hidden" name="numara" value="<?php echo $tp; ?>" />

										<input type="hidden" name="cevabi_yazanin_arti_puani" value="<?php echo $ucuncuyu_yazanin_arti_puani; ?>" />

										<input type="hidden" name="cevabi_yazanin_eksi_puani" value="<?php echo $ucuncuyu_yazanin_eksi_puani; ?>" />

										<input type="hidden" name="cevabi_yazan_id" value="<?php echo $ucuncuyu_yazan_id; ?>" />

										<input type="hidden" name="cevap_id" value="<?php echo $ucuncu_id; ?>" />

										<input type="hidden" name="c_arti_puan" value="<?php echo $ucuncu_arti_puan; ?>" />

										<input type="hidden" name="c_eksi_puan" value="<?php echo $ucuncu_eksi_puan; ?>" />

										<button type="submit" name="cevap_puani_arttir" value=" " style="background-color:#FFFFFF; border-style:none; padding: 0px;"><span><i class="fa fa-angle-up fa-lg" style="color:#535A3B;"></i></span></button>

										&nbsp;<small><?php if($ucuncu_arti_puan != 0){ echo $ucuncu_arti_puan; } ?></small>

									</form>

								</div>	

								<div class="col-md-1 col-sm-2 col-xs-3">
									
									<form action="" method="POST">

										<input type="hidden" name="c_artilayanlar" value="<?php echo $ucuncu_artilayanlar; ?>" />

										<input type="hidden" name="c_eksileyenler" value="<?php echo $ucuncu_eksileyenler; ?>" />

										<input type="hidden" name="numara" value="<?php echo $tp; ?>" />

										<input type="hidden" name="cevabi_yazanin_arti_puani" value="<?php echo $ucuncuyu_yazanin_arti_puani; ?>" />

										<input type="hidden" name="cevabi_yazanin_eksi_puani" value="<?php echo $ucuncuyu_yazanin_eksi_puani; ?>" />

										<input type="hidden" name="cevabi_yazan_id" value="<?php echo $ucuncuyu_yazan_id; ?>" />

										<input type="hidden" name="cevap_id" value="<?php echo $ucuncu_id; ?>" />

										<input type="hidden" name="c_arti_puan" value="<?php echo $ucuncu_arti_puan; ?>" />

										<input type="hidden" name="c_eksi_puan" value="<?php echo $ucuncu_eksi_puan; ?>" />

										<button type="submit" name="cevap_puani_azalt" value=" " style="background-color:#FFFFFF; border-style:none; padding: 0px;"><span><i class="fa fa-angle-down fa-lg" style="color:#535A3B;"></i></span></button>

										&nbsp;<small><?php if($ucuncu_eksi_puan != 0){ echo $ucuncu_eksi_puan; } ?></small>

									</form>

								</div>	

								<div class="col-md-1 col-sm-2 col-xs-3">

									<div class="btn-group" role="group" aria-label="...">
									
										<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color: #FFFFFF; border-style: none;">

											<i class="fa fa-ellipsis-h fa-lg" style="color:#535A3B;"></i>

										</button>

										<ul class="dropdown-menu dropdown-menu-left">

											<li><a href="#" title="Rapor Et" onclick="return false" onmousedown="javascript:ackapa('sikayet_kutusu<?php echo $ucuncu_id; ?>');">Şikayet Et</a></li>
											
											<li style="padding-left: 15px;">

												<?php

													$takib = 0;

													$takipciler = explode(",", $uye_takip);

													foreach ($takipciler as $key => $value) {
														
														if ($value == $ucuncuyu_yazan_id) {
															
															$takib = 1;

														}

													}

													if ($takib == 0) { ?>

														<form action="" method="POST" style="margin-bottom: 0px;">
															<input type="hidden" name="giriyazanid" value="<?php echo $ucuncuyu_yazan_id; ?>">
															<input type="hidden" name="numara" value="<?php echo $tp; ?>" />
															<input type="submit" name="takip" value="Takip Et" style="background-color: white; border-style: none;  width: 100%; text-align: left;" onclick="return window.confirm('Olum emin misin lan?')">
														</form>

													<?php }elseif($takib == 1){ ?>

														<form action="" method="POST" style="margin-bottom: 0px;">
															<input type="hidden" name="giriyazanid" value="<?php echo $ucuncuyu_yazan_id; ?>">
															<input type="hidden" name="numara" value="<?php echo $tp; ?>" />
															<input type="submit" name="takipbirak" value="Takibi Bırak" style="background-color: white; border-style: none;  width: 100%; text-align: left;" onclick="return window.confirm('Olum emin misin lan?')">
														</form>

													<?php }

												?>		

											</li>

											<?php if($uye_tipi == '1'){?>

												<li><a href="#" title="Uzaklaştırma" onclick="return false" onmousedown="javascript:ackapa('ban_kutusu<?php echo $ucuncu_id; ?>');">Uzaklaştırma</a></li>

											<?php } ?>

										</ul>

									</div>

								</div>

								<div class="col-md-9 col-sm-6 col-xs-3" style="text-align: right;">

									<?php echo $cn.'.'.$ccn.'.'.$un; ?>
									
								</div>

							<?php 	} }else{ ?>

								<div class="row" style="padding: 10px;">

									<div class="col-md-1 col-sm-2 col-xs-3">
										
										<i class="fa fa-angle-up fa-lg" style="color:#535A3B;"></i></span>&nbsp;<small><?php echo $ucuncu_arti_puan; ?></small>

									</div>	

									<div class="col-md-1 col-sm-2 col-xs-3">
										
										<i class="fa fa-angle-down fa-lg" style="color:#535A3B;"></i></span>&nbsp;<small><?php echo $ucuncu_eksi_puan; ?></small>

									</div>	

									<div class="col-md-10 col-sm-8 col-xs-6" style="text-align: right;">

										<?php echo $cn.'.'.$ccn.'.'.$un; ?>
											
									</div>

								</div>

							<?php } ?>

								<?php if($girdi == '1'){

								if($ucuncuyu_yazan_id == $uye_id || $uye_tipi == '1' || $uye_tipi == '2'){ ?>

								<div class="col-md-12" id="duzenleme_divi_<?php echo $ucuncu_id; ?>" style="display:none;">
															
									<form action="" method="POST">

										<div class="form-group"><textarea name="cevap_metni" class="form-control" cols="5"><?php echo $duzenlenen_ucuncu_metni; ?></textarea></div>
										<input type="hidden" name="cevap_id" value="<?php echo $ucuncu_id; ?>">
										<input type="hidden" name="numara" value="<?php echo $tp; ?>">
										<input type="hidden" name="giritipi" value="2">
										<div class="form-group"><button type="submit" name="cevap_duzenle" value=" " class="btn btn-default">Düzenle</button></div>
							
									</form>

								</div>

								<?php } ?>

								<div class="col-md-12" id="sikayet_kutusu<?php echo $ucuncu_id; ?>" style="display:none;">

									<form action="" method="POST">

										<div class="form-group"><textarea name="cevapSikayetKutusu" class="form-control" placeholder="Bu paylaşımı yapan kullanıcı hakkında şikayetiniz nedir?"></textarea></div>
										<input type="hidden" name="cevabi_yazan_id" value="<?php echo $ucuncuyu_yazan_id; ?>">
										<input type="hidden" name="cevap_id" value="<?php echo $ucuncu_id; ?>">
										<div class="form-group"><button type="submit" name="cevap_sikayet_formu" value=" " class="btn btn-default">Şikayet Et</button></div>
									</form>

								</div>

								<?php if ($uye_tipi == '1' && $ucuncunun_tipi != '1') { ?>

								<div class="col-md-12" id="ban_kutusu<?php echo $ucuncu_id; ?>" style="display:none;">

									<h4><strong>Uzaklaştırma Formu</strong></h4>

									<form action="" method="POST">
									
										<input type="text" name="ceza_sebebi" placeholder="ceza sebebi" class="form-control">

										<input type="hidden" name="girdi_id" value="<?php echo $ucuncu_id; ?>">

										<input type="hidden" name="ban_tipi" value="2">

										<input type="hidden" name="kisi_id" value="<?php echo $ucuncuyu_yazan_id; ?>">

										<input type="submit" name="banla" value="Uzaklaştır">

									</form>

								</div>

								<?php } }?>

							</div> <!-- ÜÇÜNCÜL GİRİLER DİV BİTİŞİ-->

		<?php 	} //ÜÇÜNCÜL GİRİLER ENGELLİ KONTROL İFİ BİTİŞİ

			}

		}// ÜÇÜNCÜL GİRİLER WHİLE DÖNGÜSÜ BİTİŞ

		?>

					<a href="#" onclick="return false" onmousedown="javascript:ackapa('cevaplamadiviiki<?php echo $cevabin_cevabi_id; ?>');"><div class="col-md-10 col-md-offset-2 col-md-10 col-md-offset-2 col-xs-10 col-xs-offset-2 cevapcubugu text-center" style="background-color: <?php echo $uye_renk; ?>"><small style="color:white;"><?php echo $ikincili_yazanin_adi; ?> adlı yazarın altına yazmak için tıkla.</small></div></a>
					<div class="col-md-10 col-md-offset-2 col-md-10 col-md-offset-2 col-xs-10 col-xs-offset-2" id="cevaplamadiviiki<?php echo $cevabin_cevabi_id; ?>" style="display:none; text-align:center;">
						<form action="" method="POST">
							<textarea name="cevap_metni" placeholder="Girini girerken kullanabileceğin fonksiyonlar : (bkz.başlık adı) / (url.herhangibir link) / (res.resim linki) / (you.youtube videosu linki) / (dai.dailymotion videosu linki)" class="form-control"></textarea>
							<input type="hidden" name="cevabi_yazan_id" value="<?php echo $cevaba_cevap_yazan_id; ?>">
							<input type="hidden" name="cevap_turu" value="2" />
							<input type="hidden" name="cevab_id" value="<?php echo $cevabin_cevabi_id; ?>" />
							<input type="hidden" name="numara" value="<?php echo $tp; ?>" />
							<button type="submit" name="cevap_formu" value=" " class="btn btn-default" style="margin-top:5px;">Gönder</button>
						</form>
					</div>

		<?php 	 

				} //İKİNCİL GİRİLER ENGELLİ KONTROL İFİ BİTİŞİ

			}

		}// İKİNCİL GİRİLER WHİLE DÖNGÜSÜ BİTİŞ

		?>

					<a href="#" onclick="return false" onmousedown="javascript:ackapa('cevaplamadiviiki<?php echo $cevap_id; ?>');"><div class="col-md-11 col-md-offset-1 col-sm-11 col-sm-offset-1 col-xs-11 col-xs-offset-1 cevapcubugu text-center" style="background-color: <?php echo $uye_renk; ?>"><small style="color:white;"><?php echo $cevabi_yazanin_adi;?> adlı yazarın altına yazmak için tıkla.</small></div></a>
					<div class="col-md-11 col-md-offset-1 col-sm-11 col-sm-offset-1 col-xs-11 col-xs-offset-1" id="cevaplamadiviiki<?php echo $cevap_id; ?>" style="display:none; text-align:center;">
						<form action="" method="POST">
							<textarea name="cevap_metni" placeholder="Girini girerken kullanabileceğin fonksiyonlar : (bkz.başlık adı) / (url.herhangibir link) / (res.resim linki) / (you.youtube videosu linki) / (dai.dailymotion videosu linki)" class="form-control"></textarea>
							<input type="hidden" name="cevabi_yazan_id" value="<?php echo $cevabi_yazan_id; ?>">
							<input type="hidden" name="cevap_turu" value="1" />
							<input type="hidden" name="cevab_id" value="<?php echo $cevap_id; ?>" />
							<input type="hidden" name="numara" value="<?php echo $tp; ?>" />
							<button type="submit" name="cevap_formu" value=" " class="btn btn-default" style="margin-top:5px;">Gönder</button>
						</form>
					</div>

		<?php
					

				} // BİRİNCİL GİRİLER ENGELLİ KONTROL İFİ BİTİŞİ

			}

		} //BİRİNCİL GİRİLER WHİLE DÖNGÜSÜ BİTİŞ

		?>

					</div> <!-- GİRİLER DİVİNİN KAPANIŞI-->

				<?php if ($girdi == 1) { ?>

					<br/>

					<div class="row">	

						<div class="col-md-12" style="text-align:center;">

							<form action="" method="POST">
				
								<textarea name="cevap_metni" placeholder="Girini girerken kullanabileceğin fonksiyonlar : (bkz.başlık adı) / (url.herhangibir link) / (res.resim linki) / (you.youtube videosu linki) / (dai.dailymotion videosu linki)" class="form-control"></textarea>

								<input type="hidden" name="cevap_turu" value="0" />

								<input type="hidden" name="numara" value="<?php echo $tp; ?>" />

								<input type="hidden" name="cevab_id" value="<?php echo $konu_id; ?>" />
					
								<button type="submit" name="cevap_formu" value=" " class="btn btn-default" style="margin-top:5px;">Gönder</button>
				
							</form>	

						</div>

					</div>

				<?php }elseif($girdi == 0){ ?>

					<br/><div class="alert alert-info">Yorum yazmak için üye değilsen <a href="http://kurtsozluk.net/uye_ol.php">buraya</a> tıkla, üye isen nereden giriş yapman gerektiğini biliyorsundur zaten.</div>

				<?php } ?> 

					<div class="row">

						<div class="col-md-12" style="text-align: center;">

							<?php

								echo '<div class="btn-group" role="group" aria-label="...">';

								$i = 0;

								if($ss != 0){

									while($i <= $ss) { 

										$i++;

										echo '<a href="http://kurtsozluk.net/baslik.php?s='.$i.'&'.$baslikurl.'_'.$konu_id.'"><button type="button" class="btn btn-default">'.$i.'</button></a>';
									}

								}

								echo '</div>';

							?>

						</div>

					</div>

				</div> <!-- COL-MD-9 DİVİ KAPANIŞ -->

			</div> <!-- ROW DİVİ KAPANIŞ -->

		</div> <!-- CONTAINER DİVİ KAPANIŞ -->

		<?php include 'template/jscss.html'; ?>

	</body>

	<script src='autosize/dist/autosize.min.js'></script>
	<script>
		autosize(document.querySelectorAll('textarea'));
	</script>

</html>