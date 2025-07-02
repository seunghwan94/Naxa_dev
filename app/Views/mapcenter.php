<?php
// .env 에 넣어둔 NAVER 키
$naverKey = getenv('NAVER_MAP_KEY');
?>
<!-- ──────────────────────────────── 상단 -->
<div class="container">
  <h2 class="map-title">🗺️ 지도 중간점 찾기</h2>

  <!-- 🔍 검색·현재위치·모드 -->
  <div style="margin:1rem 0; display:flex; flex-wrap:wrap; gap:.5rem;">
    <input id="searchInput"
           placeholder="장소·주소 검색"
           style="flex:1 1 250px; padding:.45rem 1rem; border-radius:8px; border:1px solid #ccc;">
    <button id="search-btn">🔍 검색</button>
    <button id="loc-btn">📍 현재 위치</button>

    <!-- 4 가지 계산 모드 -->
    <select id="modeSel" style="padding:.45rem 1rem; border-radius:8px;">
      <option value="distance">거리(직선)</option>
      <!-- <option value="subway">지하철</option>
      <option value="bus">버스</option>
      <option value="mix">지하철+버스</option> -->
    </select>
  </div>

  <!-- 🗺️ 지도 -->
  <div id="map-container">
    <div id="map"
         style="width:100%; height:500px; border-radius:18px;
                overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,.1);"></div>

    <!-- 🎨 팔레트 & 리셋·캡처 -->
    <div class="palette-panel">
      <label for="colorPicker">팔레트:</label>
      <input type="color" id="colorPicker" value="#6366f1">
      <button id="reset-btn">전체 지우기</button>
      <button id="capture-btn">지도 캡처</button>
    </div>

    <!-- 📋 좌표 목록 -->
    <div class="points-list"></div>
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

function initMap () {
  /* ───────── 상태 변수 ───────── */
  const pts    = [];           // {lat,lng,color,marker}
  const lines  = [];           // naver.maps.Polyline[]
  let centerMk = null;         // 중간점 마커
  let curCol   = document.getElementById('colorPicker').value;

  /* ───────── 지도 생성 ───────── */
  const map = new naver.maps.Map('map', {
    center: new naver.maps.LatLng(37.5665,126.9780), // (서울 시청)
    zoom  : 12,
    zoomControl: true,
    zoomControlOptions: { position: naver.maps.Position.TOP_RIGHT }
  });

  /* 🎨 팔레트 */
  document.getElementById('colorPicker').addEventListener('input', e => {
    curCol = e.target.value;
  });

  /* 🖱️ 지도 클릭 → 점 추가 */
  naver.maps.Event.addListener(map, 'click', e => {
    addPoint(e.coord.lat(), e.coord.lng(), curCol);
  });

  /* 📍 현재 위치 */
  document.getElementById('loc-btn').onclick = () => {
    if (!navigator.geolocation) return alert('브라우저가 위치 기능을 지원하지 않습니다');
    navigator.geolocation.getCurrentPosition(
      pos => {
        const { latitude:lat, longitude:lng } = pos.coords;
        map.setCenter(new naver.maps.LatLng(lat,lng));
        addPoint(lat, lng, '#0ea5e9');
      },
      () => alert('현재 위치를 가져올 수 없습니다')
    );
  };

  /* 🔍 검색 */
  document.getElementById('search-btn').onclick = () => {
    const q = document.getElementById('searchInput').value.trim();
    if (!q) return;

    naver.maps.Service.geocode({ query:q }, (status, res) => {
      if (status !== naver.maps.Service.Status.OK || !res.result.items.length) {
        return alert('검색 결과가 없습니다');
      }
      const { y:lat, x:lng } = res.result.items[0].point;
      map.setCenter(new naver.maps.LatLng(+lat, +lng));
      addPoint(+lat, +lng, curCol);
    });
  };

  /* ➕ 점 추가 함수 */
  function addPoint(lat, lng, color) {
    const marker = new naver.maps.Marker({
      position : new naver.maps.LatLng(lat,lng),
      map      : map,
      draggable: true,
      icon     : {
        content:`<div style="width:18px;height:18px;border-radius:50%;
                          background:${color};border:3px solid #fff;
                          box-shadow:0 0 0 2px rgba(0,0,0,.4);"></div>`,
        anchor : new naver.maps.Point(9,9)
      }
    });

    const p = { lat, lng, color, marker };
    pts.push(p);

    // 드래그해도 즉시 재계산
    marker.addListener('dragend', () => {
      const pos = marker.getPosition();
      p.lat = pos.lat(); p.lng = pos.lng();
      redraw();
    });

    redraw();
  }

  /* 🔄 전체 UI 다시 그리기 */
  async function redraw() {
    // 선·중앙 삭제
    lines.forEach(l => l.setMap(null));
    lines.length = 0;
    if (centerMk) { centerMk.setMap(null); centerMk = null; }

    // ▶ 중간점 계산 (2개 이상일 때)
    if (pts.length > 1) {
      const mode = document.getElementById('modeSel').value;
      const resp = await fetch(`${API_BASE}/midpoint`, {
        method : 'POST',
        headers: { 'Content-Type':'application/json' },
        body   : JSON.stringify({
                    mode,
                    origins: pts.map(p => [p.lat, p.lng])
                  })
      }).then(r=>r.ok ? r.json(): null);

      if (!resp || !resp.ok) {
        alert('중간점 계산 실패');
        return;
      }

      const { midLat, midLng, paths } = resp;

      // ☆ 중심 마커
      centerMk = new naver.maps.Marker({
        position: new naver.maps.LatLng(midLat, midLng),
        map,
        icon:{ content:`<div style="width:26px;height:26px;border-radius:50%;
                              background:#facc15;border:3px solid #fff;
                              box-shadow:0 0 0 2px #f59e0b;"></div>`,
               anchor : new naver.maps.Point(13,13) }
      });

      // ☆ 선 그리기
      paths.forEach((path, idx) => {
        lines.push(new naver.maps.Polyline({
          path: path.map(([lat,lng]) => new naver.maps.LatLng(lat,lng)),
          strokeColor : pts[idx].color,
          strokeWeight: 4,
          strokeOpacity:.8,
          map
        }));
      });
    }

    renderList();
  }

  /* 📝 목록 렌더 */
  function renderList() {
    document.querySelector('.points-list').innerHTML =
      pts.map((p,i)=>`
        <div class="pt-row">
          <span class="pt-no" style="background:${p.color}">${i+1}</span>
          <span class="pt-coord">${p.lat.toFixed(5)}, ${p.lng.toFixed(5)}</span>
          <button class="del-btn" onclick="removePt(${i})">삭제</button>
        </div>
      `).join('');
  }
  window.removePt = i => { pts[i].marker.setMap(null); pts.splice(i,1); redraw(); };

  /* 🧹 전체 지우기 */
  document.getElementById('reset-btn').onclick = () => {
    pts.forEach(p=>p.marker.setMap(null)); pts.length = 0;
    lines.forEach(l=>l.setMap(null));      lines.length= 0;
    if (centerMk) centerMk.setMap(null);
    redraw();
  };

  /* 📸 캡처(안내) */
  document.getElementById('capture-btn').onclick =
    () => alert('html2canvas 등 라이브러리를 사용해 캡처 기능을 구현하세요');
}
</script>
