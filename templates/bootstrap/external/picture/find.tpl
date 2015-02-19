{* Purpose of this template: Display a popup selector of pictures for scribite integration *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{lang}" lang="{lang}">
<head>
    <title>{gt text='Search and select picture'}</title>
    <link type="text/css" rel="stylesheet" href="{$baseurl}style/core.css" />
    <link type="text/css" rel="stylesheet" href="{$baseurl}modules/MUImage/style/style.css" />
    <link type="text/css" rel="stylesheet" href="{$baseurl}modules/MUImage/style/finder.css" />
    {assign var='ourEntry' value=$modvars.ZConfig.entrypoint}
    <script type="text/javascript">/* <![CDATA[ */
        if (typeof(Zikula) == 'undefined') {var Zikula = {};}
        Zikula.Config = {'entrypoint': '{{$ourEntry|default:'index.php'}}', 'baseURL': '{{$baseurl}}'}; /* ]]> */</script>
        <script type="text/javascript" src="{$baseurl}javascript/ajax/proto_scriptaculous.combined.min.js"></script>
        <script type="text/javascript" src="{$baseurl}javascript/helpers/Zikula.js"></script>
        <script type="text/javascript" src="{$baseurl}javascript/livepipe/livepipe.combined.min.js"></script>
        <script type="text/javascript" src="{$baseurl}javascript/helpers/Zikula.UI.js"></script>
        <script type="text/javascript" src="{$baseurl}javascript/helpers/Zikula.ImageViewer.js"></script>
    <script type="text/javascript" src="{$baseurl}modules/MUImage/javascript/MUImage_finder.js"></script>
{if $editorName eq 'tinymce'}
    <script type="text/javascript" src="{$baseurl}modules/Scribite/includes/tinymce/tiny_mce_popup.js"></script>
{/if}
</head>
<body>
    <p>{gt text='Switch to'}:
    <a href="{modurl modname='MUImage' type='external' func='finder' objectType='album' editor=$editorName}" title="{gt text='Search and select album'}">{gt text='Albums'}</a>
    </p>
    <form action="{$ourEntry|default:'index.php'}" id="mUImageSelectorForm" method="get" class="z-form">
    <div>
        <input type="hidden" name="module" value="MUImage" />
        <input type="hidden" name="type" value="external" />
        <input type="hidden" name="func" value="finder" />
        <input type="hidden" name="objectType" id="ObjectType" value="{$objectType}" />
        <input type="hidden" name="editor" id="editorName" value="{$editorName}" />

        <fieldset>
            <legend>{gt text='Search and select picture'}</legend>

            <div class="z-formrow">
                <label for="mUImagePasteAs">{gt text='Paste as'}:</label>
                    <select id="mUImagePasteAs" name="pasteas">
                        <option value="1">{gt text='Link to the picture'}</option>
                        <option value="2">{gt text='ID of picture'}</option>
                        <option value="3">{gt text='Thumbnail of picture'}</option>
                    </select>
            </div>

            <div class="z-formrow">
                <label for="mUImageWidth">{gt text='Width'}:</label>
                    <input type="text" id="mUImageWidth" name="mUImageWidth" style="width: 150px" class="z-floatleft" style="margin-right: 10px" />
            </div>
            
           {* <div class="z-formrow">
                <label for="mUImageHeight">{gt text='Height'}:</label>
                    <input type="text" id="mUImageHeight" name="mUImageHeight" style="width: 150px" class="z-floatleft" style="margin-right: 10px" />
            </div> *}

            <div class="z-formrow">
                <label for="mUImageObjectId">{gt text='Picture'}:</label>
                    <div id="muimageItemContainer">
                        <ul>
                        {foreach item='picture' from=$items}
                            <li>
                                <a href="#" onclick="muimage.finder.selectItem({$picture.id})" onkeypress="muimage.finder.selectItem({$picture.id})">{$picture->getTitleFromDisplayPattern()}</a>
                                <input type="hidden" id="url{$picture.id}" value="{modurl modname='MUImage' type='user' func='display' ot='picture'  id=$picture.id fqurl=true}" />
                                <input type="hidden" id="title{$picture.id}" value="{$picture->getTitleFromDisplayPattern()|replace:"\"":""}" />
                                <input type="hidden" id="desc{$picture.id}" value="{capture assign='description'}{if $picture.description ne ''}{$picture.description}{/if}
                                {/capture}{$description|strip_tags|replace:"\"":""}" />
                                <input type="hidden" id="path{$picture.id}" value="{$picture.imageUploadFullPathURL}" />
                                <input type="hidden" id="width{$picture.id}" value="{$picture.imageUploadMeta.width}" />
                                <input type="hidden" id="height{$picture.id}" value="{$picture.imageUploadMeta.height}" />
                            </li>
                        {foreachelse}
                            <li>{gt text='No entries found.'}</li>
                        {/foreach}
                        </ul>
                    </div>
            </div>

            <div class="z-formrow">
                <label for="mUImageSort">{gt text='Sort by'}:</label>
                    <select id="mUImageSort" name="sort" style="width: 150px" class="z-floatleft" style="margin-right: 10px">
                    <option value="id"{if $sort eq 'id'} selected="selected"{/if}>{gt text='Id'}</option>
                    <option value="workflowState"{if $sort eq 'workflowState'} selected="selected"{/if}>{gt text='Workflow state'}</option>
                    <option value="title"{if $sort eq 'title'} selected="selected"{/if}>{gt text='Title'}</option>
                    <option value="description"{if $sort eq 'description'} selected="selected"{/if}>{gt text='Description'}</option>
                    <option value="showTitle"{if $sort eq 'showTitle'} selected="selected"{/if}>{gt text='Show title'}</option>
                    <option value="showDescription"{if $sort eq 'showDescription'} selected="selected"{/if}>{gt text='Show description'}</option>
                    <option value="imageUpload"{if $sort eq 'imageUpload'} selected="selected"{/if}>{gt text='Image upload'}</option>
                    <option value="imageView"{if $sort eq 'imageView'} selected="selected"{/if}>{gt text='Image view'}</option>
                    <option value="albumImage"{if $sort eq 'albumImage'} selected="selected"{/if}>{gt text='Album image'}</option>
                    <option value="pos"{if $sort eq 'pos'} selected="selected"{/if}>{gt text='Pos'}</option>
                    <option value="createdDate"{if $sort eq 'createdDate'} selected="selected"{/if}>{gt text='Creation date'}</option>
                    <option value="createdUserId"{if $sort eq 'createdUserId'} selected="selected"{/if}>{gt text='Creator'}</option>
                    <option value="updatedDate"{if $sort eq 'updatedDate'} selected="selected"{/if}>{gt text='Update date'}</option>
                    </select>
                    <select id="mUImageSortDir" name="sortdir" style="width: 100px">
                        <option value="asc"{if $sortdir eq 'asc'} selected="selected"{/if}>{gt text='ascending'}</option>
                        <option value="desc"{if $sortdir eq 'desc'} selected="selected"{/if}>{gt text='descending'}</option>
                    </select>
            </div>

            <div class="z-formrow">
                <label for="mUImagePageSize">{gt text='Page size'}:</label>
                    <select id="mUImagePageSize" name="num" style="width: 50px; text-align: right">
                        <option value="5"{if $pager.itemsperpage eq 5} selected="selected"{/if}>5</option>
                        <option value="10"{if $pager.itemsperpage eq 10} selected="selected"{/if}>10</option>
                        <option value="15"{if $pager.itemsperpage eq 15} selected="selected"{/if}>15</option>
                        <option value="20"{if $pager.itemsperpage eq 20} selected="selected"{/if}>20</option>
                        <option value="30"{if $pager.itemsperpage eq 30} selected="selected"{/if}>30</option>
                        <option value="50"{if $pager.itemsperpage eq 50} selected="selected"{/if}>50</option>
                        <option value="100"{if $pager.itemsperpage eq 100} selected="selected"{/if}>100</option>
                    </select>
            </div>

            <div class="z-formrow">
                <label for="mUImageSearchTerm">{gt text='Search for'}:</label>
                    <input type="text" id="mUImageSearchTerm" name="searchterm" style="width: 150px" class="z-floatleft" style="margin-right: 10px" />
                    <input type="button" id="mUImageSearchGo" name="gosearch" value="{gt text='Filter'}" style="width: 80px" />
            </div>
            
            <div style="margin-left: 6em">
                {pager display='page' rowcount=$pager.numitems limit=$pager.itemsperpage posvar='pos' template='pagercss.tpl' maxpages='10'}
            </div>
            <input type="submit" id="mUImageSubmit" name="submitButton" value="{gt text='Change selection'}" />
            <input type="button" id="mUImageCancel" name="cancelButton" value="{gt text='Cancel'}" />
            <br />
        </fieldset>
    </div>
    </form>

    <script type="text/javascript">
    /* <![CDATA[ */
        document.observe('dom:loaded', function() {
            muimage.finder.onLoad();
        });
    /* ]]> */
    </script>

    {*
    <div class="muimage-finderform">
        <fieldset>
            {modfunc modname='MUImage' type='admin' func='edit'}
        </fieldset>
    </div>
    *}
</body>
</html>