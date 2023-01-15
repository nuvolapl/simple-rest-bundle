<?php

declare(strict_types=1);

namespace Nuvola\SimpleRestBundle\Pagination;

class LimitOffsetPagination
{
    public readonly int $limit;
    public readonly int $offset;

    public function __construct(
        string|int $limit,
        string|int $offset,
    ) {
        $this->offset = (int) $offset;
        $this->limit  = (int) $limit;
    }
}
