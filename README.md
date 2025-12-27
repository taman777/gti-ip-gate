# gti-ip-gate

GTI製の **軽量 IP ベース・アクセスゲート**です。  
WordPress やフレームワークに依存せず、**ディレクトリ単位で IP 制限を行う**ことを目的としています。

モダンな管理画面を備え、ブラウザ上から安全にアクセス許可設定をメンテナンスできます。

---

## 特徴

- **ブラウザベースの管理画面**: 洗練された UI で IP アドレス（CIDR対応）を管理可能。
- **二重の保護**: IP 制限に加えて、管理画面自体を ID/パスワードで保護。
- **軽量・高速**: データベース不要。`.htaccess` + プレーン PHP のみで動作。
- **安心設計**: 設定はテキストファイル保存。FTP だけで万が一の復旧が可能。
- **セキュア**: 設定ファイル（.cgi）への直接アクセスは禁止済み。

---

## ディレクトリ構成

```text
/root_dir
 ├─ .htaccess               # 全てのリクエストをゲートへ集約する司令塔
 └─ /admin
     ├─ index.php           # アクセス制御を行うコントローラ
     ├─ admin.php           # 管理ダッシュボード（IP・認証管理）
     ├─ allow_ips_conf.cgi  # 許可IP設定ファイル（自動生成）
     └─ admin_conf.cgi      # 管理者認証設定ファイル（自動生成）
```

---

## セットアップ

### 1. ファイルの配置
`root_dir` の中身を、保護したいディレクトリにアップロードします。

### 2. 初期ログイン
ブラウザで `/admin.php` を含む URL にアクセスします。
- **初期ID**: `admin`
- **初期パスワード**: `admin`

※ ログイン後、画面下部の設定アイコンから ID とパスワードを必ず変更してください。

### 3. IP の追加
管理画面に表示されている「あなたの現在のIPアドレス」を確認し、「このIPを追加」ボタンを押して登録します。

---

## 仕様

- **アクセス制限**: 許可された IP 以外からのアクセスはすべて `403 Forbidden` となります。
- **パス制限**: 許可 IP であっても、明示的に `admin.php` を指定しないアクセス（ルートアクセスなど）は、セキュリティのため禁止（403）しています。
- **CIDR対応**: `192.168.1.0/24` などのネットワーク単位での指定も可能です。

---

## 復旧方法（締め出し事故など）

誤って自分の IP を削除したり、パスワードを忘れたりした場合は、FTP で以下の操作を行ってください。

1.  **IP制限の解除**: `admin/allow_ips_conf.cgi` に直接自分の IP を追記するか、`.htaccess` を一時的にリネームします。
2.  **認証のリセット**: `admin/admin_conf.cgi` を削除するか、中身を `admin:admin` に書き換えます。

---

## ライセンス

MIT License

---

## 作者

株式会社ジーティーアイ（GTI）  
https://gti.co.jp  

---

# gti-ip-gate (English Summary)

**gti-ip-gate** is a lightweight, standalone IP-based access gateway.

- **Admin UI**: Manage allowed IPs and CIDR blocks via a modern browser interface.
- **Double Security**: Protected by both IP restriction and ID/Password authentication.
- **No Database**: Uses flat files (.cgi) for configuration, ensuring easy portability and recovery.
- **Setup**: Login to `/admin.php` with `admin:admin` and add your current IP.
