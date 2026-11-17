<x-customer>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            Katalog - {{ $tenant->name }}
        </h2>
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav aria-label="Breadcrumb" class="mb-4 text-sm text-slate-500">
            <a href="{{ route('customer.tenants.index') }}" class="hover:text-emerald-600">Daftar Rental</a>
            <span class="mx-2">/</span>
            <span class="font-medium text-slate-900">{{ $tenant->name }}</span>
        </nav>

        {{-- Header Tenant --}}
        <div class="mb-8 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h1 class="text-2xl font-bold text-slate-900">{{ $tenant->name }}</h1>
            <p class="mt-1 text-sm text-slate-600">{{ $tenant->address ?? $tenant->city ?? '' }}</p>
        </div>

        {{-- Search & Filter Kategori --}}
        <form method="GET" action="{{ route('customer.catalog.index', $tenant) }}" class="mb-8 flex flex-col gap-3 sm:flex-row">
            <input
                type="text"
                name="q"
                value="{{ $search ?? '' }}"
                placeholder="Cari tenda, carrier, sepatu..."
                aria-label="Cari produk"
                class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-emerald-600 focus:outline-none sm:max-w-xs"
            >

            <select
                name="category"
                aria-label="Filter kategori"
                class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-emerald-600 focus:outline-none sm:max-w-xs"
            >
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected(isset($selectedCategory) && (int) $selectedCategory === (int) $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            <div class="flex gap-2">
                <button type="submit" class="rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-emerald-700">
                    Filter
                </button>
                @if(!empty($search) || !empty($selectedCategory))
                    <a href="{{ route('customer.catalog.index', $tenant) }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        {{-- Daftar Produk --}}
        @if($products->isEmpty())
            <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-12 text-center">
                <p class="text-base font-medium text-slate-900">Tidak ada perlengkapan ditemukan</p>
                <p class="mt-1 text-sm text-slate-500">Coba ubah filter kategori atau kata kunci pencarian.</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($products as $product)
                    <article class="flex flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition hover:shadow-md">
                        {{-- Placeholder / Foto Produk --}}
                        <div class="aspect-square w-full bg-slate-100 flex items-center justify-center text-slate-400">
                            <span class="text-xs font-medium">Foto Produk</span>
                        </div>

                        <div class="flex flex-1 flex-col justify-between p-4">
                            <div>
                                @if($product->category)
                                    <span class="text-xs font-medium text-emerald-700">{{ $product->category->name }}</span>
                                @endif
                                <h2 class="mt-1 text-base font-semibold text-slate-900">
                                    <a href="{{ route('customer.catalog.show', [$tenant, $product]) }}" class="hover:text-emerald-600">
                                        {{ $product->name }}
                                    </a>
                                </h2>
                            </div>

                            <div class="mt-4 pt-3 border-t border-slate-100">
                                <p class="text-sm font-bold text-slate-900">
                                    Rp {{ number_format($product->daily_price ?? $product->price ?? 0, 0, ',', '.') }}
                                    <span class="text-xs font-normal text-slate-500">/ hari</span>
                                </p>
                                <a href="{{ route('customer.catalog.show', [$tenant, $product]) }}" class="mt-3 block w-full rounded-lg bg-slate-900 px-3 py-2 text-center text-xs font-medium text-white hover:bg-slate-800">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</x-customer>