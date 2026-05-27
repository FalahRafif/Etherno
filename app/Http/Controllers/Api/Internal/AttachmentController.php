<?php

namespace App\Http\Controllers\Api\Internal;

use App\Enums\RoleName;
use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\User;
use App\Services\AttachmentSecurityService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AttachmentController extends Controller
{
    public function __construct(private AttachmentSecurityService $attachmentSecurityService)
    {
    }

    public function show(Request $request, string $attachmentUuid): Response
    {
        if (!$request->hasValidSignature()) {
            abort(403);
        }

        $user = $request->user();
        if (
            !$user instanceof User ||
            !$user->hasRole([RoleName::Admin->value, RoleName::Petugas->value])
        ) {
            abort(403);
        }

        $attachment = Attachment::query()
            ->where('uuid', $attachmentUuid)
            ->firstOrFail();

        return $this->attachmentSecurityService->buildInlineImageResponse($attachment);
    }
}
