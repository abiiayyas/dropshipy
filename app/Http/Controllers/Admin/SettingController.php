<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display the settings center.
     */
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update the integration settings.
     */
    public function update(Request $request)
    {
        $allowedKeys = [
            'store_name', 'active_courier_provider', 'biteship_api_key', 
            'global_pixel_id', 'fonnte_token', 'wa_buyer_notification_enabled', 
            'telegram_bot_token', 'telegram_chat_id'
        ];
        
        $data = $request->only($allowedKeys);

        // Set default checkbox values to 0 if not present in request
        if (!isset($data['wa_buyer_notification_enabled'])) {
            $data['wa_buyer_notification_enabled'] = '0';
        }

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->route('admin.settings.index', ['tab' => 'integrasi'])->with('success', 'Pengaturan integrasi berhasil disimpan!');
    }

    /**
     * Update Profile
     */
    public function updateProfile(\App\Http\Requests\ProfileUpdateRequest $request)
    {
        $request->user()->fill($request->validated());
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }
        $request->user()->save();
        return redirect()->route('admin.settings.index', ['tab' => 'profil'])->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Update Password
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', \Illuminate\Validation\Rules\Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.settings.index', ['tab' => 'password'])->with('success', 'Password berhasil diperbarui!');
    }
}
