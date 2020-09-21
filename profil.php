<?php

	include 'fonksiyonlar/bagla.php';

//değişkenler 

	if (isset($_GET['id']) === true && empty($_GET['id']) === false &&  is_numeric($_GET['id']) === true) {

		$profilindeki_uyenin_id = $_GET['id'];

		if (uye_id_var_mi($profilindeki_uyenin_id) == '0') {
			
			header("Location: http://kurtsozluk.net/index.php");
			
		}
	
	}else{

		header("Location: http://kurtsozluk.net/index.php");

	}	
	
	if (isset($profilindeki_uyenin_id) == true) {

		$satir = $db->query("SELECT * FROM uyeler WHERE uye_id = '{$profilindeki_uyenin_id}'")->fetch(PDO::FETCH_ASSOC);

		$p_uye_gosterim = cekf($satir['gosterim']);

		$p_uye_tipi = cekf($satir['uye_tipi']);

		$p_ceza_sayisi = cekf($satir['ceza_sayisi']);

		$p_uye_devre = cekf($satir['uye_devre']);
	
		$profilindeki_uyenin_adi = cekf($satir['uye_adi']);

		$p_son_giris_tarihi = cekf($satir['son_giris_tarihi']);

		$p_uye_foto = cekf($satir['foto']);

		if (empty($p_uye_foto) === true) {
			
			$p_uye_foto = 'icon/kurtico.png';

		}

		$p_uye_tarihi = cekf($satir['uyelik_tarihi']);

		$p_uye_motto = cekf($satir['uye_motto']);

		$p_engelliler = cekf($satir['engelliler']);

		$p_arsiv = cekf($satir['arsiv']);

	}	

	$adkon = $db->query("SELECT * FROM basliklar WHERE baslik = '{$profilindeki_uyenin_adi}' AND silindi = '0' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
		
	$ad_kon_id = cekf($adkon['baslikid']);

	$adbaslikurl = cekf($adkon['baslikurl']);

	if ($girdi == 1) {

		//TAKİPLEŞME VAR MI KONTROL

		$takib = 0;

		$takipciler = explode(",", $uye_takip);

		foreach ($takipciler as $key => $value) {
			
			if ($value == $profilindeki_uyenin_id) {
				
				$takib = 1;

			}

		}

		//ENGELLEŞME VAR MI KONTROL

		$engellimi = 0;

		$engellileri_patlat = explode(",", $uye_engellileri);

		foreach ($engellileri_patlat as $key => $value) {
			
			if ($value == $profilindeki_uyenin_id) {
				
				$engellimi = 1;

			}

		}

		$m_engelli = 0;

		$p_engelliler_patlat = explode(",", $p_engelliler);

		foreach ($p_engelliler_patlat as $key => $value) {
			
			if ($value == $uye_id) {
				
				$m_engelli = 1;

			}

		}

		//TAKİP KODLARI

		if (isset($_POST['takip']) === true) {
			
			if (empty($uye_takip) === true) {
				
				$y_takip = $profilindeki_uyenin_id;

			}else{

				$y_takip = "$uye_takip,$profilindeki_uyenin_id";

			}

			$query = $db->prepare("UPDATE uyeler SET takip = ? WHERE uye_id = ?"); 

			$guncelle = $query->execute(array($y_takip,$uye_id));

			header("Location: http://kurtsozluk.net/profil.php?id=".$profilindeki_uyenin_id);

			exit();

		}

	//TAKİP KALDIRMA KODLARI

		if (isset($_POST['notakip']) === true) {
			
			foreach ($takipciler as $key => $value) {
				
				if ($profilindeki_uyenin_id == $value) {
					
					unset($takipciler[$key]);

				}

			}

			$takipcileri_topla = implode(",", $takipciler);

			$query = $db->prepare("UPDATE uyeler SET takip = ? WHERE uye_id = ?"); 

			$guncelle = $query->execute(array($takipcileri_topla,$uye_id));

			header("Location: http://kurtsozluk.net/profil.php?id=".$profilindeki_uyenin_id);

			exit();

		}

	//ENGELLEME KODLARI

		if (isset($_POST['engelle']) === true) {
			
			if (empty($uye_engellileri) === true) {
				
				$y_engelliler = $profilindeki_uyenin_id;

			}else{

				$y_engelliler = "$uye_engellileri,$profilindeki_uyenin_id";

			}

			$query = $db->prepare("UPDATE uyeler SET engelliler = ? WHERE uye_id = ?"); 

			$guncelle = $query->execute(array($y_engelliler,$uye_id));

			header("Location: http://kurtsozluk.net/profil.php?id=".$profilindeki_uyenin_id);

			exit();

		}

	//ENGELİ KALDIRMA KODLARI

		if (isset($_POST['engeli_kaldir']) === true) {
			
			foreach ($engellileri_patlat as $key => $value) {
				
				if ($profilindeki_uyenin_id == $value) {
					
					unset($engellileri_patlat[$key]);

				}

			}

			$engellileri_topla = implode(",", $engellileri_patlat);

			$query = $db->prepare("UPDATE uyeler SET engelliler = ? WHERE uye_id = ?"); 

			$guncelle = $query->execute(array($engellileri_topla,$uye_id));

			header("Location: http://kurtsozluk.net/profil.php?id=".$profilindeki_uyenin_id);

			exit();

		}

	}

?>

<!doctype html>

<html>
	
	<head>

		<title><?php echo $profilindeki_uyenin_adi; ?></title>

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

				<div class="col-md-6 col-sm-12 col-xs-12"  style="padding: 0px;">

					<div class="div2">

						<div class="row">
							
							<div class="col-md-6 col-sm-6 col-xs-4">

								<img src="<?php echo $p_uye_foto; ?>" class="img-responsive img-thumbnail" alt="<?php echo $profilindeki_uyenin_adi; ?>" width="100" height="auto">

							</div>

							<div class="col-md-2 col-sm-2 col-xs-3">
								
								<?php

									if ($girdi == 1 && $uye_id != $profilindeki_uyenin_id) {
										
										if ($takib == 0) {

											echo '<form action="" method="POST">

													<button type="submit" name="takip" value=" " style="background-color:#FFFFFF; border-style:none;"><b>Takip Et</b></button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											
												</form>';

										}elseif($takib == 1){

											echo '<form action="" method="POST">

													<button type="submit" name="notakip" value=" " style="background-color:#FFFFFF; border-style:none;"><b>Takibi Bırak</b></button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

												</form>';

										}

									}

								?>

							</div>

							<div class="col-md-2 col-sm-2 col-xs-3">
								
								<?php

									if ($giris_yapti_mi == 1 && $uye_id != $profilindeki_uyenin_id && $p_uye_tipi == '0') {
										
										if ($engellimi == 0) {

											echo '<form action="" method="POST">

													<button type="submit" name="engelle" value=" " style="background-color:#FFFFFF; border-style:none;"><b>Engelle</b></button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											
												</form>';

										}elseif($engellimi == 1){

											echo '<form action="" method="POST">

													<button type="submit" name="engeli_kaldir" value=" " style="background-color:#FFFFFF; border-style:none;"><b>Engelleme</b></button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

												</form>';

										}

									}

								?>

							</div>

							<div class="col-md-2 col-sm-2 col-xs-2">
						
								<?php

									if ($giris_yapti_mi == 1 && $uye_id != $profilindeki_uyenin_id && $engellimi == 0 && $m_engelli == 0) {

										echo '<a href="http://kurtsozluk.net/mesaj.php?id='.$profilindeki_uyenin_id.'"><b>Mesaj</b></a>';

									} 

								?>

							</div>

						</div>

						<hr/>

						<div class="row">
					
							<div class="col-md-12 col-xs-12">
								
								<strong><a href="http://kurtsozluk.net/baslik.php?<?php echo $adbaslikurl; ?>_<?php echo $ad_kon_id; ?>"><?php echo $profilindeki_uyenin_adi; ?></a></strong>
								
								<?php if(cevrimici_mi($profilindeki_uyenin_id) === '1'){ echo '<small style="color:green;">(çevrimiçi)</small>'; }else{ echo '<small style="color:red;">(çevrimdışı)</small>'; } echo '<small>[Devre :'.$p_uye_devre.'] '; if($p_uye_tipi == '1'){ echo 'Yönetici'; }elseif($p_uye_tipi == '2'){ echo 'Yardımcı'; } ?></small>
								
								<?php if(isset($uye_id)){ if ($uye_id == 1) { echo $p_uye_gosterim; } }?>
							</div>

						</div>

						<hr/>

						<div class="row">
					
							<div class="col-md-12 col-xs-12">
								
								"<?php echo $p_uye_motto; ?>"

							</div>

						</div>

						<hr/>

						<div class="row">
							
							<div class="col-md-6 col-sm-6"><strong>En son geldiği tarih</strong>&nbsp;&nbsp;&nbsp;<?php echo $p_son_giris_tarihi; ?></div>
							<div class="col-md-6 col-sm-6"><strong>Buraya ilk geldiği gün</strong>&nbsp;&nbsp;&nbsp;<?php echo $p_uye_tarihi; ?></div>

						</div>

						<hr/>

						<div class="row">
							
							<div class="col-md-12">
								
								<b>Sicil Kaydı</b><br/>

								<?php

									$query = $db->query("SELECT * FROM ceza WHERE cezali_adi = '{$profilindeki_uyenin_adi}' ORDER BY ceza_id DESC", PDO::FETCH_ASSOC);

									if ( $query->rowCount() ){

										foreach( $query as $row ){

											$ceza = cekf($row['ceza_sebebi']);

											$ceza_saniye = cekf($row['ceza_saniye']);

											$ceza_bitis =  date("Y-m-d H:i:s",$ceza_saniye);

											echo $ceza.'('.$ceza_bitis.')<br/>';

										}

									}

								?>

							</div>

						</div>

						<hr/>

						<?php if(isset($uye_id)){ if($profilindeki_uyenin_id == $uye_id && empty($uye_engellileri) === false){?> 

						<div class="row">
							
							<div class="col-md-12">
								
								<b>Engellediklerin</b><br/>

								<?php 

									foreach ($p_engelliler_patlat as $key => $value) {

										$bc = $db->query("SELECT * FROM uyeler WHERE uye_id = '{$value}'")->fetch(PDO::FETCH_ASSOC);

										$e_uye_adi = cekf($bc['uye_adi']);

										echo '<a href="http://kurtsozluk.net/profil.php?id='.$value.'">'.$e_uye_adi.'</a>, ';

									}

								?>

							</div>

						</div>

						<hr/>

					<?php } }?>

					</div>

					<div class="row div2">

						<div class="col-md-6 col-sm-6 col-xs-6" style="text-align: center;"><a href="#" title="Entryler" onclick="return false" onmousedown="javascript:ackapa1('entryler','arsiv');" style="text-decoration: underline;"><b>Girileri</b><small>(<?php $sorgu = $db->prepare("SELECT COUNT(*) FROM giriler WHERE giriyazarid = '{$profilindeki_uyenin_id}' AND silindi = '0'"); $sorgu->execute(); $cevap_sayisi = $sorgu->fetchColumn(); echo $cevap_sayisi; ?>)</small></a></div>

						<div class="col-md-6 col-sm-6 col-xs-6" style="text-align: center;"><a href="#" title="Arşiv" onclick="return false" onmousedown="javascript:ackapa1('arsiv','entryler');" style="text-decoration: underline;"><b>Arşiv</b></a></div>

					</div>

					<div id="entryler">
							
					<?php

						if (isset($_GET['s']) === true && empty($_GET['s']) === false && is_numeric($_GET['s']) === true) {
							
							$sayfa = yollaf($_GET['s']);

							$son = $sayfa * 20;

						}else{

							$sayfa = 1;

							$son = 20;

						}

						$a = 0;

						$query = $db->query("SELECT * FROM giriler WHERE giriyazarid = '{$profilindeki_uyenin_id}' AND silindi = '0' ORDER BY giriid DESC LIMIT 0,$son", PDO::FETCH_ASSOC);

						if ( $query->rowCount() ){

							foreach( $query as $row ){

								$a++;

								$c_konu_id = cekf($row['giribaslikid']);

								$konu_adi_cek = $db->query("SELECT * FROM basliklar WHERE baslikid = '{$c_konu_id}' AND silindi = '0'")->fetch(PDO::FETCH_ASSOC);
									
								$konu_baslik = cekf($konu_adi_cek['baslik']);

								$baslikurl = cekf($konu_adi_cek['baslikurl']);

								$cevap_adedi = cekf($konu_adi_cek['giriadedi']);

								$g_tipi = cekf($row['giritipi']);

								$cevap_metni = cekf($row['girimetin']);

								$cevap_metni = bkz($cevap_metni);

								$cevap_metni = res($cevap_metni);

								$cevap_metni = url($cevap_metni);

								$cevap_metni = you($cevap_metni);

								$cevap_metni = dai($cevap_metni);

								$cevap_metni = nl2br($cevap_metni);

								$arti_puan = cekf($row['giriarti']);

								if($arti_puan == 0){$arti_puan = '';}

								$eksi_puan = cekf($row['girieksi']);

								if($eksi_puan == 0){$eksi_puan = '';}

								$saniye = cekf($row['girisaniye']);

								$ne_kadar_once = ne_kadar_once($su_an,$saniye);		

								$cevap_tarihi = cekf($row['giritarih']);						

					?>

							<div class="row div2">
								
								<div class="row">
									
									<div class="col-md-12">
										
										<a name="<?php echo $a; ?>" href="http://kurtsozluk.net/baslik.php?<?php echo $baslikurl; ?>_<?php echo $c_konu_id; ?>">

											<strong><?php echo $konu_baslik; ?> </strong> <small>(<?php echo $cevap_adedi; ?>)</small>

										</a>

									</div>

								</div>

								<div class="row">
									
									<div class="col-md-12">
										
										<?php echo '<p class="text-justify" style="word-wrap: break-word; margin:0px;">'.$cevap_metni.'</p>'; ?>

									</div>

								</div>

								<div class="row">

									<div class="col-md-2 col-sm-2 col-xs-2">

										<i class="fa fa-angle-up fa-lg" style="color:#535A3B;"></i>&nbsp;&nbsp;<small><?php echo $arti_puan; ?></small>

									</div>

									<div class="col-md-2 col-sm-2 col-xs-2">

										<i class="fa fa-angle-down fa-lg" style="color:#535A3B;"></i>&nbsp;&nbsp;<small><?php echo $eksi_puan; ?></small>

									</div>

									<div class="col-md-5 col-sm-4 col-xs-5">

										<i title="Yazan" class="fa fa-clock-o" style="color:teal;"></i>&nbsp;<a href="#" title="<?php echo $cevap_tarihi; ?>"><?php echo $ne_kadar_once; ?></a>

									</div>

								</div>

							</div>	

					<?php


							}

						}

					?>

						<div class="row">
						
							<div class="col-md-12 text-center">

								<?php

									$kaldigiyer = $son - 1;

									$sayfa++;

									echo '<a href="http://kurtsozluk.net/profil.php?id='.$profilindeki_uyenin_id.'&s='.$sayfa.'#'.$kaldigiyer.'"><button class="btn btn-default">Daha Fazla Göster</button></a><br/><br/><br/>';
									

								?>		

							</div>

						</div>

					</div>

					<div id="arsiv" style="display: none;">
						
						<?php

							if (empty($p_arsiv) === false) {

								$p_arsiv_patlat = explode(",", $p_arsiv);

								foreach ($p_arsiv_patlat as $key => $value) {

									if (konu_id_var_mi($value) == '1') {

										$kbc = $db->query("SELECT * FROM basliklar WHERE baslikid = '{$value}' AND silindi = '0'")->fetch(PDO::FETCH_ASSOC);

										$a_konu_baslik = cekf($kbc['baslik']);

										$baslikurl = cekf($kbc['baslikurl']);
											
										$konuyu_acan_id = cekf($kbc['baslikacanid']);

										$konu_id = cekf($kbc['baslikid']);

										$konu_baslik = strtolower(cekf($kbc['baslik']));

										$search = array('Ç','Ğ','Ö','Ş','Ü');
						
										$replace = array('ç','ğ','ö','ş','ü');
										
										$konu_baslik = str_replace($search,$replace,$konu_baslik);

										$cevap_adedi = cekf($kbc['giriadedi']);

										$kaac = $db->query("SELECT * FROM uyeler WHERE uye_id = '{$konuyu_acan_id}'")->fetch(PDO::FETCH_ASSOC);
											
										$konuyu_acanin_adi = cekf($kaac['uye_adi']);

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

							}else{

								echo '<br/><div class="alert alert-danger" role="alert">Bu arşivde hiç başlık bulunmamaktadır.</div>';

							}

						?>

						<br/><br/><br/><br/><br/>

					</div>

				</div>

				<div class="col-md-3 hidden-sm hidden-xs">

					<?php include 'template/inpagebeta.php'; ?>

				</div>

			</div>
			
		</div>

		<?php include 'template/jscss.html'; ?>

	</body>

</html>