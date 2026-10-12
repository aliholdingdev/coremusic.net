<?php

declare(strict_types=1);

namespace CoreMusic\Component;

/**
 * CardComponent — Dinamik veri ile kart gösterimi.
 *
 * Kullanım:
 *   $card = new CardComponent([
 *       'title'  => 'Şarkı Adı',
 *       'artist' => 'Sanatçı',
 *       'image'  => '/img/cover.jpg',
 *       'url'    => '/track/123',
 *       'meta'   => '3:45',
 *   ]);
 *   echo $card->render();
 *
 * @package CoreMusic\Component
 */
class CardComponent extends AbstractComponent
{
    public function key(): string
    {
        return 'cm-card';
    }

    protected function renderTemplate(): string
    {
        $title  = $this->h($this->get('title', ''));
        $artist = $this->h($this->get('artist', ''));
        $image  = $this->attr($this->get('image', ''));
        $url    = $this->attr($this->get('url', '#'));
        $meta   = $this->h($this->get('meta', ''));
        $size   = $this->h($this->get('size', 'default')); // default | compact | wide

        $sizeClass = $size !== 'default' ? " cm-card--{$size}" : '';

        $html = <<<HTML
<div class="cm-card{$sizeClass}" data-cm-component="cm-card">
    <a href="{$url}" class="cm-card__link">
HTML;

        if ($image !== '') {
            $html .= <<<HTML
        <div class="cm-card__image-wrap">
            <img class="cm-card__image" src="{$image}" alt="{$title}" loading="lazy">
        </div>
HTML;
        }

        $html .= <<<HTML
        <div class="cm-card__body">
            <h3 class="cm-card__title">{$title}</h3>
HTML;

        if ($artist !== '') {
            $html .= <<<HTML
            <p class="cm-card__artist">{$artist}</p>
HTML;
        }

        if ($meta !== '') {
            $html .= <<<HTML
            <span class="cm-card__meta">{$meta}</span>
HTML;
        }

        $html .= <<<HTML
        </div>
    </a>
</div>
HTML;

        return $html;
    }
}
