<?php

declare(strict_types=1);

namespace CoreMusic\Component;

/**
 * ModalComponent — Popup/dialog gösterimi.
 *
 * Kullanım:
 *   $modal = new ModalComponent([
 *       'id'      => 'confirm-modal',
 *       'title'   => 'Silme Onayı',
 *       'content' => '<p>Bu kaydı silmek istediğinize emin misiniz?</p>',
 *       'size'    => 'default', // small | default | large
 *       'actions' => [
 *           ['label' => 'İptal', 'type' => 'secondary', 'data-action' => 'close'],
 *           ['label' => 'Sil',   'type' => 'danger',    'data-action' => 'confirm'],
 *       ],
 *   ]);
 *   echo $modal->render();
 *
 * @package CoreMusic\Component
 */
class ModalComponent extends AbstractComponent
{
    public function key(): string
    {
        return 'cm-modal';
    }

    protected function renderTemplate(): string
    {
        $id      = $this->attr($this->get('id', 'cm-modal-' . uniqid()));
        $title   = $this->h($this->get('title', ''));
        $content = (string) ($this->get('content', '')); // HTML olarak render edilir
        $size    = $this->h($this->get('size', 'default'));
        /** @var array<int, array<string, string>> $actions */
        $actions = $this->get('actions', []);
        $closeBtn = !empty($this->get('showClose', true));

        $sizeClass = $size !== 'default' ? " cm-modal--{$size}" : '';

        $html = <<<HTML
<div class="cm-modal{$sizeClass}" id="{$id}" role="dialog" aria-modal="true" aria-labelledby="{$id}-title" hidden>
    <div class="cm-modal__overlay" data-cm-modal-close></div>
    <div class="cm-modal__container">
        <div class="cm-modal__header">
            <h2 class="cm-modal__title" id="{$id}-title">{$title}</h2>
HTML;

        if ($closeBtn) {
            $html .= <<<HTML
            <button class="cm-modal__close" data-cm-modal-close aria-label="Kapat">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                    <path d="M15 5L5 15M5 5l10 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </button>
HTML;
        }

        $html .= <<<HTML
        </div>
        <div class="cm-modal__body">
            {$content}
        </div>
HTML;

        if (!empty($actions)) {
            $html .= "\n        " . '<div class="cm-modal__footer">';

            foreach ($actions as $action) {
                $label  = $this->h($action['label'] ?? '');
                $type   = $this->attr($action['type'] ?? 'primary');
                $extras = '';
                foreach ($action as $key => $val) {
                    if ($key !== 'label' && $key !== 'type') {
                        $safeKey = $this->attr($key);
                        $safeVal = $this->attr($val);
                        $extras .= " {$safeKey}=\"{$safeVal}\"";
                    }
                }
                $html .= <<<HTML
            <button class="cm-modal__action cm-modal__action--{$type}"{$extras}>{$label}</button>
HTML;
            }

            $html .= "\n        </div>";
        }

        $html .= <<<HTML
    </div>
</div>
HTML;

        return $html;
    }
}
