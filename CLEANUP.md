# Repo cleanup commands (run from the repo root)

```bash
# 1. Remove the dead config with fake credentials (it is never required by any file)
git rm proj2/config.php

# 2. Remove the old dashboard (history keeps it if you ever need it)
git rm -r proj2/old

# 3. The htaccess file was named "acc.htaccess", so Apache never loaded it.
git rm proj2/acc.htaccess        # replaced by the new proj2/.htaccess

# 4. Copy in the fixed files from dashboard-fixes/proj2/ (overwrite), then:
git add -A
git commit -m "Fix stored XSS in chat, move DB credentials out of repo, lock setup.php, add README"
```

Optional: the old fake password string is still in git history. It is a placeholder, so rewriting history is not needed,
but never reuse it as a real password.

Existing rows: accounts registered before this fix have names/emails stored HTML-escaped (e.g. `O&#039;Brien`).
For a demo DB the simplest fix is to truncate `students` and re-register.
