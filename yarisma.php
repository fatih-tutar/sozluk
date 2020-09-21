<?php

	include 'fonksiyonlar/bagla.php';

	if ($girdi != 1) {
		
		header("Location:cikis.php");

		exit();

	}else{

		if ($uye_id != 1) {
		
			header("Location:index.php");

			exit();

		}else{

			$yarismaid = cekf($_GET['id']);

			$yarismabilgileri = $db->query("SELECT * FROM yarismalar WHERE yarismaid = '{$yarismaid}'")->fetch(PDO::FETCH_ASSOC);

			$yarisanlar = cekf($yarismabilgileri['yarisanlar']);

			$puanlar = cekf($yarismabilgileri['puanlar']);

			$sorular = cekf($yarismabilgileri['sorular']);

			if (isset($_POST['yarismacikayit'])) {
				
				$yarismaciadi = yollaf($_POST['yarismaciadi']);

				if (empty($yarisanlar) === false) {
					
					$yarisanlar = $yarisanlar.",".$yarismaciadi;

					$puanlar = $puanlar.",1";

					$yarisanlistesi = $db->prepare("UPDATE yarismalar SET yarisanlar = ?, puanlar = ? WHERE yarismaid = ?"); 

					$guncelle = $yarisanlistesi->execute(array($yarisanlar,$puanlar,$yarismaid));

				}else{

					$yarisanlar = $yarismaciadi;

					$puanlar = 1;

					$yarisanlistesi = $db->prepare("UPDATE yarismalar SET yarisanlar = ?, puanlar = ? WHERE yarismaid = ?"); 

					$guncelle = $yarisanlistesi->execute(array($yarisanlar,$puanlar,$yarismaid));

				}

				header("Location:yarisma.php?id=".$yarismaid);

				exit();

			}

			if (isset($_POST['puanarttir'])) {
				
				$yarismacikey = yollaf($_POST['yarismacikey']);

				$puanlardizisi = explode(",", $puanlar);

				$puanlardizisi[$yarismacikey] = $puanlardizisi[$yarismacikey] + 1;

				$puanlar = implode(",", $puanlardizisi);

				$puanlariguncelleme = $db->prepare("UPDATE yarismalar SET puanlar = ? WHERE yarismaid = ?"); 

				$guncelle = $puanlariguncelleme->execute(array($puanlar,$yarismaid));

				header("Location:yarisma.php?id=".$yarismaid);

				exit();

			}

		}

	}

?>
<!DOCTYPE html>

<html>

<head>

	<title></title>

	<meta name="keywords" content="kurt, sözlük" />

	<?php include 'template/head.php'; ?>

</head>

<body>

	<?php

		$sorgu = $db->prepare("SELECT COUNT(*) FROM sorular"); $sorgu->execute();

		$sorusayisi  = $sorgu->fetchColumn();

		do{ 

			$rastgelesoru = rand(1,$sorusayisi); 

		}while(strstr($sorular, $rastgelesoru));

		$soruyugetir = $db->query("SELECT * FROM sorular WHERE soruid = '{$rastgelesoru}'")->fetch(PDO::FETCH_ASSOC);

		$soru = cekf($soruyugetir['soru']);

		$cevap = cekf($soruyugetir['cevap']);

		if (empty($sorular) === false) {
			
			$sorular = $sorular.",".$rastgelesoru;

			$sorulistesi = $db->prepare("UPDATE yarismalar SET sorular = ? WHERE yarismaid = ?"); 

			$guncelle = $sorulistesi->execute(array($sorular,$yarismaid));

		}else{

			$sorular = $rastgelesoru;

			$sorulistesi = $db->prepare("UPDATE yarismalar SET sorular = ? WHERE yarismaid = ?"); 

			$guncelle = $sorulistesi->execute(array($sorular,$yarismaid));

		}

	?>

	<div class="container-fluid">

		<div class="row">
			
			<div class="col-md-12" style="height: 30px;">
				
				<!-- AÇIKLAMA -->

			</div>

		</div>
		
		<div class="row">
			
			<div class="col-md-12" style="text-align: center;">

				<b style="font-size: 60px;">ÖDÜLLÜ BİLGİ YARIŞMASI</b>

			</div>

		</div>

		<div class="row" style="margin:30px 0px 30px 0px;">

			<div class="col-md-2"></div>
			
			<div class="col-md-2" style="text-align: center;">
				
				<button class="btn btn-primary btn-lg btn-block" onclick="return false" onmousedown="javascript:ackapa3('sorucevapdivi','aciklamadivi','skordivi');"><b style="font-size: 40px;">Soru</b></button>

			</div>

			<div class="col-md-2" style="text-align: center;">
				
				<button class="btn btn-success btn-lg btn-block" onclick="return false" onmousedown="javascript:ackapa('cevapdivi');"><b style="font-size: 40px;">Cevap</b></button>

			</div>

			<div class="col-md-2" style="text-align: center;">
				
				<button class="btn btn-danger btn-lg btn-block" onclick="return false" onmousedown="javascript:ackapa3('aciklamadivi','sorucevapdivi','skordivi');"><b style="font-size: 40px;">Açıklama</b></button>

			</div>

			<div class="col-md-2" style="text-align: center;">
				
				<button class="btn btn-warning btn-lg btn-block" onclick="return false" onmousedown="javascript:ackapa3('skordivi','sorucevapdivi','aciklamadivi');"><b style="font-size: 40px;">Skor Tablosu</b></button>

			</div>

		</div>

		<div id="skordivi" class="alert alert-warning" style="text-align: center; display: none;">

			<div class="row">
				
				<div class="col-md-3"></div>

				<div class="col-md-6">

					<?php

						$yarisanlardizisi = explode(",", $yarisanlar);

						$puanlardizisi = explode(",", $puanlar);

						foreach ($yarisanlardizisi as $key => $value) {

							$puani = $puanlardizisi[$key];
							
					?>

							<div class="row">
								
								<div class="col-md-8" style="text-align: left;"><b style="font-size: 50px;"><?php echo $value; ?></b></div>

								<div class="col-md-2"><b style="font-size: 50px;"><?php echo $puani; ?></b></div>

								<div class="col-md-2" style="padding-top: 25px;">

									<form action="" method="POST">

										<input type="hidden" name="yarismacikey" value="<?php echo $key; ?>">
									
										<button type="submit" name="puanarttir" style="border-style: none; background-color: #fcf8e3;"><i class="fa fa-plus fa-2x"></i></button>

									</form>

								</div>

							</div>

					<?php

						}

					?>

					<hr/>

					<div class="row">
						
						<div class="col-md-4"></div>

						<div class="col-md-4">
							
							<form action="" method="POST">

								<input type="text" name="yarismaciadi" class="form-control" placeholder="Yarışmacı Adı"><br/>
								
								<button class="btn btn-warning btn-block" type="submit" name="yarismacikayit">Kaydet</button>

							</form>

						</div>

						<div class="col-md-4"></div>

					</div>

				</div>

				<div class="col-md-3"></div>

			</div>

		</div>

		<div id="aciklamadivi" class="alert alert-danger" style="text-align: center;">
			
			<b style="font-size: 140px; line-height: 200px;">

				Ödül miktarı beğeni sayısına göre belirlenmektedir.

			</b>

		</div>

		<div id="sorucevapdivi" style="display: none; text-align: center;">

			<div class="row" style="margin:50px 0px 50px 0px;">

				<div class="col-md-12" style="min-height: 250px;">

					<div id="sorudivi" class="alert alert-info" style="text-align: center;">

						<b style="font-size: 70px; color: #337ab7;"><?php echo "Soru : ".$soru; ?></b>

					</div>

				</div>

			</div>

			<div class="row" style="margin:50px 0px 50px 0px;">

				<div class="col-md-12">
					
					<div id="cevapdivi" class="alert alert-success" style="display: none; text-align: center;">

						<b style="font-size: 70px;"><?php echo "Cevap : ".$cevap; ?></b>

					</div>

				</div>

			</div>

		</div>

	</div>

	<?php include 'template/jscss.html'; ?>

</body>

</html>