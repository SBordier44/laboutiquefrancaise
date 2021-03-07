<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class FormFormatterExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('parseTags', [$this, 'parseTagsToHtml'])
        ];
    }

    public function parseTagsToHtml(string $string): string
    {
        $tagsToReplace = [
            '[br]' => '<br>',
            '[b]' => '<strong>',
            '[/b]' => '</strong>',
            '[i]' => '<i>',
            '[/i]' => '</i>'
        ];

        foreach ($tagsToReplace as $k => $v) {
            $string = str_replace($k, $v, $string);
        }

        return $string;
    }
}
