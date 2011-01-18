<?php
        /*  função de defição de parcelamento
         * 
         */
        function getInfoParcelamento($de1,$ate1,$juros1,$de2,$ate2,$juros2,$de3,$ate3,$juros3) {

        $config = array();
        $max = 12;

        $config['de1'] = (int)$de1;
        $config['ate1'] = (int)$ate1;
        $config['juros1'] = $juros1;
        $config['de2'] = (int)$de2;
        $config['ate2'] = (int)$ate2;
        $config['juros2'] = $juros2;
        $config['de3'] = (int)$de3;
        $config['ate3'] = (int)$ate3;
        $config['juros3'] = $juros3;

        if ($config['ate1'] > $max)
            $config['ate1'] = $max;
        if ($config['ate2'] > $max)
            $config['ate2'] = $max;
        if ($config['ate3'] > $max)
            $config['ate3'] = $max;

        return $config;
    }

        /*função para checar parcelamento no MoIP
         *
         */
        function getRequestParcelamento($valor, $juros, $parcelas, $conta_moip, $ambiente_moip="https://desenvolvedor.moip.com.br/sandbox") {

$base = getAuth($ambiente_moip);
$auth = base64_encode($base);
$header[] = "Authorization: Basic " . $auth;

$ch = curl_init();
$timeout = 0;
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_URL, "$ambiente_moip/ws/alpha/ChecarValoresParcelamento/$conta_moip/$parcelas/$juros/$valor");
curl_setopt($ch, CURLOPT_USERPWD, $user . ":" . $passwd);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
$conteudo = curl_exec($ch);
$erro = curl_error($ch);
curl_close($ch);
		// API Checa Pagamento Direto //

        $res = simplexml_load_string($conteudo);

        $result = array();
        $i = 1;
        foreach ($res as $resposta) {
            foreach ($resposta as $data) {
                if ($data->getName() == "ValorDaParcela") {
                    $result[$i]['total'] = $data->attributes()->Total;
                    $result[$i]['valor'] = $data->attributes()->Valor;
                    $i++;
                }
            }
        }

        return $result;
    }


    /* função matroz para o parcelamento
     *
     */
    function getParcelamento_SON($valor, $de1=false,$ate1=false,$juros1=false,$de2=false,$ate2=false,$juros2=false,$de3=false,$ate3=false,$juros3=false, $conta_moip=false, $ambiente_moip="https://desenvolvedor.moip.com.br/sandbox") {

        $valor = number_format($valor, "2", ".", "") / 100;
        $parcelamento = getInfoParcelamento($de1,$ate1,$juros1,$de2,$ate2,$juros2,$de3,$ate3,$juros3);

        /* pega as 3 faixas de parcelamento. Essa rotina poderia ser feita com um
         * um Ãºnico looping. Manteremos dessa forma para ficar mais didÃ¡tico
         */

        $result = array();
        if ($parcelamento['ate1'] > 0) {
            $result1 = getRequestParcelamento($valor, $parcelamento['juros1'], $parcelamento['ate1'], $conta_moip, $ambiente_moip);
            foreach ($result1 as $k => $v) {
                if ($k >= $parcelamento['de1'])
                    $result[$k] = $v;
            }
        }
        if ($parcelamento['ate2'] > $parcelamento['ate1']) {
            $result1 = getRequestParcelamento($valor, $parcelamento['juros2'], $parcelamento['ate2'], $conta_moip, $ambiente_moip);
            foreach ($result1 as $k => $v) {
                if ($k >= $parcelamento['de2'])
                    $result[$k] = $v;
            }
        }

        if ($parcelamento['ate3'] > $parcelamento['ate2']) {
            $result1 = getRequestParcelamento($valor, $parcelamento['juros3'], $parcelamento['ate3'], $conta_moip, $ambiente_moip);
            foreach ($result1 as $k => $v) {
                if ($k >= $parcelamento['de3'])
                    $result[$k] = $v;
            }
        }

        return $result;
    }



    /* Função para gerar header de autenicação no MoIP
     *
     */
function getAuth($ambiente_moip){

if ($ambiente_moip == "https://www.moip.com.br"){

$action_url = "https://www.moip.com.br"; //producao
$action_token = "DPA3ZEEJR2MWNGK5FACBUNNARBYBOQNK"; //producao
$action_key = "9CKWTHK9JAX2B6QK0ORMZBLKZFW5PR3MEXZFKG9H"; //producao

}else{

$action_url = "https://desenvolvedor.moip.com.br/sandbox"; //sandbox
$action_token = "PE8ZECRX4ZPV3OF7HRG136HEPDOOTNUB";  //sandbox
$action_key = "KBJW7FOG1M5WV1J7KCFURVO4TMTVTTBSBM3ZNJW4"; //sandbox

} 

if ($token == ""){
$token = $action_token;
} else {
$token = $_POST["TOKEN"];
}

if ($key == ""){
$key = $action_key;
} else {
$key = $_POST["KEY"];
}

$base = $token . ":" . $key;

return $base;

}

/* função consulta CEP e recupera endereço correto
 * WebService free (RepublicaVirtual.com.br)
 *
 */
function getConsultaCep($cep){

$cep = ereg_replace("[^0-9]", "", $cep);
$ch = curl_init();
        $timeout = 0;
        curl_setopt($ch, CURLOPT_URL, "http://republicavirtual.com.br/web_cep.php?cep=$cep");
        curl_setopt($ch, CURLOPT_USERPWD, $user . ":" . $passwd);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $conteudo = curl_exec($ch);
        $erro = curl_error($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        // API Checa Pagamento Direto //

        if($info['http_code'] == "200"){
           $res = simplexml_load_string($conteudo);
        if($res->uf != false){
        $endereco =  array('tipo_lagradouro' => utf8_encode($res->tipo_logradouro),
                           'logradouro' => utf8_encode($res->logradouro),
                           'bairro' => utf8_encode($res->bairro),
                           'cidade' => utf8_encode($res->cidade),
                           'uf' => $res->uf,
                           );

          return $endereco;
        }else{
            return 'falha';
        }
        }else{
            return 'falha';
        }
}

/* caso consulta de CEP não esteja hativo sistema tenta identificar UF com base no estado digitado.
 *
 */
function getUf($setUf){

  $setUf = strtolower($setUf['cidade']);
  $uf_estado = ereg_replace("[^a-zA-Z0-9]", "", strtr($setUf, "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ ", "aaaaeeiooouucaaaaeeiooouuc"));

  if ($uf_estado == "acre")
      $UF = "AC";
  if ($uf_estado == "alagoas")
      $UF = "AL";
  if ($uf_estado == "amazonas")
      $UF = "AM";
  if ($uf_estado == "amapa")
      $UF = "AP";
  if ($uf_estado == "bahia")
      $UF = "BA";
  if ($uf_estado == "ceara")
      $UF = "CE";
  if ($uf_estado == "distritofederal")
      $UF = "DF";
  if ($uf_estado == "espiritosanto" || $uf_estado == "esparitosanto")
      $UF = "ES";
  if ($uf_estado == "goias")
      $UF = "GO";
  if ($uf_estado == "maranhao" || $uf_estado == "maranheo")
      $UF = "MA";
  if ($uf_estado == "minasgerais")
      $UF = "MG";
  if ($uf_estado == "matogrossodosul")
      $UF = "MS";
  if ($uf_estado == "matogrosso")
      $UF = "MT";
  if ($uf_estado == "para")
      $UF = "PA";
  if ($uf_estado == "paraiba" || $uf_estado == "paraaba")
      $UF = "PB";
  if ($uf_estado == "pernambuco")
      $UF = "PE";
  if ($uf_estado == "piaui"  || $uf_estado == "piaua")
      $UF = "PI";
  if ($uf_estado == "parana")
      $UF = "PR";
  if ($uf_estado == "riodejaneiro")
      $UF = "RJ";
  if ($uf_estado == "riograndedonorte")
      $UF = "RN";
  if ($uf_estado == "rondonia" || $uf_estado == "rondenia")
      $UF = "RO";
  if ($uf_estado == "roraima")
      $UF = "RR";
  if ($uf_estado == "riograndedosul")
      $UF = "RS";
  if ($uf_estado == "santacatarina")
      $UF = "SC";
  if ($uf_estado == "sergipe")
      $UF = "SE";
  if ($uf_estado == "saopaulo" || $uf_estado == "seopaulo")
      $UF = "SP";
  if ($uf_estado == "tocantins")
      $UF = "TO";
  if ($UF == "")
      $UF = "SP";

  return $UF;
}

/* ferramenta para recuperar numero do endereço após uma virgula "," digitado no endereço/rua
 * 
 */
function getNunberAddress($lagradouro){

        $numero = explode(',',$lagradouro);
        $numero = ereg_replace("[^0-9]", "", $numero[1]);

        if($numero != ""){
    return $numero;
        }else{
    return "?";
        }

}

/* função para gerar debug.txt
 * 
 */
	if (!function_exists('log_var')) {
		  function log_var($var, $name='', $to_file=false, $name_file='temp'){
		    if ($to_file==true) {
		        $txt = @fopen($name_file.'_debug.txt','a');
		        if ($txt){
    		        fwrite($txt, "-----------------------------------\n");
    		        fwrite($txt, $name."\n");
    		        fwrite($txt,  print_r($var, true)."\n");
    		        fclose($txt);//
                }
		    } else {
		         echo '<pre><b>'.$name.'</b><br>'.
		              print_r($var,true).'</pre>';
		    }
		  }
	}

?>