<x-customer>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            Jelajah Rental Outdoor
        </h2>
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Temukan Penyedia Rental Outdoor</h1>
            <p class="mt-2 text-sm text-slate-600">Pilih penyedia perlengkapan outdoor terpercaya di dekat lokasi pendakian atau kotamu.</p>
        </div>

        {{-- Form Pencarian Tenant --}}
        <form method="GET" action="{{ route('customer.tenants.index') }}" class="mb-8 flex flex-col gap-3 sm:flex-row">
            <input
                type="text"
                name="q"
                value="{{ $search ?? '' }}"
                placeholder="Cari nama rental atau kota..."
                aria-label="Cari nama rental atau kota"
                class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-600/20 sm:max-w-md"
            >
            <div class="flex gap-2">
                <button type="submit" class="rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2">
                    Cari Rental
                </button>
                @if(!empty($search))
                    <a href="{{ route('customer.tenants.index') }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        {{-- Grid Tenant --}}
        @if($tenants->isEmpty())
            <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-12 text-center">
                <p class="text-base font-medium text-slate-900">Penyedia rental tidak ditemukan</p>
                <p class="mt-1 text-sm text-slate-500">Coba gunakan kata kunci nama atau lokasi lain.</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($tenants as $tenant)
                    <a href="{{ route('customer.catalog.index', $tenant) }}" class="group flex flex-col justify-between rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-emerald-500 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-emerald-600">
                        <div>
                            <div class="flex items-center justify-between">
                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700">
                                    Aktif
                                </span>
                            </div>
                            <h2 class="mt-3 text-lg font-semibold text-slate-900 group-hover:text-emerald-700">
                                {{ $tenant->name }}
                            </h2>
                            <p class="mt-1 text-sm text-slate-500">
                                {{ $tenant->address ?? $tenant->city ?? 'Lokasi tersedia di detail' }}
                            </p>
                        </div>
                        <div class="mt-6 flex items-center text-sm font-medium text-emerald-600">
                            Lihat Katalog &rarr;
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $tenants->links() }}
            </div>
        @endif
    </div>
</x-customer>