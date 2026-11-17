<x-customer>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ $product->name }} - {{ $tenant->name }}
        </h2>
    </x-slot>

    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav aria-label="Breadcrumb" class="mb-6 text-sm text-slate-500">
            <a href="{{ route('customer.tenants.index') }}" class="hover:text-emerald-600">Daftar Rental</a>
            <span class="mx-2">/</span>
            <a href="{{ route('customer.catalog.index', $tenant) }}" class="hover:text-emerald-600">{{ $tenant->name }}</a>
            <span class="mx-2">/</span>
            <span class="font-medium text-slate-900">{{ $product->name }}</span>
        </nav>

        <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
            {{-- Foto Produk --}}
            <div class="aspect-square w-full overflow-hidden rounded-2xl border border-slate-200 bg-slate-100 flex items-center justify-center text-slate-400">
                <span class="text-sm font-medium">Foto Utama Produk</span>
            </div>

            {{-- Detail Informasi --}}
            <div class="flex flex-col justify-between">
                <div>
                    @if($product->category)
                        <span class="inline-flex items-center rounded-md bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">
                            {{ $product->category->name }}
                        </span>
                    @endif

                    <h1 class="mt-3 text-2xl font-bold text-slate-900 sm:text-3xl">{{ $product->name }}</h1>
                    <p class="mt-1 text-sm text-slate-500">Disewakan oleh <strong class="text-slate-700">{{ $tenant->name }}</strong></p>

                    <div class="mt-6 border-y border-slate-200 py-4">
                        <p class="text-2xl font-bold text-emerald-700">
                            Rp {{ number_format($product->daily_price ?? $product->price ?? 0, 0, ',', '.') }}
                            <span class="text-sm font-normal text-slate-500">/ hari</span>
                        </p>
                    </div>

                    <div class="mt-6">
                        <h2 class="text-sm font-semibold text-slate-900">Deskripsi Perlengkapan</h2>
                        <div class="mt-2 whitespace-pre-line text-sm leading-relaxed text-slate-600">
                            {{ $product->description ?: 'Tidak ada deskripsi tambahan.' }}
                        </div>
                    </div>
                </div>

                {{-- Aksi Login / Booking Placeholder sesuai aturan F1 --}}
                <div class="mt-8 rounded-xl border border-slate-200 bg-slate-50 p-4">
                    @auth
                        <p class="text-sm text-slate-700">Fitur pemesanan & pemilihan tanggal sewa akan aktif pada tahap berikutnya (F2/F3).</p>
                        <button type="button" disabled class="mt-3 w-full cursor-not-allowed rounded-lg bg-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-600">
                            Pilih Tanggal Sewa (Segera Hadir)
                        </button>
                    @else
                        <p class="text-sm text-slate-700">Silakan masuk terlebih dahulu untuk mulai menyewa perlengkapan ini.</p>
                        <div class="mt-3 flex flex-col gap-2 sm:flex-row">
                            <a href="{{ route('login', ['redirect' => url()->current()]) }}" class="flex-1 rounded-lg bg-emerald-600 px-4 py-2.5 text-center text-sm font-semibold text-white hover:bg-emerald-700">
                                Masuk untuk Menyewa
                            </a>
                            <a href="{{ route('register') }}" class="flex-1 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                Daftar Akun
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</x-customer>