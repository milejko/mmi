<form{if $form->getOption('action')} action="{$form->getOption('action')}"{/if} method="{$form->getOption('method')}"
    enctype="{$form->getOption('enctype')}" class="{$form->getOption('class')}"
    data-class="{php_get_class($form)}" data-record-class="{$form->getRecordClass()}"
    {if $form->hasNotEmptyRecord()}{$form->getRecord()->getPk()}{/if} enctype="{$form->getOption('enctype')}"
    accept-charset="{$form->getOption('accept-charset')}">