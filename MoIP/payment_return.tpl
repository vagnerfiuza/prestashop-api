  <link rel="stylesheet" href="code/sexylightbox.css" type="text/css" media="all" />

  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/mootools/1.2.3/mootools-yui-compressed.js"></script>
  <script type="text/javascript" src="code/sexylightbox.v2.3.mootools.min.js"></script>

  <script type="text/javascript" src="code/lightbox.js"></script>



{if $status == 'ok'}
	<div align="center">
	<img src="https://www.moip.com.br/imgs/logo_moip.gif" alt="{l s='Pague com MoiP' mod='MoIP'}" /></div>
	<br />

{if $status_pd != ''}


{if $status_pd == 'falha'}
	<h3>{l s='Transação não processada.' mod='MoIP'}</h3>
<p class="warning">
	{l s='Sua transação não pode ser processada, por gentileza verifique os dados digitados e efetue uma nova tentativa de pagamento.' mod='MoIP'}
</p>

{if $msg != ''}
	<p><span class="price">Atenção: </span>{$msg}</p>
{/if}

{if $erro == '1'}
	<p><span class="price">Atenção: </span>{l s='Erro inesperado, transação não pode ser processada.' mod='MoIP'}</p>

	<p class="warning">
	{l s='Não foi possível ler o XML de resposta enviado pelo MoIP, ou não houve um XML valido.' mod='MoIP'}
	<a href="{$base_dir}contact-form.php">{l s='Formulário de contato' mod='MoIP'}</a>.
	</p>
{/if}

{if $erro == '2'}
	<p><span class="price">Atenção: </span>{l s='Erro interno' mod='MoIP'}</p>

	<p class="warning">
	{l s='XML de instrução ou arquivo de LOG não pode ser gerado.' mod='MoIP'}
	<a href="{$base_dir}contact-form.php">{l s='Formulário de contato' mod='MoIP'}</a>.
	</p>

{/if}
{elseif $status_pd == 'sucesso'}
	<h3>{l s='Transação processado com sucesso!' mod='MoIP'}</h3>
	<p>{l s='Sua transação está sendo processada pelo MoIP Pagamentos' mod='MoIP'}</p>
	<p>{l s='Por gentileza aguarde a identificação de seu pagamento' mod='MoIP'}</p>
        <br />
        <p><a href="{$ambiente_moip}/SearchTransaction.do?method=search&emailused={$email_comprador}&TB_iframe=true&height=500&width=800&modal=1" rel="sexylightbox" >Localize a sua transação no MoIP</a></p>

{else}
	<h3>{l s='Transação processado com sucesso!' mod='MoIP'}</h3>
	<p>{l s='Sua transação foi processada pelo MoIP Pagamentos' mod='MoIP'}</p>
	<p><b>{l s='Status: ' mod='MoIP'}</b> {$status_pd}</p>
	<p><b>{l s='Código MoIP: ' mod='MoIP'}</b> {$CodigoMoIP}</p>
        <br />
        <p><a href="{$ambiente_moip}/SearchTransaction.do?method=search&emailused={$email_comprador}&fullvalue={$totalApagarCredito}&TB_iframe=true&height=500&width=800&modal=1" rel="sexylightbox" >Localize a sua transação no MoIP</a></p>
	<br />
{/if}


{elseif $token != ''}
{if $tipo == 'boleto'}
	<h3>{l s='Transação processado com sucesso!' mod='MoIP'}</h3>
	<p>{l s='Clique no botão abaixo para imprimir o boleto gerado' mod='MoIP'}</p>
        <p><a href="{$ambiente_moip}/Instrucao.do?token={$token}&TB_iframe=true&height=500&width=680&modal=1" rel="sexylightbox" ><img src="{$base_dir}modules/MoIP/code/images/boleto.gif" alt="Imprimir Boleto" />Imprimir</a></p>
        <br />
       <p><a href="{$ambiente_moip}/SearchTransaction.do?method=search&emailused={$email_comprador}&fullvalue={$totalApagarBoleto}&TB_iframe=true&height=500&width=800&modal=1" rel="sexylightbox" >Localize a sua transação no MoIP</a></p>

{elseif $tipo == 'debito'}
	<h3>{l s='Transação processado com sucesso!' mod='MoIP'}</h3>
	<p>{l s='Clique no botão abaixo para acessar seu InternetBank e finalizar o pagamento' mod='MoIP'}</p>
        <p><a href="{$ambiente_moip}/Instrucao.do?token={$token}&TB_iframe=true&height=500&width=680&modal=1" rel="sexylightbox" ><img src="http://www.moip.com.br/imgs/buttons/bt_pagar_c01_e04.png" alt="Pagar" /></a></p>
        <br />
        <p><a href="{$ambiente_moip}/SearchTransaction.do?method=search&emailused={$email_comprador}&fullvalue={$totalApagar}&TB_iframe=true&height=500&width=800&modal=1" rel="sexylightbox" >Localize a sua transação no MoIP</a></p>

{/if}
{else}
	<p class="warning">
	{l s='Transação não finalizada até o presente momento.' mod='MoIP'}<br>
	{l s='Acesse seu carrinho de compras para escolher a forma de pagamento desejada ou entre em conato através do ' mod='MoIP'}
	<a href="{$base_dir}contact-form.php">{l s='Formulário de contato' mod='MoIP'}</a>.
	</p>
{/if}

{else}
	<p class="warning">
	{l s='Houve alguma falha no envio do seu pedido. Por Favor entre em contato com o nosso Suporte' mod='MoIP'}
	<a href="{$base_dir}contact-form.php">{l s='Formulário de contato' mod='MoIP'}</a>.
	</p>
{/if}