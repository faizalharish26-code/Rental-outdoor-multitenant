<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CustomerPaymentController extends Controller
{
    public function show(Order $order): View
    {
        $this->authorizeOrder($order);
        $order->load(['tenant', 'items.product', 'payment']);

        abort_unless($order->payment, 404);

        return view('payments.show', ['order' => $order]);
    }

    public function simulate(Order $order): RedirectResponse
    {
        $this->authorizeOrder($order);
        abort_unless(app()->environment(['local', 'testing']), 404);

        DB::transaction(function () use ($order): void {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            $payment = Payment::query()
                ->where('order_id', $lockedOrder->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($payment->status === 'paid' && $lockedOrder->payment_status === 'paid') {
                return;
            }

            if (
                $payment->provider !== 'simulation'
                || $payment->status !== 'pending'
                || $lockedOrder->status !== 'pending'
                || $lockedOrder->payment_status !== 'unpaid'
            ) {
                throw ValidationException::withMessages([
                    'payment' => 'Pembayaran simulasi hanya tersedia untuk pesanan yang masih menunggu pembayaran.',
                ]);
            }

            $payment->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            $lockedOrder->update([
                'payment_status' => 'paid',
                'status' => 'paid',
            ]);
        });

        return to_route('payments.show', $order)
            ->with('success', 'Pembayaran simulasi berhasil. Status DP pesanan sudah dibayar.');
    }

    private function authorizeOrder(Order $order): void
    {
        abort_unless((int) $order->user_id === (int) Auth::id(), 404);
    }
}
