<p class="payment_module">
	<a href="javascript:$('#envia_moip').submit();" title="{l s='Pague com MoIP' mod='MoIP'}">
		<img src="{$imgBtn}" alt="{l s='Pague com MoIP' mod='MoIP'}" />
	</a>
</p>

<form action="{$this_path_ssl}payment.php" method="post" id="envia_moip" class="hidden">

	<input type="hidden" name="valor" value="{$valor_total}" />
	<input type="hidden" name="nome" value="Pedido: {$id_cart} - {$shop_name}" />
 
{if ($cart_qties == "1")}
	<input type="hidden" name="descricao" value="Produto: {$produto}{$atributo}" />

{else}
	<input type="hidden" name="descricao" value="Pedido de compra contendo ({$cart_qties}) itens, produto indicativo: {$produto}" />
{/if}

	<input type="hidden" name="id_cliente" value="{$customer->id}" />	
	<input type="hidden" name="id_transacao" value="{$id_transacao_prefix} [{$id_cart}]" />
	<input type="hidden" name="params_payment" value="{$params}" />
		
</form>