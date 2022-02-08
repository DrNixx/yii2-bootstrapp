<?php
namespace onix\widgets;

use Yii;
use yii\base\Model;
use yii\bootstrap5\Widget;
use yii\helpers\Html;

class ModelError extends Widget
{
    /**
     * @var Model
     */
    public $model;

    /**
     * @throws \Exception
     */
    public function init()
    {
        parent::init();

        if ($this->model->hasErrors()) {
            $appendCss = isset($this->options['class']) ? ' ' . $this->options['class'] : '';
            $this->options['class'] = "alert-danger{$appendCss}";

            $items = array_map(
                function($a) {
                    return is_array($a) ? implode(';', $a) : $a;
                },
                array_values($this->model->getErrors())
            );

            $header = Yii::t('common', 'Error');
            echo Alert::widget([
                'header' => $header,
                'icon' => 'xi-attention',
                'body' => Html::ul($items),
                'options' => $this->options,
            ]);
        }
    }
}
