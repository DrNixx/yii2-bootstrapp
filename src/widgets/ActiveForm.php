<?php
namespace onix\widgets;

use yii\bootstrap5\ActiveField as ActiveFieldBase;
use yii\bootstrap5\ActiveForm as ActiveFormBase;
use yii\helpers\ArrayHelper;

class ActiveForm extends ActiveFormBase
{
    /**
     * @var bool use array-style input name
     */
    public $useModelPrefix = false;

    /**
     * @var string the default field class name when calling [[field()]] to create a new field.
     * @see fieldConfig
     */
    public $fieldClass = 'onix\widgets\ActiveField';

    /**
     * @inheritdoc
     * @return ActiveField the created ActiveFieldEx object
     */
    public function field($model, $attribute, $options = []): ActiveFieldBase
    {
        return parent::field(
            $model,
            $attribute,
            ArrayHelper::merge($options, ['useModelPrefix' => $this->useModelPrefix])
        );
    }

    /**
     * This registers the necessary JavaScript code.
     * @since 2.0.12
     */
    public function registerClientScript()
    {
        parent::registerClientScript();

        $id = $this->options['id'];
        $view = $this->getView();
        $view->registerJs(<<<JS
jQuery('#$id').on('afterValidateAttribute', function(e, attr, msg) {
    var hasError = msg.length > 0;
    var input = jQuery(attr.input);
    if (hasError) {
        input.removeClass("is-valid").addClass("is-invalid");
    } else {
        input.removeClass("is-invalid").addClass("is-valid");
    }
    console.log(attr, msg); 
});
JS
        );
    }
}
