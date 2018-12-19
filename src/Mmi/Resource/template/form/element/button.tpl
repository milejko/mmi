{$value = $_element->getValue()}
{$value = _($value)}
{* nie wolno tu zrobić toString() *}
{$unused = $_element->setValue($value)}
<input type="button" {$_htmlOptions} />
