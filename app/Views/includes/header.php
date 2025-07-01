<!-- app/Views/includes/header.php -->
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>Naxa.dev – Portfolio Showcase</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">

  <meta name="description" content="창의적인 개발자 Naxa의 프로젝트 포트폴리오. 혁신적인 웹 애플리케이션과 창의적인 솔루션을 만나보세요.">
  <meta name="keywords" content="웹개발, 포트폴리오, 개발자, JavaScript, PHP, React, 프론트엔드, 백엔드, Naxa">
  <meta name="author" content="Naxa">
  <meta name="robots" content="index, follow">
  <meta name="language" content="Korean">

  <meta property="og:type" content="website">
  <meta property="og:url" content="https://naxa.dev/">
  <meta property="og:title" content="Naxa.dev - Portfolio Showcase">
  <meta property="og:description" content="창의적인 개발자 Naxa의 프로젝트 포트폴리오. 혁신적인 웹 애플리케이션과 창의적인 솔루션을 만나보세요.">
  <meta property="og:image" content="<?= base_url('static/img/logo.png') ?>">
  <meta property="og:image:width" content="512">
  <meta property="og:image:height" content="512">
  <meta property="og:site_name" content="Naxa.dev">
  <meta property="og:locale" content="ko_KR">

  <meta property="twitter:card" content="summary_large_image">
  <meta property="twitter:url" content="https://naxa.dev/">
  <meta property="twitter:title" content="Naxa.dev - Portfolio Showcase">
  <meta property="twitter:description" content="창의적인 개발자 Naxa의 프로젝트 포트폴리오. 혁신적인 웹 애플리케이션과 창의적인 솔루션을 만나보세요.">
  <meta property="twitter:image" content="<?= base_url('static/img/logo.png') ?>">

  <link rel="apple-touch-icon" href="<?= base_url('static/img/apple-touch-icon.png') ?>">
  <link rel="icon" type="image/x-icon" href="<?= base_url('static/img/favicon.ico') ?>">
  <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('static/img/favicon-16x16.png') ?>">
  <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('static/img/favicon-32x32.png') ?>">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
  <?php if (isset($isQrPage) && $isQrPage): ?>
    <link rel="stylesheet" href="<?= base_url('css/qr.css') ?>">
  <?php endif; ?>

  <!-- Font Awesome -->
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
    crossorigin="anonymous"
    referrerpolicy="no-referrer"
  />

  <!-- Google AdSense (광고, 필요시) -->
  <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7090845684640158"
     crossorigin="anonymous"></script>
</head>
<body>
  <div class="bg-animation"></div>

  <div class="header">
    <div class="header-logo">
      <img src="<?= base_url('static/img/logo.png') ?>" alt="Naxa.dev Logo">
    </div>
    <h1>🚀 Naxa.dev</h1>
    <p>"If you fail, it's experience; if you succeed, it's your career"</p>
  </div>
