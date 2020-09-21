<?php

	include 'fonksiyonlar/bagla.php';

	if ($girdi == '0') { 

		header("Location: http://kurtsozluk.net/index.php"); 

	}else{

//DEĞİŞKENLER

		if (isset($_GET['id']) === true && empty($_GET['id']) === false && is_numeric($_GET['id']) === true) {
			
			$k_id = $_GET['id'];

			if (uye_id_var_mi($k_id) == '0') {
			
				header("Location: http://kurtsozluk.net/index.php");
				
			}

			$query = $db->prepare("UPDATE mesaj SET m_okunma = ? WHERE m_kime = ? AND m_kimden = ?"); 

			$okundu_yap = $query->execute(array('1',$uye_id,$k_id));

			$satir = $db->query("SELECT * FROM uyeler WHERE uye_id = '{$k_id}' LIMIT 1")->fetch(PDO::FETCH_ASSOC);

			$k_adi = cekf($satir['uye_adi']);

			$k_mesajlasilan = cekf($satir['msjlasilan']);

			$k_msjlasilan_patlak = explode(",", $k_mesajlasilan);

			$k_engelliler = $satir['engelliler'];

			$k_engellileri_patlat = explode(",", $k_engelliler);

			foreach ($k_engellileri_patlat as $key => $value) {
			
			if ($value == $uye_id) {
				
					header("Location: http://kurtsozluk.net/index.php?engelli");

				}

			}

		}

// MESAJ YOLLAMA

		if (isset($_POST['mesaj_formu']) && $giris_yapti_mi == 1) {

			$m_kutusu = yollaf($_POST['m_kutusu']);

			if (ctype_space($m_kutusu)) {

				echo "<script>alert('Bir şey yazmadın ki neyi yolluyorsun güzel kardeşim.');</script>";

			}elseif (empty($_POST['m_kutusu']) === true) {

				echo "<script>alert('Bir şey yazmadın ki neyi yolluyorsun güzel kardeşim.');</script>";

			}else{

				$query = $db->prepare("INSERT INTO mesaj SET m_kimden = ?, m_kime = ?, m_metin = ?, m_okunma = ?, m_saniye = ?, m_tarihi = ?");

				$mesaji_yolla = $query->execute(array($uye_id, $k_id, $m_kutusu, '0', $su_an, $now));

				if (empty($uye_mesajlasilan) === true) {
					
					$son_hali = $k_id;

				}else{

					foreach ($msjlasilan_patlak as $key => $value) {
						
						if ($value == $k_id) {

							unset($msjlasilan_patlak[$key]);

						}

					}

					$msjlasilan_topla = implode(",", $msjlasilan_patlak);

					if (empty($msjlasilan_topla) === true) {
						
						$son_hali = "$k_id";

					}else{

						$son_hali = "$k_id,$msjlasilan_topla";

					}

				}

				if (empty($k_mesajlasilan) === true) {
					
					$k_son_hali = $uye_id;

				}else{

					foreach ($k_msjlasilan_patlak as $key => $value) {
						
						if ($value == $uye_id) {

							unset($k_msjlasilan_patlak[$key]);

						}

					}

					$k_msjlasilan_topla = implode(",", $k_msjlasilan_patlak);

					if (empty($k_msjlasilan_topla) === true) {
						
						$k_son_hali = "$uye_id";

					}else{

						$k_son_hali = "$uye_id,$k_msjlasilan_topla";

					}

				}

				$query = $db->prepare("UPDATE uyeler SET msjlasilan = ? WHERE uye_id = ?"); 

				$guncelle = $query->execute(array($son_hali,$uye_id));

				$query = $db->prepare("UPDATE uyeler SET msjlasilan = ? WHERE uye_id = ?"); 

				$guncelle = $query->execute(array($k_son_hali,$k_id));
				
				header("Location: http://kurtsozluk.net/mesaj.php?id=".$k_id);

				exit();

			}

		}

//MESAJ ŞERİDİ SİLME

		if (isset($_POST['mesajseridisil'])) {
			
			$kimle = yollaf($_POST['kimle']);

			foreach ($msjlasilan_patlak as $key => $value) {
						
				if ($value == $kimle) {

					unset($msjlasilan_patlak[$key]);

				}

			}

			$msjlasilan_toplanmis = implode(",", $msjlasilan_patlak);

			$query = $db->prepare("UPDATE uyeler SET msjlasilan = ? WHERE uye_id = ?"); 

			$guncelle = $query->execute(array($msjlasilan_toplanmis,$uye_id));

			header("Location:http://kurtsozluk.net/mesaj.php");

			exit();

		}

	} // GİRDİ ELSE BİTİŞİ

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

				<?php if (isset($k_id)) { ?>

				<div class="col-md-9">	

					<div class="div1">

						<div><b><?php echo '<a href="http://kurtsozluk.net/profil.php?id='.$k_id.'">'.$k_adi.'</a>'; ?></b></div>

						<div style="overflow: auto; height:350px;">
							
							<?php 

								$sorgu = $db->prepare("SELECT COUNT(*) FROM mesaj WHERE (m_kimden = '{$uye_id}' AND m_kime = '{$k_id}') || (m_kime = '{$uye_id}' AND m_kimden = '{$k_id}') ORDER BY m_id ASC"); $sorgu->execute();

								$mesaj_sayimi = $sorgu->fetchColumn();

								if($mesaj_sayimi > 100){ $alt_limit = $mesaj_sayimi - 100; }else{ $alt_limit = 0; }

								$query = $db->query("SELECT * FROM mesaj WHERE (m_kimden = '{$uye_id}' AND m_kime = '{$k_id}') || (m_kime = '{$uye_id}' AND m_kimden = '{$k_id}') ORDER by m_id ASC LIMIT $alt_limit, $mesaj_sayimi", PDO::FETCH_ASSOC);

								if ( $query->rowCount() ){

									foreach( $query as $satir ){

										$a++;

										$m_metin = cekf($satir['m_metin']);

										$m_metin = bkz($m_metin);

										$m_metin = res($m_metin);

										$m_metin = url($m_metin);

										$m_metin = you($m_metin);

										$m_metin = dai($m_metin);

										$m_metin = nl2br($m_metin);

										$m_kimden = cekf($satir['m_kimden']);

										$m_saniye = cekf($satir['m_saniye']);

										$m_tarihi = cekf($satir['m_tarihi']);

										$ne_kadar_once = ne_kadar_once($su_an,$m_saniye);

										$m_okunma = cekf($satir['m_okunma']);

										if ($m_kimden != $uye_id) {

											if ($m_okunma == '1') {
												
												echo '<a name="'.$a.'"><div align="left"><div style="width:75%; background-color:#EBEEEF; padding:10px; margin:5px; border-radius:10px;"><a title="'.$m_tarihi.'">-'.$ne_kadar_once.'-</a><br/><p>'.$m_metin.'</p></div></div></a>';

											}else{

												echo '<a name="'.$a.'"><div align="left"><div style="width:75%; background-color:#EBEEEF; padding:10px; margin:5px; border-radius:10px; border:black solid 1px;"><a title="'.$m_tarihi.'">-'.$ne_kadar_once.'-</a><br/><p>'.$m_metin.'</p></div></div></a>';
											}

										}else{

											if ($m_okunma == '1') {
												
												echo '<a name="'.$a.'"><div align="right"><div style="width:75%; background-color:#EBEEEF; padding:10px; margin:5px; border-radius:10px;"><a title="'.$m_tarihi.'">-'.$ne_kadar_once.'-</a><br/><p>'.$m_metin.'</p></div></div></a>';

											}else{

												echo '<a name="'.$a.'"><div align="right"><div style="width:75%; background-color:#EBEEEF; padding:10px; margin:5px; border-radius:10px; border:black solid 1px;"><a title="'.$m_tarihi.'">-'.$ne_kadar_once.'-</a><br/><p>'.$m_metin.'</p></div></div></a>';

											}

										}

									}

								}						

							?>

						</div>

						<?php

							if (isset($_GET['id']) === true && empty($_GET['id']) === false && is_numeric($_GET['id']) === true) {

						?>

								<form action="" method="POST"> 

									<div class="form-group"><textarea name="m_kutusu" class="form-control" rows="3" placeholder="Mesajını yazarken kullanabileceğin fonksiyonlar : (bkz.başlık adı) / (url.herhangibir link) / (res.resim linki) / (you.youtube videosu linki) / (dai.dailymotion videosu linki)"></textarea></div>

									<div class="form-group text-center"><button type="submit" name="mesaj_formu" value=" " class="btn btn-primary">Gönder</button></div>

								</form>

						<?php } ?>

					</div>

				</div>

				<?php }else{ ?>

				<div class="col-md-9" style="padding: 0px;">

					<div class="row">

						<br/><?php include 'template/masthead.php'; ?>

					</div>

					<div class="row">

						<div class="col-md-6">

							<?php

								foreach ($msjlasilan_patlak as $key => $value) {

									$santero = $db->query("SELECT * FROM uyeler WHERE uye_id = '{$value}'")->fetch(PDO::FETCH_ASSOC);
										
									$mlasilan_adi = $santero['uye_adi'];

									$sorgu = $db->prepare("SELECT COUNT(*) FROM mesaj WHERE m_kime = '{$uye_id}' AND m_kimden = '{$value}' AND m_okunma = '0'"); $sorgu->execute();

									$okunmiyan_mesaji = $sorgu->fetchColumn();

									$sorgu = $db->prepare("SELECT COUNT(*) FROM mesaj WHERE (m_kimden = '{$uye_id}' AND m_kime = '{$value}') || (m_kime = '{$uye_id}' AND m_kimden = '{$value}')"); $sorgu->execute();

									$mesaj_num = $sorgu->fetchColumn();

									if ($mesaj_num > 100) {
										
										$mesaj_num = 100;

									}

									$mesaj_num = $mesaj_num - 1;

									if ($okunmiyan_mesaji != 0) {

										echo '<div class="row div2">

												<div class="col-md-1 col-sm-1 col-xs-1"></div>

												<div class="col-md-7 col-sm-7 col-xs-7"><a href="http://kurtsozluk.net/mesaj.php?id='.$value.'#'.$mesaj_num.'" style="color:red; font-weight:bolder;">'.$mlasilan_adi.'</a></div>

											</div>';

									}else{

										echo '<div class="row div2">

													<div class="col-md-1 col-sm-1 col-xs-1"></div>

													<div class="col-md-7 col-sm-7 col-xs-7"><a href="http://kurtsozluk.net/mesaj.php?id='.$value.'#'.$mesaj_num.'">'.$mlasilan_adi.'</a></div>

													<div class="col-md-2 col-sm-2 col-xs-2">

														<form action="" method="POST" style="margin:0px;">

															<input type="hidden" name="kimle" value="'.$value.'" />

															<button type="submit" name="mesajseridisil" value=" " style="background-color:#FFFFFF; border-style:none; height:20px;"><span><i class="fa fa-trash fa-lg" style="color:#535A3B;"></i></span></button>

														</form>

													</div>
												</div>';

									}		

								} 

							?>

						</div>

						<div class="col-md-6 hidden-sm hidden-xs">
						
							<?php include 'template/inpagebeta.php'; ?>

						</div>

					</div>

				</div>

				<?php } ?>

			</div>

		</div>

		<?php include 'template/jscss.html'; ?>

	</body>

</html>