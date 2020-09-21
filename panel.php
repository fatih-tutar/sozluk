<?php

include 'fonksiyonlar/bagla.php';

if ($girdi != 1 || $uye_id != 1) {
	
	header("Location: index.php");

	exit();

}else{

//GÖSTERİMİ SIFIRLA

	if (isset($_POST['sifirla'])) {

		$puan = yollaf($_POST['puan']);
		
		$kisiid = yollaf($_POST['kisiid']);

		$query = $db->prepare("UPDATE uyeler SET gosterim = gosterim - '{$puan}' WHERE uye_id = ?"); 

		$guncelle = $query->execute(array($kisiid));

		header("Location: http://kurtsozluk.net/panel.php");

		exit();

	}

//ŞİKAYET SİL

	if (isset($_POST['sil'])) {
			
			$sikayet_id = yollaf($_POST['sikayet_id']);

			$query = $db->prepare("UPDATE sikayetler SET silindi = ? WHERE sikayet_id = ?"); 

			$guncelle = $query->execute(array('1',$sikayet_id));

			header("Location: panel.php");

			exit();

		}

//ÜYE TİPİNİ DEĞİŞTİRME

	if (isset($_POST['degistir'])) {
			
		$uyeid = yollaf($_POST['uyeid']);

		$uyetipi = yollaf($_POST['uyetipi']);

		$query = $db->prepare("UPDATE uyeler SET uye_tipi = ? WHERE uye_id = ?"); 

		$guncelle = $query->execute(array($uyetipi,$uye_id));

		header("Location: panel.php");

		exit();

	}

//ŞİFREYİ 123456 YAPMA

	if (isset($_POST['sifredegistir'])) {
		
		$uyeidsi = yollaf($_POST['uyeidsi']);

		$sifre = md5('123456');

		$query = $db->prepare("UPDATE uyeler SET uye_sifre = ? WHERE uye_id = ?"); 

		$guncelle = $query->execute(array($sifre,$uyeidsi));

		header("Location:panel.php");

		exit();

	}

//BAŞLIKLARIN GİRİ SAYILARINI GÜNCELLEME KODLARI

	if (isset($_POST['sayiguncelle'])) {
		
		$baslikid = yollaf($_POST['baslikid']);

		$girisayisi = yollaf($_POST['girisayisi']);

		$query = $db->prepare("UPDATE basliklar SET giriadedi = ? WHERE baslikid = ?"); 

		$guncelle = $query->execute(array($girisayisi,$baslikid));

		header("Location:http://kurtsozluk.net/panel.php");

		exit();

	}

//ESKİYE GİTME KODLARI

	if (isset($_POST['eskiyegit'])) {
		
		$tarihsaat = yollaf($_POST['tarihsaat']);

		$saniye = strtotime($tarihsaat);

		$query = $db->prepare("DELETE FROM basliklar WHERE basliksaniye > ?");

		$delete = $query->execute(array($saniye));

		$query = $db->prepare("DELETE FROM giriler WHERE girisaniye > ?");

		$delete = $query->execute(array($saniye));

		$query = $db->prepare("DELETE FROM bildirimler WHERE bildirim_tarihi > ?");

		$delete = $query->execute(array($tarihsaat));

		header("Location:http://kurtsozluk.net/panel.php");

		exit();

	}

// MAİLDEN DİĞER BİLGİLERİ ÇEKME

	if (isset($_POST['maildenbul'])) {
		
		$mail = yollaf($_POST['mail']);

		$query = $db->query("SELECT * FROM uyeler WHERE uye_mail = '{$mail}'")->fetch(PDO::FETCH_ASSOC);

		$uye_adi = $query['uye_adi'];

		$uye_id = $query['uye_id'];

		$telefon = $query['telefon'];

	}

	if (isset($_POST['sorugonder'])) {
		
		$soru = yollaf($_POST['soru']);

		$cevap = yollaf($_POST['cevap']);

		$query = $db->prepare("INSERT INTO sorular SET soru = ?, cevap = ?");

		$insert = $query->execute(array($soru, $cevap));

		header("Location:panel.php");

		exit();

	}

	if (isset($_POST['yarismabaslat'])) {
			
		$yarismaadi = yollaf($_POST['yarismaadi']);

		$query = $db->prepare("INSERT INTO yarismalar SET yarismaadi = ?, yarisanlar = ?, puanlar = ?, sorular = ?, yarismasaniye = ?");

		$insert = $query->execute(array($yarismaadi,'','','',$su_an));

		header("Location:panel.php");

		exit();

	}

}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Kurt Sözlük</title>

	<meta name="description" content="Her konuda başlıklar aç. Reklam gelirini yazarlarıyla paylaşan sözlük." />

	<meta name="keywords" content="kurt, sözlük" />

	<?php include 'template/head.php'; ?>
	<?php include 'template/jscss.html'; ?>

</head>
<body>

	<?php include 'template/banner.php'; ?>

	<div class="container-fluid">
		<div class="row">
			<div class="col-md-2">
				
				<div style="padding: 10px;"><a href="#" onclick="return false" onmousedown="javascript:ackapa('siralama')">Gösterime Göre Sıralama</a></div>
				<div style="padding: 10px;"><a href="#" onclick="return false" onmousedown="javascript:ackapa('uyetipidegistirme')">Üye Tipi Değiştirme</a></div>
				<div style="padding: 10px;"><a href="#" onclick="return false" onmousedown="javascript:ackapa('sifre')">Şifre 123456</a></div>
				<div style="padding: 10px;"><a href="#" onclick="return false" onmousedown="javascript:ackapa('sikayetler')">Şikayetler</a></div>
				<div style="padding: 10px;"><a href="#" onclick="return false" onmousedown="javascript:ackapa('uzaklastirmalar')">Uzaklaştırmalar</a></div>
				<div style="padding: 10px;"><a href="#" onclick="return false" onmousedown="javascript:ackapa('girenler')">Girenler</a></div>
				<div style="padding: 10px;"><a href="#" onclick="return false" onmousedown="javascript:ackapa('girisayi')">Giri Sayı</a></div>
				<div style="padding: 10px;"><a href="#" onclick="return false" onmousedown="javascript:ackapa('temizlik')">Temizlik</a></div>
				<div style="padding: 10px;"><a href="#" onclick="return false" onmousedown="javascript:ackapa('mailden')">Mailden Bilgi Bulma</a></div>
				<div style="padding: 10px;"><a href="#" onclick="return false" onmousedown="javascript:ackapa('yarisma')">Yarışma</a></div>

			</div>
			<div class="col-md-10">

				<br/>

				<div id="yarisma">
					
					<form action="" method="POST">

						<textarea class="form-control" name="soru" cols="3" placeholder="Soru Metni"></textarea><br/>
						
						<input type="text" name="cevap" class="form-control" placeholder="Cevap"><br/>

						<button class="btn btn-warning" type="submit" name="sorugonder">Gönder</button>

					</form>

					<form action="" method="POST">

						<input type="text" name="yarismaadi" class="form-control" placeholder="Yarışma Adı"><br/>
						
						<button class="btn btn-danger" type="submit" name="yarismabaslat">Yarışma Başlat</button>

					</form>

					<?php

						$yarismacek = $db->query("SELECT * FROM yarismalar", PDO::FETCH_ASSOC);

						if ( $yarismacek->rowCount() ){

							foreach( $yarismacek as $yc ){

								$yarismaid = cekf($yc['yarismaid']);

								$yarismaadi = cekf($yc['yarismaadi']);

								echo '<a href="yarisma.php?id='.$yarismaid.'" target="_blank">'.$yarismaadi.'</a>';

							}

						}

					?>

				</div>

				<div id="mailden" style="display: none;">
			
					<form action="" method="POST">
						
						<input type="text" name="mail" placeholder="MAİLİNİ YAZ">

						<input type="submit" name="maildenbul" value="Bul">

					</form>

					<?php echo $uye_id." ".$uye_adi." ".$telefon; ?>

				</div>
				
				<div id="temizlik" style="display: none;">
			
					<form action="" method="POST">
						
						<input type="text" name="tarihsaat" placeholder="0000-00-00 00:00:00">

						<input type="submit" name="eskiyegit" value="Eskiye Git">

					</form>

				</div>

				<div id="girisayi" style="display: none;">
					
					<form action="" method="POST">
						
						<input type="text" name="baslikid" placeholder="Başlık ID Gir">

						<input type="text" name="girisayisi" placeholder="Giri Sayısını Gir">

						<input type="submit" name="sayiguncelle" value="Güncelle">

					</form>

				</div>

				<div id="girenler" style="display: none;">

					<div class="row">
						
						<div class="col-md-4">

							<br/><h3>Günlük</h3><br/>
							
							<?php

								$bigunonce = date('Y-m-d H:i:s', time() - 86400);

								$sorgu = $db->prepare("SELECT COUNT(*) FROM uyeler WHERE son_giris_tarihi > '{$bigunonce}' AND uye_id > '65'"); $sorgu->execute();

								$gunlukgiren = $sorgu->fetchColumn();

								echo $gunlukgiren."<br/>";

								$query = $db->query("SELECT * FROM uyeler WHERE son_giris_tarihi > '{$bigunonce}' AND uye_id > '65'", PDO::FETCH_ASSOC);

								if ( $query->rowCount() ){

									foreach( $query as $row ){

										$uye_adi = cekf($row['uye_adi']);

										echo $uye_adi.'<br/>';

									}

								}

							?>

						</div>

						<div class="col-md-4">

							<br/><h3>Haftalık</h3><br/>
							
							<?php

								$bigunonce = date('Y-m-d H:i:s', time() - 604800);

								$sorgu = $db->prepare("SELECT COUNT(*) FROM uyeler WHERE son_giris_tarihi > '{$bigunonce}' AND uye_id > '65'"); $sorgu->execute();

								$haftalikgiren = $sorgu->fetchColumn();

								echo $haftalikgiren."<br/>";

								$query = $db->query("SELECT * FROM uyeler WHERE son_giris_tarihi > '{$bigunonce}' AND uye_id > '65'", PDO::FETCH_ASSOC);

								if ( $query->rowCount() ){

									foreach( $query as $row ){

										$uye_adi = cekf($row['uye_adi']);

										echo $uye_adi.'<br/>';

									}

								}

							?>

						</div>

						<div class="col-md-4">

							<br/><h3>Aylık</h3><br/>
							
							<?php

								$bigunonce = date('Y-m-d H:i:s', time() - 2419200);

								$sorgu = $db->prepare("SELECT COUNT(*) FROM uyeler WHERE son_giris_tarihi > '{$bigunonce}' AND uye_id > '65'"); $sorgu->execute();

								$aylikgiren = $sorgu->fetchColumn();

								echo $aylikgiren."<br/>";

								$query = $db->query("SELECT * FROM uyeler WHERE son_giris_tarihi > '{$bigunonce}' AND uye_id > '65'", PDO::FETCH_ASSOC);

								if ( $query->rowCount() ){

									foreach( $query as $row ){

										$uye_adi = cekf($row['uye_adi']);

										echo $uye_adi.'<br/>';

									}

								}

							?>

						</div>

						<div class="col-md-4"></div>

						<div class="col-md-4"></div>

					</div>

				</div>

				<div id="sifre" style="display: none;">
					
					<form action="" method="POST">
						
						<input type="text" name="uyeidsi" placeholder="UYE ID">

						<input type="submit" name="sifredegistir">

					</form>

				</div>

				<div id="uyetipidegistirme" style="display: none;">
					
					<form action="" method="POST">
						
						<input type="text" name="uyeid" placeholder="üye id">

						<input type="text" name="uyetipi" placeholder="üye tipi">

						<input type="submit" name="degistir" value="Değiştir">

					</form>

				</div>

				<div id="siralama" style="display: none;">
					
					<?php

						echo '<div class="row">
									<div class="col-md-2"><b>Yazar Adı</b></div>
									<div class="col-md-1"><b>Gösterim</b></div>
									<div class="col-md-2"><b>Gerçek Adı</b></div>
									<div class="col-md-3"><b>IBAN</b></div>
								</div><hr style="margin:10px;"/>';

						$birhaftaonce = time() - 604800;

						$birhaftaonce = date("Y-m-d H:i:s", $birhaftaonce);

						$query = $db->query("SELECT * FROM uyeler WHERE son_giris_tarihi > '{$birhaftaonce}' ORDER BY gosterim DESC LIMIT 50", PDO::FETCH_ASSOC);

						if ( $query->rowCount() ){

							foreach( $query as $row ){

								$yazarid = cekf($row['uye_id']);
								
								$yazar_adi = cekf($row['uye_adi']);

								$gosterim = cekf($row['gosterim']);

								$ad_soyad = cekf($row['ad_soyad']);

								$iban = cekf($row['iban']);

								if (empty($iban) === false) {
									

					?>

								<div class="row">
									<div class="col-md-2"><a href="http://kurtsozluk.net/profil.php?id=<?php echo $yazarid; ?>"><?php echo $yazar_adi; ?></a></div>
									<div class="col-md-1"><?php echo $gosterim; ?></div>
									<div class="col-md-2"><?php echo $ad_soyad; ?></div>
									<div class="col-md-3"><?php echo $iban; ?></div>
									<div class="col-md-4">
										<form action="" method="POST">
											<input type="text" name="puan" placeholder="ödemeyi düş">
											<input type="hidden" name="kisiid" value="<?php echo $yazarid; ?>">
											<input type="submit" name="sifirla" value="Ödeme Yap">
										</form>
									</div>
								</div><hr style="margin:10px;"/>

					<?php

								}

							}

						}

					?>

				</div>

				<div id="sikayetler" style="display: none;">

					<div class="row cerceve" style="padding: 20px;">

						<?php 

							$query = $db->query("SELECT * FROM sikayetler WHERE silindi = '0' ORDER BY sikayet_id DESC", PDO::FETCH_ASSOC);

							if ( $query->rowCount() ){

								foreach( $query as $row ){

									$sikayet_tarihi = cekf($row['sikayet_tarihi']);

									$sikayet_id= cekf($row['sikayet_id']);
									
									$sikayet_eden_id = cekf($row['sikayet_eden_id']);

									$sat = $db->query("SELECT * FROM uyeler WHERE uye_id = '{$sikayet_eden_id}'")->fetch(PDO::FETCH_ASSOC);
										
									$sikayet_eden_adi = cekf($sat['uye_adi']);

									$sikayet_edilen_id = cekf($row['sikayet_edilen_id']);

									$sat = $db->query("SELECT * FROM uyeler WHERE uye_id = '{$sikayet_edilen_id}'")->fetch(PDO::FETCH_ASSOC);
										
									$sikayet_edilen_adi = cekf($sat['uye_adi']);

									$girdi_id = cekf($row['sikayet_konu_id']);

									$sat = $db->query("SELECT * FROM giriler WHERE giriid = '{$girdi_id}'")->fetch(PDO::FETCH_ASSOC);
										
									$girdi_metin = cekf($sat['girimetin']);

									$konu_id = cekf($sat['giribaslikid']);

									$sikayet = cekf($row['sikayet_raporu']);

									echo '<div class="col-md-12">

											<b>Şikayet Eden : </b>'.$sikayet_eden_adi.'<br/>

											<b>Şikayet Edilen : </b><a href="profil.php?id='.$sikayet_edilen_id.'">'.$sikayet_edilen_adi.'</a><br/>

											<b>Şikayet Edilen Girdi : </b><a href="baslik.php?id='.$konu_id.'" target="_blank">'.$girdi_metin.'</a><br/>

											<b>Şikayet Sebebi : </b>'.$sikayet.'</br>

											<b>Şikayet Tarihi : </b>'.$sikayet_tarihi.'<br/>

											<form action="" method="POST">

												<input type="hidden" name="sikayet_id" value="'.$sikayet_id.'">
												
												<input type="submit" name="sil" value="sil">

											</form>

										</div><hr/>';

								}

							}

						?>

					</div>

				</div>

				<div id="uzaklastirmalar" style="display: none;">
					
					<?php

						$query = $db->query("SELECT * FROM ceza ORDER BY ceza_id DESC", PDO::FETCH_ASSOC);

						if ( $query->rowCount() ){

							foreach( $query as $row ){

								$ban_tipi = cekf($row['ban_tipi']);

								$girdi_id = cekf($row['girdi_id']);

								if ($ban_tipi == '1') {
								
									$girdi_link = '<a href="konu.php?id='.$girdi_id.'">Konuya Git</a>';

								}elseif ($ban_tipi == '2') {
									
									$girdi_link = '<a href="girdi.php?id='.$girdi_id.'">Girdiye Git</a>';

								}
								
								$cezali_adi = cekf($row['cezali_adi']);

								$ceza_sebebi = cekf($row['ceza_sebebi']);

								$ceza_veren = cekf($row['ceza_veren']);

								$cvb = $db->query("SELECT * FROM uyeler WHERE uye_id = '{$ceza_veren}'")->fetch(PDO::FETCH_ASSOC);

								$ceza_veren_adi = cekf($cvb['uye_adi']);

								echo '<div class="row">

											<div class="col-md-12">

												<b>Cezalı Adı : </b>'.$cezali_adi.'<br/>

												<b>Ceza Sebebi : </b>'.$ceza_sebebi.'</a><br/>

												<b>Ceza Veren Adı : </b>'.$ceza_veren_adi.'<br/>

												<b>Ceza Girdi : </b>'.$girdi_link.'</br>

											</div>

										</div><hr/>';

							}

						}

					?>

				</div>

			</div>
		</div>

	</div>

</body>

</html>