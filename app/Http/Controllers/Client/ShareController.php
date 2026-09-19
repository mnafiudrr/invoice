<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\ShareLink;
use App\Services\ShareLinkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ShareController extends Controller
{
    public function __construct(private ShareLinkService $shareLinkService) {}

    public function show(ShareLink $shareLink): View
    {
        abort_if($shareLink->isExpired(), 404);

        if (session("share_access.{$shareLink->token}") !== true) {
            return view('public.share-password', ['shareLink' => $shareLink]);
        }

        return view('public.share-invoice', [
            'shareLink' => $shareLink,
            'invoice' => $shareLink->invoice->load('project', 'items', 'payments', 'files'),
        ]);
    }

    public function showPassword(ShareLink $shareLink): View
    {
        abort_if($shareLink->isExpired(), 404);

        return view('public.share-password', ['shareLink' => $shareLink]);
    }

    public function checkPassword(Request $request, ShareLink $shareLink): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        if (! $this->shareLinkService->verify($shareLink, $request->string('password'))) {
            return back()
                ->withErrors(['password' => 'Incorrect password.'])
                ->onlyInput('password');
        }

        session(["share_access.{$shareLink->token}" => true]);

        return redirect()->route('shares.show', $shareLink);
    }

    public function pdf(ShareLink $shareLink): StreamedResponse
    {
        $invoice = $shareLink->invoice;

        abort_if($invoice === null, 404);

        $file = $invoice->files()
            ->where('type', File::TYPE_INVOICE)
            ->latest()
            ->first();

        abort_if($file === null, 404, 'No PDF generated for this invoice yet.');

        return Storage::disk('private')->response($file->path, $file->original_filename, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$file->original_filename.'"',
        ]);
    }

    public function file(ShareLink $shareLink, File $file): StreamedResponse
    {
        $invoice = $shareLink->invoice;

        abort_if($invoice === null || $file->invoice_id !== $invoice->id, 404);

        return Storage::disk('private')->response($file->path, $file->original_filename);
    }
}
