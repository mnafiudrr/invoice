# Rule: Security

Non-negotiable security practices for this app. See `.docs/plans/scopes/security.md` for the full requirement set.

## Conventions

- **Passwords** — always `Hash::make()` at rest; verify with `Hash::check()`. Never plaintext. Never SHA-256/SHA-512.
- **Storage** — private files under `storage/app/private/` via a dedicated disk. Never `public/`. No `storage:link` symlink for invoices.
- **Filenames** — generated UUID/ULID; original names only as metadata in `files`.
- **Serving files** — only through authorized endpoints:
  ```php
  // check authorization first
  abort_unless(session("project_access.{$project->id}", false), 403);
  return Storage::disk('private')->response($file->path, $file->original_filename, [
      'Content-Disposition' => 'inline',
  ]);
  ```
- **Auth** — all `/admin*` behind `auth`. No registration route.
- **Rate limiting** — login + project password attempts throttled.
- **CSRF** — Laravel default; keep enabled. No `@csrf` omission in forms.
- **Validation** — server-side on every input (Form Requests).
- **Client pages** — render only whitelisted fields; never echo `path`, `id`, internal values.

## Checklist before merge

- [ ] No plaintext passwords committed/seeded in code.
- [ ] No `public/storage` symlink pointing at invoices.
- [ ] No direct file URLs exposed to clients.
- [ ] Password attempt routes throttled.
- [ ] Client pages contain no admin links/internal IDs.
- [ ] CSRF intact on all POST forms.

## Anti-patterns

- Hashing with `md5`/`sha1`/`sha256` for passwords.
- Storing uploads in `public/`.
- Relying on URL obscurity alone for access control.
- `{!! $request->input(...) !!}` unescaped output.