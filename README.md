# gti-ip-gate

GTI製の **軽量 IP ベース・アクセスゲート**です。  
WordPress やフレームワークに依存せず、  
**ディレクトリ単位で IP 制限を行う**ことを目的としています。

IP Login Restrictor の思想をベースに、  
よりシンプルで安全な **スタンドアロン版**として設計されています。

---

## 特徴

- WordPress 非依存
- フレームワーク不要
- データベース不要
- `.htaccess` + プレーン PHP のみ
- 設定はテキストファイル 1 枚
- FTP だけで復旧可能（締め出し事故に強い）

---

## 想定用途

- 管理画面・検証環境の IP 制限
- 開発途中サイトの限定公開
- BASIC 認証を使わないクローズド環境
- CMS を使わないシンプルな管理画面の保護

---

## ディレクトリ構成

```text
/root_dir
 └─ .htaccess
    └─ すべてのアクセスを制御するゲート

/admin
 ├─ index.php
 │   └─ すべての処理を受けるコントローラ
 ├─ admin.php
 │   └─ 管理ダッシュボード（実処理）
 ├─ allow_ips_conf.sample.cgi
 │   └─ IP 設定サンプル
 └─ allow_ips_conf.cgi
     └─ 実際に使用する IP 設定ファイル
```

---

## 初期セットアップ（重要）

### 1. IP 設定ファイルを準備する

サンプルファイルをリネームします。

```bash
admin/allow_ips_conf.sample.cgi
↓
admin/allow_ips_conf.cgi
```

### 2. 自分の IP アドレスを追加する

`admin/allow_ips_conf.cgi` を編集し、  
**必ず最初に自分の IP アドレスを記述してください。**

```text
# Allowed IPs
203.0.113.10
198.51.100.0/24
```

- 1行に1IPを記述します
- CIDR 表記に対応しています
- `#` から始まる行はコメントとして扱われます

⚠️ **注意**  
ここに自分の IP を記述しないまま設置すると、  
即座にアクセスできなくなります。

---

## 締め出してしまった場合の復旧方法

1. FTP やファイルマネージャーでサーバーに接続
2. `admin/allow_ips_conf.cgi` に自分の IP を追加
3. もしくは一時的に `.htaccess` を無効化

データベース操作や PHP ファイルの編集は不要です。

---

## セキュリティ設計について

- Apache レベル + PHP レベルの二重チェック
- 管理ディレクトリ（`admin`）の直アクセスは禁止
- 認証機構を持たず、IP 制限に特化

※ 本ツールは **認証システムではありません**。  
IP ベースのアクセス制御専用ツールです。

---

## ライセンス

MIT License

---

## 作者

株式会社ジーティーアイ（GTI）  
https://gti.co.jp  

GitHub: https://github.com/taman777

---

---

# gti-ip-gate (English)

**gti-ip-gate** is a lightweight, IP-based access gate developed by GTI.  
It provides **directory-level IP restriction** without relying on WordPress,
frameworks, or databases.

This project is inspired by *IP Login Restrictor* and redesigned as a  
**fully standalone and minimal solution**.

---

## Features

- No WordPress dependency
- No framework
- No database
- Uses only `.htaccess` and plain PHP
- File-based configuration
- Easy recovery via FTP (lockout-safe)

---

## Use Cases

- Restrict admin or management pages by IP
- Protect staging or development environments
- Create closed-access environments without BASIC authentication
- Secure simple admin panels without CMS dependencies

---

## Directory Structure

```text
/root_dir
 └─ .htaccess

/admin
 ├─ index.php
 ├─ admin.php
 ├─ allow_ips_conf.sample.cgi
 └─ allow_ips_conf.cgi
```

---

## Initial Setup (IMPORTANT)

### 1. Prepare the IP configuration file

Rename the sample file:

```bash
admin/allow_ips_conf.sample.cgi
↓
admin/allow_ips_conf.cgi
```

### 2. Add your IP address

Edit `admin/allow_ips_conf.cgi` and  
**make sure to add your own IP address first.**

```text
# Allowed IPs
203.0.113.10
198.51.100.0/24
```

- One IP per line
- CIDR notation is supported
- Lines starting with `#` are treated as comments

⚠️ **WARNING**  
If your IP address is not listed in this file,  
access will be denied immediately.

---

## Recovery (Lockout Prevention)

If you accidentally block yourself:

1. Connect to the server via FTP or file manager
2. Add your IP address to `admin/allow_ips_conf.cgi`
3. Or temporarily disable the `.htaccess` file

No database access or PHP code editing is required.

---

## License

MIT License

---

## Author

GTI  
https://gti.co.jp  

Maintained by @taman777
