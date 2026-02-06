<?php

declare(strict_types=1);

namespace Tests\Unit;

use MetaFramework\Dictionnaries\Enum\DictionnaryType;
use Tests\TestCase;

class DictionnaryTypeTest extends TestCase
{
    public function test_default_case_is_simple(): void
    {
        $this->assertSame('simple', DictionnaryType::default());
    }
}
