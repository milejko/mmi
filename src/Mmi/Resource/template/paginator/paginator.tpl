{$_showPages = (($_paginator->getOption('showPages') > 2) ? $_paginator->getOption('showPages') : 2) - 2}
{$_halfPages = php_floor($_showPages / 2)}
{$_pageVariable = $_paginator->getOption('pageVariable')}
{$_page = $_paginator->getPage()}
{$_pagesCount = $_paginator->getPagesCount()}
{$_previousLabel = $_paginator->getOption('previousLabel')}
{$_nextLabel = $_paginator->getOption('nextLabel')}
{$_hashHref = $_paginator->getOption('hashHref')}

{$_modCtrlAct = ['module' => $_paginator->getRequest()->getModuleName(), 'controller' => $_paginator->getRequest()->getControllerName(), 'action' => $_paginator->getRequest()->getActionName()]}

{if $_pagesCount < 2}
    {exit}
{/if}

<ul class="pagination">

{* previous page *}
{if $_page > 1}
    {$_firstPage = (($_page - 1) > 1) ? ($_page - 1) : null}
    {$_previousUrl = url($_modCtrlAct + [$_pageVariable => $_firstPage], false) . $_hashHref}
    {headLink(['rel' => 'prev', 'href' => $_previousUrl])}
    <li class="previous page page-item">
        <a class="page-link" data-page="{$_firstPage}" href="{$_previousUrl}">{$_previousLabel}</a>
    </li>
{/if}

{* the first page *}
{if 1 == $_page}
    <li class="first current active page page-item">
        <a data-page="1" class="page-link" href="#">1</a>
    </li>
{else}
    <li class="first page page-item">
        <a data-page="1" href="{url($_modCtrlAct + [$_pageVariable => null], false) . $_hashHref}">1</a>
    </li>
{/if}

{* obliczanie zakresów *}
{$_rangeBegin = (($_page - $_halfPages) > 2) ? ($_page - $_halfPages) : 2}
{$_rangeBeginExcess = $_halfPages - ($_page - 2)}
{$_rangeBeginExcess = ($_rangeBeginExcess > 0) ? $_rangeBeginExcess : 0}

{$_rangeEnd = (($_page + $_halfPages) < $_pagesCount) ? ($_page + $_halfPages) : $_pagesCount - 1}
{$_rangeEndExcess = $_halfPages - ($_pagesCount - $_page - 1)}
{$_rangeEndExcess = ($_rangeEndExcess > 0) ? $_rangeEndExcess : 0}

{$_rangeEnd = (($_rangeEnd + $_rangeBeginExcess) < $_pagesCount) ? ($_rangeEnd + $_rangeBeginExcess) : $_pagesCount - 1}
{$_rangeBegin = (($_rangeBegin - $_rangeEndExcess) > 2) ? ($_rangeBegin - $_rangeEndExcess) : 2}

{* first dots *}
{if $_rangeBegin > 2}
    <li class="dots page page-item">
        {$_firstDots = php_floor($_rangeBegin / 2)}
        <a class="page-link" data-page="{$_firstDots}" href="{url($_modCtrlAct + [$_pageVariable => $_firstDots], false) . $_hashHref}">...</a>
    </li>
{/if}

{* pages in the range *}
{for $_i = $_rangeBegin; $_i <= $_rangeEnd; $_i++}
    {if $_i == $_page}
        <li class="current active page page-item">
            <a data-page="{$_i}" class="page-link" href="#">{$_i}</a>
        </li>
    {else}
        <li class="page page-item">
            <a class="page-link" data-page="{$_i}" href="{url($_modCtrlAct + [$_pageVariable => $_i], false) . $_hashHref}">{$_i}</a>
        </li>
    {/if}
{/for}

{* last dots *}
{if $_rangeEnd < $_pagesCount - 1}
    <li class="dots page page-item">
        {$_lastDots = php_ceil(($_rangeEnd + $_pagesCount) / 2)}
        <a class="page-link" data-page="{$_lastDots}" href="{url($_modCtrlAct + [$_pageVariable => $_lastDots], false) . $_hashHref}">...</a>
    </li>
{/if}

{* the last page *}
{if $_pagesCount == $_page}
    <li class="last current active page page-item">
        <a data-page="{$_pagesCount}" class="page-link" href="#">{$_pagesCount}</a>
    </li>
{else}
    <li class="last page page-item">
        <a class="page-link" data-page="{$_pagesCount}" href="{url($_modCtrlAct + [$_pageVariable => $_pagesCount], false) . $_hashHref}">{$_pagesCount}</a>
    </li>
{/if}

{* the next page *}
{if $_page < $_pagesCount}
    {$_nextUrl = url($_modCtrlAct + [$_pageVariable => $_page + 1], false) . $_hashHref}
    {headLink(['rel' => 'next', 'href' => $_nextUrl])}
    <li class="next page page-item">
        <a data-page="{$_page + 1}" href="{$_nextUrl}">{$_nextLabel}</a>
    </li>
{/if}
</ul>