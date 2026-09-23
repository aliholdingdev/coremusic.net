<?php

declare(strict_types=1);

namespace CoreMusic\Component;

/**
 * FormComponent — Dinamik form oluşturma.
 *
 * Kullanım:
 *   $form = new FormComponent([
 *       'action'  => '/api/settings',
 *       'method'  => 'POST',
 *       'fields'  => [
 *           ['type' => 'text', 'name' => 'username', 'label' => 'Kullanıcı Adı', 'required' => true],
 *           ['type' => 'email', 'name' => 'email', 'label' => 'E-posta'],
 *           ['type' => 'select', 'name' => 'role', 'label' => 'Rol', 'options' => ['user' => 'Kullanıcı', 'admin' => 'Admin']],
 *           ['type' => 'checkbox', 'name' => 'terms', 'label' => 'Koşulları kabul ediyorum'],
 *       ],
 *       'submit'  => 'Kaydet',
 *   ]);
 *   echo $form->render();
 *
 * @package CoreMusic\Component
 */
class FormComponent extends AbstractComponent
{
    public function key(): string
    {
        return 'cm-form';
    }

    protected function renderTemplate(): string
    {
        $action = $this->attr($this->get('action', '#'));
        $method = $this->attr($this->get('method', 'POST'));
        $id     = $this->attr($this->get('id', 'cm-form-' . uniqid()));
        /** @var array<int, array<string, mixed>> $fields */
        $fields = $this->get('fields', []);
        $submit = $this->h($this->get('submit', 'Gönder'));

        $html = <<<HTML
<form class="cm-form" id="{$id}" action="{$action}" method="{$method}" novalidate>
    <div class="cm-form__fields">
HTML;

        foreach ($fields as $field) {
            $html .= $this->renderField($field);
        }

        $html .= <<<HTML
    </div>
    <div class="cm-form__actions">
        <button type="submit" class="cm-form__submit">{$submit}</button>
    </div>
</form>
HTML;

        return $html;
    }

    /**
     * Tek bir form alanını render eder.
     *
     * @param array<string, mixed> $field Alan tanımı
     */
    private function renderField(array $field): string
    {
        $type     = (string) ($field['type'] ?? 'text');
        $name     = $this->attr((string) ($field['name'] ?? ''));
        $label    = $this->h((string) ($field['label'] ?? $name));
        $value    = $this->attr((string) ($field['value'] ?? ''));
        $required = !empty($field['required']) ? ' required' : '';
        $fieldId  = $this->attr("{$name}");
        $error    = (string) ($field['error'] ?? '');

        $errorClass = $error !== '' ? ' cm-form__field--error' : '';

        $html = <<<HTML
    <div class="cm-form__field{$errorClass}">
        <label class="cm-form__label" for="{$fieldId}">{$label}</label>
HTML;

        switch ($type) {
            case 'textarea':
                $escaped = $this->h($value);
                $html .= <<<HTML
        <textarea class="cm-form__input cm-form__input--textarea" id="{$fieldId}" name="{$name}"{$required}>{$escaped}</textarea>
HTML;
                break;

            case 'select':
                /** @var array<string, string> $options */
                $options = $field['options'] ?? [];
                $html .= <<<HTML
        <select class="cm-form__input cm-form__input--select" id="{$fieldId}" name="{$name}"{$required}>
HTML;
                foreach ($options as $optValue => $optLabel) {
                    $optVal  = $this->attr($optValue);
                    $optText = $this->h($optLabel);
                    $selected = $optValue === $value ? ' selected' : '';
                    $html .= <<<HTML
            <option value="{$optVal}"{$selected}>{$optText}</option>
HTML;
                }
                $html .= "\n        </select>";
                break;

            case 'checkbox':
                $checked = $value === '1' || $value === 'true' ? ' checked' : '';
                $html .= <<<HTML
        <input class="cm-form__input cm-form__input--checkbox" type="checkbox" id="{$fieldId}" name="{$name}" value="1"{$required}{$checked}>
HTML;
                break;

            case 'radio':
                /** @var array<string, string> $radioOptions */
                $radioOptions = $field['options'] ?? [];
                foreach ($radioOptions as $rVal => $rLabel) {
                    $rValue = $this->attr($rVal);
                    $rText  = $this->h($rLabel);
                    $rId    = $this->attr("{$name}-{$rVal}");
                    $checked = $rVal === $value ? ' checked' : '';
                    $html .= <<<HTML
        <label class="cm-form__radio-label">
            <input class="cm-form__input cm-form__input--radio" type="radio" id="{$rId}" name="{$name}" value="{$rValue}"{$required}{$checked}>
            <span>{$rText}</span>
        </label>
HTML;
                }
                break;

            default: // text, email, password, number, search, url, tel
                $safeType = $this->attr($type);
                $html .= <<<HTML
        <input class="cm-form__input cm-form__input--{$safeType}" type="{$safeType}" id="{$fieldId}" name="{$name}" value="{$value}"{$required}>
HTML;
                break;
        }

        if ($error !== '') {
            $errorHtml = $this->h($error);
            $html .= <<<HTML
        <span class="cm-form__error">{$errorHtml}</span>
HTML;
        }

        $html .= "\n    </div>";

        return $html;
    }
}
