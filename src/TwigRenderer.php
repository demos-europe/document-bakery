<?php

declare(strict_types=1);

namespace DemosEurope\DocumentCompiler;

use Twig\Environment;
use Twig\Loader\ArrayLoader;

class TwigRenderer
{
    /**
     * @var Environment
     */
    private $twig;

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
