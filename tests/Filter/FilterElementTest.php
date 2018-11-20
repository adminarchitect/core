<?php

namespace Terranet\Administrator\Tests\Filter;

use Terranet\Administrator\Filter\Enum;

class FilterElementTest extends \PHPUnit\Framework\TestCase
{
    /** @test */
    public function it_allows_options_list_for_enum_type()
    {
        $select = Enum::make('select')->setOptions($options = [1, 2, 3, 4, 5]);

        $this->assertSame(
            $options,
            $select->getOptions()
        );
    }

    /** @test */
    public function it_allows_callable_options_for_enum_types()
    {
        $select = Enum::make('select');

        $realOptions = [1, 2, 3, 4, 5];
        $options = function () use ($realOptions) {
            return $realOptions;
        };

        $select->setOptions($options);

        $this->assertSame(
            $realOptions,
            $select->getOptions()
        );
    }

    /** @test */
    public function it_allows_a_query_builder()
    {
        $select = Enum::make('test');

        $select->setOptions([1, 2, 3, 4, 5]);

        $select->setQuery($query = function ($query, $value = null) {
            return $query->where('test', $value);
        });

        $this->assertSame(
            $query,
            $select->getQuery()
        );
    }
}
