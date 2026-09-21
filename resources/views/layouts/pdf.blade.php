<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; color: #111827; margin: 0; background: #ffffff; }
        .sheet { width: 100%; padding: 28px 28px; box-sizing: border-box; }
        .top-line { width: 100%; height: 2px; background: #000; margin-bottom: 22px; }
        .parties { display: flex; justify-content: space-between; }
        .from-block p { margin: 2px 0; font-size: 9.2pt; }
        .company { font-size: 14.5pt; font-weight: bold; margin: 0 0 2px; }
        .bill-to { text-align: right; }
        .bill-to p { margin: 2px 0; font-size: 9.2pt; }
        .bill-to .label { font-weight: bold; margin-bottom: 2px; }

        .meta { display: flex; justify-content: flex-end; gap: 0; margin-top: 30px; }
        .meta .col { text-align: right; margin-left: 40px; }
        .meta .col p { margin: 2px 0; font-size: 9.2pt; }
        .meta .col .k { font-weight: bold; }
        .meta .col .v { }

        .items { width: 100%; border-collapse: collapse; margin-top: 26px; table-layout: fixed; }
        .items th { background: #f5f5f5; border: 1px solid #555555; font-size: 9.2pt; text-align: left; padding: 6px 8px; }
        .items th.num, .items td.num { text-align: right; }
        .items td { font-size: 9.2pt; border: 1px solid #555555; padding: 6px 8px; }
        .items th.desc { width: 60%; }
        .items th.qty { width: 10%; }
        .items th.price { width: 12%; }
        .items th.amt { width: 18%; }

        .totals { margin-top: 10px; }
        .totals .row { display: flex; justify-content: flex-end; font-size: 9.2pt; padding: 3px 0; }
        .totals .row .k { width: 90px; text-align: right; font-weight: bold; }
        .totals .row .v { width: 120px; text-align: right; }
        .totals .row.total { border-top: 1px solid #000; margin-top: 4px; padding-top: 6px; }

        .notes, .terms { margin-top: 22px; font-size: 9.2pt; }
        .notes h3, .terms h3 { font-size: 9.2pt; margin: 0 0 4px; }
        .notes p, .terms p { margin: 2px 0; white-space: pre-line; }

        .paid-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: 10; display: table; width: 100%; height: 100%; text-align: center; }
        .paid-stamp-cell { display: table-cell; vertical-align: middle; }
        .paid-stamp { display: inline-block; color: rgba(22, 163, 74, 0.3); border: 9px solid rgba(22, 163, 74, 0.3); font-weight: 700; font-size: 102px; letter-spacing: 9px; text-transform: uppercase; padding: 12px 42px; border-radius: 12px; transform: translateY(150px) rotate(-25deg); -webkit-transform: translateY(150px) rotate(-25deg); }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>