@extends('layouts.storefront')

@section('title', 'Detail Pesanan ' . $order->order_number . ' - Toko Tri Bhakti At Taqwa')

@section('content')
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6 flex-wrap">
                <a href="{{ route('home') }}" class="hover:text-emerald-600">Beranda</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <a href="{{ route('orders.index') }}" class="hover:text-emerald-600">Pesanan Saya</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-gray-800 font-medium">{{ $order->order_number }}</span>
            </nav>

            {{-- Order Header & Status --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                <div class="flex flex-col sm:flex-row justify-between gap-4 mb-6">
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">{{ $order->order_number }}</h1>
                        <p class="text-sm text-gray-500 mt-1">{{ $order->created_at->format('d F Y, H:i') }} WIB</p>
                    </div>
                    <span class="px-4 py-2 rounded-full text-sm font-semibold {{ $order->status_color }} self-start">{{ $order->status_label }}</span>
                </div>

                {{-- Status Tracker --}}
                <div class="relative z-0 mt-8 mb-4">
                    @php
    $statuses = ['pending_payment' => 'Menunggu Pembayaran', 'processing' => 'Diproses', 'shipped' => 'Dikirim', 'completed' => 'Selesai'];
    $statusOrder = array_keys($statuses);
    $currentIndex = array_search($order->status, $statusOrder);
    if ($order->status === 'cancelled') {
        $currentIndex = -1;
    }
                    @endphp
                    {{-- Progress bar --}}
                    <div class="absolute top-5 left-[12.5%] right-[12.5%] h-1 bg-gray-200 -z-10">
                        <div class="h-full bg-emerald-500 transition-all rounded-full"
                            style="width: {{ $currentIndex > 0 ? ($currentIndex / (count($statuses) - 1)) * 100 : 0 }}%"></div>
                    </div>

                    <div class="flex items-start justify-between">
                        @foreach($statuses as $key => $label)
                            @php $index = array_search($key, $statusOrder); @endphp
                            <div class="flex flex-col items-center text-center flex-1 relative z-10">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold mb-2 transition-colors duration-300
                                        {{ $index <= $currentIndex ? 'bg-emerald-500 text-white shadow-md shadow-emerald-200' : 'bg-gray-100 text-gray-400 border border-gray-200' }}">
                                    @if($index < $currentIndex || ($order->status === 'completed' && $index === $currentIndex))
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </div>
                                <span class="text-xs font-medium {{ $index <= $currentIndex ? 'text-emerald-700' : 'text-gray-400' }} tracking-wide">{{ $label }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if($order->status === 'cancelled')
                    <div class="mt-4 bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
                        ⚠️ Pesanan ini telah dibatalkan.
                    </div>
                @endif
            </div>

            {{-- Info Pengiriman & Pembayaran (Bersebelahan) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                {{-- Info Pengiriman --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-800 mb-4">Info Pengiriman</h3>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-gray-500">Penerima:</span> <span class="font-medium text-gray-800">{{ $order->recipient_name }}</span></div>
                        <div><span class="text-gray-500">Telepon:</span> <span class="font-medium text-gray-800">{{ $order->recipient_phone }}</span></div>
                        <div><span class="text-gray-500">Alamat:</span> <span class="font-medium text-gray-800">{{ $order->shipping_address }}</span></div>
                        @if($order->notes)
                            <div><span class="text-gray-500">Catatan:</span> <span class="font-medium text-gray-800">{{ $order->notes }}</span></div>
                        @endif
                    </div>
                </div>

                {{-- Info Pembayaran (Versi Compact) --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col h-full">
                    <h3 class="font-bold text-gray-800 mb-4">Info Pembayaran</h3>

                    {{-- Basic Info --}}
                    <div class="space-y-3">
                        <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                            <span class="text-sm text-gray-500">Metode</span>
                            <span class="text-sm font-medium text-gray-800">{{ $order->payment_method_label }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Status</span>
                            <span
                                class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $order->status_color }}">{{ $order->status_label }}</span>
                        </div>
                    </div>

                    {{-- Transfer Instructions & Form Upload --}}
                    @if($order->payment_method === 'transfer_bank')
                        <div class="mt-4 bg-gray-50 border border-gray-200 rounded-xl p-4">
                            <div class="flex justify-between items-start mb-1">
                                <span class="text-xs text-gray-500">Transfer ke:</span>
                                <span class="text-xs text-gray-500">Nominal:</span>
                            </div>
                            <div class="flex justify-between items-end mb-1">
                                <div>
                                    <p class="text-sm font-bold text-gray-800">DANA 082260202047</p>
                                    <p class="text-xs text-gray-500">a.n. Ahmad Syaiful Huda</p>
                                </div>
                                <p class="text-sm font-bold text-emerald-600">{{ $order->formatted_total }}</p>
                            </div>

                            {{-- Form Upload (Tampil jika belum dibayar) --}}
                            @if($order->status === 'pending_payment' && !$order->payment_receipt)
                                <form action="{{ route('orders.upload-receipt', $order) }}" method="POST" enctype="multipart/form-data"
                                    class="mt-4 pt-4 border-t border-gray-200" x-data="{ fileName: null }">
                                    @csrf
                                    <div class="space-y-3">
                                        <div class="relative">
                                            <input type="file" name="payment_receipt" id="payment_receipt_show" required accept="image/*"
                                                class="hidden" @change="fileName = $event.target.files[0].name">
                                            <label for="payment_receipt_show" 
                                                class="flex flex-col items-center justify-center w-full h-24 px-4 transition bg-white border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:border-emerald-400 hover:bg-emerald-50/30 group">
                                                <div class="flex flex-col items-center justify-center" x-show="!fileName">
                                                    <svg class="w-6 h-6 mb-1 text-gray-400 group-hover:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    <p class="text-xs text-gray-500">
                                                        <span class="font-bold text-emerald-600">Klik untuk upload</span> bukti
                                                    </p>
                                                </div>
                                                <div class="flex flex-col items-center justify-center text-center" x-show="fileName" x-cloak>
                                                    <svg class="w-6 h-6 mb-1 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                    </svg>
                                                    <p class="text-xs font-bold text-emerald-800 truncate max-w-[150px]" x-text="fileName"></p>
                                                </div>
                                            </label>
                                        </div>
                                        <button type="submit"
                                            class="w-full py-2.5 bg-emerald-600 text-white text-sm font-bold rounded-xl hover:bg-emerald-700 transition-all shadow-md shadow-emerald-100">
                                            Kirim Bukti
                                        </button>
                                    </div>
                                    @error('payment_receipt') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </form>
                            @endif
                        </div>
                    @endif

                    {{-- Compact Receipt Preview (Tampil jika sudah ada bukti) --}}
                    @if($order->payment_receipt)
                        <div class="mt-auto pt-4"> {{-- mt-auto akan mendorong kotak ini ke bawah jika ada sisa ruang --}}
                            <div class="flex items-center justify-between p-3 rounded-xl border border-gray-200 bg-white shadow-sm">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $order->payment_receipt_url }}" alt="Bukti"
                                        class="w-10 h-10 object-cover rounded-md border border-gray-100">
                                    <div>
                                        <p class="text-sm font-bold text-gray-800">Bukti Transfer</p>
                                        <p class="text-xs text-gray-500">Telah diunggah</p>
                                    </div>
                                </div>
                                <a href="{{ $order->payment_receipt_url }}" target="_blank"
                                    class="px-3 py-1.5 bg-gray-50 hover:bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg border border-gray-200 transition-colors">
                                    Lihat
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Order Items (Produk di Bawah) --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 mb-4">Produk yang Dipesan</h3>
                <div class="space-y-3">
                    @foreach($order->items as $item)
                        <div class="flex gap-3">
                            @if($item->product)
                                <img src="{{ $item->product->image_url }}" alt="{{ $item->product_name }}" class="w-16 h-16 rounded-lg object-cover shrink-0">
                            @else
                                <div class="w-16 h-16 rounded-lg bg-gray-100 shrink-0 flex items-center justify-center text-gray-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 line-clamp-1">{{ $item->product_name }}</p>
                                <p class="text-xs text-gray-500">{{ $item->quantity }} x {{ $item->formatted_price }}</p>
                                <p class="text-sm font-semibold text-emerald-600">{{ $item->formatted_subtotal }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-gray-100 mt-4 pt-4 space-y-2 text-sm max-w-sm ml-auto">
                    <div class="flex justify-between"><span class="text-gray-500">Subtotal</span><span class="font-semibold">{{ $order->formatted_subtotal }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Ongkos Kirim</span><span class="font-semibold">{{ $order->formatted_shipping_cost }}</span></div>
                    <div class="flex justify-between border-t border-gray-100 pt-2"><span class="font-bold">Total</span><span class="font-extrabold text-emerald-600 text-lg">{{ $order->formatted_total }}</span></div>
                </div>
            </div>
        </div>
@endsection