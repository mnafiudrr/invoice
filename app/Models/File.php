<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class File extends Model
{
    use HasFactory;

    public const TYPE_INVOICE = 'invoice';

    public const TYPE_PAYMENT_RECEIPT = 'payment_receipt';

    public const TYPE_PAYMENT_PROOF = 'payment_proof';

    public const TYPE_OTHER = 'other';

    public static array $types = [
        self::TYPE_INVOICE,
        self::TYPE_PAYMENT_RECEIPT,
        self::TYPE_PAYMENT_PROOF,
        self::TYPE_OTHER,
    ];

    protected $fillable = [
        'project_id',
        'invoice_id',
        'type',
        'path',
        'original_filename',
        'mime_type',
        'size',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
