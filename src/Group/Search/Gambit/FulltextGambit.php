<?php

/*
 * This file is part of Flarum.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Flarum\Group\Search\Gambit;

use Flarum\Search\GambitInterface;
use Flarum\Search\SearchState;
use Flarum\Group\GroupRepository;

class FulltextGambit implements GambitInterface
{
    /**
     * @var GroupRepository
     */
    protected $groups;

    /**
     * @param \Flarum\Group\GroupRepository $groups
     */
    public function __construct(GroupRepository $groups)
    {
        $this->groups = $groups;
    }

    /**
     * @param $searchValue
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function geGroupSearchSubQuery($searchValue)
    {
        return $this->groups
            ->query()
            ->select('id')
            ->where('name_singular', 'like', "{$searchValue}%");
    }

    /**
     * {@inheritdoc}
     */
    public function apply(SearchState $search, $searchValue)
    {
        $search->getQuery()
            ->whereIn(
                'id',
                $this->geGroupSearchSubQuery($searchValue)
            );
    }
}
