<?php
namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class Midpoint extends ResourceController
{
    protected $format = 'json';

    public function index()
    {
        $req  = $this->request->getJSON(true);

        // ① points 또는 origins 어느 키든 허용
        $orig = $req['points'] ?? $req['origins'] ?? [];
        $mode = $req['mode']   ?? 'distance';

        if (count($orig) < 2) {
            return $this->respond(['ok' => false, 'msg' => '2개 이상 지점 필요'], 400);
        }

        // ② 좌표 배열로 표준화  [lat,lng]
        $coords = array_map(function ($p) {
            // 객체 → 배열 변환
            if (is_array($p) && isset($p['lat'], $p['lng'])) {
                return [floatval($p['lat']), floatval($p['lng'])];
            }
            // 이미 [0,1] 인덱스 배열
            if (is_array($p) && isset($p[0], $p[1])) {
                return [floatval($p[0]), floatval($p[1])];
            }
            // 잘못된 형식
            throw new \RuntimeException('invalid point format');
        }, $orig);

        /* ──────────── distance 모드만 우선 구현 ─────────── */
        if ($mode !== 'distance') {
            return $this->respond(['ok' => false, 'msg' => '현재 distance 모드만 지원'], 501);
        }

        // ③ 산술 중심
        $mid = $this->mean($coords);   // [lat,lng]

        // ④ 각 점 ↔ 중심 선
        $paths = [];
        foreach ($coords as $c) {
            $paths[] = [ $c, $mid ];   // [[lat,lng], [lat,lng]]
        }

        return $this->respond([
            'ok'     => true,
            'midLat' => $mid[0],
            'midLng' => $mid[1],
            'paths'  => $paths
        ]);
    }

    /** 간단 평균 */
    private function mean(array $pts): array
    {
        $lat = $lng = 0.0;
        foreach ($pts as $p) { $lat += $p[0]; $lng += $p[1]; }
        $n = count($pts);
        return [$lat / $n, $lng / $n];
    }
}
