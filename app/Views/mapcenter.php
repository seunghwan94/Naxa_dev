<?php
// .env 에 넣어둔 NAVER 키
$naverKey = getenv('NAVER_MAP_KEY');
?>
<!-- ──────────────────────────────── 상단 -->
<div class="container">
  <h2 class="map-title">🗺️ 지도 중간점 찾기</h2>

  <!-- 🗺️ 지도 -->
  <div id="map-container">
    <div id="map"
         style="width:100%; height:500px; border-radius:18px;
                overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,.1);"></div>

    <!-- 🎨 팔레트 & 리셋·캡처·현재위치 -->
    <div class="palette-panel" style="margin-top:.75rem; display:flex; flex-wrap:wrap; gap:.5rem;">
      <label for="colorPicker">팔레트:</label>
      <input type="color" id="colorPicker">
      <button id="loc-btn">📍 현재 위치</button>
      <button id="reset-btn">전체 지우기</button>
      <button id="capture-btn">지도 캡처</button>
    </div>

    <!-- 📋 좌표 목록 -->
    <div class="points-list" style="margin-top:1rem;"></div>
  </div>
</div>

<!-- ──────────────────────────────── 스크립트 -->
<!-- ① NAVER 지도 SDK (geocoder 포함) -->
<script src="https://oapi.map.naver.com/openapi/v3/maps.js?ncpKeyId=<?= $naverKey ?>&submodules=geocoder&callback=initMap"
        defer></script>

<!-- ② 인증 실패 콜백 -->
<script>window.navermap_authFailure = () => {
  alert('네이버 지도 인증 실패: 키 또는 도메인 등록 확인!');
};</script>

<!-- ③ 메인 로직 -->
<script defer>
const API_BASE = '<?= site_url('api') ?>';   // /api/… 경로

/* ───────── 유틸 : 노란 계열(45°~65°) 제외한 HEX 랜덤 ───────── */
function randomColor () {
  let h;
  do { h = Math.floor(Math.random()*360); } while (45 <= h && h <= 65); // 노란색 제외
  const s = 0.7 + Math.random()*0.2;   // 0.70–0.90
  const l = 0.45 + Math.random()*0.15; // 0.45–0.60

  // HSL → RGB → HEX
  const a = s * Math.min(l, 1 - l);
  const f = n => {
    const k = (n + h / 30) % 12;
    const c = l - a * Math.max(Math.min(k - 3, 9 - k, 1), -1);
    return Math.round(255 * c).toString(16).padStart(2, '0');
  };
  return `#${f(0)}${f(8)}${f(4)}`;
}

function initMap () {
  /* ───────── 상태 ───────── */
  const pts = [], lines = [];
  let centerMk = null;

  let currentColor = randomColor();  // 최초 색
  let manualMode   = false;          // false → 랜덤, true → 고정

  /* 🎨 팔레트 초기 값 세팅 */
  const colorPicker = document.getElementById('colorPicker');
  colorPicker.value = currentColor;

  /* ───────── 지도 ───────── */
  const map = new naver.maps.Map('map', {
    center: new naver.maps.LatLng(37.5665, 126.9780),
    zoom  : 12,
    zoomControl: true,
    zoomControlOptions: { position: naver.maps.Position.TOP_RIGHT }
  });

  /* 팔레트 변경 → 고정 모드 전환 */
  colorPicker.addEventListener('input', e => {
    currentColor = e.target.value;
    manualMode   = true;
  });

  /* 🖱️ 지도 클릭 → 점 추가 */
  naver.maps.Event.addListener(map, 'click', e => {
    // 랜덤 모드면 새 색 생성 & 팔레트 반영
    if (!manualMode) {
      currentColor   = randomColor();
      colorPicker.value = currentColor;
    }
    addPoint(e.coord.lat(), e.coord.lng(), currentColor);
  });

  /* 📍 현재 위치 */
  document.getElementById('loc-btn').onclick = () => {
    if (!navigator.geolocation)
      return alert('브라우저가 위치 기능을 지원하지 않습니다');
    navigator.geolocation.getCurrentPosition(
      pos => {
        const { latitude: lat, longitude: lng } = pos.coords;
        map.setCenter(new naver.maps.LatLng(lat, lng));
        addPoint(lat, lng, '#0ea5e9');
      },
      () => alert('현재 위치를 가져올 수 없습니다')
    );
  };

  /* ➕ 점 추가 */
  function addPoint(lat, lng, color) {
    const marker = new naver.maps.Marker({
      position : new naver.maps.LatLng(lat, lng),
      map, draggable: true,
      icon:{ content:`<div style="width:18px;height:18px;border-radius:50%;
                              background:${color};border:3px solid #fff;
                              box-shadow:0 0 0 2px rgba(0,0,0,.4);"></div>`,
             anchor:new naver.maps.Point(9,9)}
    });
    const p = {lat,lng,color,marker};
    pts.push(p);

    marker.addListener('dragend', () => {
      const pos = marker.getPosition();
      p.lat = pos.lat(); p.lng = pos.lng();
      redraw();
    });

    redraw();
  }

  /* 🔄 재렌더 */
  async function redraw() {
    lines.forEach(l=>l.setMap(null)); lines.length = 0;
    if (centerMk) { centerMk.setMap(null); centerMk = null; }

    if (pts.length > 1) {
      const resp = await fetch(`${API_BASE}/midpoint`, {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body:JSON.stringify({mode:'distance', origins:pts.map(p=>[p.lat,p.lng])})
      }).then(r=>r.ok ? r.json() : null);

      if (!resp || !resp.ok) { alert('중간점 계산 실패'); return; }

      const { midLat, midLng, paths } = resp;
      centerMk = new naver.maps.Marker({
        position:new naver.maps.LatLng(midLat, midLng), map,
        icon:{content:`<div style="width:26px;height:26px;border-radius:50%;
                                background:#facc15;border:3px solid #fff;
                                box-shadow:0 0 0 2px #f59e0b;"></div>`,
              anchor:new naver.maps.Point(13,13)}
      });

      paths.forEach((path, idx) => {
        lines.push(new naver.maps.Polyline({
          path:path.map(([lat,lng])=>new naver.maps.LatLng(lat,lng)),
          strokeColor:pts[idx].color, strokeWeight:4, strokeOpacity:.8, map
        }));
      });
    }
    renderList();
  }

  /* 📝 목록 */
  function renderList(){
    document.querySelector('.points-list').innerHTML =
      pts.map((p,i)=>`
        <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.25rem;">
          <span style="display:inline-block;width:22px;height:22px;border-radius:50%;
                       background:${p.color};color:#fff;font-size:.8rem;
                       display:flex;align-items:center;justify-content:center;">${i+1}</span>
          <span style="flex:1;">${p.lat.toFixed(5)}, ${p.lng.toFixed(5)}</span>
          <button onclick="removePt(${i})">삭제</button>
        </div>`).join('');
  }
  window.removePt = i => { pts[i].marker.setMap(null); pts.splice(i,1); redraw(); };

  /* 🧹 전체 지우기 & 모드 초기화(랜덤) */
  document.getElementById('reset-btn').onclick = () => {
    pts.forEach(p=>p.marker.setMap(null)); pts.length = 0;
    lines.forEach(l=>l.setMap(null));      lines.length = 0;
    if (centerMk) centerMk.setMap(null);
    manualMode   = false;
    currentColor = randomColor();
    colorPicker.value = currentColor;
    redraw();
  };

  /* 📸 캡처(안내) */
  document.getElementById('capture-btn').onclick =
    () => alert('html2canvas 등 라이브러리를 사용해 캡처 기능을 구현하세요');
}
</script>
