<ul id="messenger" class="messenger">
    {foreach $_messenger->getMessages() as $message}
        {$class = 'warning'}
        {$icon = 'warning-sign'}
        {if $message.type}
            {$class = $message['type']}
            {$icon = ($message['type'] == 'error') ? 'remove-sign' : 'ok'}
        {/if}
        <li class="notice {$class}"><i class="icon-{$icon} icon-large"></i><div class="alert">{$_messenger->prepareTranslatedMessage($message)}<a class="close-alert" href="#"></a></div></li>
    {/foreach}
</ul>
