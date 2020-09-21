<?php

	include 'fonksiyonlar/bagla.php'; 

//İNDEXE YÖNLENDİRME MESAJLARI

	if (isset($_GET['engelli'])) {

		echo "<script>alert('Bu arkadaşa mesaj atamazsın seni engellemiş.');</script>";

	}

	if (isset($_GET['yapma'])) {

		echo "<script>alert('Url ile oynama güzel kardeşim.');</script>";

	}

	if ($girdi == 1) {
		if (yeni_uye_mi($uye_id) == 1) {
			$hata = '<br/><div class="alert alert-success" role="alert"><a href="http://kurtsozluk.net/kurt.php">Hoşgeldin. Buraya tıklayarak burası hakkında genel bir bilgiye sahip olabilirsin.</a></div>';
		}
	}

	if ($girdi == 1) {

		$sorgu = $db->prepare("SELECT COUNT(*) FROM ceza WHERE cezali_adi = '{$uye_adi}' AND ceza_saniye > '{$su_an}' ORDER BY ceza_id DESC LIMIT 1");
		$sorgu->execute();
		$ceza_sayi = $sorgu->fetchColumn();

		if ($ceza_sayi != '0') {

			header("Location: http://kurtsozluk.net/cikis.php");

			exit();

		}

	}

// HATALAR

	if (isset($_GET['my'])) {

		echo "<script>alert('Mesaj kutunuz boş.');</script>";

	}

//GİRİŞ YAPMADAN KULLANILAMAYACAK FONKSİYONLAR

if ($girdi == '1') {

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

				$giriguncel = $db->prepare("UPDATE giriler SET girieksi = ?, giriarti = ?, artilayanlar = ?, eksileyenler = ? WHERE giriid = ?"); 

				$giriyiguncelle = $giriguncel->execute(array($c_eksi_puan_y,$c_arti_puan_y,$c_artilayanlar_y,$c_eksileyenler_y,$c_id));

				$uyeguncel = $db->prepare("UPDATE uyeler SET arti_puan = ?, eksi_puan = ? WHERE uye_id = ?"); 

				$uyeyiguncelle = $uyeguncel->execute(array($cevabi_yazanin_arti_puani_y,$cevabi_yazanin_eksi_puani_y,$cevabi_yazan_id));	

				if (isset($_GET['s']) === true && empty($_GET['s']) === false) {

					$link = "http://kurtsozluk.net/index.php?s=".$sayfa."#".$numara;

				}else{

					$link = "http://kurtsozluk.net/index.php#".$numara;
					
				}

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

				$query = $db->prepare("UPDATE giriler SET girieksi = :girieksi, giriarti = :giriarti, artilayanlar = :artilayanlar, eksileyenler = :eksileyenler WHERE giriid = :giriid");
				
				$update = $query->execute(array("girieksi" => $c_eksi_puan_y, "giriarti" => $c_arti_puan_y, "artilayanlar" => $c_artilayanlar_y, "eksileyenler" => $c_eksileyenler_y, "giriid" => $c_id));	

				$query = $db->prepare("UPDATE uyeler SET arti_puan = :arti_puan, eksi_puan = :eksi_puan WHERE uye_id = :uye_id LIMIT 1");
				
				$update = $query->execute(array("arti_puan" => $cevabi_yazanin_arti_puani_y, "eksi_puan" => $cevabi_yazanin_eksi_puani_y, "uye_id" => $cevabi_yazan_id));	

				if (isset($_GET['s']) === true && empty($_GET['s']) === false) {

					$link = "http://kurtsozluk.net/index.php?s=".$sayfa."#".$numara;

				}else{

					$link = "http://kurtsozluk.net/index.php#".$numara;
					
				}

				header("Location: ".$link);

				exit();


			}else{

				echo "<script>alert('Daha önce eksi puan vermişsin.');</script>";

			}

		}

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

				<div class="col-md-6 col-sm-12 col-xs-12" style="padding: 0px;">

					<?php echo $hata; ?>

					<div class="col-xs-12 visible-sm visible-xs" style="position: fixed; z-index: 2; bottom: 0px; padding: 0px">
						<form action="http://kurtsozluk.net/arama.php" method="POST" style="margin:0px;">
							<div class="input-group">
								<input name="aranan_baslik" class="form-control" type="text" placeholder="Başlıklarda Ara">
								<span class="input-group-addon">
									<button type="submit" value=" " name="basliklarda_arama_formu" style="border-style:none; background-color:#eeeeee;">
										<i class="fa fa-search"></i>
									</button>
								</span>
							</div>
						</form>
					</div>

					<div class="div2">

						<div class="row hidden-sm hidden-xs">
							
							<div class="col-md-12"><b>Son Giriler</b></div>

						</div>

						<div class="row visible-sm visible-xs">
						
							<div class="col-md-4 col-sm-4 col-xs-4" style="text-align: center;">
								
								<form action="" method="POST" style="margin: 0px;">
									<input type="hidden" name="mframe" value="basliklar">
						            <button type="submit" style="background-color: white; border-style: none;" name="mobframe"><b>Başlıklar</b></button>
						        </form>

							</div>

							<div class="col-md-4 col-sm-4 col-xs-4" style="text-align: center;">
								
								<form action="" method="POST" style="margin: 0px;">
									<input type="hidden" name="mframe" value="giriler">
						            <button type="submit" style="background-color: white; border-style: none;" name="mobframe"><b>Giriler</b></button>
						        </form>

							</div>

							<div class="col-md-4 col-sm-4 col-xs-4" style="text-align: center;">
								
								<form action="" method="POST" style="margin: 0px;">
									<input type="hidden" name="mframe" value="gundem">
							        <button type="submit" style="background-color: white; border-style: none;" name="mobframe"><b>Gündem</b></button>
							    </form>

							</div>

						</div>
						
						<?php include 'template/sol_liste.php'; ?>
						
						<?php include 'template/sag_liste.php'; ?>

						<?php if ($_SESSION['mframe'] == 'gundem' || $_SESSION['mframe'] == 'basliklar') { ?> <div id="songiriler" style="display: none;"> <?php }else{ ?> <div id="songiriler"> <?php } ?>

							<?php

							$engelliler_patlat = explode(",", $uye_engellileri);

							if (isset($_GET['s']) === true && empty($_GET['s']) === false && is_numeric($_GET['s']) === true){

								$son = cekf($_GET['s']);

							}else{

								$son = 20;
							}

							$a = 0;

							$query = $db->query("SELECT * FROM giriler WHERE silindi = '0' AND giritipi = '0' ORDER BY girisaniye DESC LIMIT 0,$son", PDO::FETCH_ASSOC);
							
							if ( $query->rowCount() ){
							
							    foreach( $query as $satir ){
						
								$konuyu_acan_id = cekf($satir['giriyazarid']);

								$engelli_bu = 0;

								if ($girdi == 1) {

									foreach ($engelliler_patlat as $key => $value) {
										
										if ($konuyu_acan_id == $value) {
											
											$engelli_bu = 1;

										}

									}

									$kaelc = $db->query("SELECT * FROM uyeler WHERE uye_id = '{$konuyu_acan_id}' ORDER BY uye_id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);

									$kaelistesi = cekf($kaelc['engelliler']);

									$kaelpatlat = explode(",", $kaelistesi); 

									foreach ($kaelpatlat as $key => $value) {
										
										if ($uye_id == $value) {
											
											$engelli_bu = 1;

										}

									}

								}

								if ($engelli_bu == 0) {

									$a++;

									$giriid = cekf($satir['giriid']);

									$konu_id = cekf($satir['giribaslikid']);

									$baslikcek = $db->query("SELECT * FROM basliklar WHERE baslikid = '{$konu_id}' LIMIT 1")->fetch(PDO::FETCH_ASSOC);

									$giribaslik = cekf($baslikcek['baslik']);

									$baslikurl = cekf($baslikcek['baslikurl']);

									$cevap_adedi = cekf($baslikcek['giriadedi']);

									$konu_baslik = strtolower($giribaslik);

									$search = array('Ç','Ğ','Ö','Ş','Ü');
					
									$replace = array('ç','ğ','ö','ş','ü');
									
									$konu_baslik = str_replace($search,$replace,$konu_baslik);

									$k_tipi = cekf($satir['giritipi']);

									$konu_metni = cekf($satir['girimetin']);

									$konu_metni = bkz($konu_metni);

									$konu_metni = nl2br($konu_metni);

									$konu_metni = res($konu_metni);

									$konu_metni = url($konu_metni);

									$konu_metni = you($konu_metni);

									$konu_metni = dai($konu_metni);

									$k_arti_puan = cekf($satir['giriarti']);

									$k_eksi_puan = cekf($satir['girieksi']);

									$row = $db->query("SELECT * FROM uyeler WHERE uye_id = '{$konuyu_acan_id}'")->fetch(PDO::FETCH_ASSOC);

									$konuyu_acanin_adi = cekf($row['uye_adi']);

									$cevabi_yazanin_arti_puani = cekf($row['arti_puan']);

									$cevabi_yazanin_eksi_puani = cekf($row['eksi_puan']);

									$cevabi_yazanin_tipi = cekf($row['uye_tipi']);

									$tarih = cekf($satir['giritarih']);

									$saniye = cekf($satir['girisaniye']);

									$ne_kadar_once = ne_kadar_once($su_an,$saniye);

									$c_artilayanlar = cekf($satir['artilayanlar']);

									$c_eksileyenler = cekf($satir['eksileyenler']);

						?>			

									<hr style="margin-top: 10px; margin-bottom: 10px;" />
											
										<div class="row">
											
											<div class="col-md-12">
												
												<a name="<?php echo $a; ?>" href="http://kurtsozluk.net/baslik.php?<?php echo $baslikurl; ?>_<?php echo $konu_id; ?>">

													<strong><?php echo $konu_baslik; ?> </strong> <small>(<?php echo $cevap_adedi; ?>)</small>

												</a>

											</div>

										</div>

										<div class="row" style="max-height: 75px; overflow:hidden;">
											
											<div class="col-md-12">
												
												<?php echo '<p class="text-justify" style="word-wrap: break-word; margin:0px;">'.$konu_metni.'</p>'; ?>

											</div>

										</div>

										<div class="row">
											
											<div class="col-md-1 col-sm-2 col-xs-2" style="text-align: center; padding: 0px; margin: 0px;">

												<form action="" method="POST" style="padding: 0px; margin: 0px;">

													<input type="hidden" name="c_artilayanlar" value="<?php echo $c_artilayanlar; ?>" />

													<input type="hidden" name="c_eksileyenler" value="<?php echo $c_eksileyenler; ?>" />

													<input type="hidden" name="numara" value="<?php echo $a; ?>" />

													<input type="hidden" name="cevabi_yazanin_arti_puani" value="<?php echo $cevabi_yazanin_arti_puani; ?>" />

													<input type="hidden" name="cevabi_yazanin_eksi_puani" value="<?php echo $cevabi_yazanin_eksi_puani; ?>" />

													<input type="hidden" name="cevabi_yazan_id" value="<?php echo $konuyu_acan_id; ?>" />

													<input type="hidden" name="cevap_id" value="<?php echo $giriid; ?>" />

													<input type="hidden" name="c_arti_puan" value="<?php echo $k_arti_puan; ?>" />

													<input type="hidden" name="c_eksi_puan" value="<?php echo $k_eksi_puan; ?>" />

													<button type="submit" name="cevap_puani_arttir" value=" " style="background-color:#FFFFFF; border-style:none; padding: 0px;"><span><i class="fa fa-angle-up fa-lg" style="color:#535A3B;"></i></span></button>

													&nbsp;<small><?php if($k_arti_puan != 0){ echo $k_arti_puan; } ?></small>

												</form>

											</div>

											<div class="col-md-1 col-sm-2 col-xs-2" style="text-align: center; padding: 0px; margin: 0px;">

												<form action="" method="POST" style="padding: 0px; margin: 0px; padding: 0px; margin: 0px;">

													<input type="hidden" name="c_artilayanlar" value="<?php echo $c_artilayanlar; ?>" />

													<input type="hidden" name="c_eksileyenler" value="<?php echo $c_eksileyenler; ?>" />

													<input type="hidden" name="numara" value="<?php echo $a; ?>" />

													<input type="hidden" name="cevabi_yazanin_arti_puani" value="<?php echo $cevabi_yazanin_arti_puani; ?>" />

													<input type="hidden" name="cevabi_yazanin_eksi_puani" value="<?php echo $cevabi_yazanin_eksi_puani; ?>" />

													<input type="hidden" name="cevabi_yazan_id" value="<?php echo $konuyu_acan_id; ?>" />

													<input type="hidden" name="cevap_id" value="<?php echo $giriid; ?>" />

													<input type="hidden" name="c_arti_puan" value="<?php echo $k_arti_puan; ?>" />

													<input type="hidden" name="c_eksi_puan" value="<?php echo $k_eksi_puan; ?>" />

													<button type="submit" name="cevap_puani_azalt" value=" " style="background-color:#FFFFFF; border-style:none; padding: 0px;"><span><i class="fa fa-angle-down fa-lg" style="color:#535A3B;"></i></span></button>

													&nbsp;<small><?php if($k_eksi_puan != 0){ echo $k_eksi_puan; } ?></small>

												</form>

											</div>

											<div class="col-md-10 col-sm-8 col-xs-8" style="text-align: right; padding: 0px; margin: 0px;">

												<a href="http://kurtsozluk.net/profil.php?id=<?php echo $konuyu_acan_id; ?>"><?php echo $konuyu_acanin_adi; ?></a>&nbsp;&nbsp;&nbsp;&nbsp;

											</div>

										</div>

						<?php

									}

								}

							}

						?>

							<hr/>

							<div class="row">

								<div class="col-md-1"></div>
												
								<div class="col-md-10 text-center">

									<?php

										$sonbir = $son + 1;

										$son = $son + 20;

										echo '<a href="http://kurtsozluk.net/index.php?s='.$son.'#'.$sonbir.'"><button class="btn btn-default btn-lg btn-block">Daha Fazla Göster</button></a>';
										

									?>		

								</div>

								<div class="col-md-1"></div>

							</div>

							<div class="visible-xs"><br/><br/></div>

						</div>

					</div>

				</div> <!-- ALTILIK AKIŞ DİVİ BİTİŞİ-->

				<div class="col-md-3 hidden-sm hidden-xs">

					<?php include 'template/inpagebeta.php'; ?>
					
				</div>

			</div> <!-- BÜYÜK SAYFA ROW DİVİ BİTİŞİ -->
			
		</div> <!-- BÜYÜK CONTAİNER FLUİD DİVİ BİTİŞİ-->

		<?php include 'template/jscss.html'; ?>

	</body>

</html>