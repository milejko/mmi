{$baseId = $_element->getId()}
{$value = $_element->getValue()}
<ul id="{$baseId}-list">
    {foreach $_element->getMultioptions() as $key => $caption}
        {$keyUrl = $key|url}
        {* reset pola *}
        {$unused = $_element->setValue($key)->unsetOption('checked')->setId($baseId . '-' . $keyUrl)}
        {* ustalenie zaznaczenia *}
        {if $value !== null && $value == $key}
            {$_element->setOption('checked', '')}
        {/if}
        {* wartość wyłączona *}
        {if php_strpos($key, ':disabled') !== false}
            {$a = $_element->setDisabled()}
        {/if}
        <li id="{$_element->getId()}-item">
            <input type="radio" {$_htmlOptions} />
            <label for="{$_element->getId()}">{$caption}</label>
        </li>
    {/foreach}
    {* reset całego pola *}
    {$unused = $_element->setId($baseId)->setValue($value)}
</ul>