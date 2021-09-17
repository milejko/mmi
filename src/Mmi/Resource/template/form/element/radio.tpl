{$baseId = $_element->getId()}
{$value = $_element->getValue()}
{$id = 0}
<ul id="{$baseId}-list">
    {foreach $_element->getMultioptions() as $key => $caption}
        {$id++}
        {$checked = ''}
        {$keyUrl = $key|url}
        {* reset pola *}
        {$unused = $_element->setValue($key)->unsetOption('checked')->setId($baseId . '-' . $keyUrl)}
        {* ustalenie zaznaczenia *}
        {if $value !== null && $value == $key}
            {$checked = 'checked'}
        {/if}
        {* wartość wyłączona *}
        {if php_strpos($key, ':disabled') !== false}
            {$a = $_element->setDisabled()}
        {/if}
        <li id="{$_element->getId()}-item">
            <input value="{$key}" id="{$baseId}-{$id}" type="radio" {$_htmlOptions} {$checked} />
            <label for="{$baseId}-{$id}">{_($caption)}</label>
        </li>
    {/foreach}
    {* reset całego pola *}
    {$unused = $_element->setId($baseId)->setValue($value)}
</ul>
