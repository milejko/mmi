{$value = $_element->getValue()}
{$value = $value|input}
{* nie wolno tu zrobić toString() *}
{$unused = $_element->setValue($value)}
<input type="text" {$_htmlOptions} />