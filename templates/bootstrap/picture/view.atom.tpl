{* purpose of this template: pictures atom feed *}
{assign var='lct' value='user'}
{if isset($smarty.get.lct) && $smarty.get.lct eq 'admin'}
    {assign var='lct' value='admin'}
{/if}
{muimageTemplateHeaders contentType='application/atom+xml'}<?xml version="1.0" encoding="{charset assign='charset'}{if $charset eq 'ISO-8859-15'}ISO-8859-1{else}{$charset}{/if}" ?>
<feed xmlns="http://www.w3.org/2005/Atom">
{gt text='Latest pictures' assign='channelTitle'}
{gt text='A direct feed showing the list of pictures' assign='channelDesc'}
    <title type="text">{$channelTitle}</title>
    <subtitle type="text">{$channelDesc} - {$modvars.ZConfig.slogan}</subtitle>
    <author>
        <name>{$modvars.ZConfig.sitename}</name>
    </author>
{assign var='numItems' value=$items|@count}
{if $numItems}
{capture assign='uniqueID'}tag:{$baseurl|replace:'http://':''|replace:'/':''},{$items[0].createdDate|dateformat|default:$smarty.now|dateformat:'%Y-%m-%d'}:{modurl modname='MUImage' type=$lct func='display' ot='picture'  id=$items[0].id}{/capture}
    <id>{$uniqueID}</id>
    <updated>{$items[0].updatedDate|default:$smarty.now|dateformat:'%Y-%m-%dT%H:%M:%SZ'}</updated>
{/if}
<link rel="alternate" type="text/html" hreflang="{lang}" href="{modurl modname='MUImage' type=$lct func='main' fqurl=true}" />
<link rel="self" type="application/atom+xml" href="{php}echo substr(\System::getBaseUrl(), 0, strlen(\System::getBaseUrl())-1);{/php}{getcurrenturi}" />
<rights>Copyright (c) {php}echo date('Y');{/php}, {$baseurl}</rights>

{foreach item='picture' from=$items}
    <entry>
        <title type="html">{$picture->getTitleFromDisplayPattern()|notifyfilters:'muimage.filterhook.pictures'}</title>
        <link rel="alternate" type="text/html" href="{modurl modname='MUImage' type=$lct func='display' ot='picture'  id=$picture.id fqurl=true}" />
        {capture assign='uniqueID'}tag:{$baseurl|replace:'http://':''|replace:'/':''},{$picture.createdDate|dateformat|default:$smarty.now|dateformat:'%Y-%m-%d'}:{modurl modname='MUImage' type=$lct func='display' ot='picture'  id=$picture.id}{/capture}
        <id>{$uniqueID}</id>
        {if isset($picture.updatedDate) && $picture.updatedDate ne null}
            <updated>{$picture.updatedDate|dateformat:'%Y-%m-%dT%H:%M:%SZ'}</updated>
        {/if}
        {if isset($picture.createdDate) && $picture.createdDate ne null}
            <published>{$picture.createdDate|dateformat:'%Y-%m-%dT%H:%M:%SZ'}</published>
        {/if}
        {if isset($picture.createdUserId)}
            {usergetvar name='uname' uid=$picture.createdUserId assign='cr_uname'}
            {usergetvar name='name' uid=$picture.createdUserId assign='cr_name'}
            <author>
               <name>{$cr_name|default:$cr_uname}</name>
               <uri>{usergetvar name='_UYOURHOMEPAGE' uid=$picture.createdUserId assign='homepage'}{$homepage|default:'-'}</uri>
               <email>{usergetvar name='email' uid=$picture.createdUserId}</email>
            </author>
        {/if}

        <summary type="html">
            <![CDATA[
            {$picture.description|truncate:150:"&hellip;"|default:'-'}
            ]]>
        </summary>
        <content type="html">
            <![CDATA[
            {$picture.title|replace:'<br>':'<br />'}
            ]]>
        </content>
    </entry>
{/foreach}
</feed>
