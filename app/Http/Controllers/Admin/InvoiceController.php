<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MarkPaidRequest;
use App\Http\Requests\StoreFileRequest;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\File;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\ShareLink;
use App\Services\FileService;
use App\Services\InvoiceService;
use App\Services\PaymentService;
use App\Services\ShareLinkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoiceController extends Controller
{
    public function __construct(
        private InvoiceService $invoiceService,
        private PaymentService $paymentService,
        private FileService $fileService,
        private ShareLinkService $shareLinkService,
    ) {}

    public function index(Request $request): View
    {
        $query = Invoice::with('project')->latest();

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->integer('project_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        return view('admin.invoices.index', [
            'invoices' => $query->paginate(20),
            'projects' => Project::orderBy('name')->get(),
            'statuses' => Invoice::$statuses,
        ]);
    }

    public function create(Request $request): View
    {
        $project = null;

        if ($request->filled('project_id')) {
            $project = Project::findOrFail($request->integer('project_id'));
        }

        return view('admin.invoices.create', [
            'project' => $project,
            'projects' => Project::orderBy('name')->get(),
            'languages' => Invoice::$languages,
        ]);
    }

    public function store(StoreInvoiceRequest $request): RedirectResponse
    {
        $invoice = $this->invoiceService->create($request->validated());

        return redirect()
            ->route('admin.invoices.show', $invoice)
            ->with('success', 'Invoice created.');
    }

    public function show(Invoice $invoice): View
    {
        return view('admin.invoices.show', [
            'invoice' => $invoice->load('project', 'items', 'payments', 'files', 'shareLinks'),
        ]);
    }

    public function edit(Invoice $invoice): View
    {
        return view('admin.invoices.edit', [
            'invoice' => $invoice->load('items', 'project'),
            'project' => $invoice->project,
            'projects' => Project::orderBy('name')->get(),
            'languages' => Invoice::$languages,
        ]);
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice): RedirectResponse
    {
        $this->invoiceService->update($invoice, $request->validated());

        return redirect()
            ->route('admin.invoices.show', $invoice)
            ->with('success', 'Invoice updated.');
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        $this->invoiceService->delete($invoice);

        return redirect()
            ->route('admin.invoices.index')
            ->with('success', 'Invoice deleted.');
    }

    public function preview(Invoice $invoice): View
    {
        return view('admin.invoices.preview', [
            'invoice' => $invoice->load('project', 'items', 'payments', 'files'),
        ]);
    }

    public function generatePdf(Invoice $invoice): RedirectResponse
    {
        $this->invoiceService->generatePdf($invoice);

        return redirect()
            ->route('admin.invoices.show', $invoice)
            ->with('success', 'Invoice PDF generated.');
    }

    public function downloadPdf(Invoice $invoice): StreamedResponse
    {
        return $this->invoiceService->streamPdf($invoice);
    }

    public function downloadFile(Invoice $invoice, File $file): StreamedResponse
    {
        abort_if($file->invoice_id !== $invoice->id, 404);

        return Storage::disk('private')->response($file->path, $file->original_filename);
    }

    public function changeStatus(Request $request, Invoice $invoice): RedirectResponse
    {
        $request->validate([
            'status' => ['required', Rule::in(Invoice::$statuses)],
        ]);

        $this->invoiceService->changeStatus($invoice, $request->string('status'));

        return redirect()
            ->route('admin.invoices.show', $invoice)
            ->with('success', 'Invoice status updated.');
    }

    public function markPaid(MarkPaidRequest $request, Invoice $invoice): RedirectResponse
    {
        $this->paymentService->markAsPaid($invoice, $request->validated());

        return redirect()
            ->route('admin.invoices.show', $invoice)
            ->with('success', 'Invoice marked as paid.');
    }

    public function storeFile(StoreFileRequest $request, Invoice $invoice): RedirectResponse
    {
        $this->fileService->storeForInvoice($invoice, $request->file('file'), $request->string('type'));

        return redirect()
            ->route('admin.invoices.show', $invoice)
            ->with('success', 'Document uploaded.');
    }

    public function deleteFile(Invoice $invoice, File $file): RedirectResponse
    {
        $this->fileService->delete($file);

        return redirect()
            ->route('admin.invoices.show', $invoice)
            ->with('success', 'Document deleted.');
    }

    public function createShare(Invoice $invoice): RedirectResponse
    {
        $result = $this->shareLinkService->createForInvoice($invoice);

        return redirect()
            ->route('admin.invoices.show', $invoice)
            ->with('share_link', $result['link'])
            ->with('share_password', $result['password'])
            ->with('success', 'Share link created.');
    }

    public function revokeShare(Invoice $invoice, ShareLink $shareLink): RedirectResponse
    {
        $this->shareLinkService->revoke($shareLink);

        return redirect()
            ->route('admin.invoices.show', $invoice)
            ->with('success', 'Share link revoked.');
    }
}
