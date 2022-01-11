<?php

namespace App\ValueObject\Dto;

class TodoListData
{
    public function __construct(
        public string $status,
        public int $priority,
        public string $title,
        public ?string $description,
        public string $userKey,
        public ?int $parentId,
    ) {}
}
