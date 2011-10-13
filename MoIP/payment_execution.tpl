{capture name=path}{l s='Shipping'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<h2>{l s='MoIP Pagamentos' mod='MoIP'}</h2>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

<link href="{$base_dir_ssl}/modules/MoIP/code/payment.css" rel="stylesheet" type="text/css" media="all" />


<h3>{l s='Pagamento via MoIP' mod='MoIP'}</h3>
<form action="{$this_path_ssl}validation.php" method="post">
            <div>
                <img src="https://www.moip.com.br/moiplabs/logo_moip.png">
                <br><br>
                <input type="hidden" name="total_format" value="{$total_format}" />
{if $resp_pd_boleto eq 'true'}
                       			<label> <input onclick="document.getElementById('moip_debito').style.display='none';document.getElementById('moip_credito').style.display='none'" type="radio" id="forma_pagamento" name="forma_pagamento" title="forma_pagamento" value="BoletoBancario"/> Boleto Bancário</label>
                                         <br><br>
{/if}
{if $resp_pd_debito eq 'true'}
                                <label><input onclick="document.getElementById('moip_debito').style.display='block';document.getElementById('moip_credito').style.display='none'" type="radio" id="forma_pagamento" name="forma_pagamento" title="forma_pagamento" value="DebitoBancario"/> Débito Online</label>
                    <div id="moip_debito" style="display:none">
                        <ul style="margin-left: 40px;" >
                            <li style="display:block; vertical-align: center;"><label><input type="radio" id="debito_instituicao" name="debito_instituicao" value="BancoDoBrasil"/> <img  src="https://www.moip.com.br/moiplabs/bb_16x16.png" alt="Banco do Brasil"> Banco do Brasil</label></li>
                            <li><label><input type="radio" id="debito_instituicao" name="debito_instituicao" value="Bradesco"/> <img src="https://www.moip.com.br/moiplabs/bradesco_16x16.png"  alt="Bradesco"> Bradesco</label></li>
                            <li><label><input type="radio" id="debito_instituicao" name="debito_instituicao" value="Itau"/> <img src="https://www.moip.com.br/moiplabs/itau_16x16.png" alt="Itaú" > Itaú</label></li>
                            <li><label><input type="radio" id="debito_instituicao" name="debito_instituicao" value="BancoReal"/> <img src="https://www.moip.com.br/moiplabs/banco_real_16x16.png"  alt="Banco Real"> Banco Real</label></li>
                            <li><label><input type="radio" id="debito_instituicao" name="debito_instituicao" value="Banrisul"/> <img src="https://www.moip.com.br/moiplabs/banrisul_16x16.png"  alt="Banrisul"> Banrisul</label></li>
                        </ul>
                    </div><br><br>
 {/if}

 {if $resp_pd_credito eq 'true'}
                        <label><input onclick="document.getElementById('moip_credito').style.display='block';document.getElementById('moip_debito').style.display='none'" type="radio" id="forma_pagamento" name="forma_pagamento" title="forma_pagamento" value="CartaoCredito"/> Cartão de crédito</label>
                        <div id="moip_credito" style="display:none">
                            <ul style="margin-left: 40px;">
                                <li style="margin-bottom: 5px;"><label>Cartão: &nbsp;
                                        <select name="credito_instituicao" id="credito_instituicao">
                                            <option value="Visa">Visa</option>
                                            <option value="Mastercard">Mastercard</option>
                                            <option value="AmericanExpress">American Express</option>
                                            <option value="Diners">Diners</option>
                                            <option value="Hipercard">Hipercard</option>
                                        </select></label>
                                </li>
                                <li style="margin-bottom: 5px;">Número:
                                        <input type="text" name="credito_numero" id="credito_numero" class="input-text">
                                        &nbsp;&nbsp;&nbsp;Expiração: 
                                        <input type="text" name="credito_expiracao_mes" id="credito_expiracao_mes" class="input-text" style="width: 20px" maxlength="2">&nbsp;<b>/</b>
                                        <input type="text" name="credito_expiracao_ano" id="credito_expiracao_ano" style="width: 20px" class="input-text" maxlength="2">
                                </li>
                                	<li style="margin-bottom: 5px;"><label>Código de segurança: <input type="text" name="credito_codigo_seguranca" id="credito_codigo_seguranca" style="width: 40px" class="input-text" maxlength="4"></label></li>

 {if $aceitar_parcelamento eq '1'}
			<li>
                                <label>Opções de pagamento:
                                    <select name="credito_parcelamento" id="credito_parcelamento">
                                {foreach from=$parcelamento key=k item=v}
                                        <option value="({$k})[{$v.total}]">{$k} x R$ {$v.valor} | Total: R$ {$v.total}</option>
                                {/foreach}
                                    </select>
                            </label>
                        </li>
 {else}
			<li>
                                <label>Opções de pagamento:
                                    <select name="credito_parcelamento" id="credito_parcelamento">
                                          <option value="{1}[{$total_format}]">1 x R$ {$total_format} </option>
                                    </select>
                            </label>
                        </li>

{/if}
                        <br><hr width="100%" align="center" color="#728CC3"/><br>
                                    <span>
                                        <table border="0" cellpadding="0" cellspacing="0">
                                            <tr style="height: 25px;"><br />
                                                <td width="110">Nome no cartão:</td>
                                                <td><input type="text" name="credito_portador_nome" id="credito_portador_nome" style="width: 250px" class="input-text"></td>
                                            </tr>
                                            <tr style="height: 25px;">
                                                <td>CPF:</td>
                                                <td><input type="text" name="credito_portador_cpf" id="credito_portador_cpf"  style="width: 110px" class="input-text" maxlength="14"> <span style="font-size: 10px;"><i>Ex. 111.111.111-11</i></span></td>
                                            </tr>
                                            <tr style="height: 25px;">
                                                <td>Telefone:</td>
                                                <td><input type="text" name="credito_portador_telefone" id="credito_portador_telefone"  style="width: 110px" class="input-text" maxlength="13"> <span style="font-size: 10px;"><i>Ex. (11)1111-1111</i></span></td>
                                            </tr>
                                            <tr style="height: 25px;">
                                                <td>Data nascimento:</td>
                                                <td><input type="text" name="credito_portador_nascimento" id="credito_portador_nascimento"  style="width: 90px;" class="input-text" maxlength="10"> <span style="font-size: 10px;"><i>Ex. 30/11/1980</i></span></td>
                                            </tr>
                                        </table>
                                    </span>
                                </ul>
                            </div>
                           <br><br>
{/if}

                       <a href="http://www.moip.com.br" target="_blank"><img src="https://www.moip.com.br/moiplabs/url.png" border="0"></a>
                     </div>

</p>




</p>
<p class="cart_navigation">
	<a href="{$base_dir_ssl}order.php?step=3" class="button_large">{l s='Outras formas de pagamento' mod='MoIP'}</a>
	<input type="submit" name="submit" value="{l s='Confirmar Compra' mod='MoIP'}" class="exclusive_large" />
</p>
        <input type="hidden" name="instrucao" value="API" />
        <input type="hidden" name="descricao" value="{$descricao}" />
        <input type="hidden" name="id_Address" value="{$id_Address}" />
        <input type="hidden" name="id_cart_payment" value="{$id_cart_payment}" />
        <input type="hidden" name="id_cliente" value="{$id_cliente}" />
</form>
