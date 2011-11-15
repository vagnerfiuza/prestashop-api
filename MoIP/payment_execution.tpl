{capture name=path}{l s='Shipping'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<h2>{l s='MoIP Pagamentos' mod='MoIP'}</h2>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sem título</title>

<link href="{$base_dir_ssl}/modules/MoIP/code/default.css" type="text/css" rel="stylesheet" />
<script src="{$base_dir_ssl}/modules/MoIP/code/jquery.min.js" type="text/javascript"></script>
<script src="{$base_dir_ssl}/modules/MoIP/code/jquery.validate.js" type="text/javascript"></script>
<script src="{$base_dir_ssl}/modules/MoIP/code/jquery.maskedinput-1.1.4.pack.js" type="text/javascript"></script>

{literal}
<script type="text/javascript">
    $(document).ready(function() {
        $("#credito_portador_telefone").mask("(99)9999-9999");
        $("#credito_portador_cpf").mask("999.999.999-99");
        $("#credito_portador_nascimento").mask("99/99/9999");
    });

    $(document).ready(function() {
            if($("#moip_credito_forma").is(':checked')){
            $("#moip_credito").show();
            }
            if($("#moip_debito_forma").is(':checked')){
            $("#moip_debito").show();
            }
        $('#submit').click(function() {
            var valor = "";
            //Executa Loop entre todas as Radio buttons com o name de valor
            $('input:radio[name=forma_pagamento]').each(function() {
                //Verifica qual está selecionado
                if ($(this).is(':checked'))
                    valor = $(this).val();
            })
//            alert(valor);
            if(valor == 'CartaoCredito'){

                    $("#formulario").validate({
                        rules : {
                            credito_portador_telefone : {
                                required : true
                            },
                            credito_portador_nome: {
                                required : true
                            },
                            credito_portador_nascimento: {
                                required : true
                            },
                            credito_portador_cpf: {
                                required : true
                            }
                        },
                        messages : {
                            credito_numero: "*",
                            credito_codigo_seguranca: "*",
                            credito_expiracao_mes: "*",
							credito_expiracao_ano: "*",
                            credito_portador_telefone : "<i>Ex. (11)1111-1111</i>",
                            credito_portador_nome : "*",
                            credito_portador_nascimento : "<i>Ex. 30/11/1980</i>",
                            credito_portador_cpf : "<i>Ex. 111.111.111-11</i>"
                        }

                });
            }else{
                $("#formulario").unbind("submit").submit();
            }
        });
        var i = 0;
        $("#formulario").submit(function() {
            $("#submit").attr('disabled', true);
            
            i++;
            if ( i > 1 ) { alert(5);return false; }
            });
    });

</script>
{/literal}
</head>

<body>
<form method="post" name="formulario" id="formulario" action="{$this_path_ssl}validation.php">
<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
  <tr>
    <td width="10%" align="left" valign="top"><img src="img/moip.jpg" width="109" height="64" alt="MoIP" /></td>
    <td width="3%" align="left" valign="middle"><img src="img/divisa.jpg" width="14" height="46" /></td>
    <td width="87%" align="right" valign="middle"><p class="p1"><strong>Esse pagamento será processado pelo MoIP Pagamentos</strong></p></td>
  </tr>
  <tr>
    <td colspan="3" align="left" valign="top"><hr class="hr1"/> </td>
  </tr>

    <tr>
    <td colspan="3" height="57" align="center" valign="middle" class="option"><label class="p1"> <strong>MoIP Pagamentos<br>
      <br>
      Boleto, Débito em conta, Cartão de Credito, Financiamento e Saldo MoIP </strong> </label></td>
  </tr>

  <tr>
    <td colspan="3" align="left" valign="top">&nbsp; </td>
  </tr>

  <tr>
    <td height="199" colspan="3" align="left" valign="top">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">

  {if $resp_pd_boleto eq 'true'}
      <tr>
        <td width="32%" colspan="3" align="center" valign="middle" class="option" id="moip_boleto_forma"><input onClick="document.getElementById('moip_debito').style.display='none';document.getElementById('moip_credito').style.display='none'" type="radio" id="moip_boleto_forma" name="forma_pagamento" title="forma_pagamento" value="BoletoBancario"/>
          <label for="radio5" class="p1"><strong>Boleto Bancário</strong></label></td>
      </tr>

      <tr>
        <td colspan="3" align="center" valign="middle">&nbsp; </td>
      </tr>
{/if}
{if $resp_pd_debito eq 'true'}
  <tr>
    <td colspan="3" align="center" valign="middle" class="option"><input onClick="document.getElementById('moip_debito').style.display='block';document.getElementById('moip_credito').style.display='none'" type="radio" id="moip_debito_forma" name="forma_pagamento" title="forma_pagamento" value="DebitoBancario"/>
      <label for="radio6" class="p1"><strong>Débito Online</strong></label>
      </td>
   </tr>

  <tr>
    <td colspan="3" align="center" valign="middle"><div id="moip_debito" class="sub-table" style="display:none">
      <table width="100%" border="0" id="option2">
        <tr>
          <td width="100%"><div id="option2">
            <input type="radio" id="debito_instituicao" name="debito_instituicao" value="BancoDoBrasil"/>
            <strong class="p1"><img  src="https://www.moip.com.br/moiplabs/bb_16x16.png" alt="Banco do Brasil"> Banco do Brasil</strong></div>
            <div id="option2">
              <input type="radio" id="debito_instituicao" name="debito_instituicao" value="Bradesco"/>
              <strong class="p1"><img src="https://www.moip.com.br/moiplabs/bradesco_16x16.png"  alt="Bradesco"> Bradesco</strong></div>
            <div id="option2">
              <input type="radio" id="debito_instituicao" name="debito_instituicao" value="Itau"/>
              <strong class="p1"><img src="https://www.moip.com.br/moiplabs/itau_16x16.png" alt="Itaú" > Itaú</strong></div>
            <div id="option2">
              <input type="radio" id="debito_instituicao" name="debito_instituicao" value="BancoReal"/>
              <strong class="p1"><img src="https://www.moip.com.br/moiplabs/banco_real_16x16.png"  alt="Banco Real"> Banco Real</strong></div>
            <div id="option2">
              <input type="radio" id="debito_instituicao" name="debito_instituicao" value="Banrisul"/>
              <strong class="p1"><img src="https://www.moip.com.br/moiplabs/banrisul_16x16.png"  alt="Banrisul"> Banrisul</strong></div></td>
        </tr>
      </table>
    </div>
      &nbsp;
      </td>
  </tr>
 {/if}
 {if $resp_pd_credito eq 'true'}
  <tr>
    <td colspan="3" align="center" valign="middle" class="option">
    <input onClick="document.getElementById('moip_credito').style.display='block';document.getElementById('moip_debito').style.display='none'" type="radio" id="moip_credito_forma" name="forma_pagamento" title="forma_pagamento" value="CartaoCredito"/>
      <label for="radio7" class="p1"><strong>Cartão de Crédito</strong></label></td>
  </tr>

  <tr>
    <td colspan="3" align="center" valign="middle"><div id="moip_credito" class="sub-table" style="display:none">
      <table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" id="option2">
        <tr>
          <td width="38%" height="30" align="left" valign="middle" class="p1"><strong>Cartão de crédito:</strong></td>
          <td width="62%" height="30" colspan="2" align="left" valign="middle"><span class="p2">
            <label for="select3"></label>
            <select name="credito_instituicao" id="credito_instituicao">
              <option value="Visa">Visa</option>
              <option value="Mastercard">Mastercard</option>
              <option value="AmericanExpress">American Express</option>
              <option value="Diners">Diners</option>
              <option value="Hipercard">Hipercard</option>
            </select>
          </span></td>
        </tr>
        <tr>
          <td height="30" align="left" valign="middle"><span class="p1"><strong>Número:</strong></span></td>
          <td height="30" colspan="2" align="left" valign="middle"><label for="textfield2"></label>
            <span class="p2">
              <input type="text" name="credito_numero" id="credito_numero" class="required" style="width: 160px" maxlength="19">
            </span></td>
        </tr>
        <tr>
          <td height="30" align="left" valign="middle"><span class="p1"><strong>Expiração:</strong></span></td>
          <td height="30" colspan="2" align="left" valign="middle"><input type="text" name="credito_expiracao_mes" id="credito_expiracao_mes"  class="required"style="width: 30px;"  maxlength="2">
            /
            <input type="text" name="credito_expiracao_ano" id="credito_expiracao_ano" style="width: 30px;" class="required" maxlength="2"></td>
        </tr>
        <tr>
          <td height="30" align="left" valign="middle"><span class="p1"><strong>Código de Segurança:</strong></span></td>
          <td height="30" colspan="2" align="left" valign="middle"><input type="text" name="credito_codigo_seguranca" id="credito_codigo_seguranca" style="width: 40px;" class="required" maxlength="4"></td>
        </tr>
        <tr>
          <td height="30" align="left" valign="middle"><span class="p1"><strong>Opções de pagamento:</strong></span></td>
          <td height="30" colspan="2" align="left" valign="middle">
{if $aceitar_parcelamento eq '1'}
            <select name="credito_parcelamento" id="credito_parcelamento">
{foreach from=$parcelamento key=k item=v}
              <option value="({$k})[{$v.total}]">{$k} x R$ {$v.valor} | Total: R$ {$v.total}</option>
{/foreach}
            </select>
{else}
            <select name="credito_parcelamento" id="credito_parcelamento">
              <option value="{1}[{$total_format}]">1 x R$ {$total_format} </option>
            </select>
{/if}
	 </td>
        </tr>
        <tr>
          <td height="30" colspan="3" align="left" valign="middle"><hr  class="hr2"/></td>
        </tr>
        <tr>
          <td height="30" align="left" valign="middle" class="p1"><strong>Nome do cartão:</strong></td>
          <td height="30" colspan="2" align="left" valign="middle"><span class="p2">
            <input type="text" name="credito_portador_nome" id="credito_portador_nome" style="width: 250px" class="required">
          </span></td>
        </tr>
        <tr>
          <td height="30" align="left" valign="middle" class="p1"><strong>CPF:</strong></td>
          <td height="30" colspan="2" align="left" valign="middle"><input type="text" name="credito_portador_cpf" id="credito_portador_cpf"  style="width: 110px" class="required input" maxlength="14"></td>
        </tr>
        <tr>
          <td height="30" align="left" valign="middle" class="p1"><strong>Telefone:</strong></td>
          <td height="30" colspan="2" align="left" valign="middle"><input type="text" name="credito_portador_telefone" id="credito_portador_telefone"  style="width: 110px" class="required input" maxlength="13"></td>
        </tr>
        <tr>
          <td height="30" align="left" valign="middle" class="p1"><strong>Data de Nascimento:</strong></td>
          <td height="30" colspan="2" align="left" valign="middle"><input type="text" name="credito_portador_nascimento" id="credito_portador_nascimento"  style="width: 110px;" class="required input" maxlength="10"></td>
        </tr>
        <tr>
          <td height="30" align="left" valign="middle">&nbsp;</td>
          <td height="30" colspan="2" align="left" valign="middle"></td>
        </tr>
      </table>
    </div>
      &nbsp;
      </td>
  </tr>
  {/if}
    </table>&nbsp;
   </td>
  </tr>
    <tr>
    <td align="left" valign="middle">
    <a href="{$base_dir_ssl}order.php?step=3"><img src="img/outras-formas-pgto.jpg" width="168" height="27" /></a>
    </td>
    <td align="left" valign="top">&nbsp;</td>
      <td align="right" valign="middle">
    <input id="submit" type="image" name="submit" src="img/confirmar-compra.jpg" width="168" height="31" alt="confirmar compra" />
    </td>
  </tr>
  <tr>
    <td colspan="3" align="left" valign="top">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" align="right" valign="top" class="p1"><strong>www.moip.com.br
    </strong></td>
  </tr>
  <tr>
    <td colspan="3" align="left" valign="top"><hr class="hr1"/></td>
  </tr>
  <tr>
    <td align="left" valign="top">&nbsp;</td>
    <td align="left" valign="top"></td>
    <td align="right" valign="middle">
  </tr>
        <input type="hidden" name="instrucao" value="API" />
        <input type="hidden" name="descricao" value="{$descricao}" />
        <input type="hidden" name="id_Address" value="{$id_Address}" />
        <input type="hidden" name="id_cart_payment" value="{$id_cart_payment}" />
        <input type="hidden" name="id_cliente" value="{$id_cliente}" />
</table>
</form>
</body>
