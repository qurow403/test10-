# test10

# 環境構築
Docker ビルド
1.git clone git@github.com:qurow403/test10-.git
2.docker-compose up -d --build

---

Lavavel 環境構築
1.docker-compose exec php bash
2.composer install
3.cp .env.example .env
4..env ファイルの変更

　DB_HOSTをmysqlに変更
　DB_DATABASEをlaravel_dbに変更
　DB_USERNAMEをlaravel_userに変更
　DB_PASSをlaravel_passに変更

5.php artisan key:generate
6.php artisan migrate
7.php artisan test

---

Nuxt 環境構築
1.node -v
2.yarn install
3.cp .env.example .env
4.`.env`ファイルの編集

　FRONTEND_URL=http://localhost:3000
　FIREBASE_CREDENTIALS=/path/to/your/firebase-adminsdk.json  ← 各自で配置した JSON のパスを指定

5.yarn run dev

---

Firebase 認証について

- Firebase のサービスアカウント JSON ファイルは Git では共有していません。
- 開発者は Firebase コンソールから自分の JSON ファイルを取得し、`FIREBASE_CREDENTIALS` に指定してください。
- ファイル例: `src/storage/firebase/firebase-adminsdk.json`
- `.env` に正しいパスが設定されていないと、Firebase 認証は動作しません。

---

# ER図
![ER図](docs/er_diagram.png)

---

# 使用技術
・PHP 8.4.3
・Laravel Framework 8.83.29
・nuxt@4.1.2
・axios@1.12.2
・vue@3.5.22
・Node.js v22.19.0
・Yarn 1.22.22

---

# URL
・開発環境：http://localhost/  http://localhost:3000
・新規登録ページ：http://localhost:3000/register
・ログインページ：http://localhost:3000/login
・投稿一覧ページ：http://localhost:3000/posts
・投稿詳細ページ：http://localhost:3000/posts/{id}
・phpMyAdmin：http://localhost:8080/

