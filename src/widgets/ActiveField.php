<?php
namespace onix\widgets;

use Exception;
use Yii;
use yii\bootstrap4\ActiveField as ActiveFieldBase;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

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
     * @inheritdoc
     */
    public $checkOptions = [
        'class' => ['widget' => 'custom-check-input'],
        'labelOptions' => [
            'class' => ['widget' => '']
        ]
    ];

    /**
     * @inheritdoc
     */
    public $radioOptions = [
        'class' => ['widget' => 'custom-radio-input'],
        'labelOptions' => [
            'class' => ['widget' => '']
        ]
    ];

    /**
     * @var View
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
    public $checkTemplate = <<<HTML
<div class="form-check {controlStyle}">
    {input}
    {beginLabel}{labelTitle}{endLabel}
    {error}
    {hint}
</div>
HTML;

    /**
     * @inheritdoc
     */
    public $radioTemplate = <<<HTML
<div class="form-check">
    {input}
    {beginLabel}{labelTitle}{endLabel}
    {error}
    {hint}
</div>
HTML;

    /**
     * @inheritdoc
     */
    public $checkHorizontalTemplate = <<<HTML
{beginWrapper}
<div class="form-check form-check-inline">
    {input}
    {beginLabel}{labelTitle}{endLabel}
</div>
{error}
{endWrapper}
{hint}
HTML;

    /**
     * @inheritdoc
     */
    public $radioHorizontalTemplate = <<<HTML
{beginWrapper}
<div class="form-check form-check-inline">
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
     *
     * @throws Exception
     */
    public function widget($class, $config = [])
    {
        if (is_subclass_of($class, 'onix\widgets\IInputWidget')) {
            $config['field'] = $this;
        }

        if (!isset($config['options'])) {
            $config['options'] = [];
        }

        $config['options'] = $this->checkName($config['options']);

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
    public function checkbox($options = [], $enclosedByLabel = false)
    {
        $this->parts['{controlStyle}'] = isset($options['controlStyle']) ? $options['controlStyle'] : '';
        return parent::checkbox($this->checkName($options), $enclosedByLabel);
    }

    public function radio($options = [], $enclosedByLabel = true)
    {
        return parent::radio($this->checkName($options), $enclosedByLabel);
    }

    public function radioList($items, $options = [])
    {
        $controlStyle = isset($options['controlStyle']) ? $options['controlStyle'] : '';

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
     * Returns the view object that can be used to render views or view files.
     * The [[render()]] and [[renderFile()]] methods will use
     * this view object to implement the actual view rendering.
     * If not set, it will default to the "view" application component.
     * @return View the view object that can be used to render views or view files.
     */
    protected function getView()
    {
        if ($this->view === null) {
            $this->view = Yii::$app->getView();
        }

        return $this->view;
    }
}
