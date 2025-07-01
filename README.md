# Naxa.dev – CodeIgniter4 프로젝트

## 📝 프로젝트 개요

* CodeIgniter4 프레임워크 구조
* 정적 리소스, 데이터 파일, 동적 라우트, 환경별 분기(.env), Nginx 실서버 배포까지 표준 방식 준수

---

## 📂 폴더 구조 (2025.07 기준)

```
/ (프로젝트 루트)
├── app/
│   ├── Controllers/
│   │   ├── Home.php          # 메인 페이지 컨트롤러
│   │   └── Qr.php            # QR 코드 생성기 컨트롤러
│   ├── Views/
│   │   ├── includes/
│   │   │   ├── header.php    # 공통 헤더
│   │   │   └── footer.php    # 공통 푸터
│   │   ├── index.php         # 메인(포트폴리오) 뷰
│   │   └── qr.php            # QR 코드 생성기 뷰
│   └── Config/
│       └── Routes.php        # 라우터 설정
├── public/
│   ├── css/
│   │   ├── style.css         # 메인 스타일시트
│   │   └── qr.css            # QR 코드 전용 CSS
│   ├── static/
│   │   ├── img/
│   │   │   ├── logo.png      # 로고 이미지 등
│   │   │   ├── post.png
│   │   │   └── ...
│   │   ├── programs.prod.json # 운영용 데이터
│   │   └── programs.dev.json  # 개발용 데이터
│   ├── favicon.ico
│   └── ... (기타 정적 리소스)
├── writable/                 # CI4 로그, 캐시, 세션(깃 관리 제외)
├── .env                      # 환경 설정(개발/운영 분기, baseURL)
├── .gitignore                # Git 버전관리 예외 설정
├── README.md                 # 프로젝트 소개 문서 (이 파일)
└── ... (기타 CI4 표준 파일)
```

---

## 🔧 주요 수정/이관 내용

1. 프로젝트 전체를 CI4 디렉토리 구조에 맞게 재구성
2. 정적 리소스(css, img 등)와 programs.json은 public/ 하위로 이동
3. 공통 헤더/푸터 분리: app/Views/includes/header.php, footer.php

   * base\_url() 함수로 모든 경로 통일
4. 메인 페이지(index.php)는 CI4 뷰로 이관

   * 데이터는 컨트롤러에서 읽어서 뷰에 변수로 전달
5. QR 코드 생성기

   * qr.php를 app/Views/qr.php로 이관
   * Qr.php 컨트롤러로 라우트
   * 페이지 전용 CSS/JS/헤더 분기 처리
6. 환경별 데이터 분기

   * public/static/programs.dev.json, public/static/programs.prod.json
   * 컨트롤러에서 ENVIRONMENT에 따라 자동 분기
7. .env에서 baseURL, 환경별 config만 조정하면 코드 수정 불필요
8. Nginx 실서버 배포: root를 public/으로, try\_files/rewrites까지 CI4에 맞춤
9. .gitignore 작성

   * vendor/, writable/, .env, uploads/, IDE, OS 파일 등 포함

---

## 🚀 개발/운영 분기 예시 (.env)

```ini
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost/ci/public/'
# 운영 시: app.baseURL = 'https://naxa.dev/'
```

---

## 🖥️ Nginx 배포 예시

```nginx
server {
    listen 80;
    server_name naxa.dev www.naxa.dev;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl;
    server_name naxa.dev www.naxa.dev;
    root /var/www/html/public;     # ← 반드시 public
    index index.php index.html;
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }
    error_log  /var/log/nginx/naxa.error.log warn;
    access_log /var/log/nginx/naxa.access.log;
}
```

---

## 🛠️ 커밋 & 푸시 예시

```bash
git init
git add .
git commit -m "First CI4 migration, structure + main/QR page 이관"
git remote add origin https://github.com/seunghwan94/Naxa_dev.git
git push -u origin main
```

---

## 🤝 기여/문의

* 개발/운영 환경에 따라 .env, programs.json 등만 교체!
* 문의/이슈: [GitHub Issue](https://github.com/seunghwan94/Naxa_dev/issues) 또는 [naxa.dev](https://naxa.dev)

---

**실제 사용 환경에 맞게 항목/설명을 자유롭게 추가해도 OK!**
**특정 세부내용(예: DB, API 연동, 배포 자동화 등) 필요하면 언제든 요청 주셔도 맞춤 안내드립니다.**
