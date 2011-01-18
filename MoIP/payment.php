<?php

/* SSL Management */
$useSSL = true;

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../header.php');
include(dirname(__FILE__).'/MoIP.php');

if (!$cookie->isLogged())
    Tools::redirect('authentication.php?back=order.php');
$MoIP = new MoIP();

$dados_carrinho = array('valor' => $_POST['valor'],
				 'nome' => $_POST['nome'],
				 'descricao' => $_POST['descricao'],
				 'id_cliente' => $_POST['id_cliente'],
				 'id_transacao' => $_POST['id_transacao'],
				 'layout' => $_POST['layout'],
				 'url_retorno' => $_POST['url_retorno'],
                                 'id_cart' => $_POST['id_cart']
);

$adicionais = array('ambiente_moip' => $_POST['ambiente_moip'],
					'login_moip' => $_POST['login_moip']
	);
$params_payment = $_POST['params_payment'];

echo $MoIP->execPayment($cart);

include_once(dirname(__FILE__).'/../../footer.php');

?>