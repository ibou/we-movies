<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Serializer\Attribute\SerializedName;

class MovieListResponseDto
{
    /**
     * @param MovieDto[] $results
     */
    public function __construct(
        public readonly int $page,
        #[SerializedName('total_results')]
        public readonly int $totalResults,
        #[SerializedName('total_pages')]
        public readonly int $totalPages,
        public readonly array $results,
    ) {
    }
}