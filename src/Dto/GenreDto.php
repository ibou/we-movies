<?php

declare(strict_types=1);

namespace App\Dto;


final readonly class GenreDto
{
    public function __construct(
        public int $id,
        public string $name
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name']
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}