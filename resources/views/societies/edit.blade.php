<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-emerald-600">Community operations</p>
                <h2 class="text-3xl font-extrabold leading-tight text-slate-900">Edit Society</h2>
            </div>
            <p class="max-w-xl text-sm font-medium text-slate-500">Keep society profile, onboarding, and contact information accurate for daily operations.</p>
        </div>
    </x-slot>

    <style>
        * { box-sizing: border-box; }

        .society-form-shell {
            background: linear-gradient(180deg, #f8fafc 0%, #f1f8f4 48%, #f8fafc 100%);
        }

        .form-panel,
        .form-section,
        .form-aside {
            border: 1px solid rgba(15, 23, 42, .08);
            background: #ffffff;
            box-shadow: 0 10px 28px rgba(15, 23, 42, .07);
        }

        .form-section {
            position: relative;
            overflow: hidden;
        }

        .form-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(16, 185, 129, .08), transparent 42%);
            pointer-events: none;
        }

        .form-field {
            border: 1px solid #e2e8f0 !important;
            border-radius: .75rem !important;
            color: #334155;
            font-weight: 600;
            min-height: 2.85rem;
            transition: border-color .16s ease, box-shadow .16s ease;
        }

        .form-field:focus {
            border-color: #10b981 !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, .14) !important;
        }

        .form-label {
            color: #334155;
            font-size: .8rem;
            font-weight: 800;
        }

        .section-mark {
            background: linear-gradient(135deg, #059669, #22d3ee);
            box-shadow: 0 12px 28px rgba(16, 185, 129, .25);
        }

        .reveal {
            opacity: 0;
            transform: translate3d(0, 18px, 0);
            transition: opacity .48s ease, transform .48s ease;
            transition-delay: var(--reveal-delay, 0ms);
        }

        .reveal.is-visible {
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }

        @media (prefers-reduced-motion: reduce) {
            .reveal {
                opacity: 1;
                transform: none;
                transition: none;
            }
        }
    </style>

    <div class="society-form-shell min-h-screen py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="form-panel reveal rounded-2xl p-5">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-7h6v7"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">{{ $society->name }}</h3>
                            <p class="text-sm font-medium text-slate-500">Update profile details and keep the directory reliable.</p>
                        </div>
                    </div>

                    <a href="{{ route('societies.index') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-extrabold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100 sm:w-auto">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Back to Societies
                    </a>
                </div>
            </section>

            @if ($errors->any())
                <div class="reveal rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-bold text-rose-800 shadow-sm" data-dismissible-alert>
                    <div class="flex items-start justify-between gap-3">
                        <span>Please review the highlighted fields and try again.</span>
                        <button type="button" class="-mr-1 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-rose-700 transition hover:bg-rose-100 focus:outline-none focus:ring-4 focus:ring-rose-200" data-dismiss-alert aria-label="Dismiss alert">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('societies.update', $society) }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1fr_20rem]">
                    <div class="space-y-6">
                        <section class="form-section reveal rounded-2xl p-5 sm:p-6" style="--reveal-delay: 70ms;">
                            <div class="relative z-10">
                                <div class="mb-5 flex items-center gap-3">
                                    <div class="section-mark flex h-10 w-10 items-center justify-center rounded-xl text-white">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.45-2.72A1 1 0 013 16.38V5.62a1 1 0 011.45-.9L9 7m0 13l6-3m-6 3V7m6 10l4.55 2.28A1 1 0 0021 18.38V7.62a1 1 0 00-.55-.9L15 4m0 13V4m0 0L9 7"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-extrabold text-slate-900">Society details</h3>
                                        <p class="text-sm font-medium text-slate-500">Identity, address, and operational location.</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                    <div class="md:col-span-2">
                                        <label for="name" class="form-label">Society name <span class="text-rose-500">*</span></label>
                                        <x-text-input id="name" name="name" type="text" class="form-field mt-2 block w-full" :value="old('name', $society->name)" required autofocus />
                                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                    </div>

                                    <div class="md:col-span-2">
                                        <label for="address" class="form-label">Address</label>
                                        <textarea id="address" name="address" rows="4" class="form-field mt-2 block w-full px-4 py-3 shadow-sm" placeholder="Street, landmark, area">{{ old('address', $society->address) }}</textarea>
                                        <x-input-error class="mt-2" :messages="$errors->get('address')" />
                                    </div>

                                    <div>
                                        <label for="city" class="form-label">City</label>
                                        <x-text-input id="city" name="city" type="text" class="form-field mt-2 block w-full" :value="old('city', $society->city)" placeholder="Ahmedabad" />
                                        <x-input-error class="mt-2" :messages="$errors->get('city')" />
                                    </div>

                                    <div>
                                        <label for="joining_month" class="form-label">Joining month</label>
                                        <x-text-input id="joining_month" name="joining_month" type="text" class="form-field mt-2 block w-full" :value="old('joining_month', $society->joining_month)" placeholder="March" />
                                        <x-input-error class="mt-2" :messages="$errors->get('joining_month')" />
                                    </div>

                                    <div>
                                        <label for="flats_families" class="form-label">Flats / Families</label>
                                        <x-text-input id="flats_families" name="flats_families" type="text" class="form-field mt-2 block w-full" :value="old('flats_families', $society->flats_families)" placeholder="120" />
                                        <x-input-error class="mt-2" :messages="$errors->get('flats_families')" />
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="form-section reveal rounded-2xl p-5 sm:p-6" style="--reveal-delay: 120ms;">
                            <div class="relative z-10">
                                <div class="mb-5 flex items-center gap-3">
                                    <div class="section-mark flex h-10 w-10 items-center justify-center rounded-xl text-white">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m8-4a4 4 0 10-8 0 4 4 0 008 0zm6 0a3 3 0 10-6 0 3 3 0 006 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-extrabold text-slate-900">Contact people</h3>
                                        <p class="text-sm font-medium text-slate-500">Chairman, secretary, and communication channels.</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                    <div>
                                        <label for="chairman_name" class="form-label">Chairman name</label>
                                        <x-text-input id="chairman_name" name="chairman_name" type="text" class="form-field mt-2 block w-full" :value="old('chairman_name', $society->chairman_name)" placeholder="Full name" />
                                        <x-input-error class="mt-2" :messages="$errors->get('chairman_name')" />
                                    </div>

                                    <div>
                                        <label for="secretary_name" class="form-label">Secretary name</label>
                                        <x-text-input id="secretary_name" name="secretary_name" type="text" class="form-field mt-2 block w-full" :value="old('secretary_name', $society->secretary_name)" placeholder="Full name" />
                                        <x-input-error class="mt-2" :messages="$errors->get('secretary_name')" />
                                    </div>

                                    <div>
                                        <label for="contact_person_email" class="form-label">Contact person email</label>
                                        <x-text-input id="contact_person_email" name="contact_person_email" type="email" class="form-field mt-2 block w-full" :value="old('contact_person_email', $society->contact_person_email)" placeholder="name@example.com" />
                                        <x-input-error class="mt-2" :messages="$errors->get('contact_person_email')" />
                                    </div>

                                    <div>
                                        <label for="phone" class="form-label">Contact number</label>
                                        <x-text-input id="phone" name="phone" type="text" class="form-field mt-2 block w-full" :value="old('phone', $society->phone)" placeholder="+91 98765 43210" />
                                        <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>

                    <aside class="form-aside reveal h-fit rounded-2xl p-5" style="--reveal-delay: 160ms;">
                        <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-extrabold text-slate-900">Save changes</h3>
                        <p class="mt-2 text-sm font-medium leading-6 text-slate-500">Review the society name, contact people, and phone or email before updating the directory.</p>

                        <div class="mt-5 rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs font-extrabold uppercase tracking-wide text-slate-500">Current record</p>
                            <p class="mt-2 text-sm font-black text-slate-900">{{ $society->name }}</p>
                            <p class="mt-1 text-xs font-semibold text-slate-500">{{ $society->city ?: 'City not set' }}</p>
                        </div>

                        <div class="mt-6 space-y-3">
                            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-extrabold text-white shadow-lg shadow-slate-900/15 transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-200">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Update Society
                            </button>
                            <a href="{{ route('societies.index') }}" class="inline-flex w-full items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-extrabold text-slate-700 transition hover:bg-slate-50">
                                Cancel
                            </a>
                        </div>
                    </aside>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const revealItems = document.querySelectorAll('.reveal');
                document.querySelectorAll('[data-dismiss-alert]').forEach((button) => {
                    button.addEventListener('click', () => {
                        button.closest('[data-dismissible-alert]')?.remove();
                    });
                });

                if (!('IntersectionObserver' in window)) {
                    revealItems.forEach((item) => item.classList.add('is-visible'));
                    return;
                }

                const observer = new IntersectionObserver((entries) => {
                    entries.forEach((entry) => {
                        if (!entry.isIntersecting) return;

                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    });
                }, { threshold: .12 });

                revealItems.forEach((item) => observer.observe(item));
            });
        </script>
    @endpush
</x-app-layout>
