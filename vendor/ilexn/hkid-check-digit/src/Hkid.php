<?php

declare(strict_types=1);

namespace Ilex\Validation\HkidValidation;

final class Hkid implements HkidValueInterface
{
    /**
     * @var string
     */
    private string $part1;

    /**
     * @var string
     */
    private string $part2;

    /**
     * @var string
     */
    private string $part3;

    public function __construct(
        string $part1,
        string $part2,
        string $part3
    ) {
        $this->part1 = $part1;
        $this->part2 = $part2;
        $this->part3 = $part3;
    }

    /**
     * @return string
     */
    public function getPart1(): string
    {
        return $this->part1;
    }

    /**
     * @return string
     */
    public function getPart2(): string
    {
        return $this->part2;
    }

    /**
     * @return string
     */
    public function getPart3(): string
    {
        return $this->part3;
    }

    public function format(): string
    {
        return sprintf('%s%s(%s)', $this->getPart1(), $this->getPart2(), $this->getPart3());
    }
}
