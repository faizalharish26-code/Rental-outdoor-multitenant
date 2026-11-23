<x-layouts::app :title="__('Dashboard')">
    <style>
        .outdoor-dash {
            --dash-surface: #ffffff;
            --dash-surface-muted: #f8fafc;
            --dash-border: #e2e8f0;
            --dash-text: #0f172a;
            --dash-text-muted: #64748b;
            --dash-accent: #059669;
            --dash-accent-bright: #10b981;
            --dash-accent-soft: rgba(16, 185, 129, 0.12);
            --dash-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.06), 0 4px 10px -6px rgba(15, 23, 42, 0.04);
            --dash-shadow-hover: 0 20px 30px -10px rgba(5, 150, 105, 0.14), 0 8px 16px -8px rgba(15, 23, 42, 0.06);
        }

        .dark .outdoor-dash {
            --dash-surface: #171717;
            --dash-surface-muted: #262626;
            --dash-border: #2e2e2e;
            --dash-text: #f5f5f5;
            --dash-text-muted: #a3a3a3;
            --dash-accent-soft: rgba(16, 185, 129, 0.16);
            --dash-shadow: 0 12px 28px -8px rgba(0, 0, 0, 0.45);
            --dash-shadow-hover: 0 20px 35px -12px rgba(16, 185, 129, 0.22);
        }

        .outdoor-dash .dash-hero {
            position: relative;
            overflow: hidden;
            border-radius: 20px;
            background:
                radial-gradient(circle at 88% 18%, rgba(16, 185, 129, 0.28), transparent 45%),
                radial-gradient(circle at 15% 85%, rgba(20, 184, 166, 0.16), transparent 40%),
                linear-gradient(135deg, #090d16 0%, #0f172a 55%, #062e22 100%);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: var(--dash-shadow);
            animation: dashFadeUp 0.45s ease-out both;
        }

        .outdoor-dash .dash-hero-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(to right, rgba(255, 255, 255, 0.04) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.04) 1px, transparent 1px);
            background-size: 28px 28px;
            mask-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.75), transparent);
            pointer-events: none;
        }

        .outdoor-dash .dash-cta-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 12px;
            padding: 12px 20px;
            font-size: 0.875rem;
            font-weight: 600;
            color: #042f24;
            background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
            box-shadow: 0 8px 20px -4px rgba(16, 185, 129, 0.45);
            transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
        }

        .outdoor-dash .dash-cta-btn:hover {
            transform: translateY(-2px);
            filter: brightness(1.05);
            box-shadow: 0 12px 24px -4px rgba(16, 185, 129, 0.55);
        }

        .outdoor-dash .dash-cta-btn:focus-visible {
            outline: 2px solid #ffffff;
            outline-offset: 2px;
        }

        .outdoor-dash .dash-card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-radius: 18px;
            background-color: var(--dash-surface);
            border: 1px solid var(--dash-border);
            padding: 22px;
            box-shadow: var(--dash-shadow);
            transition: transform 0.22s ease, border-color 0.22s ease, box-shadow 0.22s ease;
            animation: dashFadeUp 0.5s ease-out both;
        }

        .outdoor-dash .dash-card:nth-child(2) {
            animation-delay: 0.07s;
        }

        .outdoor-dash .dash-card:nth-child(3) {
            animation-delay: 0.14s;
        }

        .outdoor-dash .dash-card-interactive:hover {
            transform: translateY(-4px);
            border-color: rgba(16, 185, 129, 0.55);
            box-shadow: var(--dash-shadow-hover);
        }

        .outdoor-dash .dash-icon-box {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background-color: var(--dash-accent-soft);
            color: var(--dash-accent-bright);
        }

        .outdoor-dash .dash-icon-box-muted {
            background-color: var(--dash-surface-muted);
            color: var(--dash-text-muted);
        }

        .outdoor-dash .dash-step-item {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            border-radius: 14px;
            padding: 16px;
            background-color: var(--dash-surface-muted);
            border: 1px solid var(--dash-border);
            transition: border-color 0.2s ease, transform 0.2s ease;
        }

        .outdoor-dash .dash-step-item:hover {
            border-color: rgba(16, 185, 129, 0.4);
            transform: translateY(-1px);
        }

        .outdoor-dash .dash-step-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            flex-shrink: 0;
            border-radius: 9px;
            font-size: 0.78rem;
            font-weight: 700;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            box-shadow: 0 4px 10px -2px rgba(5, 150, 105, 0.4);
        }

        .outdoor-dash .dash-step-badge-muted {
            background: #cbd5e1;
            color: #334155;
            box-shadow: none;
        }

        .dark .outdoor-dash .dash-step-badge-muted {
            background: #3f3f46;
            color: #e4e4e7;
        }

        @keyframes dashFadeUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <div class="outdoor-dash flex h-full w-full flex-1 flex-col gap-6">
        {{-- Hero Banner --}}
        <section class="dash-hero p-6 text-white sm:p-8">
            <div class="dash-hero-grid" aria-hidden="true"></div>

            <div class="relative z-10 flex flex-col justify-between gap-6 lg:flex-row lg:items-center">
                <div class="space-y-2.5">
                    <div class="inline-flex items-center gap-2 rounded-full bg-emerald-400/15 px-3.5 py-1 text-xs font-medium text-emerald-300 ring-1 ring-inset ring-emerald-400/30">
                        <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                        <span>Akun Aktif &bull; {{ auth()->user()->email ?? 'Pelanggan' }}</span>
                    </div>

                    <h1 class="text-2xl font-bold tracking-tight text-white sm:text-3xl">
                        Halo, {{ auth()->user()->name ?? 'Pengguna' }}
                    </h1>
                    <p class="max-w-xl text-sm leading-relaxed text-slate-300">
                        Jelajahi vendor penyewaan perlengkapan outdoor aktif dan pantau progres pemesanan Anda.
                    </p>
                </div>

                <div class="shrink-0">
                    <a
                        href="{{ Route::has('customer.tenants.index') ? route('customer.tenants.index') : url('/tenants') }}"
                        class="dash-cta-btn"
                    >
                        <span>Jelajah Tenant</span>
                        <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>
            </div>
        </section>

        {{-- 3 Kartu Ringkasan --}}
        <section class="grid gap-5 md:grid-cols-3" aria-label="Ringkasan Fitur Pelanggan">
            {{-- Kartu 1: Katalog & Tenant --}}
            <a
                href="{{ Route::has('customer.tenants.index') ? route('customer.tenants.index') : url('/tenants') }}"
                class="dash-card dash-card-interactive group"
            >
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="dash-icon-box" aria-hidden="true">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                            </svg>
                        </span>
                        <span class="rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:text-emerald-400">
                            Aktif
                        </span>
                    </div>

                    <h2 class="mt-4 text-base font-bold text-slate-900 transition-colors group-hover:text-emerald-600 dark:text-white dark:group-hover:text-emerald-400">
                        Katalog &amp; Tenant
                    </h2>
                    <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">
                        Cari tenant berdasarkan nama atau lokasi, filter kategori, dan cek tarif harian.
                    </p>
                </div>

                <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-3.5 text-xs font-semibold text-emerald-700 dark:border-neutral-800 dark:text-emerald-400">
                    <span>Buka daftar tenant</span>
                    <span class="transition-transform group-hover:translate-x-1" aria-hidden="true">&rarr;</span>
                </div>
            </a>

            {{-- Kartu 2: Keranjang & Booking --}}
            <div class="dash-card">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="dash-icon-box dash-icon-box-muted" aria-hidden="true">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                            </svg>
                        </span>
                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600 dark:bg-neutral-800 dark:text-neutral-400">
                            Tahap F2–F3
                        </span>
                    </div>

                    <h2 class="mt-4 text-base font-bold text-slate-900 dark:text-white">
                        Keranjang &amp; Booking
                    </h2>
                    <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">
                        Atur tanggal sewa dan booking perlengkapan pada satu tenant terpilih.
                    </p>
                </div>

                <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-3.5 text-xs font-medium text-slate-400 dark:border-neutral-800">
                    <span>0 item dipilih</span>
                    <span>Segera hadir</span>
                </div>
            </div>

            {{-- Kartu 3: Riwayat Pesanan --}}
            <div class="dash-card">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="dash-icon-box dash-icon-box-muted" aria-hidden="true">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z" />
                            </svg>
                        </span>
                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600 dark:bg-neutral-800 dark:text-neutral-400">
                            Tahap F8
                        </span>
                    </div>

                    <h2 class="mt-4 text-base font-bold text-slate-900 dark:text-white">
                        Riwayat Pesanan
                    </h2>
                    <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">
                        Pantau status pembayaran, serah-terima, dan pengembalian alat.
                    </p>
                </div>

                <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-3.5 text-xs font-medium text-slate-400 dark:border-neutral-800">
                    <span>Belum ada transaksi</span>
                    <span>Segera hadir</span>
                </div>
            </div>
        </section>

        {{-- Bagian Bawah: Alur Singkat --}}
        <section class="dash-card" aria-label="Alur Penyewaan">
            <div>
                <h2 class="text-base font-bold text-slate-900 dark:text-white">Alur Penyewaan</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Langkah awal menggunakan akun pelanggan</p>
            </div>

            <div class="mt-5 grid gap-4 sm:grid-cols-3">
                <div class="dash-step-item">
                    <span class="dash-step-badge">1</span>
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Pilih Tenant Aktif</h3>
                        <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Temukan penyedia sewa sesuai lokasi tujuan.</p>
                    </div>
                </div>

                <div class="dash-step-item">
                    <span class="dash-step-badge">2</span>
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Cek Katalog &amp; Tarif</h3>
                        <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Filter kategori dan periksa harga per hari.</p>
                    </div>
                </div>

                <div class="dash-step-item">
                    <span class="dash-step-badge dash-step-badge-muted">3</span>
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Booking &amp; DP</h3>
                        <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Dilanjutkan pada tahap booking &amp; pembayaran.</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-layouts::app>