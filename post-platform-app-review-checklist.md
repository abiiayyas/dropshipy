# Checklist App Review — Semua Platform
**Produk:** TryPost (post.diginiaga.com) — social media scheduler untuk perusahaan media
**Model:** Opsi A (satu app milik Diginiaga, klien tinggal connect)

---

## 🥇 PRIORITAS: Meta (Facebook + Instagram)
Ini yang paling ketat & wajib lolos dulu. Jadi gate utama produk.

### 1. Pra-syarat
- [ ] **Facebook Developer account** — developers.facebook.com (akun pribadi/korporat)
- [ ] **Konfirmasi email + nomor HP** di Facebook
- [ ] **Buat App tipe Business** → apps.facebook.com
- [ ] **Business Verification** (verifikasi bisnis resmi)
  - Dokumen legal perusahaan (NPWP, Akta, profile perusahaan)
  - Butuh: website aktif, alamat, kontak, rekening bank
  - Proses: 2-5 hari kerja
- [ ] Upload **privacy policy** + terms of service (wajib di halaman app)
  - Privacy policy harus jelas: data apa yang diambil, gimana dipakai, gimana dihapus

### 2. Scopes / Permission yang diminta
Untuk social media scheduler, yang dibutuhkan:
```
- Instagram Content Publishing (untuk nge-post ke IG)
- Instagram Basic (baca profil)
- Facebook Login
- Pages Manage (kelola Page)
- Pages Read Engagement (baca interaksi)
- Pages Show List (lihat daftar Page)
- Public Profile
- ads_management (OPSIONAL — kalau mau jual fitur boosting/jadwal iklan)
```

### 3. Platform Config di Meta Developer Portal
- [ ] Tambah platform **Web**
- [ ] Site URL: `https://post.diginiaga.com`
- [ ] **Redirect/Valid OAuth URI**: `https://post.diginiaga.com/oauth/facebook/callback` (cek routing TryPost — pakai path yang benar)
- [ ] **Add Tokens / Login**: sama di masukkan ke Setting
- [ ] **Deauthorize Callback URL**: `https://post.diginiaga.com/oauth/facebook/deauthorize`

### 4. Advanced Access (permission per-scope)
Setiap scope yang butuh public access (Instagram Content Publishing, Pages, dll) mesti diisi:
- [ ] **Business use case** — jelaskan kenapa app butuh scope ini
- [ ] **Demo video screen recording** (2-5 menit) — tunjukkan alur connect → publish ke IG/FB
- [ ] Jawab pertanyaan app review (6-10 pertanyaan bisnis/teknis)

### 5. App Review — Timeline realistis
| Tahap | Estimasi |
|-------|----------|
| Setup app + verifikasi bisnis | 2-5 hari |
| Setiap scope Advanced Access | 1-2 minggu per scope |
| Total semua scope | **1-2 bulan** (jalankan paralel) |

⚠️ **Tips biar cepat lolos:**
- Mulai dengan **scope minimal dulu** (publish + basic), expand nanti
- Demo video harus tunjukkan jalan nyata, jangan slideshow
- Jelaskan cara data di-delete (wajib privacy)
- Gunakan langkah program **"On-Demand Access"** untuk publish ke halaman sendiri (tanpa review) untuk development

---

## 📱 TikTok
Lebih longgar dari Meta tapi tetap ada review.
### Pra-syarat
- [ ] TikTok Developer Portal — developers.tiktok.com
- [ ] Buat App (type: Web/Mobile)
- [ ] Login: akun TikTok + **Business account** / authorize
### Konfigurasi
- [ ] Redirect URI: `https://post.diginiaga.com/oauth/tiktok/callback`
- [ ] Scopes: `user.info.basic`, `video.list`, `video.publish` (post video ke TikTok)
- [ ] **Content Publishing API** — approval terpisah (form + review)
  - Butuh: explain business case, demo video, apps screencast
  - TikTok kadang minta **$** atau partner program untuk publish API penuh
- [ ] Privacy policy + TOS
### Catatan unik
- TikTok **rate limit ketat** — app baru bisa kena throttling awal
- Content Publishing API review realitanya 1-4 minggu

---

## 📺 YouTube (Google Cloud)
Paling mudah & cepat vs platform lain.
### Pra-syarat
- [ ] Google Cloud Console — console.cloud.google.com
- [ ] Buat project baru
- [ ] Aktifkan **YouTube Data API v3**
- [ ] Buat **OAuth Consent Screen** (eksternal)
  - Isi: app name, email, privacy policy, TOS
  - Scope: `youtube.upload`, `youtube.readonly`, `youtube.force-ssl`
### Konfigurasi
- [ ] OAuth Client ID + Secret (type: Web)
- [ ] Redirect URI: `https://post.diginiaga.com/oauth/google/callback`
- [ ] Submit consent screen → biasanya **langsung approved** untuk Internal, External butuh review ringan (1-3 hari)
### Catatan unik
- Google paling gampang — API key + OAuth cukup
- Upload quota default 10k units/hari (cukup untuk daily scheduling)
- Bisa pakai **publish tanpa review** kalau "Testing" mode + whitelisted test user

---

## 💼 LinkedIn
Untuk B2B / perusahaan media, LinkedIn penting.
### Pra-syarat
- [ ] LinkedIn Developer Portal — developer.linkedin.com
- [ ] Buat app
- [ ] Konfirmasi identitas (KYC ringan)
### Konfigurasi
- [ ] Redirect URI: `https://post.diginiaga.com/oauth/linkedin/callback`
- [ ] Scopes: `r_liteprofile`, `w_member_social` (post)
- [ ] **Company Page posting** butuh: `w_organization_social` + jadi admin halaman tersebut
### Catatan unik
- **LinkedIn Open Platform Change** — untuk post ke Organization Page butuh partner status dulu (form + review)
- Member post (`w_member_social`) langsung bisa pakai
- Review 1-4 minggu untuk organization access

---

## 🐦 X / Twitter
Sekarang berbayar & ketat untuk API v2 write.
### Pra-syarat
- [ ] X Developer Portal — developer.x.com
- [ ] Upgrade ke **Basic** ($100-200/bln) atau **Pro** ($5k/bln) untuk write access
  - ⚠️ Sejak 2023, free tier **tidak bisa post** (read only)
- [ ] Buat app + project
### Konfigurasi
- [ ] Redirect URI: `https://post.diginiaga.com/oauth/twitter/callback`
- [ ] Scopes: `tweet.write`, `users.read`, `offline.access`
- [ ] User Authentication settings + enable OAuth 2.0
### Catatan unik
- **Biaya bulanan** — pertimbangkan: klien media pake X banyak? Kalau ya, masukkan ke harga
- Review ringan (1-7 hari) tapi musti bayar tier

---

## 📌 Pinterest (OPSIONAL)
### Pra-syarat
- [ ] Pinterest Developer — developers.pinterest.com
- [ ] Buat app
### Konfigurasi
- [ ] Redirect URI: `https://post.diginiaga.com/oauth/pinterest/callback`
- [ ] Scopes: `boards:read`, `pins:read`, `pins:write`
### Catatan unik
- Paling longgar — review cepat (bahkan bisa self-serve)
- Kurang relevan untuk media company Indonesia (kecuali target fashion/food/travel)

---

## 🗂️ Ringkasan Semua Platform

| Platform | Developer Account | App Review | Biaya | Kesulitan | Prioritas |
|----------|------------------|------------|-------|-----------|-----------|
| Facebook + IG | Meta Developer | 🔴 Berat | Gratis | Tinggi | 🔥 WAJIB |
| TikTok | TikTok Developer | 🟡 Sedang | Gratis* | Sedang | Tinggi |
| YouTube | Google Cloud | 🟢 Ringan | Gratis | Rendah | Tinggi |
| LinkedIn | LinkedIn Developer | 🟡 Sedang | Gratis | Sedang | Sedang |
| X/Twitter | X Developer | 🟢 Ringan | $100+/bulan | Rendah | Opsional |
| Pinterest | Pinterest Developer | 🟢 Ringan | Gratis | Rendah | Opsional |

*TikTok Content Publishing API kadang butuh partner/approved partner

---

## 🎯 Strategi Peluncuran Produk (Saran)

**Fase 1 — MVP (minggu 1-2):**
- Meta App mode Live via **On-Demand Access** (publish ke halaman sendiri/klien yang jadi admin, tanpa full App Review)
- Google YouTube (paling cepat lolos)
- TikTok (lolos cepat, publish API berjalan)

**Fase 2 — Scale (bulan 1-2):**
- Apply full **App Review Meta** untuk semua scope (multi-user, bukan cuma admin)
- LinkedIn organization access
- Tambah platform lain sesuai permintaan klien

**Fase 3 — Monetize:**
- X/Twitter berbayar jadi **fitur premium** (klien bayar untuk itu)
- Masukkan biaya review/tier ke setup fee

---

## 📌 Dokumen yang Perlu Disiapkan Sekali (berlaku semua platform)
Yang paling banyak dimepetin & bikin review lambat:
- [ ] **Privacy Policy** — template, isi data collection & deletion flow
- [ ] **Terms of Service** — syarat penggunaan, disclaimer content scheduling
- [ ] **Data Deletion Policy** — platform wajib lihat cara hapus data
- [ ] **Logo + App Icon** (tinggi-res)
- [ ] **Demo Video** — connect account → buat post → schedule → publish (bikin sekali, dipake semua platform pakai sudut beda)

---

## 🛠️ Langkah Eksekusi Berikutnya
1. [ ] Saya buatkan **Privacy Policy + TOS** dasar untuk post.diginiaga.com
2. [ ] Buat **demo video script** (connect → schedule → publish)
3. [ ] Setup **Meta App** pertama (kamu login, saya pandu isi form)
4. [ ] Cek routing OAuth TryPost — verifikasi path callback yang benar
5. [ ] Isi credentials platform di TryPost (Settings → Social Accounts)

---

Catatan model bisnis: **seluruh App Review ini jadi barrier-to-entry.** Saat kamu jalan ke klien dengan "semua integrasi udah jadi, tinggal connect", itu nilai jual besar vs kompetitor kecil yang harus mulai dari nol.
