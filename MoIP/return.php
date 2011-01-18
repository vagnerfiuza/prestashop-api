<?php

/* SSL Management */

$useSSL = true;

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../header.php');
include(dirname(__FILE__).'/MoIP.php');
include(dirname(__FILE__).'/api.php');


if (!$cookie->isLogged())
    Tools::redirect('authentication.php?back=order.php');
$MoIP = new MoIP();

$KEY_NASP = Configuration::get('KEY_NASP');

$status_pd = $_GET['status_pd'];
$CodigoMoIP = $_GET['CodigoMoIP'];
$msg = $_GET['msg'];
$erro = $_GET['erro'];
$token = $_GET['token'];
$tipo = $_GET['tipo'];

log_var("Token: ".$token, "return | MoIP Labs Debug", true, $KEY_NASP);


echo $MoIP->hookPaymentReturn($status_pd, $CodigoMoIP, $msg, $erro, $token, $tipo);

include_once(dirname(__FILE__).'/../../footer.php');


?>