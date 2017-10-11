{$value = $_element->getValue()}
{* usuwanie value, by nie renderowało się w htmlOptions *}
{$unused = $_element->unsetOption('value')}
<textarea {$_htmlOptions}>{$value|input}</textarea>