<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; color: #111827; margin: 0; }
        .invoice { max-width: 720px; margin: 40px auto; padding: 0 24px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #111827; padding-bottom: 24px; }
        .header h1 { margin: 0; font-size: 28px; letter-spacing: 2px; text-transform: uppercase; }
        .number { font-size: 16px; font-weight: 600; margin: 6px 0 0; }
        .date { font-size: 12px; color: #6b7280; margin: 2px 0 0; }
        .paid-stamp { display: inline-block; color: rgba(22, 163, 74, 0.6); border: 15px solid rgba(22, 163, 74, 0.6); font-weight: 700; font-size: 170px; letter-spacing: 15px; text-transform: uppercase; padding: 20px 70px; border-radius: 20px; transform: rotate(-25deg); -webkit-transform: rotate(-25deg); }
        .parties { display: flex; justify-content: space-between; margin-top: 24px; }
        .parties h2 { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #6b7280; margin: 0 0 6px; }
        .parties p { margin: 2px 0; font-size: 13px; }
        .items { width: 100%; border-collapse: collapse; margin-top: 32px; }
        .items th { text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #6b7280; border-bottom: 1px solid #d1d5db; padding: 8px 6px; }
        .items th.num, .items td.num { text-align: right; }
        .items td { font-size: 13px; padding: 10px 6px; border-bottom: 1px solid #f3f4f6; }
        .totals { margin-left: auto; width: 280px; margin-top: 24px; }
        .totals .row { display: flex; justify-content: space-between; font-size: 13px; padding: 4px 0; }
        .totals .row.total { border-top: 2px solid #111827; margin-top: 6px; padding-top: 8px; font-weight: 700; font-size: 15px; }
        .notes { margin-top: 32px; }
        .notes h2 { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #6b7280; margin: 0 0 6px; }
        .notes p { font-size: 13px; margin: 0; }
        .thanks { margin-top: 48px; font-size: 13px; color: #6b7280; }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>