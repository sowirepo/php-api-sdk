<?php

declare(strict_types=1);

use Sowiso\SDK\SowisoApi;

it('can be instantiated', function () {
    expect(new SowisoApi())->toBeInstanceOf(SowisoApi::class);
});
