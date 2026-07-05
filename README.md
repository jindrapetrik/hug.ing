# hug.ing - send virtual hugs

Sometimes, all it takes is knowing that someone is thinking of you.
Send a virtual hug to someone you care about.
Add a short message, share the link, and give someone a small moment of warmth and joy.

## Source code & License
Source code of the whole website is available under [license AGPLv3](LICENSE.md).

## How to run on your own server
1. Setup Apache server with PHP 8.1+
2. Setup MariaDB/MySQL database.
3. Create database structure using [hug.table.sql](sources/hug.table.sql) script.
4. Copy `config.default.php` to `config.php`
   - update appropriate configuration (db connection, server URL)
5. Copy `ver.default.php` to `ver.php`
   - set the page version for display in the footer

## Author
Jindra Petřík (aka JPEXS) is the author.
