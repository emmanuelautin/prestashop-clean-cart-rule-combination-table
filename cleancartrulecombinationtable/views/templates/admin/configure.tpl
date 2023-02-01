<div class="panel">
    <h3>{l s='Clean Cart Rule Combination Table' mod='cleancartrulecombinationtable'}</h3>
    <p>{l s='This module will delete unused data from ps_cart_rule_combination table.' mod='cleancartrulecombinationtable'}</p>
    <form action="{$form_url}" method="post">
        <button type="submit" name="clean_table" class="btn btn-default">
            <i class="icon-eraser"></i> {l s='Supprimer les combinaisons dont les regles paniers sont desactives ' mod='cleancartrulecombinationtable'}
        </button>
        <button type="submit" name="clean_table_more" class="btn btn-default">
            <i class="icon-eraser"></i> {l s='Supprimer les combinaisons dont les regles paniers sont supprimés' mod='cleancartrulecombinationtable'}
        </button>
    </form>
    {foreach from=$confirmations item=confirmation}
        <div class="alert alert-success">
            {$confirmation}
        </div>
    {/foreach}
    {foreach from=$errors item=error}
        <div class="alert alert-error">
            {$error}
        </div>
    {/foreach}
</div>