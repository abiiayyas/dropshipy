<?php

namespace App\Http\Controllers;

use App\Models\CustomerOrderClaim;
use App\Models\Order;
use App\Services\WhatsAppService;
use App\Support\PhoneNumber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class CustomerAccountController extends Controller
{
    public function index(Request $request): View
    {
        $orders = $request->user()
            ->orders()
            ->with(['product', 'shipment'])
            ->latest()
            ->paginate(10);

        return view('account.dashboard', compact('orders'));
    }

    public function showOrder(int $order): View
    {
        $order = auth()->user()
            ->orders()
            ->with(['product', 'productVariant.optionValues', 'shipment', 'landingPage'])
            ->whereKey($order)
            ->firstOrFail();

        return view('account.order', compact('order'));
    }

    public function claimForm(): View
    {
        return view('account.claim');
    }

    public function requestClaim(Request $request, WhatsAppService $whatsapp): RedirectResponse
    {
        $validated = $request->validate([
            'order_reference' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
        ]);

        $reference = trim($validated['order_reference']);
        $phone = PhoneNumber::normalize($validated['phone']);
        $order = Order::with('shipment')
            ->whereNull('user_id')
            ->where(function ($query) use ($reference): void {
                $query->where('order_number', $reference)
                    ->orWhereHas('shipment', fn ($shipments) => $shipments->where('tracking_number', $reference));
            })
            ->first();

        $matched = $order && hash_equals(PhoneNumber::normalize($order->customer_phone), $phone);
        if (! $matched) {
            return back()->with('status', 'Jika data cocok, kode verifikasi akan dikirim ke nomor WhatsApp pada order.');
        }

        CustomerOrderClaim::where('user_id', $request->user()->id)
            ->whereNull('verified_at')
            ->delete();

        $code = (string) random_int(100000, 999999);
        $claim = CustomerOrderClaim::create([
            'user_id' => $request->user()->id,
            'order_id' => $order->id,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes(10),
        ]);

        $sent = $whatsapp->sendMessage(
            $order->customer_phone,
            "Kode verifikasi akun untuk order #{$order->order_number}: {$code}. Berlaku 10 menit. Jangan bagikan kode ini."
        );

        if (! $sent) {
            $claim->delete();

            return back()->withErrors(['order_reference' => 'Verifikasi WhatsApp belum tersedia. Hubungi admin toko.']);
        }

        $request->session()->put('customer_claim_id', $claim->id);

        return redirect()
            ->route('account.orders.claim.verify.form')
            ->with('status', 'Kode verifikasi sudah dikirim ke nomor WhatsApp pada order.');
    }

    public function verifyClaimForm(Request $request): View|RedirectResponse
    {
        if (! $this->pendingClaim($request)) {
            return redirect()->route('account.orders.claim.form')
                ->withErrors(['code' => 'Permintaan verifikasi sudah tidak berlaku.']);
        }

        return view('account.claim-verify');
    }

    public function verifyClaim(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $claim = $this->pendingClaim($request);
        if (! $claim) {
            return redirect()->route('account.orders.claim.form')
                ->withErrors(['code' => 'Permintaan verifikasi sudah tidak berlaku.']);
        }

        if ($claim->attempts >= 5) {
            return back()->withErrors(['code' => 'Terlalu banyak percobaan. Minta kode baru.']);
        }

        $claim->increment('attempts');
        if (! Hash::check($validated['code'], $claim->code_hash)) {
            return back()->withErrors(['code' => 'Kode verifikasi tidak valid.']);
        }

        DB::transaction(function () use ($claim, $request): void {
            $order = Order::query()->lockForUpdate()->findOrFail($claim->order_id);
            if ($order->user_id !== null && $order->user_id !== $request->user()->id) {
                abort(403, 'Order sudah terhubung ke akun lain.');
            }

            $order->update(['user_id' => $request->user()->id]);
            $claim->update(['verified_at' => now()]);
        });

        $request->session()->forget('customer_claim_id');

        return redirect()->route('account.dashboard')->with('status', 'Order berhasil ditambahkan ke akun Anda.');
    }

    private function pendingClaim(Request $request): ?CustomerOrderClaim
    {
        $claimId = $request->session()->get('customer_claim_id');
        if (! $claimId) {
            return null;
        }

        $claim = CustomerOrderClaim::whereKey($claimId)
            ->where('user_id', $request->user()->id)
            ->whereNull('verified_at')
            ->first();

        if (! $claim || $claim->expires_at->isPast()) {
            return null;
        }

        return $claim;
    }
}
