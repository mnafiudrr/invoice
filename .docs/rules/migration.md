# Rule: Migrations

## Conventions

- One table per migration file. Use Laravel's default naming (`create_projects_table`, `add_paid_at_to_payments_table`).
- Use the schema builder; no raw SQL unless unavoidable.
- Always use `$table->foreignId(...)->constrained()->cascadeOnDelete()` (or the appropriate delete rule) for FKs.
- Add useful indexes: unique columns get unique indexes; FK columns get indexed.
- Decimal money columns: `decimal('amount', 15, 2)`, never `float`.
- Nullable fields are explicit: `->nullable()`.
- `id()` bigint increments; `timestamps()` present on every table.
- Use `foreignUuid`/`uuid` only for future share tokens, not for MVP PKs.

## Conventions for this project (see `data-model.md`)

```php
Schema::create('projects', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('name');
    $table->string('slug')->unique();
    $table->string('client_name');
    $table->string('client_email');
    $table->string('client_company')->nullable();
    $table->text('description')->nullable();
    $table->string('access_password_hash');
    $table->timestamps();
});
```

## Rules

- Money: `decimal(15,2)`, default `0` where sensible.
- Dates vs timestamps: use `date` for issued/paid/due (no time needed); `timestamps` for created/updated.
- Status strings: `varchar` with app-level enum constants (no DB enum unless PostgreSQL native enum is deliberately wanted).
- Do not add migrations that aren't documented in `data-model.md` without updating it.

## Anti-patterns

- No `float` for money.
- No missing FKs (`constrained()`).
- No modifying tables without a down method that restores the previous state.