<?php
namespace onix\widgets;

use yii\bootstrap5\Html;
use yii\bootstrap5\Widget;
use yii\helpers\ArrayHelper;

class Alert extends Widget
{
    /**
     * @var string the header content
     */
    public $header;

    /**
     * @var string the heading icon class
     */
    public $icon = "";

    /**
     * @var string the body content in the alert component. Note that anything between
     * the [[begin()]] and [[end()]] calls of the Alert widget will also be treated
     * as the body content, and will be rendered before this.
     */
    public $body;

    /**
     * @var array|false the options for rendering the close button tag.
     * The close button is displayed in the header of the modal window. Clicking
     * on the button will hide the modal window. If this is false, no close button will be rendered.
     *
     * The following special options are supported:
     *
     * - tag: string, the tag name of the button. Defaults to 'button'.
     * - label: string, the label of the button. Defaults to '&times;'.
     *
     * The rest of the options will be rendered as the HTML attributes of the button tag.
     * Please refer to the [Alert documentation](http://getbootstrap.com/components/#alerts)
     * for the supported HTML attributes.
     */
    public $closeButton = [];

    private $isCloseRendered = false;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        $this->initOptions();

        echo Html::beginTag('div', $this->options) . "\n";
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        echo $this->renderHeader();
        echo $this->renderCloseButton();
        echo $this->body;
        echo $this->renderCloseButton();
        echo "\n" . Html::endTag('div');

        $this->registerPlugin('alert');
    }

    protected function renderHeader()
    {
        if (!empty($this->header)) {
            $icon = !empty($this->icon) ? "<i class=\"{$this->icon}\"></i>" : '';
            return $message = "<h4 class=\"pull-left alert-heading\">{$icon}{$this->header}!</h4>\n";
        }

        return '';
    }

    /**
     * Renders the close button.
     * @return string the rendering result
     */
    protected function renderCloseButton()
    {
        if (!$this->isCloseRendered && (($closeButton = $this->closeButton) !== false)) {
            $tag = ArrayHelper::remove($closeButton, 'tag', 'button');
            $label = ArrayHelper::remove($closeButton, 'label', Html::tag('span', '&times;', [
                'aria-hidden' => 'true'
            ]));
            if ($tag === 'button' && !isset($closeButton['type'])) {
                $closeButton['type'] = 'button';
            }

            $this->isCloseRendered = true;

            return Html::tag($tag, $label, $closeButton)."\n<div class=\"clearfix\"></div>\n";
        } else {
            return '';
        }
    }

    /**
     * Initializes the widget options.
     * This method sets the default values for various options.
     */
    protected function initOptions()
    {
        Html::addCssClass($this->options, ['widget' => 'alert']);

        if ($this->closeButton !== false) {
            $this->closeButton = array_merge([
                'data-dismiss' => 'alert',
                'class' => ['widget' => 'close'],
            ], $this->closeButton);

            Html::addCssClass($this->options, ['alert-dismissible']);
        }
        if (!isset($this->options['role'])) {
            $this->options['role'] = 'alert';
        }
    }
}