<?php
namespace Pixelpark\Bkkvbu\Interceptor;

/**
 * Class CsvFinisher
 * @package Pixelpark\Bkkvbu\Finisher
 *
 */
class CombineInterceptor extends \CosmoCode\SimpleForm\Interceptor\AbstractInterceptor {

    public function intercept() {
        $typoScript = $this->getInterceptorConfiguration();

        $combinedString = '';
        $combinedFieldName = $typoScript['combinedFieldName'];
        $values = $typoScript['values'];

        // If combinedField name is empty nothing will be done
        if (! $combinedFieldName) {
            return false;
        }

        foreach ($values as $value) {
            if (! empty($value['text'])) {
                $combinedString .= $value['text'];
            } elseif (! empty($value['field'])) {
                $fieldText = $this->formDataHandler->getFormValue($value['field']);

                if ($fieldText) {
                    $combinedString .= $fieldText;
                }
            }
        }

        $this->formDataHandler->setFormValue($combinedFieldName, $combinedString);
    }
}
