<?php

/*
 * This file is part of Flarum.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Flarum\Group\Query;

use Flarum\Filter\FilterInterface;
use Flarum\Filter\FilterState;
use Flarum\Search\AbstractRegexGambit;
use Flarum\Search\SearchState;
use Illuminate\Database\Query\Builder;

class NameFilterGambit extends AbstractRegexGambit implements FilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function apply(SearchState $search, $bit)
    {
        return parent::apply($search, $bit);
    }

    /**
     * {@inheritdoc}
     */
    public function getGambitPattern()
    {
        return 'name_singular:(.+)';
    }

    /**
     * {@inheritdoc}
     */
    protected function conditions(SearchState $search, array $matches, $negate)
    {
        $this->constrain($search->getQuery(), $matches[1], $negate);
    }

    public function getFilterKey(): string
    {
        return 'name_singular';
    }

    public function filter(FilterState $filterState, string $filterValue, bool $negate)
    {
        $this->constrain($filterState->getQuery(), $filterValue, $negate);
    }

    protected function constrain(Builder $query, $rawName, bool $negate)
    {
        $groupIdentifiers = explode(',', trim($rawQuery, '"'));

        $ids = [];
        $names = [];
        foreach ($groupIdentifiers as $identifier) {
            if (is_numeric($identifier)) {
                $ids[] = $identifier;
            } else {
                $names[] = $identifier;
            }
        }

        $query->whereIn('id', $ids)
            ->orWhereIn('name_singular', $names)
            ->orWhereIn('name_plural', $names);
    }
}
