{* ustawianie zmiennych *}
{$_showPages = (($_paginator->getOption('showPages') > 2) ? $_paginator->getOption('showPages') : 2) - 2}
{$_halfPages = php_floor($_showPages / 2)}
{$_pageVariable = $_paginator->getOption('pageVariable')}
{$_page = $_paginator->getPage()}
{$_pagesCount = $_paginator->getPagesCount()}
{$_previousLabel = $_paginator->getOption('previousLabel')}
{$_nextLabel = $_paginator->getOption('nextLabel')}
{$_hashHref = $_paginator->getOption('hashHref')}

{$_modCtrlAct = ['module' => $_paginator->getRequest()->getModuleName(), 'controller' => $_paginator->getRequest()->getControllerName(), 'action' => $_paginator->getRequest()->getActionName()]}

<div class="paginator">

{* pierwsza strona *}
{if $_page > 1}
    {$_firstPage = (($_page - 1) > 1) ? ($_page - 1) : null}
    {$_previousUrl = url($_modCtrlAct + [$_pageVariable => $_firstPage]) . $_hashHref}
    {headLink(['rel' => 'prev', 'href' => $_previousUrl])}
    <span class="previous page"><a data-page="{$_firstPage}" href="{$_previousUrl}">{$_previousLabel}</a></span>
{else}
    <span class="previous page">{$_previousLabel}</span>
{/if}

{* generowanie strony pierwszej *}
{if 1 == $_page}
    <span class="current page">1</span>
{else}
    <span class="page"><a data-page="" href="{url($_modCtrlAct + [$_pageVariable => null]) . $_hashHref}">1</a></span>
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

{* pierwsza strona w zakresie *}
{if $_rangeBegin > 2}
    <span class="dots page">
        <a data-page="{php_floor((1 + $_rangeBegin) / 2)}" href="{url($_modCtrlAct + [$_pageVariable => php_floor((1 + $_rangeBegin) / 2)]) . $_hashHref}">...</a>
    </span>
{/if}

{* generowanie stron w zakresie *}
{for $_i = $_rangeBegin; $_i <= $_rangeEnd; $_i++}
    {if $_i == $_page}
        <span class="current page">{$_i}</span>
    {else}
        <span class="page"><a data-page="{$_i}" href="{url($_modCtrlAct + [$_pageVariable => $_i]) . $_hashHref}">{$_i}</a></span>
    {/if}
{/for}

{* ostatnia strona w zakresie *}
{if $_rangeEnd < $_pagesCount - 1}
    <span class="dots page"><a data-page="{php_ceil(($_rangeEnd + $_pagesCount) / 2)}" href="{url($_modCtrlAct + [$_pageVariable => ceil(($_rangeEnd + $_pagesCount) / 2)]) . $_hashHref}">...</a></span>
{/if}

{* ostatnia strona w ogóle *}
{if $_pagesCount == $_page}
    <span class="last current page">{$_pagesCount}</span>
{else}
    <span class="last page"><a data-page="{$_pagesCount}" href="{url($_modCtrlAct + [$_pageVariable => $_pagesCount]) . $_hashHref}">{$_pagesCount}</a></span>
{/if}

{* generowanie guzika następny *}
{if $_page < $_pagesCount}
    {$_nextUrl = url($_modCtrlAct + [$_pageVariable => $_page + 1]) . $_hashHref}
    {headLink(['rel' => 'next', 'href' => $_nextUrl])}
    <span class="next page"><a data-page="{$_page + 1}" href="{$_nextUrl}">{$_nextLabel}</a></span>
{else}
    <span class="next page">{$_nextLabel}</span>
{/if}
</div>