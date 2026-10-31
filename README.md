# Student dashboard + messaging

A small PHP/MySQL web app for students. You sign up, keep a to-do list, play some music while you work, and message other students on the platform.

I made it to get comfortable with plain PHP (no framework), PDO and sessions, and to see how a login system and a chat actually fit together.

## What it does

- Register and log in. Passwords are hashed with `password_hash`, and the session id is regenerated on login.
- Task list with a title, due date, priority and description. You can filter by priority or search by keyword. You only ever see your own tasks.
- A basic audio player with previous/next. The tracks live in the `music/` folder.
- Messaging between two students. There are no websockets: the page asks the server for new messages every 3 seconds. It's not elegant, but it works and it's easy to follow.

## Running it

You need PHP 8+ and MySQL or MariaDB.

```bash
cd proj2
cp config.example.php config.local.php    # put your DB name, user and password in it
mysql -u root -e "CREATE DATABASE etud CHARACTER SET utf8mb4"
php setup.php                             # creates the tables, then locks itself
php -S localhost:8080
```

Then open `http://localhost:8080/register.php`. Create two accounts and log into them in two different browsers to try the chat.

If you'd rather not use a config file, `DB_HOST`, `DB_NAME`, `DB_USER` and `DB_PASS` as environment variables work too.

## Layout

```
proj2/
  connect.php, config.example.php   database connection
  setup.php                         one-time table creation
  register.php, login.php, logout.php
  dashboard.php, process_task.php   tasks and filters
  play_audio.php, music/            the player
  messages/                         chat (messages.php, get.php, send.php, messages.js)
```

## A bug I found in my own code

While reviewing this project I noticed the chat was open to stored XSS. `messages.js` put message text straight into `innerHTML`, so someone could send `<img src=x onerror=...>` and the script would run in the other person's browser. The PHP side was escaping things correctly, which is why I missed it for a while.

It now builds the message with `textContent`, so anything a user types is shown as plain text. I tested it with a few payloads before and after.

While I was in there I also:

- stopped calling `htmlspecialchars()` before saving to the database. It was storing `O&#039;Brien` instead of `O'Brien`. I escape when displaying now.
- removed the hardcoded database credentials from the repo
- made `setup.php` refuse to run from outside localhost, and refuse to run twice
- renamed `acc.htaccess` to `.htaccess`. With the old name Apache never read it, so the "block config.php" rule did nothing.

## What's still missing

I'd want these before calling it production-ready:

- CSRF tokens on the forms and on `send.php`
- rate limiting on login
- a Content-Security-Policy header (the pages use some inline scripts, so I'd have to move those out first)
- `HttpOnly` / `SameSite` flags on the session cookie
- real-time messages instead of polling
- file attachments in chat (the `file_path` column exists in the table, but nothing uses it yet)

## Stack

PHP (PDO), MySQL, vanilla JavaScript, CSS.
