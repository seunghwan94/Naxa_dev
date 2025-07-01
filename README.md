
# Naxa.dev [CodeIgniter4] 

## 프로젝트 개요
- CodeIgniter4(CI4) - php 8.1 이상
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
### 1. 필수 패키지 및 PHP 환경 준비
```
sudo apt update && sudo apt upgrade -y
sudo apt install nginx php8.3 php8.3-fpm php8.3-cli php8.3-mbstring php8.3-intl php8.3-xml php8.3-mysql php8.3-curl php8.3-zip unzip git -y

cd /var/www/html
sudo git clone https://github.com/seunghwan94/Naxa_dev.git .

# 반드시 public/ writable/ 권한 부여(서버 오류 99% 방지!)
sudo chown -R www-data:www-data /var/www/html
sudo chmod -R 755 /var/www/html
sudo chmod -R 777 /var/www/html/writable
sudo mkdir -p /var/www/html/writable/cache /var/www/html/writable/logs
sudo chown -R www-data:www-data /var/www/html/writable
```

### 2. 환경별 .env 설정 예시
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

    ssl_certificate     /etc/ssl/naxa/cert.pem;
    ssl_certificate_key /etc/ssl/naxa/private.key;
    ssl_protocols       TLSv1.2 TLSv1.3;
    ssl_ciphers         HIGH:!aNULL:!MD5;

    root /var/www/html/public;         # ← public으로 변경
    index index.php index.html;

    # 정적 파일/라우팅 처리
    location / {
        try_files $uri $uri/ /index.php?$query_string;    # CI4 필수!
    }

    # PHP 처리
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }

    # JSON 파일 외부 차단
    location ~* \.json$ {
        deny all;
    }

    error_log  /var/log/nginx/naxa.error.log warn;
    access_log /var/log/nginx/naxa.access.log;
}

```

---

## 문의
- https://naxa.dev
- https://github.com/seunghwan94/Naxa_dev
