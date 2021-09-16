<?php

/*
 * This file is part of Flarum.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Flarum\Api\Controller;

use Flarum\Api\Serializer\CurrentUserSerializer;
use Flarum\Api\Serializer\GroupSerializer;
use Flarum\Http\RequestUtil;
use Flarum\Http\SlugManager;
use Flarum\User\User;
use Flarum\Group\Group;
use Flarum\Group\GroupRepository;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class ShowGroupController extends AbstractShowController
{
    /**
     * {@inheritdoc}
     */
    public $serializer = GroupSerializer::class;

    /**
     * @var SlugManager
     */
    protected $slugManager;

    /**
     * @var GroupRepository
     */
    protected $groups;

    /**
     * @param SlugManager $slugManager
     * @param GroupRepository $users
     */
    public function __construct(SlugManager $slugManager, GroupRepository $groups)
    {
        $this->slugManager = $slugManager;
        $this->groups = $groups;
    }

    /**
     * {@inheritdoc}
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $id = Arr::get($request->getQueryParams(), 'id');
        $actor = RequestUtil::getActor($request);

        if (Arr::get($request->getQueryParams(), 'bySlug', false)) {
            $group = $this->slugManager->forResource(Group::class)->fromSlug($id, $actor);
        } else {
            $group = $this->groups->findOrFail($id, $actor);
        }

        return $group;
    }
}
