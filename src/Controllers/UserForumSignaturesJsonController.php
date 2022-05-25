<?php

namespace Railroad\Railforums\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Railroad\Permissions\Exceptions\NotAllowedException;
use Railroad\Permissions\Services\PermissionService;
use Railroad\Railforums\Repositories\UserSignaturesRepository;
use Railroad\Railforums\Requests\SignatureJsonCreateRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class UserForumSignaturesJsonController extends Controller
{
    /**
     * @var UserSignaturesRepository
     */
    protected $userSignaturesRepository;

    /**
     * @var PermissionService
     */
    private $permissionService;

    /**
     * UserForumSignaturesJsonController constructor.
     *
     * @param PermissionService $permissionService
     * @param UserSignaturesRepository $userSignaturesRepository
     */
    public function __construct(
        PermissionService $permissionService,
        UserSignaturesRepository $userSignaturesRepository
    ) {
        $this->permissionService = $permissionService;
        $this->userSignaturesRepository = $userSignaturesRepository;
    }

    /**
     * @param SignatureJsonCreateRequest $request
     * @return JsonResponse
     * @throws NotAllowedException
     */
    public function store(SignatureJsonCreateRequest $request)
    {
        $this->permissionService->canOrThrow(auth()->id(), 'create-user-signature');

        $now =
            Carbon::now()
                ->toDateTimeString();

        $signature = $this->userSignaturesRepository->create(
            array_merge(
                $request->only(
                    [
                        'signature',
                    ]
                ),
                [
                    'brand' => config('railforums.brand'),
                    'user_id' => auth()->id(),
                    'created_at' => $now,
                ]
            )
        );

        return response()->json($signature);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws NotAllowedException
     * @throws Throwable
     */
    public function update(Request $request, $id)
    {
        $oldSignature = $this->userSignaturesRepository->getUserSignature($id);

        if ($oldSignature && $oldSignature['user_id'] != auth()->id()) {
            $this->permissionService->canOrThrow(auth()->id(), 'update-user-signature');
        }

        $signature = $this->userSignaturesRepository->updateOrCreate(
            ['user_id' => $id],
            [
                'signature' => $request->get('signature'),
                'updated_at' => Carbon::now()
                    ->toDateTimeString(),
                'brand' => config('railforums.brand'),
                'user_id' => $id,
            ]
        );

         return response()->json($signature);
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function delete($id)
    {
        $signature = $this->userSignaturesRepository->read($id);
        throw_if(!$signature, new NotFoundHttpException());

        if ($signature['user_id'] != auth()->id()) {
            $this->permissionService->canOrThrow(auth()->id(), 'delete-user-signature');
        }

        $result = $this->userSignaturesRepository->destroy($id);
        throw_if(!$result, new NotFoundHttpException());

        return new JsonResponse(null, 204);
    }
}
