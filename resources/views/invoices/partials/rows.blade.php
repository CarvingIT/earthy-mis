@foreach ($allSocieties as $society)
    @php
        $invoice = $society->invoices->first();
        $expectedAmount = ((float)$society->flats_families) * ((float)$society->rate_per_flat);
    @endphp
    <tr>
        <!-- Society Name & Contact -->
        <td>
            <div class="flex items-start gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-sm font-black text-slate-700">
                    {{ mb_strtoupper(mb_substr($society->name ?? 'S', 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="font-extrabold text-slate-900">{{ $society->name }}</p>
                    @if (filled($society->contact_person_email))
                        <a href="mailto:{{ $society->contact_person_email }}" class="mt-1 inline-block text-xs font-bold text-emerald-700 hover:text-emerald-800">{{ $society->contact_person_email }}</a>
                    @else
                        <p class="mt-1 text-xs font-medium text-slate-400">Email not set</p>
                    @endif
                </div>
            </div>
        </td>

        <!-- Invoice Details -->
        <td>
            <div class="font-semibold text-slate-700">
                {{ Carbon\Carbon::parse($month . '-01')->format('M Y') }}
            </div>
            @if ($invoice)
                <div class="font-mono text-xs text-slate-400 mt-1">{{ $invoice->invoice_number }}</div>
            @else
                <div class="text-xs text-slate-400 mt-1">-</div>
            @endif
        </td>

        <!-- Flats & Rate -->
        <td>
            <div class="font-semibold text-slate-700">
                {{ $society->flats_families ?: '0' }} flats
            </div>
            <div class="text-xs text-slate-400 mt-1">
                @ ₹{{ number_format((float)$society->rate_per_flat, 2) }}
            </div>
        </td>

        <!-- Total Amount -->
        <td>
            <div class="font-extrabold text-slate-900">
                ₹{{ number_format($invoice ? (float)$invoice->total_amount : $expectedAmount, 2) }}
            </div>
        </td>

        <!-- Status -->
        <td>
            @if (!$invoice)
                <span class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-extrabold text-slate-500">
                    Not Generated
                </span>
            @elseif ($invoice->status === 'sent')
                <span class="inline-flex rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-extrabold text-emerald-700">
                    Sent
                </span>
            @elseif ($invoice->status === 'failed')
                <div class="relative group inline-block">
                    <span class="inline-flex rounded-full border border-rose-200 bg-rose-50 px-2.5 py-1 text-xs font-extrabold text-rose-700 cursor-pointer">
                        Failed
                    </span>
                    <!-- Error Tooltip -->
                    <div class="absolute bottom-full left-1/2 z-20 mb-2 w-64 -translate-x-1/2 rounded-xl bg-slate-900 p-3 text-xs text-white shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition duration-200">
                        <p class="font-extrabold mb-1">Error Log:</p>
                        <p class="font-mono text-[10px] leading-relaxed break-words line-clamp-4">{{ $invoice->error_log }}</p>
                        <div class="absolute top-full left-1/2 h-2 w-2 -translate-x-1/2 -translate-y-1 rotate-45 bg-slate-900"></div>
                    </div>
                </div>
            @else
                <span class="inline-flex rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1 text-xs font-extrabold text-amber-700">
                    Pending
                </span>
            @endif
        </td>

        <!-- Processed At -->
        <td>
            <span class="text-xs font-medium text-slate-500">
                {{ $invoice && $invoice->sent_at ? $invoice->sent_at->format('d-M-y H:i') : '-' }}
            </span>
        </td>

        <!-- Actions -->
        <td>
            <div class="flex justify-end gap-1.5">
                @php
                    $actionType = 'send';
                    $btnLabel = 'Send';
                    if ($invoice) {
                        if ($invoice->status === 'sent') {
                            $actionType = 'resend';
                            $btnLabel = 'Re-send';
                        } elseif ($invoice->status === 'failed') {
                            $actionType = 'retry';
                            $btnLabel = 'Retry';
                        }
                    }
                @endphp

                <a class="inline-flex h-9 items-center justify-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 text-xs font-bold text-slate-700 transition hover:bg-slate-900 hover:text-white" 
                   href="{{ route('invoices.society-pdf', [$society, 'month' => $month]) }}" target="_blank" title="View PDF">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    PDF
                </a>

                <button type="button" 
                        onclick="confirmSingleDispatch('{{ $society->id }}', '{{ addslashes($society->name) }}', '{{ $society->contact_person_email }}', '{{ $actionType }}')" 
                        class="inline-flex h-9 items-center justify-center gap-1 rounded-lg border border-slate-200 bg-slate-100 px-2.5 text-xs font-bold text-slate-700 transition hover:bg-slate-900 hover:text-white">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89M9 11l3 3L22 4"/>
                    </svg>
                    {{ $btnLabel }}
                </button>
            </div>
        </td>
    </tr>
@endforeach
