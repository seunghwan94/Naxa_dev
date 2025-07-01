<div class="container">
  <!-- 프로젝트 현황 통계 -->
  <div class="stats">
    <h2 style="color: var(--text); margin-bottom: 1rem;">프로젝트 현황</h2>
    <div class="stats-grid">
      <div class="stat-item">
        <div class="stat-number"><?= count($programs) ?></div>
        <div class="stat-label">완성된 프로젝트</div>
      </div>
      <div class="stat-item">
        <div class="stat-number">∞</div>
        <div class="stat-label">아이디어</div>
      </div>
      <div class="stat-item">
        <div class="stat-number">24/7</div>
        <div class="stat-label">개발 열정</div>
      </div>
    </div>
  </div>

  <!-- 프로그램 카드 그리드 -->
  <div class="grid">
    <?php foreach ($programs as $index => $p): ?>

      <!-- (1) 네 번째 카드 뒤에 광고 삽입 -->
      <?php if ($index === 3): ?>
        <div class="card ad-card" style="animation-delay: <?= ($index + 0.1) * 0.1 ?>s;">
          <!-- Google AdSense 광고 슬롯 예시 -->
          <ins class="adsbygoogle"
               style="display:block"
               data-ad-client="ca-pub-XXXXXXXXXXXX"
               data-ad-slot="YYYYYYYYYY"
               data-ad-format="auto"
               data-full-width-responsive="true"></ins>
          <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
          </script>
        </div>
      <?php endif; ?>

      <!-- (2) 일반 프로그램 카드 -->
      <div class="card" style="animation-delay: <?= $index * 0.1 ?>s;">
        <div class="card-image">
          <img src="<?= htmlspecialchars($p['img']) ?>"
               alt="<?= htmlspecialchars($p['title']) ?>"
               loading="lazy">
        </div>
        <div class="card-content">
          <h2 class="card-title"><?= htmlspecialchars($p['title']) ?></h2>
          <p class="card-description"><?= htmlspecialchars($p['description']) ?></p>
          <div class="card-detail"><?= htmlspecialchars($p['detail']) ?></div>
          <a href="<?= htmlspecialchars($p['url']) ?>"
             class="card-link">바로가기 →</a>
        </div>
      </div>

    <?php endforeach; ?>
  </div>
</div>