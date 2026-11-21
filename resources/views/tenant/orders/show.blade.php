<x-layouts::app :title="'Detail Pesanan'">
    <div class="mx-auto max-w-5xl space-y-6 p-4 sm:p-8">
        <header class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-amber-800 dark:text-amber-300">
                    {{ $tenant->name }}
                </p>
                <h1 class="mt-1 text-3xl font-bold tracking-tight">
                    Detail pesanan
                </h1>
                <p class="mt-2 text-zinc-600 dark:text-zinc-300">
                    {{ $order->order_number }}
                </p>
            </div>

            <a
                href="{{ route('tenant.orders.index', $tenant) }}"
                class="inline-flex min-h-11 items-center rounded-lg border border-zinc-300 px-4 py-2 font-semibold hover:bg-zinc-100 dark:border-zinc-700 dark:hover:bg-zinc-800"
            >
                Kembali ke daftar pesanan
            </a>
        </header>

        <section class="grid gap-4 sm:grid-cols-2">
            <article class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                <h2 class="font-semibold">Pelanggan</h2>
                <p class="mt-2">{{ $order->user?->name ?? 'Pelanggan' }}</p>
            </article>

            <article class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                <h2 class="font-semibold">Status pesanan</h2>
                <p class="mt-2">{{ ucfirst($order->status) }}</p>
            </article>

            <article class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                <h2 class="font-semibold">Mulai sewa</h2>
                <p class="mt-2">
                    {{ \Illuminate\Support\Carbon::parse($order->start_date)->format('d/m/Y H:i') }}
                </p>
            </article>

            <article class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                <h2 class="font-semibold">Selesai sewa</h2>
                <p class="mt-2">
                    {{ \Illuminate\Support\Carbon::parse($order->end_date)->format('d/m/Y H:i') }}
                </p>
            </article>
        </section>

        <section class="space-y-4">
            <div>
                <h2 class="text-xl font-bold">Barang dalam pesanan</h2>
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">
                    Rincian produk dan biaya yang tersimpan pada pesanan ini.
                </p>
            </div>

            @if ($order->items->isEmpty())
                <div class="rounded-xl border border-dashed border-zinc-300 p-6 text-zinc-600 dark:border-zinc-700 dark:text-zinc-300">
                    Belum ada rincian barang pada pesanan ini.
                </div>
            @else
                <div class="overflow-x-auto rounded-xl border border-zinc-200 dark:border-zinc-700">
                    <table class="min-w-[650px] w-full text-left text-sm">
                        <thead class="bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                            <tr>
                                <th scope="col" class="p-4">Produk</th>
                                <th scope="col" class="p-4">Jumlah</th>
                                <th scope="col" class="p-4">Harga per hari</th>
                                <th scope="col" class="p-4">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @foreach ($order->items as $item)
                                <tr>
                                    <td class="p-4 font-semibold">
                                        {{ $item->product?->name ?? 'Produk sudah tidak tersedia' }}
                                    </td>
                                    <td class="p-4">{{ $item->quantity }}</td>
                                    <td class="p-4">
                                        Rp {{ number_format((float) $item->price_per_day, 0, ',', '.') }}
                                    </td>
                                    <td class="p-4">
                                        Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-zinc-50 dark:bg-zinc-900">
                            <tr>
                                <th colspan="3" scope="row" class="p-4 text-right">
                                    Total pesanan
                                </th>
                                <td class="p-4 font-bold">
                                    Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </section>
    </div>
</x-layouts::app>