<?php

//SELECT

$query = $db->query("SELECT * FROM uyeler WHERE uye_id = '{$id}'")->fetch(PDO::FETCH_ASSOC);

//SELECT TOPLU

$query = $db->query("SELECT * FROM uyeler", PDO::FETCH_ASSOC);

if ( $query->rowCount() ){

	foreach( $query as $row ){

		print $row['kulanici_adi']."<br />";

	}

}

//INSERT

$query = $db->prepare("INSERT INTO uyeler SET giribaslikid = ?, giriustid = ?, giriyazarid = ?, girimetin = ?, giritarih = ?, girisaniye = ?, giritipi = ?, m_sehir = ?, y_sehir = ?");

$insert = $query->execute(array($o_konu_id, $o_konu_id, $uye_id, $konu_metni, $now, $su_an, '0', $uye_m_sehir, $uye_y_sehir));

//UPDATE

$query = $db->prepare("UPDATE uyeler SET gosterim = gosterim + 1 WHERE uye_id = ?"); 

$guncelle = $query->execute(array('1'));


//COUNT

$sorgu = $db->prepare("SELECT COUNT(*) FROM ceza WHERE cezali_adi = '{$uye_adi}' AND ceza_saniye > '{$su_an}' ORDER BY ceza_id DESC LIMIT 1"); $sorgu->execute();

$ceza_sayi = $sorgu->fetchColumn();

//DELETE

$query = $db->prepare("DELETE FROM uyeler WHERE uy_id = ?");

$delete = $query->execute(array('id'));

?>