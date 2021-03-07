<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\Category;

class Search
{
    public ?string $string = '';
    /**
     * @var Category[]
     */
    public array $categories = [];
}
