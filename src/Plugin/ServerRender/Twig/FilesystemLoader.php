<?php

namespace l24n\Twigen\Plugin\ServerRender\Twig;

use l24n\Twigen\Plugin\ServerRender\FrontMatter;
use Twig\Loader\FilesystemLoader as TwigFilesystemLoader;
use Twig\Source;

class FilesystemLoader extends TwigFilesystemLoader
{
    public function getSourceContext(string $name): Source
    {
        if (null === $path = $this->findTemplate($name)) {
            return new Source('', $name, '');
        }

        return new Source((new FrontMatter($path))->body(), $name, $path);
    }
}