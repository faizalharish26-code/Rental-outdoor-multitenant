<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class CheckoutForm extends Component
{
    public $tenant_id;
    public $start_date;
    public $end_date;
    
    // Simulasi data keranjang peminjaman
    public $items = [
        [
            'product_id' => '',
            'product_name' => 'Carrier Deuter 65L',
            'price_per_day' => 35000,
            'quantity' => 1
        ]
    ];

    public function submitOrder()
    {
        $this->validate([
            'tenant_id' => 'required|uuid',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $startDate = new \DateTime($this->start_date);
            $endDate = new \DateTime($this->end_date);
            $days = $startDate->diff($endDate)->days ?: 1;

            $totalAmount = 0;

            // Simpan ke tabel orders
            $order = Order::create([
                'user_id' => auth()->id() ?? '11111111-1111-1111-1111-111111111111', // Dummy ID jika belum login
                'tenant_id' => $this->tenant_id,
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'total_amount' => 0,
                'status' => 'pending',
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
            ]);

            // Simpan rincian ke tabel order_items
            foreach ($this->items as $item) {
                $productId = $item['product_id'] ?: '22222222-2222-2222-2222-222222222222'; // Dummy ID Product Wisnu
                
                $subtotal = $item['quantity'] * $item['price_per_day'] * $days;
                $totalAmount += $subtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'quantity' => $item['quantity'],
                    'price_per_day' => $item['price_per_day'],
                    'subtotal' => $subtotal,
                ]);
            }

            // Update total harga keseluruhan pada order
            $order->update(['total_amount' => $totalAmount]);

            DB::commit();

            session()->flash('message', 'Peminjaman berhasil dibuat dengan nomor: ' . $order->order_number);
            return redirect()->to('/checkout');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal memproses pesanan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.checkout-form');
    }
}