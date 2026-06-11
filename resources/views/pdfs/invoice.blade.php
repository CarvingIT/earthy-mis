<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tax Invoice - {{ $invoice->invoice_number }}</title>
    <style>
        @page {
            size: A4;
            margin: 20mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #1a1a1a;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .table-borderless td {
            border: none;
            padding: 2px 0;
            vertical-align: top;
        }
        .invoice-table {
            border: 1px solid #000000;
        }
        .invoice-table th, .invoice-table td {
            border: 1px solid #000000;
            padding: 8px 10px;
            text-align: left;
            vertical-align: middle;
        }
        .invoice-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .text-right {
            text-align: right !important;
        }
        .text-center {
            text-align: center !important;
        }
        .font-bold {
            font-weight: bold;
        }
        .header-title {
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 1px;
            margin: 0 0 10px 0;
            text-transform: uppercase;
        }
        .company-name {
            font-size: 13px;
            font-weight: bold;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .section-title {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background-color: #f2f2f2;
            padding: 4px 6px;
            border: 1px solid #000000;
            margin-bottom: 5px;
        }
        .info-block {
            border: 1px solid #000000;
            padding: 8px;
            min-height: 90px;
        }
        .bank-details td {
            padding: 2px 0;
        }
        .footer-declaration {
            font-size: 9px;
            line-height: 1.3;
            color: #444444;
        }
        .signature-space {
            margin-top: 40px;
            border-top: 1px dashed #000000;
            padding-top: 5px;
            width: 180px;
            float: right;
            text-align: center;
        }
        .clear {
            clear: both;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Main Outer Layout -->
        <table class="table-borderless">
            <tr>
                <!-- Left Company Address -->
                <td style="width: 55%; padding-right: 20px;">
                    <div class="company-name">Earthy Companions Services Pvt. Ltd. FIWM</div>
                    <div style="margin-bottom: 10px;">
                        Flat No. C-402, S. No. 43/78,79,80,82,<br>
                        Sai Leela Manaji Nagar, Narhe, Pune - 411041<br>
                        <strong>Contact:</strong> M. No. 8412037640 | Ecspl.Fiwm@gmail.com<br>
                        <strong>GSTIN:</strong> 27AAHCE5853F1ZI<br>
                        <strong>State:</strong> Maharashtra (Code: 27)
                    </div>
                </td>
                
                <!-- Right Invoice Details -->
                <td style="width: 45%; text-align: right;">
                    <div class="header-title">Tax Invoice</div>
                    <table class="table-borderless" style="margin-top: 5px; float: right; width: auto;">
                        <tr>
                            <td class="font-bold" style="padding-right: 15px; text-align: right;">Invoice No:</td>
                            <td style="text-align: left;">{{ $invoice->invoice_number }}</td>
                        </tr>
                        <tr>
                            <td class="font-bold" style="padding-right: 15px; text-align: right;">Invoice Date:</td>
                            <td style="text-align: left;">{{ now()->format('d-M-y') }}</td>
                        </tr>
                        <tr>
                            <td class="font-bold" style="padding-right: 15px; text-align: right;">Billing Month:</td>
                            <td style="text-align: left;">{{ Carbon\Carbon::parse($invoice->billing_month . '-01')->format('F Y') }}</td>
                        </tr>
                        <tr>
                            <td class="font-bold" style="padding-right: 15px; text-align: right;">Destination:</td>
                            <td style="text-align: left;">A/P KODIT, PURANDAR, PUNE</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Billed To Section -->
        <table class="table-borderless" style="margin-top: 10px;">
            <tr>
                <td style="width: 100%;">
                    <div class="section-title">Billed To</div>
                    <div class="info-block">
                        <table class="table-borderless" style="margin: 0;">
                            <tr>
                                <td class="font-bold" style="width: 15%;">Society Name:</td>
                                <td style="width: 45%;">{{ $society->name }}</td>
                                <td class="font-bold" style="width: 15%;">Contact Name:</td>
                                <td style="width: 25%;">{{ $society->chairman_name ?: ($society->secretary_name ?: 'N/A') }}</td>
                            </tr>
                            <tr>
                                <td class="font-bold">Address:</td>
                                <td>{{ $society->address }}, {{ $society->city }}</td>
                                <td class="font-bold">Contact No:</td>
                                <td>{{ $society->phone ?: 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="font-bold">Email:</td>
                                <td>{{ $society->contact_person_email ?: 'N/A' }}</td>
                                <td class="font-bold">Vehicle No:</td>
                                <td>{{ $society->vehicle_number ?: 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Items Table -->
        <table class="invoice-table" style="margin-top: 15px;">
            <thead>
                <tr>
                    <th class="text-center" style="width: 8%;">Sl No.</th>
                    <th style="width: 47%;">Description of Services</th>
                    <th class="text-center" style="width: 12%;">HSN/SAC</th>
                    <th class="text-center" style="width: 11%;">Qty (Flats)</th>
                    <th class="text-right" style="width: 11%;">Rate/Flat</th>
                    <th class="text-right" style="width: 11%;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">1</td>
                    <td class="font-bold">Transport Charges - Waste Collection</td>
                    <td class="text-center">996791</td>
                    <td class="text-center">{{ $society->flats_families }}</td>
                    <td class="text-right">{{ number_format((float)$society->rate_per_flat, 2) }}</td>
                    <td class="text-right">{{ number_format((float)$invoice->total_amount, 2) }}</td>
                </tr>
                <!-- Empty spacer rows to pad the table -->
                <tr>
                    <td class="text-center" style="height: 40px;"></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr class="font-bold">
                    <td colspan="5" class="text-right" style="background-color: #f2f2f2;">Total:</td>
                    <td class="text-right" style="background-color: #f2f2f2;">INR {{ number_format((float)$invoice->total_amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Summary & Bank Details -->
        <table class="table-borderless" style="margin-top: 15px;">
            <tr>
                <td style="width: 55%; padding-right: 15px;">
                    <div style="margin-bottom: 12px;">
                        <span class="font-bold">Amount in Words:</span><br>
                        <span style="font-style: italic; font-size: 10px;">{{ $amountInWords }}</span>
                    </div>
                    
                    <div class="section-title">Bank Details</div>
                    <div class="info-block" style="min-height: auto;">
                        <table class="table-borderless bank-details" style="margin: 0;">
                            <tr>
                                <td class="font-bold" style="width: 35%;">Account Name:</td>
                                <td style="width: 65%;">ECSPL FIWM</td>
                            </tr>
                            <tr>
                                <td class="font-bold">Bank Name:</td>
                                <td>HDFC Bank</td>
                            </tr>
                            <tr>
                                <td class="font-bold">A/C Number:</td>
                                <td>50200104372991</td>
                            </tr>
                            <tr>
                                <td class="font-bold">Branch & IFSC:</td>
                                <td>FC Road, Pune & HDFC0000103</td>
                            </tr>
                            <tr>
                                <td class="font-bold">PAN Number:</td>
                                <td>AAHCE5853F</td>
                            </tr>
                        </table>
                    </div>
                </td>
                
                <td style="width: 45%; vertical-align: bottom;">
                    <div class="footer-declaration">
                        <strong>Declaration:</strong><br>
                        We declare that this invoice shows the actual price of the services described and that all particulars are true and correct.
                    </div>
                    <div style="margin-top: 30px; text-align: right;">
                        <div class="company-name" style="font-size: 9px; margin-bottom: 40px;">For Earthy Companions Services Pvt. Ltd.</div>
                        <div class="signature-space">
                            Authorized Signatory
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        <div class="clear"></div>
    </div>
</body>
</html>
