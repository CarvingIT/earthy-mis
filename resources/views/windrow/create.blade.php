<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-emerald-600">Composting operations</p>
                <h2 class="text-3xl font-extrabold leading-tight text-slate-900">Create Windrow</h2>
            </div>
            <p class="max-w-xl text-sm font-medium text-slate-500">Record a new composting windrow with start date, weight, and processing timeline.</p>
        </div>
    </x-slot>

    <style>
        * { box-sizing: border-box; }

        .windrow-form-shell {
            background: linear-gradient(180deg, #f8fafc 0%, #f1f8f4 48%, #f8fafc 100%);
        }

        .form-panel,
        .form-section,
        .form-aside {
            border: 1px solid rgba(15, 118, 110, 0.12);
            border-radius: 1rem;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(14px);
            box-shadow: 0 18px 48px rgba(15, 118, 110, 0.08), 0 2px 6px rgba(15, 23, 42, 0.04);
        }

        .form-section {
            padding: 24px;
        }

        .form-section-title {
            font-size: 1rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-section-title svg {
            width: 20px;
            height: 20px;
            color: #10b981;
        }

        .form-divider {
            height: 1px;
            background: rgba(148, 163, 184, 0.25);
            margin: 20px 0;
        }

        .form-field {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        .form-input,
        .form-select {
            width: 100%;
            padding: 10px 14px;
            border-radius: 0.75rem;
            border: 1px solid rgba(148, 163, 184, 0.4);
            background: #ffffff;
            font-size: 0.9rem;
            color: #1e293b;
            transition: all 0.18s ease;
        }

        .form-input:focus,
        .form-select:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
        }

        .form-help {
            font-size: 0.75rem;
            color: #6b7280;
            margin-top: 6px;
        }

        .form-error {
            font-size: 0.75rem;
            color: #dc2626;
            margin-top: 6px;
        }

        .btn-save-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 12px 20px;
            border-radius: 9999px;
            font-size: 0.9rem;
            font-weight: 600;
            color: #ffffff;
            background: linear-gradient(135deg, #10b981, #059669);
            box-shadow: 0 12px 24px rgba(16, 185, 129, 0.25);
            border: none;
            cursor: pointer;
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }

        .btn-save-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 32px rgba(16, 185, 129, 0.35);
        }

        .btn-cancel {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 12px 20px;
            border-radius: 9999px;
            font-size: 0.9rem;
            font-weight: 600;
            color: #374151;
            background: #ffffff;
            border: 1px solid rgba(148, 163, 184, 0.4);
            cursor: pointer;
            transition: all 0.18s ease;
            text-decoration: none;
        }

        .btn-cancel:hover {
            background: #f9fafb;
            border-color: #9ca3af;
        }

        .tip-box {
            padding: 16px;
            border-radius: 0.75rem;
            background: rgba(16, 185, 129, 0.08);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .tip-box h4 {
            font-size: 0.85rem;
            font-weight: 700;
            color: #065f46;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .tip-box p {
            font-size: 0.8rem;
            color: #065f46;
            line-height: 1.5;
        }

        .alert-error {
            padding: 12px 16px;
            border-radius: 0.75rem;
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.25);
            color: #991b1b;
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }

        @keyframes revealUp {
            from {
                opacity: 0;
                transform: translateY(18px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .reveal-up {
            animation: revealUp 0.45s ease both;
        }
    </style>

    <div class="py-8 windrow-form-shell">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <!-- Error Alert -->
            @if ($errors->any())
                <div class="alert-error reveal-up mb-6">
                    <strong>Please fix the following errors:</strong>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('windrow.store') }}" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                @csrf

                <!-- Main Form Column -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Windrow Details Section -->
                    <div class="form-panel form-section reveal-up" style="animation-delay: 0.05s">
                        <h3 class="form-section-title">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Windrow Details
                        </h3>

                        <div class="form-field">
                            <label for="windrow_number" class="form-label">Windrow Number *</label>
                            <input id="windrow_number" name="windrow_number" type="text" class="form-input" value="{{ old('windrow_number') }}" placeholder="e.g., WR-001" required />
                            @error('windrow_number')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                            <p class="form-help">Unique identifier for this windrow (e.g., WR-001, WR-002)</p>
                        </div>

                        <div class="form-field">
                            <label for="weight_in" class="form-label">Weight IN</label>
                            <input id="weight_in" name="weight_in" type="text" class="form-input" value="{{ old('weight_in') }}" placeholder="e.g., 5000 kg" />
                            @error('weight_in')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                            <p class="form-help">Total weight of material loaded into this windrow</p>
                        </div>
                    </div>

                    <!-- Timeline Section -->
                    <div class="form-panel form-section reveal-up" style="animation-delay: 0.1s">
                        <h3 class="form-section-title">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Processing Timeline
                        </h3>

                        <div class="form-field">
                            <label for="start_date" class="form-label">Start Date *</label>
                            <input id="start_date" name="start_date" type="date" class="form-input" value="{{ old('start_date', date('Y-m-d')) }}" required />
                            @error('start_date')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                            <p class="form-help">Date when composting started in this windrow</p>
                        </div>

                        <div class="form-field">
                            <label for="end_date" class="form-label">End Date</label>
                            <input id="end_date" name="end_date" type="date" class="form-input" value="{{ old('end_date') }}" />
                            @error('end_date')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                            <p class="form-help">Expected or actual end date of active composting phase</p>
                        </div>

                        <div class="form-field">
                            <label for="out_date" class="form-label">Out Date</label>
                            <input id="out_date" name="out_date" type="date" class="form-input" value="{{ old('out_date') }}" />
                            @error('out_date')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                            <p class="form-help">Date when material was removed from the windrow</p>
                        </div>

                        <div class="form-field">
                            <label for="screening_date" class="form-label">Screening Date</label>
                            <input id="screening_date" name="screening_date" type="date" class="form-input" value="{{ old('screening_date') }}" />
                            @error('screening_date')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                            <p class="form-help">Date when the material was screened/processed</p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 reveal-up" style="animation-delay: 0.15s">
                        <a href="{{ route('windrow.index') }}" class="btn-cancel">Cancel</a>
                        <button type="submit" class="btn-save-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Save Windrow
                        </button>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Quick Tips -->
                    <div class="form-aside form-section reveal-up" style="animation-delay: 0.2s">
                        <div class="tip-box">
                            <h4>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                Quick Tip
                            </h4>
                            <p>Windrows are long piles of organic material used in composting. Track each windrow's lifecycle from start to screening to optimize your composting operations.</p>
                        </div>
                    </div>

                    <!-- Save Button (Sticky) -->
                    <div class="form-aside form-section reveal-up" style="animation-delay: 0.25s">
                        <button type="submit" class="btn-save-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Save Windrow
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('reveal-up');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.reveal-up').forEach(el => observer.observe(el));
        });
    </script>
</x-app-layout>
