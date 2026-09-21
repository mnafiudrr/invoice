<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; color: #111827; margin: 0; }
        .invoice { position: relative; max-width: 720px; margin: 40px auto; padding: 0 24px; box-sizing: border-box; }

        .head { width: 100%; }
        .head .label { font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #9ca3af; margin: 0; }
        .head .number { font-size: 24px; font-weight: 600; color: #111827; margin: 4px 0 0; }
        .head .date { font-size: 14px; color: #6b7280; margin: 4px 0 0; }

        .parties { width: 100%; margin-top: 24px; border-top: 1px solid #f3f4f6; padding-top: 24px; }
        .parties .col-label { font-size: 12px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em; color: #9ca3af; margin: 0; }
        .parties .company { font-size: 14px; font-weight: 600; color: #111827; margin: 8px 0 0; }
        .parties .line { font-size: 14px; color: #4b5563; margin: 4px 0 0; }

        .items-wrap { margin-top: 24px; border-top: 1px solid #f3f4f6; padding-top: 24px; }
        .items { width: 100%; border-collapse: collapse; }
        .items th { font-size: 12px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; padding: 8px 16px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        .items th.num { text-align: right; }
        .items td { font-size: 14px; padding: 12px 16px; border-bottom: 1px solid #f3f4f6; }
        .items td.desc { color: #111827; }
        .items td.num { text-align: right; color: #4b5563; }
        .items td.amt { color: #111827; }

        .totals { width: 300px; margin-left: auto; margin-top: 16px; font-size: 14px; }
        .totals .row { width: 100%; }
        .totals .row td { padding: 4px 0; }
        .totals .k { color: #6b7280; }
        .totals .v { text-align: right; font-weight: 500; }
        .totals .row.total td { border-top: 1px solid #e5e7eb; padding-top: 8px; }
        .totals .total .k, .totals .total .v { font-weight: 700; color: #111827; }

        .box { margin-top: 24px; background: #f9fafb; border-radius: 8px; padding: 16px; }
        .box .box-label { font-size: 12px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; margin: 0; }
        .box .box-body { font-size: 14px; color: #374151; margin: 4px 0 0; white-space: pre-line; }

        .thanks { margin-top: 24px; font-size: 14px; color: #9ca3af; }

        .paid-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: 10; display: table; width: 100%; height: 100%; text-align: center; }
        .paid-stamp-cell { display: table-cell; vertical-align: middle; }
        .paid-stamp { display: inline-block; color: rgba(22, 163, 74, 0.3); border: 9px solid rgba(22, 163, 74, 0.3); font-weight: 700; font-size: 102px; letter-spacing: 9px; text-transform: uppercase; padding: 12px 42px; border-radius: 12px; transform: translateY(225px) rotate(-25deg); -webkit-transform: translateY(225px) rotate(-25deg); }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>