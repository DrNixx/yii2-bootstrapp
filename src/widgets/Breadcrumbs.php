<?php
namespace onix\widgets;

use Yii;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs as BreadcrumbsBase;

class Breadcrumbs extends BreadcrumbsBase
{
    /**
     * @inheritdoc
     */
    public $tag = 'ol';

    /**
     * @var string the template used to render each inactive item in the breadcrumbs. The token `{link}`
     * will be replaced with the actual HTML link for each inactive item.
     */
    public $itemTemplate = "<li class=\"breadcrumb-item\">{link}</li>\n";
    /**
     * @var string the template used to render each active item in the breadcrumbs. The token `{link}`
     * will be replaced with the actual HTML link for each active item.
     */
    public $activeItemTemplate = "<li class=\"breadcrumb-item active\" aria-current=\"page\">{link}</li>\n";

    /**
     * @return string|void
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        if (empty($this->links)) {
            return;
        }

        $links = [];
        if ($this->homeLink === null) {
            $links[] = $this->renderItem([
                'label' => Yii::t('common', 'Home'),
                'url' => Yii::$app->homeUrl,
            ], $this->itemTemplate);
        } elseif ($this->homeLink !== false) {
            $links[] = $this->renderItem($this->homeLink, $this->itemTemplate);
        }

        foreach ($this->links as $link) {
            if (!is_array($link)) {
                $link = ['label' => $link];
            }
            $links[] = $this->renderItem($link, isset($link['url']) ? $this->itemTemplate : $this->activeItemTemplate);
        }

        $list = Html::tag($this->tag, implode('', $links), $this->options);

        echo Html::tag("nav", $list, ['aria-label' => "breadcrumb", 'role' => "navigation"]);
    }
}
