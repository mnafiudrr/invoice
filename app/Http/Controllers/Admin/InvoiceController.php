<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\Invoice;
use App\Models\Project;
use App\Services\InvoiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoiceController extends Controller
{
    public function __construct(private InvoiceService $invoiceService) {}

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

    public function create(): View
    {
        return view('admin.invoices.create', [
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
            'invoice' => $invoice->load('project', 'items', 'payments', 'files'),
        ]);
    }

    public function edit(Invoice $invoice): View
    {
        return view('admin.invoices.edit', [
            'invoice' => $invoice->load('items'),
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
        return view('pdf.invoice', [
            'invoice' => $invoice->load('project', 'items'),
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
}
