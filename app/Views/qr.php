<div class="container">
  <!-- QR 코드 미리보기 패널 -->
  <div class="panel qr-display">
    <h2 class="panel-title"><span>📱</span> QR 코드 미리보기</h2>
    <div class="qr-container">
      <div id="qr-code"></div>
      <div id="qr-placeholder" class="qr-placeholder">
        데이터를 입력하면 QR 코드가 생성됩니다
      </div>
    </div>
    <div class="download-section">
      <button id="download-png" class="download-btn" disabled>PNG 다운로드</button>
      <button id="download-svg" class="download-btn" disabled>SVG 다운로드</button>
    </div>
  </div>

  <!-- 입력 + 고급 설정 패널 -->
  <div class="panel">
    <h2 class="panel-title"><span>✏️</span> 데이터 입력</h2>

    <div class="input-group">
      <label class="input-label" for="data-input">데이터</label>
      <input
        type="text"
        id="data-input"
        class="input-field"
        placeholder="https://example.com"
      />
    </div>

    <div class="type-buttons">
      <button class="type-btn active" data-type="url"><span>🔗</span> URL(도메인)</button>
      <button class="type-btn"         data-type="phone"><span>📞</span> 전화</button>
      <button class="type-btn"         data-type="email"><span>📧</span> 이메일</button>
      <button class="type-btn"         data-type="memo"><span>📝</span> 메모</button>
    </div>

    <div class="settings-section">
      <h3 class="panel-title" style="font-size:1.2rem;"><span>🎨</span> 고급 설정</h3>
      <div class="settings-grid">
        <div class="input-group">
          <label class="input-label" for="shape-select">모양 스타일</label>
          <select id="shape-select" class="select-field">
            <option value="square">사각형</option>
            <option value="rounded">둥근 모서리</option>
            <option value="dots">점 모양</option>
            <option value="classy">클래식</option>
            <option value="classy-rounded">클래식 둥근</option>
          </select>
        </div>

        <div class="input-group">
          <label class="input-label" for="logo-input">로고 삽입 (선택)</label>
          <input type="file" id="logo-input" class="file-input" accept="image/*">
        </div>

        <div class="input-group">
          <label class="input-label" for="size-select">크기</label>
          <select id="size-select" class="select-field">
            <option value="200">작음 (200px)</option>
            <option value="300">보통 (300px)</option>
            <option value="400">큼 (400px)</option>
            <option value="500">매우 큼 (500px)</option>
          </select>
        </div>

        <div class="input-group">
          <label class="input-label" for="color-mode">색상 모드</label>
          <select id="color-mode" class="select-field">
            <option value="single">단색</option>
            <option value="gradient">그라데이션</option>
          </select>
        </div>

        <div class="input-group">
          <label class="input-label" for="color1">기본 색상</label>
          <input type="color" id="color1" class="input-field" value="#000000">
        </div>

        <div class="input-group gradient-settings" style="display:none;">
          <label class="input-label" for="color2">보조 색상</label>
          <input type="color" id="color2" class="input-field" value="#6366f1">
        </div>
        <div class="input-group gradient-settings" style="display:none;">
          <label class="input-label" for="gradient-rotation">그라데이션 회전(°)</label>
          <input type="number" id="gradient-rotation" class="input-field" value="0" min="0" max="360">
        </div>

      </div>
    </div>
  </div>
</div>

<!-- 공통 푸터 직전에만 페이지 전용 스크립트 삽입 -->
<script src="https://cdn.jsdelivr.net/npm/qr-code-styling@1.9.2/lib/qr-code-styling.js"></script>
<script>
  let qrCode, currentType = 'url', hasData = false;

  function initQRCode(size) {
    qrCode = new QRCodeStyling({
      width:  size,
      height: size,
      margin: 10,
      data: "",
      imageOptions: { crossOrigin: "anonymous", margin: 10, imageSize: 0.3 },
      qrOptions: { errorCorrectionLevel: 'H' },
      dotsOptions: { type: 'square', color: '#000000' },
      backgroundOptions: { color: '#ffffff' },
      cornersSquareOptions: { type: 'square', color: '#000000' },
      cornersDotOptions: { type: 'square', color: '#000000' }
    });
  }

  function showToast(msg, type = 'success') {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = `toast show ${type}`;
    setTimeout(() => t.classList.remove('show'), 3000);
  }

  function updatePlaceholder() {
    const ph = document.getElementById('qr-placeholder'),
          qc = document.getElementById('qr-code'),
          btns = document.querySelectorAll('.download-btn');
    if (hasData) {
      ph.style.display = 'none';
      qc.style.display = 'block';
      btns.forEach(b => b.disabled = false);
    } else {
      ph.style.display = 'flex';
      qc.style.display = 'none';
      btns.forEach(b => b.disabled = true);
    }
  }

  function updateQRCodeStyle() {
    const mode      = document.getElementById('color-mode').value;
    const shapeType = document.getElementById('shape-select').value;
    const c1        = document.getElementById('color1').value;
    if (mode === 'single') {
      qrCode.update({
        dotsOptions: { type: shapeType, color: c1 }
      });
    } else {
      const c2  = document.getElementById('color2').value;
      const rot = parseInt(document.getElementById('gradient-rotation').value, 10);
      qrCode.update({
        dotsOptions: {
          type: shapeType,
          gradient: {
            type: 'linear',
            rotation: rot,
            colorStops: [
              { offset: 0, color: c1 },
              { offset: 1, color: c2 }
            ]
          }
        }
      });
    }
  }

  function updateQRData() {
    const raw = document.getElementById('data-input').value.trim();
    if (!raw) {
      hasData = false;
      updatePlaceholder();
      return;
    }
    let payload = raw;
    if (currentType === 'phone') payload = `tel:${raw}`;
    if (currentType === 'email') payload = `mailto:${raw}`;
    hasData = true;
    qrCode.update({ data: payload });
    if (!document.getElementById('qr-code').hasChildNodes()) {
      qrCode.append(document.getElementById('qr-code'));
    }
    updatePlaceholder();
    updateQRCodeStyle();
  }

  function updateInputPlaceholder() {
    const phs = {
      url:   'https://example.com',
      phone: '010-1234-5678',
      email: 'example@email.com',
      memo:  '메모를 입력하세요…'
    };
    document.getElementById('data-input').placeholder = phs[currentType];
  }

  document.addEventListener('DOMContentLoaded', () => {
    // 1) 기본 크기 설정: 모바일(≤480px)=200, 그 외=300
    const sizeSelect = document.getElementById('size-select');
    const defaultSize = window.innerWidth <= 480 ? '200' : '300';
    sizeSelect.value = defaultSize;
    initQRCode(parseInt(defaultSize, 10));

    updatePlaceholder();
    updateInputPlaceholder();
    updateQRCodeStyle();

    // 타입 버튼
    document.querySelectorAll('.type-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('.type-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentType = btn.dataset.type;
        updateInputPlaceholder();
        updateQRData();
      });
    });

    // 입력 이벤트
    document.getElementById('data-input').addEventListener('input', updateQRData);

    // 모양 변경
    document.getElementById('shape-select').addEventListener('change', updateQRCodeStyle);

    // 크기 변경
    sizeSelect.addEventListener('change', e => {
      const s = parseInt(e.target.value, 10);
      qrCode.update({ width: s, height: s });
      updateQRCodeStyle();
    });

    // 색상 모드 토글
    document.getElementById('color-mode').addEventListener('change', e => {
      document.querySelectorAll('.gradient-settings')
        .forEach(el => el.style.display = e.target.value === 'gradient' ? 'block' : 'none');
      updateQRCodeStyle();
    });

    // 색상 변경
    ['color1','color2','gradient-rotation'].forEach(id => {
      document.getElementById(id).addEventListener('input', updateQRCodeStyle);
    });

    // 로고 업로드
    document.getElementById('logo-input').addEventListener('change', e => {
      const file = e.target.files[0];
      if (!file) return qrCode.update({ image: undefined });
      if (file.size > 2 * 1024 * 1024) {
        showToast('파일 크기는 2MB 이하여야 합니다.', 'error');
        e.target.value = '';
        return;
      }
      const reader = new FileReader();
      reader.onload = evt => {
        qrCode.update({
          image: evt.target.result,
          imageOptions: { crossOrigin: "anonymous", margin: 10, imageSize: 0.3 }
        });
        showToast('로고가 추가되었습니다.');
      };
      reader.onerror = () => showToast('파일을 읽는 중 오류가 발생했습니다.', 'error');
      reader.readAsDataURL(file);
    });

    // 다운로드 핸들러
    document.getElementById('download-png').addEventListener('click', () => {
      if (!hasData) return;
      qrCode.download({ name:`qr-${Date.now()}`, extension:'png' });
      showToast('PNG 다운로드 완료.');
    });
    document.getElementById('download-svg').addEventListener('click', () => {
      if (!hasData) return;
      qrCode.download({ name:`qr-${Date.now()}`, extension:'svg' });
      showToast('SVG 다운로드 완료.');
    });
  });
</script>