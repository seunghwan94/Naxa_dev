<div class="container">
  <h2 class="map-title">지도 중간점 찾기</h2>
  <div id="map-container">
    <div id="map" style="width:100%;height:500px;border-radius:18px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.1);"></div>
    <div class="palette-panel">
      <label>팔레트:</label>
      <input type="color" id="colorPicker" value="#6366f1">
      <button id="reset-btn">전체 지우기</button>
      <button id="capture-btn">지도 캡처</button>
    </div>
    <div class="points-list"></div>
  </div>
</div>

<script src="https://dapi.kakao.com/v2/maps/sdk.js?appkey=83b4a2b729c87cb2f88423eda6d77583"></script>
<script>
    console.log(window.kakao);
const points = []; // {lat, lng, color, marker}
const lines = [];
let centerMarker = null;

// 지도 초기화
let map = new kakao.maps.Map(document.getElementById('map'), {
  center: new kakao.maps.LatLng(37.5665, 126.9780), // 서울 기준
  level: 6
});

const colorPicker = document.getElementById('colorPicker');
let currentColor = colorPicker.value;

colorPicker.addEventListener('input', e => {
  currentColor = e.target.value;
});

// 지도 클릭: 마커 추가
kakao.maps.event.addListener(map, 'click', function(mouseEvent) {
  const latlng = mouseEvent.latLng;
  addPoint(latlng.getLat(), latlng.getLng(), currentColor);
});

function addPoint(lat, lng, color) {
  const marker = new kakao.maps.Marker({
    position: new kakao.maps.LatLng(lat, lng),
    map: map,
    image: new kakao.maps.MarkerImage(
      `https://api.geoapify.com/v1/icon/?type=awesome&color=${color.replace('#','')}&size=large&icon=marker`,
      new kakao.maps.Size(40, 50), {offset: new kakao.maps.Point(20, 50)}
    )
  });
  points.push({lat, lng, color, marker});
  updateUI();
}

// UI 업데이트
function updateUI() {
  // 선 및 중심 마커/이전 점 삭제
  lines.forEach(line => line.setMap(null));
  lines.length = 0;
  if (centerMarker) { centerMarker.setMap(null); centerMarker = null; }

  // 중심 계산
  if (points.length > 1) {
    let center = getCenter(points);
    centerMarker = new kakao.maps.Marker({
      position: new kakao.maps.LatLng(center.lat, center.lng),
      map: map,
      image: new kakao.maps.MarkerImage(
        "https://t1.daumcdn.net/localimg/localimages/07/mapapidoc/markerStar.png",
        new kakao.maps.Size(36, 36)
      )
    });
    // 각 점-중심까지 선(각 점 색상)
    points.forEach(pt => {
      const line = new kakao.maps.Polyline({
        path: [
          new kakao.maps.LatLng(pt.lat, pt.lng),
          new kakao.maps.LatLng(center.lat, center.lng)
        ],
        strokeWeight: 3,
        strokeColor: pt.color,
        strokeOpacity: 0.8,
        strokeStyle: 'solid'
      });
      line.setMap(map);
      lines.push(line);
    });
  }

  // 리스트 갱신
  let html = points.map((pt,i)=>`
    <div class="pt-row">
      <span class="pt-no" style="background:${pt.color}">${i+1}</span>
      <span class="pt-coord">${pt.lat.toFixed(5)}, ${pt.lng.toFixed(5)}</span>
      <button class="del-btn" onclick="removePoint(${i})">삭제</button>
    </div>
  `).join('');
  document.querySelector('.points-list').innerHTML = html;
}

// 중심점 계산(산술평균)
function getCenter(arr) {
  let lat = arr.reduce((s,v)=>s+v.lat,0)/arr.length;
  let lng = arr.reduce((s,v)=>s+v.lng,0)/arr.length;
  return {lat,lng};
}

// 삭제
window.removePoint = function(idx) {
  points[idx].marker.setMap(null);
  points.splice(idx,1);
  updateUI();
}

// 전체 지우기
document.getElementById('reset-btn').onclick = function() {
  points.forEach(p=>p.marker.setMap(null));
  points.length=0;
  if (centerMarker) centerMarker.setMap(null);
  lines.forEach(line=>line.setMap(null));
  lines.length=0;
  updateUI();
}

// 지도 캡처(스크린샷) - canvas2image.js 등 활용 (가이드만, 실제는 JS 라이브러리 필요)
document.getElementById('capture-btn').onclick = function() {
  alert("구현: html2canvas 등 라이브러리 필요");
}
</script>
