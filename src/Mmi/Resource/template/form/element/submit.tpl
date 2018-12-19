{$value = $_element->getValue()}
{* translate *}
{$replacement = _($value)}
<input type="submit" {$_htmlOptions|replace:$value:$replacement} />
