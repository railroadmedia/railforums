<?php

namespace Railroad\Railforums\Controllers;

use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Railroad\Permissions\Services\PermissionService;
use Railroad\Railforums\DataMappers\UserCloakDataMapper;
use Railroad\Railforums\Requests\ThreadCreateRequest;
use Railroad\Railforums\Requests\ThreadUpdateRequest;
use Railroad\Railforums\Repositories\ThreadRepository;
use Railroad\Railforums\Repositories\ThreadReadRepository;
use Railroad\Railforums\Repositories\ThreadFollowRepository;
use Railroad\Railforums\Repositories\PostRepository;
use Railroad\Railforums\Services\ConfigService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserForumThreadController extends Controller
{
    /**
     * @var UserCloakDataMapper
     */
    protected $userCloakDataMapper;

    /**
     * @var ThreadRepository
     */
    protected $threadRepository;

    /**
     * @var ThreadReadRepository
     */
    protected $threadReadRepository;

    /**
     * @var ThreadFollowRepository
     */
    protected $threadFollowRepository;

    /**
     * @var PostRepository
     */
    protected $postRepository;

    /**
     * @var PermissionService
     */
    private $permissionService;

    /**
     * UserForumThreadController constructor.
     *
     * @param UserCloakDataMapper $userCloakDataMapper
     * @param ThreadRepository $threadRepository
     * @param ThreadReadRepository $threadReadRepository
     * @param ThreadFollowRepository $threadFollowRepository
     * @param PostRepository $postRepository
     * @param PermissionService $permissionService
     */
    public function __construct(
        UserCloakDataMapper $userCloakDataMapper,
        ThreadRepository $threadRepository,
        ThreadReadRepository $threadReadRepository,
        ThreadFollowRepository $threadFollowRepository,
        PostRepository $postRepository,
        PermissionService $permissionService
    ) {
        $this->userCloakDataMapper = $userCloakDataMapper;
        $this->threadRepository = $threadRepository;
        $this->threadReadRepository = $threadReadRepository;
        $this->threadFollowRepository = $threadFollowRepository;
        $this->postRepository = $postRepository;
        $this->permissionService = $permissionService;

        $this->middleware(ConfigService::$controllerMiddleware);
    }

    /**
     * @param Request $request
     * @param integer $id
     *
     * @return RedirectResponse
     */
    public function read(Request $request, $id)
    {
        if (!$this->permissionService->can(auth()->id(), 'read-threads')) {
            throw new NotFoundHttpException();
        }

        $thread = $this->threadRepository->read($id);

        if (!$thread) {
            throw new NotFoundHttpException();
        }

        $now = Carbon::now()->toDateTimeString();

        $threadRead = $this->threadReadRepository->create([
            'thread_id' => $thread->id,
            'reader_id' => $this->userCloakDataMapper->getCurrentId(),
            'read_on' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return reply()->form();
    }

    /**
     * @param Request $request
     * @param integer $id
     *
     * @return RedirectResponse
     */
    public function follow(Request $request, $id)
    {
        if (!$this->permissionService->can(auth()->id(), 'follow-threads')) {
            throw new NotFoundHttpException();
        }

        $thread = $this->threadRepository->read($id);

        if (!$thread) {
            throw new NotFoundHttpException();
        }

        $now = Carbon::now()->toDateTimeString();

        $threadFollow = $this->threadFollowRepository->create([
            'thread_id' => $thread->id,
            'follower_id' => $this->userCloakDataMapper->getCurrentId(),
            'followed_on' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return reply()->form();
    }

    /**
     * @param Request $request
     * @param integer $id
     *
     * @return RedirectResponse
     */
    public function unfollow(Request $request, $id)
    {
        if (!$this->permissionService->can(auth()->id(), 'follow-threads')) {
            throw new NotFoundHttpException();
        }

        $threadFollow = $this->threadFollowRepository->read($id);

        if (!$threadFollow) {
            throw new NotFoundHttpException();
        }

        $this->threadFollowRepository->destroy($threadFollow->id);

        return reply()->form();
    }

    /**
     * @param ThreadCreateRequest $request
     *
     * @return RedirectResponse
     */
    public function store(ThreadCreateRequest $request)
    {
        if (!$this->permissionService->can(auth()->id(), 'create-threads')) {
            throw new NotFoundHttpException();
        }

        $now = Carbon::now()->toDateTimeString();
        $authorId = $this->userCloakDataMapper->getCurrentId();

        $thread = $this->threadRepository->create(
            array_merge(
                $request->only(
                    [
                        'title',
                        'category_id',
                    ]
                ),
                [
                    'author_id' => $authorId,
                    'slug' => ThreadRepository::sanitizeForSlug(
                                $request->get('title')
                            ),
                    'state' => ThreadRepository::STATE_PUBLISHED,
                    'published_on' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            )
        );

        $this->postRepository->create(
            [
                'thread_id' => $thread->id,
                'author_id' => $authorId,
                'content' => $request->get('first_post_content'),
                'state' => PostRepository::STATE_PUBLISHED,
                'published_on' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        // todo: temporary
        $temporaryRedirectLocation = '/members/forums/thread/' . $thread->id;

        return reply()->form([true], $temporaryRedirectLocation);

        // return reply()->form();
    }

    /**
     * @param ThreadUpdateRequest $request
     * @param integer $id
     *
     * @return RedirectResponse
     */
    public function update(ThreadUpdateRequest $request, $id)
    {
        if (!$this->permissionService->can(auth()->id(), 'update-threads')) {
            throw new NotFoundHttpException();
        }

        $thread = $this->threadRepository->update(
            $id,
            array_merge(
                $this->permissionService->columns(
                    auth()->id(),
                    'update-threads',
                    $request->all(),
                    ['title']
                ),
                [
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            )
        );

        if (!$thread) {
            throw new NotFoundHttpException();
        }

        return reply()->form();
    }
}
