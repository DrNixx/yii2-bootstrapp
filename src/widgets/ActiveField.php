<?php
namespace onix\widgets;

use Yii;
use yii\bootstrap\ActiveField as ActiveFieldBase;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class ActiveField extends ActiveFieldBase
{
    /**
     * @var bool use array-style input name
     */
    public $useModelPrefix = false;

    /**
     * @inheritdoc
     */
    public $errorOptions = ['class' => 'invalid-feedback'];

    /**
     * @var \yii\web\View
     */
    private $view;

    /**
     * @var bool
     */
    private $supressWrap = false;


    public $pagesStyle = false;

    /**
     * @inheritdoc
     */
    public $template = "{label}\n{input}\n{error}\n{hint}";

    /**
     * @var string the template for display
     */
    public $displayTemplate = "{label}\n<strong>{input}</strong>\n{hint}";

    /**
     * @inheritdoc
     */
    public $checkboxTemplate = <<<HTML
<div class="checkbox {controlStyle}">
    {input}
    {beginLabel}
    {labelTitle}
    {endLabel}
    {error}
    {hint}
</div>
HTML;

    /**
     * @inheritdoc
     */
    public $radioTemplate = <<<HTML
<div class="radio">
    {beginLabel}
    111{input}
    {labelTitle}
    {endLabel}
    {error}
    {hint}
</div>
HTML;

    /**
     * @inheritdoc
     */
    public $horizontalCheckboxTemplate = <<<HTML
{beginWrapper}
<div class="checkbox">
    {beginLabel}
    {input}
    {labelTitle}
    {endLabel}
</div>
{error}
{endWrapper}
{hint}
HTML;

    /**
     * @inheritdoc
     */
    public $horizontalRadioTemplate = <<<HTML
{beginWrapper}
<div class="radio">
    {beginLabel}
    {input}
    {labelTitle}
    {endLabel}
</div>
{error}
{endWrapper}
{hint}
HTML;

    /**
     * @inheritdoc
     */
    public $inlineCheckboxListTemplate = "{label}\n{beginWrapper}\n{input}\n{error}\n{endWrapper}\n{hint}";
    /**
     * @inheritdoc
     */
    public $inlineRadioListTemplate = "{label}\n{beginWrapper}\n{input}\n{error}\n{endWrapper}\n{hint}";


    /**
     * @var string the template for hidden input
     */
    public $hiddenTemplate = "{input}\n";

    /**
     * @inheritdoc
     */
    public function textInput($options = [])
    {
        return parent::textInput($this->checkName($options));
    }

    /**
     * @inheritdoc
     */
    public function begin()
    {
        if (!$this->supressWrap) {
            return parent::begin();
        } else {
            return "";
        }
    }

    /**
     * @inheritdoc
     */
    public function end()
    {
        if (!$this->supressWrap) {
            return parent::end();
        } else {
            return "";
        }
    }

    /**
     * Renders a widget as the input of the field.
     *
     * Note that the widget must have both `model` and `attribute` properties. They will
     * be initialized with [[model]] and [[attribute]] of this field, respectively.
     *
     * If you want to use a widget that does not have `model` and `attribute` properties,
     * please use [[render()]] instead.
     *
     * For example to use the [[MaskedInput]] widget to get some date input, you can use
     * the following code, assuming that `$form` is your [[ActiveForm]] instance:
     *
     * ```php
     * $form->field($model, 'date')->widget(\yii\widgets\MaskedInput::className(), [
     *     'mask' => '99/99/9999',
     * ]);
     * ```
     *
     * If you set a custom `id` for the input element, you may need to adjust the [[$selectors]] accordingly.
     *
     * @param string $class the widget class name.
     * @param array $config name-value pairs that will be used to initialize the widget.
     * @return $this the field object itself.
     */
    public function widget($class, $config = [])
    {
        if (is_subclass_of($class, 'onix\widgets\IInputWidget')) {
            $config['field'] = $this;
        }

        parent::widget($class, $config);
        return $this;
    }

    public function pages()
    {
        $this->pagesStyle = true;
        return $this;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function display($options = [])
    {
        $options = $this->checkName($options);
        $name = isset($options['name']) ? $options['name'] : Html::getInputName($this->model, $this->attribute);
        $options['id'] = $name;

        $this->template = $this->displayTemplate;
        $value = Html::getAttributeValue($this->model, $this->attribute);

        if (isset($options['formatter'])) {
            $this->parts['{input}'] = call_user_func($options['formatter'], $value);
        } else {
            $this->parts['{input}'] = $value;
        }

        return $this;
    }


    /**
     * @inheritdoc
     */
    public function hiddenInput($options = [])
    {
        $options = $this->checkName($options);
        $name = isset($options['name']) ? $options['name'] : Html::getInputName($this->model, $this->attribute);
        $options['id'] = $name;

        $this->supressWrap = true;
        $this->template = $this->hiddenTemplate;
        return parent::hiddenInput($options);
    }

    /**
     * @inheritdoc
     */
    public function passwordInput($options = [])
    {
        return parent::passwordInput($this->checkName($options));
    }

    /**
     * @inheritdoc
     */
    public function textarea($options = [])
    {
        return parent::textarea($this->checkName($options));
    }

    /**
     * @inheritdoc
     */
    public function checkbox($options = [], $enclosedByLabel = true)
    {
        $this->parts['{controlStyle}'] = isset($options['controlStyle']) ? $options['controlStyle'] : 'check-primary';
        return parent::checkbox($this->checkName($options), $enclosedByLabel);
    }

    public function radio($options = [], $enclosedByLabel = true)
    {
        return parent::radio($this->checkName($options), $enclosedByLabel);
    }

    public function radioList($items, $options = [])
    {
        $controlStyle = isset($options['controlStyle']) ? $options['controlStyle'] : 'primary';

        if ($this->pagesStyle) {
            $options['class'] =
                [
                    "radio",
                    "radio-{$controlStyle}"
                ];
            $options['item'] = function ($index, $label, $name, $checked, $value) {
                $opt['checked'] = (bool) $checked;
                $opt['id'] = "id_{$name}_{$index}";
                $input = Html::input("radio", $name, $value, $opt);
                $label = Html::label($label, $opt['id']);
                return $input.$label;
            };
        }

        return parent::radioList($items, $this->checkName($options));
    }

    /**
     * @param array $options
     * @return array
     */
    private function checkName($options)
    {
        return (!$this->useModelPrefix) ? ArrayHelper::merge($options, ['name' => $this->attribute]): $options;
    }

    /**
     * @param array $instanceConfig the configuration passed to this instance's constructor
     * @return array the layout specific default configuration for this instance
     */
    protected function createLayoutConfig($instanceConfig)
    {
        $config = [
            'labelOptions' => [
                'class' => 'form-label'
            ],
            'hintOptions' => [
                'tag' => 'small',
                'class' => 'form-text text-muted',
            ],
            'errorOptions' => [
                'tag' => 'div',
                'class' => 'invalid-feedback',
            ],
            'inputOptions' => [
                'class' => 'form-control',
            ],
        ];

        $layout = $instanceConfig['form']->layout;

        if ($layout === 'horizontal') {
            $config['template'] = "{label}\n{beginWrapper}\n{input}\n{error}\n{endWrapper}\n{hint}";
            $cssClasses = [
                'offset' => 'col-sm-offset-3',
                'label' => 'col-sm-3',
                'wrapper' => 'col-sm-6',
                'error' => '',
                'hint' => 'col-sm-3',
            ];
            if (isset($instanceConfig['horizontalCssClasses'])) {
                $cssClasses = ArrayHelper::merge($cssClasses, $instanceConfig['horizontalCssClasses']);
            }
            $config['horizontalCssClasses'] = $cssClasses;
            $config['wrapperOptions'] = ['class' => $cssClasses['wrapper']];
            $config['labelOptions'] = ['class' => 'control-label ' . $cssClasses['label']];
            $config['errorOptions'] = ['class' => 'help-block help-block-error ' . $cssClasses['error']];
            $config['hintOptions'] = ['class' => 'help-block ' . $cssClasses['hint']];
        } elseif ($layout === 'inline') {
            $config['labelOptions'] = ['class' => 'sr-only'];
            $config['enableError'] = false;
        }

        return $config;
    }

    /**
     * Returns the view object that can be used to render views or view files.
     * The [[render()]] and [[renderFile()]] methods will use
     * this view object to implement the actual view rendering.
     * If not set, it will default to the "view" application component.
     * @return \yii\web\View the view object that can be used to render views or view files.
     */
    protected function getView()
    {
        if ($this->view === null) {
            $this->view = Yii::$app->getView();
        }

        return $this->view;
    }
}
