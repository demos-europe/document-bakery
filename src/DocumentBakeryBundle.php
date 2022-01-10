<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery;


use Symfony\Component\HttpKernel\Bundle\Bundle;

class DocumentBakeryBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
