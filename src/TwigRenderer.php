<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery;

use Twig\Environment;
use Twig\Loader\ArrayLoader;

class TwigRenderer
{
    private Environment $twig;

    public function __construct()
    {
        $loader = new ArrayLoader();

        $this->twig = new Environment($loader);
    }

    public function render(string $content, array $parameters = []): string
    {
        $template = $this->twig->createTemplate($content);
        return $template->render($parameters);
    }
}
