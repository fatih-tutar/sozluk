<?php

	include 'fonksiyonlar/bagla.php';

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

							<div class="div2">
								
								<p class="text-justify" style="word-wrap: break-word;">

									<b>Kurt Sözlük</b><br/>

									Kurt Sözlük, üyelerin her konuda başlıklar altında paylaşım yaptıkları bir internet sitesidir. Üyeler aynı zamanda

									burada para da kazanabilirler. Ama Kurt Sözlük bu sistemin suistimal edilmemesi için üyelere hangi kriterlere göre

									ödeme yaptığını gizli tutar. Sadece şunu bil ki burada ne kadar aktifsen o kadar para kazanırsın. Bir kullanıcı 

									adıyla gerçek kimliğinden sıyrıl kimseden çekinmeden özgürce yaz. 

									(Suç teşkil eden paylaşımlar yapıp siteyi kapanmanın eşiğine getirmediğin sürece sınır yok.)<br/><br/>

									<b>İstatistikler</b><br/>

									<i>Toplam Yazar Sayısı : </i>

									<?php 

									$sorgu = $db->prepare("SELECT COUNT(*) FROM uyeler"); $sorgu->execute();

									$yazar_sayisi = $sorgu->fetchColumn();

									echo $yazar_sayisi; ?><br/>

									<i>Toplam Başlık Sayısı: </i>

									<?php

									$sorgu = $db->prepare("SELECT COUNT(*) FROM basliklar"); $sorgu->execute();

									$baslik_sayisi = $sorgu->fetchColumn();

									echo $baslik_sayisi; ?><br/>

									<i>Toplam Giri Sayısı: </i>

									<?php 

									$sorgu = $db->prepare("SELECT COUNT(*) FROM giriler"); $sorgu->execute();

									$cevap_sayisi = $sorgu->fetchColumn();

									echo $cevap_sayisi; ?><br/>

									<i>Son 30 Günlük Başlık Sayısı: </i>

									<?php 

									$otuzgunonce = time() - 2592000;

									$sorgu = $db->prepare("SELECT COUNT(*) FROM basliklar WHERE basliksaniye > $otuzgunonce"); $sorgu->execute();

									$cevap_sayisi = $sorgu->fetchColumn();

									echo $cevap_sayisi; ?><br/>

									<i>Son 30 Günlük Giri Sayısı: </i>

									<?php 

									$otuzgunonce = time() - 2592000;

									$sorgu = $db->prepare("SELECT COUNT(*) FROM giriler WHERE girisaniye > $otuzgunonce"); $sorgu->execute();

									$cevap_sayisi = $sorgu->fetchColumn();

									echo $cevap_sayisi; ?><br/>

									<br/>

								</p>

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