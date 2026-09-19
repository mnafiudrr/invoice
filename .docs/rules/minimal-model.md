# Rule: Minimal Models

Models are **thin Eloquent shells** — attributes, casts, relationships, scopes. No business logic, no formatting, no side effects.

## Conventions

- One model per table in `app/Models/`, singular StudlyCase (`Project`, `Invoice`, `InvoiceItem`, `Payment`, `File`).
- Declare `$fillable` (or `$guarded`), `$casts`, relationships.
- Define **domain constants** for enums/statuses (single source of truth):

```php
class Invoice extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_SENT = 'sent';
    public const STATUS_PAID = 'paid';
    public const STATUS_CANCELLED = 'cancelled';

    public static array $statuses = [
        self::STATUS_DRAFT,
        self::STATUS_SENT,
        self::STATUS_PAID,
        self::STATUS_CANCELLED,
    ];

    protected $fillable = [
        'project_id', 'invoice_number', 'language', 'currency',
        'subtotal', 'tax', 'total', 'issued_at', 'due_at', 'status', 'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'issued_at' => 'date',
        'due_at' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
```

## Rules

- Keep helpers limited to query helpers (scopes) and simple derived checks that have no side effects, e.g. `isPaid()`.
- Money/totals formatting lives in presentation helpers/views, **not** models.
- Business logic (number generation, totals calc, PDF, payment transitions) lives in services.
- No `App::make`, no `Storage::`, no `Hash::`, no `Mail::` inside models.

## Anti-patterns

- Models that call services or write files.
- `total()` methods that format currency.
- Fat mutators that trigger side effects.
- Business rules sprinkled across models/controllers.