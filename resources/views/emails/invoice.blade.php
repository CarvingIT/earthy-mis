<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Monthly Transport Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333333;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        .header {
            border-bottom: 2px solid #10b981;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            color: #10b981;
        }
        .content p {
            margin: 0 0 15px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .details-table th, .details-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        .details-table th {
            background-color: #f3f4f6;
            color: #374151;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            font-size: 0.85em;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Earthy Companions Services Pvt. Ltd.</h2>
            <p style="margin: 5px 0 0; font-size: 0.9em; color: #6b7280;">Waste Collection and Disposal Services</p>
        </div>
        <div class="content">
            <p>Dear Sir/Madam,</p>
            <p>Please find attached the monthly Tax Invoice for transport charges (waste collection services) rendered to <strong>{{ $invoice->society->name }}</strong> for the billing month of <strong>{{ Carbon\Carbon::parse($invoice->billing_month . '-01')->format('F Y') }}</strong>.</p>
            
            <table class="details-table">
                <tr>
                    <th>Invoice Number</th>
                    <td>{{ $invoice->invoice_number }}</td>
                </tr>
                <tr>
                    <th>Billing Month</th>
                    <td>{{ Carbon\Carbon::parse($invoice->billing_month . '-01')->format('F Y') }}</td>
                </tr>
                <tr>
                    <th>Total Amount</th>
                    <td>INR {{ number_format($invoice->total_amount, 2) }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td><strong>{{ ucfirst($invoice->status) }}</strong></td>
                </tr>
            </table>

            <p>If you have any questions or require further assistance, please contact us at Ecspl.Fiwm@gmail.Com or call us at +91 8412037640.</p>
            <p>Thank you for your continued partnership.</p>
        </div>
        <div class="footer">
            <p>This is an automated email. Please do not reply directly to this message.</p>
            <p><strong>Earthy Companions Services Pvt. Ltd. FIWM</strong><br>
            Flat No. C-402, Sai Leela Manaji Nagar, Narhe, Pune - 411041</p>
        </div>
    </div>
</body>
</html>
