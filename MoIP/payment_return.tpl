  <link rel="stylesheet" href="code/sexylightbox.css" type="text/css" media="all" />
  <link rel="stylesheet" href="code/default.css" type="text/css" media="all" />

  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/mootools/1.2.3/mootools-yui-compressed.js"></script>
  <script type="text/javascript" src="code/sexylightbox.v2.3.mootools.min.js"></script>

  <script type="text/javascript" src="code/lightbox.js"></script>



{if $status == 'ok'}

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
  <tr>
    <td width="15%" align="left" valign="top"><img src="img/moip.jpg" width="109" height="64" alt="MoIP" /></td>
    <td width="4%" align="center" valign="middle"><img src="img/divisa.jpg" width="14" height="46" /></td>
    <td width="81%" align="right" valign="middle"><p class="p1"><strong>Esse pagamento será processado pelo MoIP Pagamentos</strong></p></td>
  </tr>
  <tr>
    <td colspan="3" align="left" valign="top"><hr class="hr1"/> </td>
  </tr>
  <tr>
    <td colspan="3" align="left" valign="top">&nbsp;</td>
  </tr>

{if $status_pd != ''}


{if $status_pd == 'falha'}

  <tr>
    <td colspan="3" align="left" valign="top"><p class="p1"><strong>Transação não processada.</strong></p></td>
  </tr>
  <tr>
    <td height="50" colspan="3" align="left" valign="middle">
  <p class="warning">
	{l s='Sua transação não pode ser processada, por gentileza verifique os dados digitados e efetue uma nova tentativa de pagamento.' mod='MoIP'}
</p>
    </td>
  </tr>




{if $msg != ''}
  <tr>
    <td height="30" colspan="3" align="left" valign="middle" class="p1">Atenção: {$msg}</td>
  </tr>
{/if}

{if $erro == '1'}
     <tr>
    <td height="50" colspan="3" align="left" valign="middle">
	<p class="warning">
	{l s='Não foi possível ler o XML de resposta enviado pelo MoIP, ou não houve um XML valido.' mod='MoIP'}
	<a href="{$base_dir}contact-form.php">{l s='Formulário de contato' mod='MoIP'}</a>.
	</p>
    </td>
  </tr>

  <tr>
    <td height="30" colspan="3" align="left" valign="middle" class="p1">Atenção: Erro inesperado, transação não pode ser processada.</td>
  </tr>
{/if}

{if $erro == '2'}

     <tr>
    <td height="50" colspan="3" align="left" valign="middle">
	<p class="warning">
	{l s='XML de instrução ou arquivo de LOG não pode ser gerado.' mod='MoIP'}
	<a href="{$base_dir}contact-form.php">{l s='Formulário de contato' mod='MoIP'}</a>.
	</p>
    </td>
  </tr>

  <tr>
    <td height="30" colspan="3" align="left" valign="middle" class="p1">Atenção: Erro interno.</td>
  </tr>

{/if}
{elseif $status_pd == 'sucesso'}

  <tr>
    <td colspan="3" align="left" valign="top"><p class="p1"><strong>Transação processada com sucesso!</strong></p></td>
  </tr>
  <tr>
    <td height="50" colspan="3" align="left" valign="middle" class="option">
    <p class="p1">Sua transação está sendo processada pelo MoIP Pagamentos</p>
    <p class="p1">Por gentileza aguarde a identificação de seu pagamento</p>
        <br />
    <p class="p1"><a href="{$ambiente_moip}/SearchTransaction.do?method=search&emailused={$email_comprador}&TB_iframe=true&height=500&width=800&modal=1" rel="sexylightbox" >Localize a sua transação no MoIP</a></p>

      </td>
  </tr>

{elseif $status_pd == 'Cancelado'}

  <tr>
    <td colspan="3" align="left" valign="top"><p class="p1"><strong>Transação Cancelada!</strong></p></td>
  </tr>
  <tr>
    <td height="50" colspan="3" align="left" valign="middle" class="option">
    <p class="p1">Sua transação foi cancelada pelo banco emissor do cartão.</p>
    <p class="p1">Você poderá realizar uma nova tentativa de pagamento utilizando outro cartão ou forma de pagamento</p>
    <p class="p1"><i>Status: </i>{$status_pd}</p>
    <p class="p1"><i>Código MoIP: </i>{$CodigoMoIP}</p>
      </td>
  </tr>

  <tr>
    <td height="30" colspan="3" align="left" valign="middle" class="p1"><a href="{$base_dir}/order.php?step=3">Pagar com outra forma de pagamento</a></td>
  </tr>

{else}

  <tr>
    <td colspan="3" align="left" valign="top"><p class="p1"><strong>Transação processada com sucesso!</strong></p></td>
  </tr>
  <tr>
    <td height="50" colspan="3" align="left" valign="middle" class="option">
    <p class="p1">Sua transação foi processada pelo MoIP Pagamentos</p>
    <p class="p1"><i>Status: </i>{$status_pd}</p>
    <p class="p1"><i>Código MoIP: </i>{$CodigoMoIP}</p>
        <br />
    <p class="p1"><a href="{$ambiente_moip}/SearchTransaction.do?method=search&emailused={$email_comprador}&fullvalue={$totalApagarCredito}&TB_iframe=true&height=500&width=800&modal=1" rel="sexylightbox" >Localize a sua transação no MoIP</a></p>

      </td>
  </tr>

{/if}

{elseif $token != ''}
{if $tipo == 'boleto'}

  <tr>
    <td colspan="3" align="left" valign="top"><p class="p1"><strong>Transação processada com sucesso!</strong></p></td>
  </tr>
  <tr>
    <td height="50" colspan="3" align="left" valign="middle" class="option"><p class="p1"><a href="{$ambiente_moip}/Instrucao.do?token={$token}&TB_iframe=true&height=500&width=680&modal=1" rel="sexylightbox" >Clique aqui </a>para imprimir o boleto gerado.</p>
      <p class="p1"><a href="{$ambiente_moip}/SearchTransaction.do?method=search&emailused={$email_comprador}&fullvalue={$totalApagarBoleto}&TB_iframe=true&height=500&width=800&modal=1" rel="sexylightbox">Localize a sua transação no MoIP.</a></p></td>
  </tr>


{elseif $tipo == 'debito'}

  <tr>
    <td colspan="3" align="left" valign="top"><p class="p1"><strong>Transação processada com sucesso!</strong></p></td>
  </tr>

  <tr>
    <td height="50" colspan="3" align="left" valign="middle" class="option">
    <p class="p1">Sua transação foi processada pelo MoIP Pagamentos</p>
    <p class="p1">Clique no botão abaixo para acessar seu InternetBank e finalizar o pagamento</p>
    <p class="p1"><a href="{$ambiente_moip}/Instrucao.do?token={$token}&TB_iframe=true&height=500&width=680&modal=1" rel="sexylightbox" ><img src="http://www.moip.com.br/imgs/buttons/bt_pagar_c01_e04.png" alt="Pagar" /></a></p>
        <br />
    <p class="p1"><a href="{$ambiente_moip}/SearchTransaction.do?method=search&emailused={$email_comprador}&fullvalue={$totalApagar}&TB_iframe=true&height=500&width=800&modal=1" rel="sexylightbox" >Localize a sua transação no MoIP</a></p>

      </td>
  </tr>

{/if}
{else}

  <tr>
    <td colspan="3" align="left" valign="top"><p class="p1"><strong>Transação não finalizada até o presente momento.</strong></p></td>
  </tr>

       <tr>
    <td height="50" colspan="3" align="left" valign="middle">
	<p class="warning">
	{l s='Transação não finalizada até o presente momento.' mod='MoIP'}<br>
	{l s='Acesse seu carrinho de compras para escolher a forma de pagamento desejada ou entre em conato através do ' mod='MoIP'}
	<a href="{$base_dir}contact-form.php">{l s='Formulário de contato' mod='MoIP'}</a>.
	</p>
    </td>
  </tr>


{/if}

{else}

  <tr>
    <td colspan="3" align="left" valign="top"><p class="p1"><strong>Transação não finalizada até o presente momento.</strong></p></td>
  </tr>

       <tr>
    <td height="50" colspan="3" align="left" valign="middle">
	<p class="warning">
	{l s='Houve alguma falha no envio do seu pedido. Por Favor entre em contato com o nosso Suporte' mod='MoIP'}
	<a href="{$base_dir}contact-form.php">{l s='Formulário de contato' mod='MoIP'}</a>.
	</p>
    </td>
  </tr>

{/if}

  <tr>
    <td colspan="3" align="right" valign="top">&nbsp;</td>
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
</table>