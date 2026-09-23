<?php

declare(strict_types=1);

namespace CoreMusic\Component;

/**
 * ListComponent — Liste verisi gösterimi.
 *
 * Kullanım:
 *   $list = new ListComponent([
 *       'items' => [
 *           ['title' => 'Şarkı 1', 'meta' => '3:20'],
 *           ['title' => 'Şarkı 2', 'meta' => '4:15'],
 *       ],
 *       'layout' => 'compact', // default | compact | grid
 *       'empty'  => 'Henüz şarkı eklenmedi.',
 *   ]);
 *   echo $list->render();
 *
 * @package CoreMusic\Component
 */
class ListComponent extends AbstractComponent
{
    public function key(): string
    {
        return 'cm-list';
    }

    protected function renderTemplate(): string
    {
        /** @var array<int, array<string, mixed>> $items */
        $items  = $this->get('items', []);
        $layout = $this->h($this->get('layout', 'default'));
        $empty  = $this->h($this->get('empty', ''));

        $layoutClass = $layout !== 'default' ? " cm-list--{$layout}" : '';

        $html = <<<HTML
<div class="cm-list{$layoutClass}" data-cm-component="cm-list">
HTML;

        if (empty($items)) {
            $emptyMsg = $this->h($empty ?: 'Veri bulunamadı.');
            $html .= <<<HTML
    <div class="cm-list__empty">
        <p class="cm-list__empty-text">{$emptyMsg}</p>
    </div>
HTML;
        } else {
            $html .= '    <ul class="cm-list__items">' . "\n";

            foreach ($items as $index => $item) {
                $itemTitle = $this->h((string) ($item['title'] ?? ''));
                $itemMeta  = $this->h((string) ($item['meta'] ?? ''));
                $itemUrl   = $this->attr((string) ($item['url'] ?? '#'));
                $itemImage = $this->attr((string) ($item['image'] ?? ''));
                $active    = !empty($item['active']) ? ' cm-list__item--active' : '';

                $html .= <<<HTML
        <li class="cm-list__item{$active}" data-index="{$index}">
            <a href="{$itemUrl}" class="cm-list__item-link">
HTML;

                if ($itemImage !== '') {
                    $html .= <<<HTML
                <img class="cm-list__item-image" src="{$itemImage}" alt="{$itemTitle}" loading="lazy">
HTML;
                }

                $html .= <<<HTML
                <span class="cm-list__item-title">{$itemTitle}</span>
HTML;

                if ($itemMeta !== '') {
                    $html .= <<<HTML
                <span class="cm-list__item-meta">{$itemMeta}</span>
HTML;
                }

                $html .= <<<HTML
            </a>
        </li>
HTML;
            }

            $html .= '    </ul>' . "\n";
        }

        $html .= '</div>';

        return $html;
    }
}
