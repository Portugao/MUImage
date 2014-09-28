{* purpose of this template: inclusion template for display of related Albums in user area *}
{if isset($items) && $items ne null}
{foreach name='relLoop' item='item' from=$items}
    <a title="{$item.description}" href="{modurl modname='MUImage' type='user' func='display' ot='album' id=$item.id}">
        {$item.title} 
    </a>
{/foreach}
{/if}

