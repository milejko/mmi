<form{if $_form->getOption('action')} action="{$_form->getOption('action')}"{/if} method="{$_form->getOption('method')}"
    enctype="{$_form->getOption('enctype')}" class="{$_form->getOption('class')}"
    data-class="{php_get_class($_form)}" data-record-class="{$_form->getRecordClass()}"
    {if $_form->hasNotEmptyRecord()}{$_form->getRecord()->getPk()}{/if} enctype="{$_form->getOption('enctype')}"
    accept-charset="{$_form->getOption('accept-charset')}">