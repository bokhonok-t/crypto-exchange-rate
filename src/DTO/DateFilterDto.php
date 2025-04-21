<?php

namespace App\DTO;

use DateTimeImmutable;
use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;
class DateFilterDto
{
    public function __construct(
        #[Assert\Type(DateTimeInterface::class)]
        #[Assert\NotBlank]
        public ?DateTimeImmutable $from,
        #[Assert\Type(DateTimeInterface::class)]
        #[Assert\NotBlank]
        public ?DateTimeImmutable $to
    ) {}
}