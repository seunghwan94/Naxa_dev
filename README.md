
# Naxa.dev – CodeIgniter4 기반 포트폴리오/QR 코드 사이트

## 프로젝트 개요
- PHP 단일 파일 기반 사이트를 CodeIgniter4(CI4) 프레임워크로 이관
- 환경별 개발/운영 분리, 정적/동적 데이터 구조화
- Nginx 실서버 기준 public/ 디렉토리 배포

---

## 📂 폴더 구조
```
/ (프로젝트 루트)
├── app/
│   ├── Controllers/
│   │   └── Home.php
│   ├── Views/
│   │   ├── includes/
│   │   │   ├── header.php
│   │   │   └── footer.php
│   │   ├── index.php
│   │   └── qr.php
├── public/
│   ├── css/
│   │   ├── style.css
│   │   └── qr.css
│   ├── static/
│   │   ├── img/
│   │   │   ├── logo.png
│   │   │   └── post.png
│   │   ├── programs.prod.json
│   │   └── programs.dev.json
│   └── favicon.ico
├── writable/
│   ├── cache/
│   ├── logs/
├── .env
├── .gitignore
├── README.md
```

---

## 주요 설정 및 트러블슈팅

### 1. writable/cache 권한 문제
```bash
sudo mkdir -p /var/www/html/writable/cache
sudo chown -R www-data:www-data /var/www/html/writable
sudo chmod -R 775 /var/www/html/writable
sudo systemctl restart apache2    # 또는 php8.3-fpm
```

### 2. intl, mbstring PHP 확장 미설치 에러
```bash
sudo apt install php-intl php-mbstring
sudo systemctl restart apache2
# 또는
sudo systemctl restart php8.3-fpm
```

### 3. 환경별 .env 설정 예시
개발 (로컬)
```
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost/ci/public/'
```
운영 (서버)
```
CI_ENVIRONMENT = production
app.baseURL = 'https://naxa.dev/'
```

### 4. Nginx 배포 예시
```nginx
server {
    listen 80;
    server_name naxa.dev www.naxa.dev;
    return 301 https://$host$request_uri;
}
server {
    listen 443 ssl;
    server_name naxa.dev www.naxa.dev;
    root /var/www/html/public;
    index index.php index.html;
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }
    location ~* \.json$ { deny all; }
    error_log  /var/log/nginx/naxa.error.log warn;
    access_log /var/log/nginx/naxa.access.log;
}
```

---

## .gitignore 예시
```
/writable/*
!/writable/cache/
/writable/cache/*
!/writable/logs/
/writable/logs/*
/vendor/
.env
/node_modules/
/uploads/
.DS_Store
*.swp
*.swo
*.bak
*.tmp
.idea/
.vscode/
```

---

## 문의
- https://naxa.dev
- https://github.com/seunghwan94/Naxa_dev
