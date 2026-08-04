# MedixLab — Deployment Runbook (alwaysdata)

Release: **Sell-Readiness Hardening — P0/P1** (audit trail, server-side PDFs, critical values, GDPR, backups, 2FA, E2E, sample/barcode hardening).

Live site: `https://medixlab.alwaysdata.net` · Repo: `https://github.com/Rabiikhenissi/medixLab`

---

## 1. What ships in this release

| Area | New code | Schema change |
|---|---|---|
| Audit trail | `AuditLog` model + observer + `ActivityLogController` + `admin/activity` view | `2026_08_04_000001_create_audit_logs_table` |
| Critical values | `ExamParameter`/`ResultLaboDetail` thresholds, `critical` status everywhere | `2026_08_04_000002_…`, `2026_08_04_000003_…` |
| 2FA (TOTP) | `TwoFactorController`, challenge + profile views, login hook | `2026_08_04_000004_add_two_factor_to_users_table` |
| Sample barcode hardening | idempotent `SampleController::store`, collision-safe codes | `2026_08_04_000005_make_sample_item_unique` |
| Server-side PDFs / GDPR / backups / E2E | `ExamReportPdf`, `GdprService` + commands + controller, `DatabaseBackupCommand`, tests | — |

Composer adds: `pragmarx/google2fa`, `pragmarx/google2fa-qrcode`, `simplesoftwareio/simple-qrcode`, `barryvdh/laravel-dompdf`.

---

## 2. Pre-flight (local, this machine)

All already verified — re-run before shipping:

```powershell
php artisan test            # 66/66 passing
vendor\bin\pint --test      # clean
php artisan route:list      # two-factor + gdpr + activity routes present
php artisan schedule:list   # "0 2 * * * php artisan backup:database"
```

Commit the working tree and push to `origin` so the server can pull:

```bash
git add -A
git commit -m "Sell-readiness: audit, PDF, criticals, GDPR, backups, 2FA, hardening"
git push origin main
```

---

## 3. Backup first (alwaysdata side)

From the alwaysdata panel, or over SSH from the site directory:

```bash
cd /home/<account>/www/medixlab
php artisan backup:database            # manual run → storage/app/backups/mysql-*.gz
ls -la storage/app/backups/            # confirm the .gz exists and is non-empty
```

Alwaysdata also keeps its own MySQL backups — take a panel snapshot before migrating.

---

## 4. Ship the code

Over SSH (enable SSH in the alwaysdata panel under *SSH* if needed):

```bash
cd /home/<account>/www/medixlab
git pull origin main
composer install --no-dev --prefer-dist --no-interaction   # pulls the 4 new packages
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

> If `composer` is not on PATH, use the site's PHP: `php -r "copy('https://getcomposer.org/installer','composer-setup.php');"` and run the installer, or use the alwaysdata composer plugin from the panel.

> **PHP extensions required by dompdf**: `dom`, `gd`, `mbstring`, `xml` (already confirmed present on alwaysdata).

---

## 5. Migrations

Low-traffic window only (the enum `change()` migration briefly locks the table):

```bash
cd /home/<account>/www/medixlab
php artisan migrate --force
php artisan migrate:status     # 5 new rows must show "Ran"
```

This applies exactly 5 pending migrations:

1. `2026_08_04_000001_create_audit_logs_table`
2. `2026_08_04_000002_add_critical_thresholds_to_exam_parameters_table`
3. `2026_08_04_000003_add_critical_to_result_labo_details_status` (enum `change()` → `critical`)
4. `2026_08_04_000004_add_two_factor_to_users_table`
5. `2026_08_04_000005_make_sample_item_unique` (unique index on `samples.exam_request_item_id`)

> If migration 5 fails with a duplicate-key error, some exam item already has 2+ samples. Deduplicate first:
> `DELETE s1 FROM samples s1 JOIN samples s2 ON s1.exam_request_item_id = s2.exam_request_item_id AND s1.id > s2.id;` then re-run `migrate`.

---

## 6. Seed the new RBAC features

Only `GroupSeeder` is safe to re-run on production — **do not** run the full `db:seed`
(`DemoDataSeeder`/`CliniqueEzzahraSeeder` are not idempotent and would duplicate demo data).

```bash
cd /home/<account>/www/medixlab
php artisan db:seed --class=GroupSeeder --force
```

This creates the `activity-logs` and `gdpr-management` features and their actions
(`view-activity`, `view-gdpr`, `manage-gdpr`). Then grant them to the admin group:

```bash
php artisan tinker --execute="App\Models\Group::where('code','admin')->first()->actions()->syncWithoutDetaching(App\Models\Action::pluck('id')->all());"
```

Clear the permission cache after:

```bash
php artisan cache:clear
```

---

## 7. Cron / scheduler (backups at 02:00)

The schedule (`routes/console.php`) runs `backup:database` daily at `02:00`.
Laravel does not self-trigger — alwaysdata must call the scheduler every minute:

- alwaysdata panel → **Cron / Tâches planifiées** → add task:

```
Command:  cd /home/<account>/www/medixlab && php artisan schedule:run >> storage/logs/scheduler.log 2>&1
Frequency: * * * * *
```

Then verify:

```bash
cd /home/<account>/www/medixlab
php artisan schedule:list     # shows "0 2 * * * php artisan backup:database"
```

`backup:database` shells out to `mysqldump`. Confirm it exists:

```bash
which mysqldump
```

If it is not on PATH, set the absolute path in `.env` and re-cache config:

```env
MYSQLDUMP_PATH=/usr/bin/mysqldump
```

The MySQL user in `.env` must have `SELECT`, `LOCK TABLES`, `SHOW VIEW` and `TRIGGER` privileges.
The `storage/app/backups/` directory must be writable (already used by the manual test in §3).
First scheduled run: check `storage/logs/backups.log` the morning after.

---

## 8. Post-deploy smoke checklist

| # | Check | Expected |
|---|---|---|
| 1 | `php artisan migrate:status` | all `Ran`, none Pending |
| 2 | `php artisan route:list \| grep -E "two-factor\|gdpr\|activity"` | routes listed |
| 3 | Log in as `admin@medix.com` | dashboard loads |
| 4 | Profile → **Gérer la 2FA** → enable with authenticator app → log out → log in | 6-digit code requested, then dashboard |
| 5 | Admin sidebar | shows **Activite** and **RGPD / Données** |
| 6 | Admin → Activite | recent actions listed (login/export/erase) |
| 7 | Admin → RGPD → search a user → **Exporter (JSON)** | JSON file downloaded from `storage/app/gdpr/exports` |
| 8 | Doctor → open a **completed** request → **Télécharger PDF** | downloads `rapport-medix-{id}.pdf` |
| 9 | Center → enter a result with **⚠ Critique** | purple critical badge renders; saved with status `critical` |
| 10 | Center → Samples → create sample → submit the form twice | second submit redirects to the existing sample (no duplicate) |
| 11 | Center → scan a sample barcode | sample details returned, scan logged |
| 12 | `php artisan backup:database` | new `.gz` in `storage/app/backups`, old ones pruned (keep 14) |
| 13 | `tail storage/logs/backups.log` | backup logged |
| 14 | Load re-test (light 600 req / heavy 800 req) | no 5xx, p99 within prior envelope |

---

## 9. Rollback

- **Schema**: backups exist (§3). To undo this release: `php artisan migrate:rollback --step=5`.
  Note: `--step=5` restores the pre-release schema; the 2FA columns and audit log are dropped,
  and the unique samples index is removed (re-check for duplicates before re-applying).
- **Code**: `git revert <release-commit>` or checkout the previous tag, then re-run
  `composer install --no-dev` and the three cache commands from §4.
- **RBAC**: new features/actions can be archived from Admin → Modules (no SQL needed).

---

## 10. Notes / follow-ups

- No 2FA recovery codes are stored: if an admin loses their authenticator, clear the columns via:
  `php artisan tinker --execute="App\Models\User::where('email','admin@medix.com')->update(['two_factor_secret'=>null,'two_factor_confirmed_at'=>null]);"`
- `gdpr:erase {user} --hard` cascades to clinical records (FK `cascadeOnDelete`) — verify on a copy of production before using it live.
- The scheduler cron is the only piece that cannot be tested locally; validate it the morning after deployment.
