<?php

	include 'fonksiyonlar/bagla.php';

	session_destroy();

	setcookie("gska",$k_adi,time()-3600);

	setcookie("gss",$sifreli,time()-3600);

	header("Location:http://kurtsozluk.net/giris.php");

	exit();

?>