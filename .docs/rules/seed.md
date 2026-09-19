# Rule: Seeders

Seed only what is safe and idempotent. The MVP needs exactly one owner account.

## Conventions

- `database/seeders/` with one seeder per concern: `DatabaseSeeder` orchestrates, `UserSeeder` creates the owner.
- Owner credentials come from `.env` (e.g. `OWNER_NAME`, `OWNER_EMAIL`, `OWNER_PASSWORD`) with sane `.env.example` defaults; fall back to documented defaults only for local dev.
- Use `updateOrCreate` keyed on `email` so seeding is idempotent.
- Hash the password at seed time with `Hash::make`; never commit a plaintext password to the repo.

## Example

```php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => config('app.owner_email')],
            [
                'name' => config('app.owner_name'),
                'password' => Hash::make(config('app.owner_password')),
            ]
        );
    }
}
```

## Rules

- No fake/mass demo data in production seeds.
- Do not create projects/invoices in seeds (use factories in tests instead).
- Never log or echo the seeded password.

## Anti-patterns

- Hardcoding a real password in a committed seeder.
- Non-idempotent seeds that duplicate records on re-run.