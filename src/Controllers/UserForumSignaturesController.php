<?php

namespace Railroad\Railforums\Controllers;

use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Railroad\Permissions\Exceptions\NotAllowedException;
use Railroad\Permissions\Services\PermissionService;
use Railroad\Railforums\Repositories\UserSignaturesRepository;
use Railroad\Railforums\Requests\SignatureCreateRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class UserForumSignaturesController extends Controller
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
     * UserForumSignaturesController constructor.
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
     * @param SignatureCreateRequest $request
     * @return RedirectResponse
     * @throws NotAllowedException
     */
    public function store(SignatureCreateRequest $request)
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

        $message = ['success' => true];

        return $request->has('redirect') ?
            redirect()
                ->away($request->get('redirect'))
                ->with($message) :
            redirect()
                ->back()
                ->with($message);
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
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
            ['id' => $oldSignature['id']],
            [
                'signature' => $request->get('signature'),
                'updated_at' => Carbon::now()
                    ->toDateTimeString(),
            ]
        );

        $message = ['success' => true];

        return $request->has('redirect') ?
            redirect()
                ->away($request->get('redirect'))
                ->with($message) :
            redirect()
                ->back()
                ->with($message);
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     * @throws Throwable
     */
    public function delete(Request $request, $id)
    {
        $signature = $this->userSignaturesRepository->read($id);
        throw_if(!$signature, new NotFoundHttpException());

        if ($signature['user_id'] != auth()->id()) {
            $this->permissionService->canOrThrow(auth()->id(), 'delete-user-signature');
        }

        $result = $this->userSignaturesRepository->destroy($id);
        throw_if(!$result, new NotFoundHttpException());

        $message = ['success' => true];

        return $request->has('redirect') ?
            redirect()
                ->away($request->get('redirect'))
                ->with($message) :
            redirect()
                ->back()
                ->with($message);
    }
}
