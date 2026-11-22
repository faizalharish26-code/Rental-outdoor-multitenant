<x-layouts::app.sidebar :title="$maintenanceOnly ? 'Unit dalam perawatan' : 'Unit inventaris'">
    <main class="mx-auto max-w-6xl space-y-6 p-4 sm:p-8">
        <header class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-amber-800 dark:text-amber-300">
                    {{ $tenant->name }}
                </p>
                <h1 class="mt-1 text-3xl font-bold tracking-tight">
                    {{ $maintenanceOnly ? 'Unit dalam perawatan' : 'Unit inventaris' }}
                </h1>
                <p class="mt-2 text-zinc-600 dark:text-zinc-300">
                    Catat kondisi dan status setiap barang fisik.
                </p>
            </div>

            <a href="{{ route('tenant.inventory.create', $tenant) }}"
               class="inline-flex min-h-11 items-center rounded-lg bg-green-800 px-4 py-2 font-semibold text-white hover:bg-green-700">
                Tambah unit
            </a>
        </header>

        @if (session('success'))
            <div role="status" class="rounded-lg border border-green-300 bg-green-50 p-4 text-green-900">
                {{ session('success') }}
            </div>
        @endif

        <nav class="flex flex-wrap gap-3">
            <a href="{{ route('tenant.inventory.index', $tenant) }}"
               class="rounded-lg border px-4 py-2 {{ ! $maintenanceOnly ? 'bg-zinc-900 text-white' : '' }}">
                Semua unit
            </a>
            <a href="{{ route('tenant.inventory.index', [$tenant, 'status' => 'maintenance']) }}"
               class="rounded-lg border px-4 py-2 {{ $maintenanceOnly ? 'bg-zinc-900 text-white' : '' }}">
                Dalam perawatan
            </a>
        </nav>

        <form method="GET" action="{{ route('tenant.inventory.index', $tenant) }}"
              class="flex flex-wrap gap-3">
            @if ($maintenanceOnly)
                <input type="hidden" name="status" value="maintenance">
            @endif

            <label class="sr-only" for="search">Cari unit</label>
            <input id="search" name="search" value="{{ request('search') }}"
                   placeholder="Cari kode unit atau produk"
                   class="min-h-11 rounded-lg border-zinc-300 px-3 dark:border-zinc-700 dark:bg-zinc-900">

            <button class="min-h-11 rounded-lg border px-4 py-2 font-semibold">
                Cari
            </button>
        </form>

        @if ($units->isEmpty())
            <section class="rounded-xl border border-dashed p-8 text-center">
                <h2 class="text-xl font-semibold">Belum ada unit inventaris</h2>
                <p class="mt-2 text-zinc-600">Tambahkan unit pertama untuk mulai mencatat stok fisik.</p>
            </section>
        @else
            <div class="overflow-x-auto rounded-xl border border-zinc-200 dark:border-zinc-700">
                <table class="min-w-[760px] w-full text-left text-sm">
                    <thead class="bg-zinc-100 dark:bg-zinc-800">
                        <tr>
                            <th class="p-4">Kode unit</th>
                            <th class="p-4">Produk</th>
                            <th class="p-4">Kondisi</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach ($units as $unit)
                            <tr>
                                <td class="p-4 font-semibold">{{ $unit->unit_code }}</td>
                                <td class="p-4">{{ $unit->product->name }}</td>
                                <td class="p-4">{{ ucfirst($unit->condition) }}</td>
                                <td class="p-4">
                                    @if ($unit->status === 'maintenance')
                                        <span class="rounded-full bg-amber-100 px-3 py-1 text-amber-900">
                                            Perawatan
                                        </span>
                                    @else
                                        <span class="rounded-full bg-green-100 px-3 py-1 text-green-900">
                                            Siap disewa
                                        </span>
                                    @endif

                                    @if ($unit->status === 'maintenance' && $unit->maintenance_note)
                                        <p class="mt-1 text-xs text-zinc-600">{{ $unit->maintenance_note }}</p>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <a class="font-semibold underline"
                                       href="{{ route('tenant.inventory.edit', [$tenant, $unit]) }}">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $units->links() }}
        @endif
    </main>
</x-layouts::app.sidebar>