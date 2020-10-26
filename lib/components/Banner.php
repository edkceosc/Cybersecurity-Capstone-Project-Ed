<?php

namespace lib\components;


class Banner
{

    /**
     * @var string
     */
    private $title;

    /**
     * @var string|null
     */
    private $subtitle;

    /**
     * Banner constructor.
     *
     * @param string      $title
     * @param string|null $subtitle
     */
    public function __construct(string $title, ?string $subtitle = null)
    {
        $this->title = $title;
        $this->subtitle = $subtitle;
    }

    public function render(): string
    {
        $title = $this->title;
        $subtitle = $this->subtitle;
        ob_start();
        include TEMPLATE_DIR . '/banner.php';
        return ob_get_clean();
    }

    public static function renderAppBanner(?string $pageTitle): string
    {
        $c = new Banner(AppInfo::APP_NAME, $pageTitle);
        return $c->render();
    }

}