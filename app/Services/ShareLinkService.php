<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\ShareLink;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ShareLinkService
{
    /**
     * Create a share link for an invoice. Returns the link with the plaintext password (shown once).
     *
     * @return array{link: ShareLink, password: string}
     */
    public function createForInvoice(Invoice $invoice, ?string $password = null): array
    {
        $password = $password ?? Str::random(12);

        $link = ShareLink::create([
            'project_id' => $invoice->project_id,
            'invoice_id' => $invoice->id,
            'token' => $this->uniqueToken(),
            'password_hash' => Hash::make($password),
        ]);

        return ['link' => $link, 'password' => $password];
    }

    public function findByToken(string $token): ?ShareLink
    {
        return ShareLink::where('token', $token)
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->first();
    }

    public function verify(ShareLink $link, string $password): bool
    {
        return Hash::check($password, $link->password_hash);
    }

    public function revoke(ShareLink $link): void
    {
        $link->delete();
    }

    private function uniqueToken(): string
    {
        do {
            $token = Str::random(32);
        } while (ShareLink::where('token', $token)->exists());

        return $token;
    }
}
