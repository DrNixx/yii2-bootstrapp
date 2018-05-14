<?php
namespace onix\widgets;

use Yii;
use yii\base\Model;
use yii\bootstrap\Widget;
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

            $errors = "<ul>";
            foreach ($this->model->getErrors() as $attr => $error) {
                if (is_array($error)) {
                    $error = implode("; ", $error);
                }

                $error = Html::encode($error);
                $errors .= "<li>$error</li>";
            }

            $errors .= "</ul>";

            $header = Yii::t('common', 'Error');
            $message = "<h4 class=\"alert-heading\"><i class=\"xi-attention\"></i>{$header}!</h4>{$errors}";

            echo \yii\bootstrap\Alert::widget([
                'body' => $message,
                'options' => $this->options,
            ]);
        }
    }
}
