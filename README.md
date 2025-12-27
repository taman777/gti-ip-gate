# gti-ip-gate

GTI lightweight IP-based access gate.  
Directory-level IP restriction without WordPress or any framework.

---

## What is this?

**gti-ip-gate** is a minimal IP-based access control system designed to protect
a directory using only `.htaccess` and plain PHP.

- No WordPress
- No Framework
- No Database
- Easy recovery (FTP only)

Inspired by **IP Login Restrictor**, but completely standalone.

---

## Directory Structure

```text
/root_dir
 └─ .htaccess        (IP gate & router)

/admin
 ├─ index.php        (core controller)
 ├─ admin.php        (dashboard)
 └─ allow_ips_conf.cgi (allowed IP list)
