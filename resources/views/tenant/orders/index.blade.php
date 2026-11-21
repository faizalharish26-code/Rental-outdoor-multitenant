<x-layouts::app :title="'Pesanan Toko'">
    <div class="mx-auto max-w-6xl space-y-6 p-4 sm:p-8">
        <header class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-amber-800 dark:text-amber-300">
                    {{ $tenant->name }}
                </p>
                <h1 class="mt-1 text-3xl font-bold tracking-tight">
                    Pesanan
                </h1>
                <p class="mt-2 text-zinc-600 dark:text-zinc-300">
                    Lihat pesanan yang masuk ke toko ini.
                </p>
            </div>

            <a
                href="{{ route('tenant.dashboard', $tenant) }}"
                class="inline-flex min-h-11 items-center rounded-lg border border-zinc-300 px-4 py-2 font-semibold hover:bg-zinc-100 dark:border-zinc-700 dark:hover:bg-zinc-800"
            >
                Kembali ke ringkasan
            </a>
        </header>

        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div class="w-full sm:max-w-xs">
                <label for="status" class="mb-2 block text-sm font-semibold">
                    Filter status
                </label>
                <select
                    id="status"
                    name="status"
                    class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-900"
                >
                    <option value="">Semua status</option>
                    @foreach ($statuses as $status)
                        <option
                            value="{{ $status }}"
                            @selected($selectedStatus === $status)
                        >
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button
                type="submit"
                class="inline-flex min-h-11 items-center rounded-lg bg-green-800 px-4 py-2 font-semibold text-white hover:bg-green-700"
            >
                Terapkan filter
            </button>
        </form>

        @if ($orders->isEmpty())
            <section class="rounded-xl border border-dashed border-zinc-300 p-8 text-center dark:border-zinc-700">
                <h2 class="text-xl font-semibold">Belum ada pesanan</h2>
                <p class="mt-2 text-zinc-600 dark:text-zinc-300">
                    Pesanan untuk toko ini akan tampil di sini.
                </p>
            </section>
        @else
            <div class="overflow-x-auto rounded-xl border border-zinc-200 dark:border-zinc-700">
                <table class="min-w-[760px] w-full text-left text-sm">
                    <thead class="bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                        <tr>
                            <th scope="col" class="p-4">Nomor pesanan</th>
                            <th scope="col" class="p-4">Pelanggan</th>
                            <th scope="col" class="p-4">Periode sewa</th>
                            <th scope="col" class="p-4">Total</th>
                            <th scope="col" class="p-4">Status</th>
                            <th scope="col" class="p-4">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach ($orders as $order)
                            <tr>
                                <td class="p-4 font-semibold">
                                    {{ $order->order_number }}
                                </td>
                                <td class="p-4">
                                    {{ $order->user?->name ?? 'Pelanggan' }}
                                </td>
                                <td class="p-4">
                                    {{ \Illuminate\Support\Carbon::parse($order->start_date)->format('d/m/Y') }}
                                    –
                                    {{ \Illuminate\Support\Carbon::parse($order->end_date)->format('d/m/Y') }}
                                </td>
                                <td class="p-4">
                                    Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="p-4">
                                    <span class="rounded-full bg-zinc-100 px-3 py-1 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    <a
                                        href="{{ route('tenant.orders.show', [$tenant, $order]) }}"
                                        class="font-semibold text-green-800 underline hover:text-green-700 dark:text-green-300"
                                    >
                                        Lihat detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $orders->links() }}
        @endif
    </div>
</x-layouts::app>