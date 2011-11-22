<?php

/* SSL Management */

$useSSL = true;

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/MoIP.php');
include(dirname(__FILE__).'/api.php');

$MoIP = new MoIP();
$KEY_NASP = Configuration::get('KEY_NASP');

if($_POST['cod_moip'] != ""){

    $errors = '';
    $result = false;

    if($_GET['key'] == $KEY_NASP){

        $name_status = $MoIP->newMoIPStatus($_POST['status_pagamento'], true);

        log_var("NASP validado \n Cod. MoIP: ".$_POST['cod_moip']."\n Status: ".$name_status, "validation | MoIP Labs Debug, Data: ".date("d-m-Y G:i:s"), true, $KEY_NASP);

        $result = "VERIFICADO";

        $id_var = explode('[',$_POST['id_transacao']);
        $id_var_explode = $id_var[1];
        $tmp = explode(']',$id_var_explode);
        $id_transacao_moip = $tmp[0];

        $id_moeda = Configuration::get('PS_CURRENCY_DEFAULT');

    }else{

        $result = "FALHOU";
        log_var("NASP não validado \nKEY: ".$_GET['key'], "validation | MoIP Labs Debug, Data: ".date("d-m-Y G:i:s"), true, $KEY_NASP);

    }

    if ($result == 'VERIFICADO') {

        $status = $MoIP->newMoIPStatus($_POST['status_pagamento']);

        if (!isset($_POST['valor']))
            $errors .= $MoIP->getL('valor_moip');
        if (!isset($_POST['status_pagamento']))
            $errors .= $MoIP->getL('status_pagamento_moip');
        if (!isset($_POST['id_transacao']))
            $errors .= $MoIP->getL('id_transacao_moip');
        if (!isset($_POST['email_consumidor']))
            $errors .= $MoIP->getL('email_consumidor_moip');
        if (!isset($_POST['cod_moip']))
            $errors .= $MoIP->getL('post_cod_moip');
        if (empty($errors)){

            $cart = new Cart(intval($id_transacao_moip));
            $valor_compra = number_format($_POST['valor'], 2, '.', '')/100;
            if (!$cart->id)
                $errors = $MoIP->getL('cart').' <br />';
            elseif (Order::getOrderByCartId(intval($id_transacao_moip)))
                $errors = $MoIP->getL('order').' <br />';
            else{


                $currency = new Currency
                    (intval(isset($_POST['currency_payement']) ? $_POST['currency_payement'] : $cookie->id_currency));

                //Cria order, transacao.
                $MoIP->validateOrder($id_transacao_moip, $status, $valor_compra, $MoIP->displayName, $MoIP->getL('transaction', $_POST['cod_moip'], $_POST['id_transacao'], $_POST['email_consumidor']), $mailVars, $id_moeda);


                if ($MoIP->currentOrder != ""){
                    log_var("Compra gravado no BD corretamente\nId proprio: ".$_POST['id_transacao']."\nCodigo MoIP: ".$_POST['cod_moip']."\nID transacao: ".$id_transacao_moip."\nID Order PrestaShop: ".$MoIP->currentOrder, "validation | MoIP Labs Debug", true, $KEY_NASP);
                    $MoIP->addOrder($id_transacao_moip);
                }
                else{
                    log_var("Erro ao gravar compra no BD\nId transacao: ".$id_transacao_moip, "validation | MoIP Labs Debug", true, $KEY_NASP);
                }
            }
        }

        log_var("Resultado: ".$result."\nErro: ".$errors."\nTotal: ".$valor_compra."\nStatus MoIP: ".$_POST['status_pagamento']."\nNovo Status: ".$status."\nCart: ".$cart->id."\nOrder: ".$MoIP->currentOrder."\nMoeda: ".$id_moeda, "validation | MoIP Labs Debug, Data: ".date("d-m-Y G:i:s"), true, $KEY_NASP);

    } else {
        $errors .= $MoIP->getL('VERIFICADO');
        log_var("NAO VERIFICADO", "validation | MoIP Labs Debug, Data: ".date("d-m-Y G:i:s"), true, $KEY_NASP);
    }

    $MoIP->newHistory($id_transacao_moip, $status, $errors);
    exit;
}

include(dirname(__FILE__).'/../../header.php');

if (!$cookie->isLogged())
    Tools::redirect('authentication.php?back=order.php');

if($_POST['instrucao'] == "API"){

    $aceitar_parcelamento = Configuration::get('ACEITAR_PARCELAMENTO');
    $prefixo_id_transacao = Configuration::get('MoIP_ID_TRANSACAO_PREFIX');
    $ambiente_moip = Configuration::get('ambiente_moip');
    $login_moip = Configuration::get('MoIP_BUSINESS');
    $apelido_moip = Configuration::get('MoIP_APELIDO');

    $id_Address = $_POST['id_Address'];
    $id_cliente = $_POST['id_cliente'];
    $id_cart_payment = $_POST['id_cart_payment'];

    $invoiceAddress = new Address(intval($id_Address));
    $customer = new Customer(intval($id_cliente));
    $country = new Country(intval($invoiceAddress->id_country));
    $cart = new Cart(intval($id_cart_payment));
    $order 		= new Order($MoIP->currentOrder);

    $telefone = ereg_replace("[^0-9]", "", $invoiceAddress->phone);
    $cep = ereg_replace("[^0-9]", "", $invoiceAddress->postcode);
    $uf_estado = array('cidade' => $invoiceAddress->city);

    $url_loja = "http://".$_SERVER['HTTP_HOST'].__PS_BASE_URI__;
    $moip_id_cart = $cart->id;
    $moip_id_cart_module = $MoIP->id;
    $moip_current_order = $MoIP->currentOrder;
    $moip_secure_key = $order->secure_key;

    //    $url_retorno =  $url_loja."modules/MoIP/validation.php?id=".$id_cliente."&cart=".$id_cart_payment;
    $url_retorno =  $url_loja."modules/MoIP/validation.php?id=".$id_cliente;
    $url_retorno =  htmlspecialchars($url_retorno, ENT_COMPAT, 'UTF-8');

    $endereco = getConsultaCep($cep);
    if($endereco == "falha"){
        $UF = getUf($uf_estado);
        $logradouro = $invoiceAddress->address1;
        $bairro = $invoiceAddress->address2;
        $cidade = $invoiceAddress->city;
        $numero_lagradouro = getNunberAddress($invoiceAddress->address1);
    }else{
        $UF = $endereco['uf'];
        $logradouro = $endereco['tipo_lagradouro']." ".utf8_decode($endereco['logradouro']);
        $bairro = utf8_decode($endereco['bairro']);
        $cidade = utf8_decode($endereco['cidade']);
        $numero_lagradouro = getNunberAddress($invoiceAddress->address1);
    }

    $credito_expiracao = $_POST['credito_expiracao_mes']."/".$_POST['credito_expiracao_ano'];

    if($aceitar_parcelamento == 1 && $_POST['forma_pagamento'] == "CartaoCredito"){
        $credito_parcelamento = $_POST['credito_parcelamento'];
        $a = explode ("[", $credito_parcelamento);
        $b = explode ("]",$a[1]);
        $valor_total = $b[0];

        $c = explode ("(", $credito_parcelamento);
        $d = explode (")",$c[1]);
        $credito_parcelamento_parcelas = $d[0];
        log_var("Condicao: Admin - Aceitar Parcelamento = Ativo, User - Forna de Pagamento Cartão de Cédito(Pagamento Direto)\nTotal: ".$valor_total."\nParcelas: ".$credito_parcelamento_parcelas, "validation | MoIP Labs Debug", true, $KEY_NASP);

    }elseif($_POST['forma_pagamento'] == "CartaoCredito"){
        $credito_parcelamento_parcelas = 1;
        $valor_total = number_format($cart->getOrderTotal(true, 3), 2, '.', '');
        log_var("Condicao: Admin - Aceitar Parcelamento - Inativo, User - Forna de Pagamento Cartão de Cédito(Pagamento Direto)\nTotal: ".$valor_total, "validation | MoIP Labs Debug", true, $KEY_NASP);

    }else{
        $valor_total = number_format($cart->getOrderTotal(true, 3), 2, '.', '');
        log_var("Condicao: User - Forna de Pagamento Boleto, Debito(Pagamento Direto) ou Instrucao simples\nTotal: ".$valor_total, "validation | MoIP Labs Debug", true, $KEY_NASP);

    }


    if($valor_total == '' ){
        $valor_total = number_format($cart->getOrderTotal(true, 3), 2, '.', '');
        log_var("Condicao: Total zerado ate o presente momento, gerando novo total...\nTotal: ".$valor_total, "validation | MoIP Labs Debug", true, $KEY_NASP);
    }



    $IdProprio_PS = $prefixo_id_transacao.' ['.$id_cart_payment.']';

    $xml = new DomDocument('1.0', 'UTF-8');
    $instrucao = $xml->createElement('EnviarInstrucao', '');
    $xml->appendChild($instrucao);

    $unica = $xml->createElement('InstrucaoUnica', '');
    $instrucao->appendChild($unica);

    $Razao = $xml->createElement('Razao', 'Pedido: 3'.$id_cart_payment.' | '.$prefixo_id_transacao);
    $unica->appendChild($Razao);

    $Valores = $xml->createElement('Valores', '');
    $unica->appendChild($Valores);

    $Valor = $xml->createElement('Valor', $valor_total);
    $Valores->appendChild($Valor);
    $Valor->setAttribute('moeda', 'BRL');

    $IdProprio = $xml->createElement('IdProprio', $IdProprio_PS);
    $unica->appendChild($IdProprio);

    $Mensagens = $xml->createElement('Mensagens', '');
    $unica->appendChild($Mensagens);

    $Mensagem = $xml->createElement('Mensagem', $_POST['descricao']);
    $Mensagens->appendChild($Mensagem);

    $comissionamento = Configuration::get('COMISSIONAMENTO');
    if($comissionamento) {
        $comissoes = $xml->createElement('Comissoes', '');
        for($i=1;$i<=3;$i++) {
            $comissionamento_login = Configuration::get('COMISSIONAMENTO_LOGIN_'.$i);
            $comissionamento_tipo = Configuration::get('COMISSIONAMENTO_TIPO_'.$i);
            $comissionamento_valor = Configuration::get('COMISSIONAMENTO_VALOR_'.$i);
            if($comissionamento_login && $comissionamento_tipo && $comissionamento_tipo) {
                $comissionamento = $xml->createElement('Comissonamento', '');
                $com_razao = $xml->createElement('Razao', 'Comissão '.$i);
                $comissionamento->appendChild($com_razao);
                $comissionado = $xml->createElement('Comissonado', '');
                $com_loginmoip = $xml->createElement('LoginMoIP', $comissionamento_login);
                $comissionamento->appendChild($com_loginmoip);
                $com_tipo = $xml->createElement($comissionamento_tipo == 'fixo' ? 'ValorFixo' : 'ValorPercentual', $comissionamento_valor);
                $comissionamento->appendChild($com_tipo);
                $comissoes->appendChild($comissionamento);
            }
        }
        $unica->appendChild($comissoes);
    }


    if($_POST['forma_pagamento'] == "BoletoBancario"){

        $PagamentoDireto = $xml->createElement('PagamentoDireto', '');
        $unica->appendChild($PagamentoDireto);

        $Forma = $xml->createElement('Forma', 'BoletoBancario');
        $PagamentoDireto->appendChild($Forma);

    }elseif($_POST['forma_pagamento'] == "DebitoBancario"){

        $PagamentoDireto = $xml->createElement('PagamentoDireto', '');
        $unica->appendChild($PagamentoDireto);

        $Forma = $xml->createElement('Forma', 'DebitoBancario');
        $PagamentoDireto->appendChild($Forma);

        $Instituicao = $xml->createElement('Instituicao', $_POST['debito_instituicao']);
        $PagamentoDireto->appendChild($Instituicao);

    }elseif($_POST['forma_pagamento'] == "CartaoCredito"){

        $PagamentoDireto = $xml->createElement('PagamentoDireto', '');
        $unica->appendChild($PagamentoDireto);

        $Forma = $xml->createElement('Forma', 'CartaoCredito');
        $PagamentoDireto->appendChild($Forma);

        $Instituicao = $xml->createElement('Instituicao', $_POST['credito_instituicao']);
        $PagamentoDireto->appendChild($Instituicao);

        $CartaoCredito = $xml->createElement('CartaoCredito', '');
        $PagamentoDireto->appendChild($CartaoCredito);

        $Numero = $xml->createElement('Numero', $_POST['credito_numero']);
        $CartaoCredito->appendChild($Numero);

        $Expiracao = $xml->createElement('Expiracao', $credito_expiracao);
        $CartaoCredito->appendChild($Expiracao);

        $CodigoSeguranca = $xml->createElement('CodigoSeguranca', $_POST['credito_codigo_seguranca']);
        $CartaoCredito->appendChild($CodigoSeguranca);

        $Portador = $xml->createElement('Portador', '');
        $CartaoCredito->appendChild($Portador);

        $Nome = $xml->createElement('Nome', $_POST['credito_portador_nome']);
        $Portador->appendChild($Nome);

        $Identidade = $xml->createElement('Identidade', $_POST['credito_portador_cpf']);
        $Portador->appendChild($Identidade);
        $Identidade->setAttribute('Tipo', 'CPF');

        $Telefone = $xml->createElement('Telefone', $_POST['credito_portador_telefone']);
        $Portador->appendChild($Telefone);

        $DataNascimento = $xml->createElement('DataNascimento', $_POST['credito_portador_nascimento']);
        $Portador->appendChild($DataNascimento);

        $Parcelamento = $xml->createElement('Parcelamento', '');
        $PagamentoDireto->appendChild($Parcelamento);

        $Parcelas = $xml->createElement('Parcelas', $credito_parcelamento_parcelas);
        $Parcelamento->appendChild($Parcelas);

        $Recebimento = $xml->createElement('Recebimento', 'AVista');
        $Parcelamento->appendChild($Recebimento);
    }

    $Pagador = $xml->createElement('Pagador', '');
    $unica->appendChild($Pagador);

    $Nome = $xml->createElement('Nome', $invoiceAddress->firstname." ".$invoiceAddress->lastname);
    $Pagador->appendChild($Nome);

    $Email = $xml->createElement('Email', $customer->email);
    $Pagador->appendChild($Email);

    $EnderecoCobranca = $xml->createElement('EnderecoCobranca', '');
    $Pagador->appendChild($EnderecoCobranca);

    $Logradouro = $xml->createElement('Logradouro', $logradouro);
    $EnderecoCobranca->appendChild($Logradouro);

    $Numero = $xml->createElement('Numero', $numero_lagradouro);
    $EnderecoCobranca->appendChild($Numero);

    $Bairro = $xml->createElement('Bairro', $bairro);
    $EnderecoCobranca->appendChild($Bairro);

    $Cidade = $xml->createElement('Cidade', $cidade);
    $EnderecoCobranca->appendChild($Cidade);

    $Estado = $xml->createElement('Estado', $UF);
    $EnderecoCobranca->appendChild($Estado);

    $Pais = $xml->createElement('Pais', 'BRA');
    $EnderecoCobranca->appendChild($Pais);

    $CEP = $xml->createElement('CEP', $cep);
    $EnderecoCobranca->appendChild($CEP);

    $TelefoneFixo = $xml->createElement('TelefoneFixo', $telefone);
    $EnderecoCobranca->appendChild($TelefoneFixo);

    $Recebedor = $xml->createElement('Recebedor', '');
    $unica->appendChild($Recebedor);

    $LoginMoIP = $xml->createElement('LoginMoIP', $login_moip);
    $Recebedor->appendChild($LoginMoIP);

    $Apelido = $xml->createElement('Apelido', $apelido_moip);
    $Recebedor->appendChild($Apelido);

    $URLRetorno = $xml->createElement('URLRetorno', "http://www.google.com.br/");
    $unica->appendChild($URLRetorno);

    $param = $xml->saveXML();
    $CriaXML =  $xml->save($KEY_NASP.'_enviado.xml');

    $base = getAuth($ambiente_moip);
    $auth = base64_encode($base);
    $header[] = "Authorization: Basic " . $auth;

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL,"$ambiente_moip/ws/alpha/EnviarInstrucao/Unica");
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl, CURLOPT_USERPWD, $user . ":" . $passwd);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0");
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $param);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $ret = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    $res = simplexml_load_string($ret);
    $res_xml = new DomDocument('1.0', 'UTF-8');
    $RespostaMoIP = $res_xml->createElement('RespostaMoIP', $ret);
    $res_xml->appendChild($RespostaMoIP);
    $CriaXMLRespostaMoIP =  $res_xml->save($KEY_NASP.'_resposta.xml');

    $resp_xml = $res->Resposta->Status;
    $token_link = $res->Resposta->Token;
    $layout = Configuration::get('LAYOUT');

    if($layout != "" && $layout != "default"){
        $new_layout = "&layout=".$layout;
    }

    $link_api = $ambiente_moip."/Instrucao.do?token=".$token_link.$new_layout;
    $link_api_boleto = $ambiente_moip."/Instrucao.do?token=".$token_link;

    // if redirect
    if($resp_xml == "Sucesso"){

        if($_POST['forma_pagamento'] == "BoletoBancario"){

            echo $MoIP->hookPaymentReturn(false, false, $id_cliente, false, false, $token_link, "boleto");

        }elseif($_POST['forma_pagamento'] == "DebitoBancario"){

            echo $MoIP->hookPaymentReturn(false, false, $id_cliente, false, false, $token_link, "debito");


        }elseif($_POST['forma_pagamento'] == "CartaoCredito"){

            $id_moeda = Configuration::get('PS_CURRENCY_DEFAULT');

            $id_transacao_moip_pd_credito = $id_cart_payment;
            $status_pd_credito = $res->Resposta->RespostaPagamentoDireto->Status;
            $resposta_pd_credito_codigomoip = $res->Resposta->RespostaPagamentoDireto->CodigoMoIP;

            $status = $MoIP->newMoIPStatus($status_pd_credito);

            $MoIP->validateOrder($id_transacao_moip_pd_credito, $status, $valor_total, $MoIP->displayName, $MoIP->getL('transaction', $resposta_pd_credito_codigomoip, $IdProprio_PS, $customer->email), $mailVars, $id_moeda);

            $order = new Order($MoIP->currentOrder);

            if ($MoIP->currentOrder != ""){
                log_var("Compra gravado no BD corretamente\nId proprio: ".$_POST['id_transacao']."\nCodigo MoIP: ".$_POST['cod_moip']."\nID transacao: ".$id_transacao_moip."\nID Order PrestaShop: ".$MoIP->currentOrder, "validation | MoIP Labs Debug", true, $KEY_NASP);
                $MoIP->addOrder($id_transacao_moip_pd_credito);
            }

            $MoIP->newHistory($id_transacao_moip_pd_credito, $status);

            if($status_pd_credito != ""){

                echo $MoIP->hookPaymentReturn($status_pd_credito, $resposta_pd_credito_codigomoip, $id_cliente, false, false, false, false, $valor_total);


            }else{

                echo $MoIP->hookPaymentReturn('falha');

            }

        }else{
            $link = $link_api;
            $time = '1000'; //0sg
            echo $html = $MoIP->htmlRedirect($link, $time);
        }

    }elseif($resp_xml == "Falha"){

        if($_POST['forma_pagamento'] == "CartaoCredito"){

            $erro_pd_credito = $res->Resposta->Erro;
            $erro_pd_credito = htmlspecialchars($erro_pd_credito, ENT_COMPAT, 'UTF-8');

            echo $MoIP->hookPaymentReturn('falha', false, false,$erro_pd_credito);

        }else{
            $resposta_erro = $res->Resposta->Erro;

            echo $MoIP->hookPaymentReturn('falha', false, false, $resposta_erro);

        }

    }elseif($resp_xml == ""){
        $resposta_erro = $res->Resposta->Erro;
        echo $MoIP->hookPaymentReturn('falha', false, false, false, '1');

    }

    if($param == ""){

        echo $MoIP->hookPaymentReturn('falha', false, false, false, '2');

    }

}elseif($_GET['id'] != false){

    echo $MoIP->hookPaymentReturn('sucesso', false, $_GET['id'], false, false, false, false, false, $_GET['cart']);

}else{
    echo $MoIP->hookPaymentReturn(false);

}

include_once(dirname(__FILE__).'/../../footer.php');

?>
