<div class="errors" {if $_element->getId()}id="{$_element->getId()}-errors"{/if}>
    {$_errors = $_element->getErrors()}
    {if $_errors}
        <span class="marker"></span>
        <ul>
            <li class="point first"></li>
            {foreach $_errors as $_error}
            <li class="notice error"><i class="icon-remove-sign icon-large"></i>{$_error}</li>
            {/foreach}
            <li class="close last"></li>
        </ul>
    {/if}
    <div class="clear"></div>
</div>