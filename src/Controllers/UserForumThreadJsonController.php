<?php

namespace Railroad\Railforums\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Railroad\Permissions\Services\PermissionService;
use Railroad\Railforums\Decorators\EmojiesDecorator;
use Railroad\Railforums\Repositories\PostRepository;
use Railroad\Railforums\Repositories\ThreadFollowRepository;
use Railroad\Railforums\Repositories\ThreadReadRepository;
use Railroad\Railforums\Repositories\ThreadRepository;
use Railroad\Railforums\Requests\ThreadJsonCreateRequest;
use Railroad\Railforums\Requests\ThreadJsonIndexRequest;
use Railroad\Railforums\Requests\ThreadJsonUpdateRequest;
use Railroad\Railforums\Responses\JsonPaginatedResponse;
use Railroad\Railforums\Services\ConfigService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Railroad\Railforums\Contracts\UserProviderInterface;


class UserForumThreadJsonController extends Controller
{
    const AMOUNT = 10;
    const PAGE = 1;

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
     * @var EmojiesDecorator
     */
    private $emojiesDecorator;

    protected UserProviderInterface $userProvider;

    /**
     * @param ThreadRepository $threadRepository
     * @param ThreadReadRepository $threadReadRepository
     * @param ThreadFollowRepository $threadFollowRepository
     * @param PostRepository $postRepository
     * @param PermissionService $permissionService
     * @param EmojiesDecorator $emojiesDecorator
     */
    public function __construct(
        ThreadRepository $threadRepository,
        ThreadReadRepository $threadReadRepository,
        ThreadFollowRepository $threadFollowRepository,
        PostRepository $postRepository,
        PermissionService $permissionService,
        EmojiesDecorator $emojiesDecorator,
        UserProviderInterface $userProvider
    ) {
        $this->threadRepository = $threadRepository;
        $this->threadReadRepository = $threadReadRepository;
        $this->threadFollowRepository = $threadFollowRepository;
        $this->postRepository = $postRepository;
        $this->permissionService = $permissionService;
        $this->emojiesDecorator = $emojiesDecorator;
        $this->userProvider = $userProvider;

        $this->middleware(ConfigService::$controllerMiddleware);
    }

    /**
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function read($id)
    {
        $this->permissionService->canOrThrow(auth()->id(), 'read-threads');

        PostRepository::$blockedUserIds =  $this->userProvider->getBlockedUsers();

        $thread = $this->threadRepository->read($id);

        if (!$thread) {
            throw new NotFoundHttpException();
        }

        $threadRead = $this->threadReadRepository->markRead(
            $thread->id,
            auth()->id()
        );

        return response()->json($threadRead);
    }

    /**
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function follow($id)
    {
        $this->permissionService->canOrThrow(auth()->id(), 'follow-threads');

        $thread = $this->threadRepository->read($id);

        if (empty($thread)) {
            throw new NotFoundHttpException();
        }

        $now =
            Carbon::now()
                ->toDateTimeString();

        $threadFollow = $this->threadFollowRepository->create([
            'thread_id' => $id,
            'follower_id' => auth()->id(),
            'followed_on' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return response()->json($threadFollow);
    }

    /**
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function unfollow($id)
    {
        $this->permissionService->canOrThrow(auth()->id(), 'follow-threads');

        $thread = $this->threadRepository->read($id);
        $currentUserId = auth()->id();

        if (empty($thread) || empty($currentUserId)) {
            throw new NotFoundHttpException();
        }

        $this->threadFollowRepository->query()
            ->where([
                'thread_id' => $id,
                'follower_id' => $currentUserId,
            ])
            ->delete();

        return new JsonResponse(null, 204);
    }

    /**
     * @param ThreadJsonIndexRequest $request
     *
     * @return JsonResponse
     */
    public function index(ThreadJsonIndexRequest $request)
    {
        $this->permissionService->canOrThrow(auth()->id(), 'index-threads');

        $amount = $request->get('amount') ? (int)$request->get('amount') : self::AMOUNT;
        $page = $request->get('page') ? (int)$request->get('page') : self::PAGE;
        $categoryId = $request->get('category_id', null);
        $pinned = (boolean)$request->get('pinned');
        $followed = $request->has('followed') ? (boolean)$request->get('followed') : null;
        $sortBy = $request->get('sort', '-last_post_published_on');

        PostRepository::$blockedUserIds =  $this->userProvider->getBlockedUsers();

        if($request->get('tabs', false)  || $request->get('tab')){
            $tabs = $request->get('tabs',$request->get('tab'));

            if(!is_array($request->get('tabs', $request->get('tab')))){
                $tabs = [$request->get('tabs',$request->get('tab'))];
            }

            foreach($tabs as $tab) {
                $extra = explode(',', $tab);
                if ($extra['0'] == 'followed') {
                    $followed = (boolean)$extra['1'];
                }elseif ($extra['0'] == 'all') {
                    $followed = null;
                }
            }
        }

        $pinnedThreads = $this->threadRepository->getDecoratedThreads(
            $amount,
            $page,
            ($categoryId) ? [$categoryId] : [],
            true,
            $followed
        )
            ->toArray();

        $threads = $this->threadRepository->getDecoratedThreads(
            $amount,
            $page,
            ($categoryId) ? [$categoryId] : [],
            $pinned,
            $followed,
            $sortBy
        )
            ->toArray();

        $threadsCount = $this->threadRepository->getThreadsCount(
            ($categoryId) ? [$categoryId] : [],
            $pinned,
            $followed
        );

        return new JsonPaginatedResponse(
            array_merge($pinnedThreads, $threads), $threadsCount, null, 200
        );
    }

    /**
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function show($id, Request $request)
    {
        //$this->permissionService->canOrThrow(auth()->id(), 'show-threads');
        PostRepository::$blockedUserIds =  $this->userProvider->getBlockedUsers();

        $thread =
            $this->threadRepository->getDecoratedThreadsByIds([$id])
                ->first();

        if (!$thread) {
            throw new NotFoundHttpException();
        }

        $amount = $request->get('amount', 20);
        $page = $request->get('page', 1);
        $sortBy = $request->get('sort', 'published_on');

        $posts = $this->postRepository->getDecoratedPosts($amount, $page, $id, $sortBy);
        $this->emojiesDecorator->decorate($posts);

        $thread['posts'] = $posts;
        $thread['page'] = $page;

        return response()->json($thread);
    }

    /**
     * @param ThreadJsonCreateRequest $request
     *
     * @return JsonResponse
     */
    public function store(ThreadJsonCreateRequest $request)
    {
        $this->permissionService->canOrThrow(auth()->id(), 'create-threads');

        $now =
            Carbon::now()
                ->toDateTimeString();
        $authorId = auth()->id();

        $thread = $this->threadRepository->create(
            array_merge($request->only([
                'title',
                'category_id',
            ]), [
                    'author_id' => $authorId,
                    'slug' => ThreadRepository::sanitizeForSlug(
                        $request->get('title')
                    ),
                    'state' => ThreadRepository::STATE_PUBLISHED,
                    'published_on' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
        );

        $this->postRepository->create([
            'thread_id' => $thread->id,
            'author_id' => $authorId,
            'content' => $request->get('first_post_content'),
            'state' => PostRepository::STATE_PUBLISHED,
            'published_on' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return $this->show($thread->id, $request);
    }

    /**
     * @param ThreadJsonUpdateRequest $request
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function update(ThreadJsonUpdateRequest $request, $id)
    {
        $thread = $this->threadRepository->read($id);

        if (!$thread) {
            throw new NotFoundHttpException();
        }

        if ($thread['author_id'] != auth()->id()) {
            $this->permissionService->canOrThrow(auth()->id(), 'update-threads');
        }

        $thread = $this->threadRepository->update(
            $id,
            array_merge($this->permissionService->columns(auth()->id(), 'update-threads', $request->all(), ['title']), [
                'updated_at' => Carbon::now()
                    ->toDateTimeString(),
            ])
        );

        return response()->json($thread);
    }

    /**
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function delete($id)
    {
        $thread = $this->threadRepository->read($id);

        if (!$thread) {
            throw new NotFoundHttpException();
        }

        if ($thread['author_id'] != auth()->id()) {
            $this->permissionService->canOrThrow(auth()->id(), 'delete-threads');
        }

        $result = $this->threadRepository->delete($id);

        if (!$result) {
            throw new NotFoundHttpException();
        }

        return new JsonResponse(null, 204);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function jumpToPost($id)
    {
        PostRepository::$blockedUserIds =  $this->userProvider->getBlockedUsers();

        $post = $this->postRepository->read($id);
        if (!$post) {
            throw new NotFoundHttpException();
        }

        $thread = $this->threadRepository->read($post->thread_id);

        $allPostIdsInThread = collect(
            $this->postRepository->getAllPostIdsInThread($post->thread_id)
        )
            ->pluck('id')
            ->all();
        $postPositionInThread = array_search($post->id, $allPostIdsInThread);
        $request = new \Illuminate\Http\Request();

        $request->replace([
            'amount' => 10,
            'page' => ceil(($postPositionInThread + 1) / 10),
        ]);

        return $this->show($thread->id, $request);
    }

    public function getForumRules()
    {
        return $this->jumpToPost(config('railforums.forum_rules_post_id.'.config('railforums.brand'), 1));
    }

    /**
     * @param ThreadJsonIndexRequest $request
     *
     * @return JsonResponse
     */
    public function latest(ThreadJsonIndexRequest $request)
    {
        $this->permissionService->canOrThrow(auth()->id(), 'index-threads');

        $amount = $request->get('amount') ? (int)$request->get('amount') : self::AMOUNT;
        $page = $request->get('page') ? (int)$request->get('page') : self::PAGE;

        $sortBy = $request->get('sort', '-last_post_published_on');

        PostRepository::$blockedUserIds =  $this->userProvider->getBlockedUsers();

        $threads = $this->threadRepository->getDecoratedThreads($amount, $page, [], null, null, $sortBy);
        $threadsCount = $this->threadRepository->getThreadsCount([]);

        return new JsonPaginatedResponse(
            $threads, $threadsCount, null, 200
        );
    }
}
