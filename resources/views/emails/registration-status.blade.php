<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="margin:0;background:#f8fafc;font-family:Arial,sans-serif;color:#1e293b">
    <div style="max-width:560px;margin:32px auto;padding:24px;background:#fff;border:1px solid #e2e8f0;border-radius:12px">
        <p style="color:#475569;font-size:14px">{{ $brandName }}</p>
        <h1 style="font-size:22px">Pemberitahuan pendaftaran {{ $category }}</h1>
        <p>Halo {{ $recipientName }},</p>
        @if ($status === 'received')
            <p>Terima kasih. Pendaftaran {{ $category }} Anda telah kami terima dan sedang menunggu peninjauan.</p>
            <p>Kami akan mengirimkan pemberitahuan setelah pengajuan Anda disetujui atau ditolak.</p>
        @elseif ($status === 'approved')
            <p>Selamat, pendaftaran {{ $category }} Anda telah disetujui.</p>
        @elseif ($status === 'rejected')
            <p>Mohon maaf, pendaftaran {{ $category }} Anda belum dapat disetujui.</p>
        @endif
        <p>Jika membutuhkan bantuan, silakan hubungi <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>.</p>
        <p>Salam,<br>Tim {{ $brandName }}</p>
    </div>
</body>
</html>
