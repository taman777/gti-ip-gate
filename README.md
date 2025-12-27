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


## in English

**gti-ip-gate** is a lightweight, IP-based access gate developed by GTI.  
It provides **directory-level IP restriction** without relying on WordPress,
frameworks, or databases.

This project is inspired by *IP Login Restrictor*, redesigned as a
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
    └─ Entry point and IP gate

/admin
 ├─ index.php
 │   └─ Main controller
 ├─ admin.php
 │   └─ Admin dashboard (actual logic)
 ├─ allow_ips_conf.sample.cgi
 │   └─ Sample IP configuration
 └─ allow_ips_conf.cgi
     └─ Active IP configuration file
