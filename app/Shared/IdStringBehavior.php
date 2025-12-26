<?php

namespace App\Shared;

trait IdStringBehavior
{

    private function __construct(
        public readonly string $value,
    ) {
        if (trim($this->value) == '') {
            throw InvalidIdStringExcepion::emptyId(self::class);
        }
    }

    public static function fromString(string $value): self
    {
        return new self(trim($value));
    }

    public function __toString(): string
    {
        return $this->value;
    }
}