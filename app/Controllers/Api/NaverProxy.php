<?php
namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class NaverProxy extends ResourceController
{
    private string $keyId;
    private string $secret;

    public function __construct()
    {
        $this->keyId  = getenv('NAVER_MAP_KEY_ID');
        $this->secret = getenv('NAVER_MAP_KEY_SECRET');
    }

    /**
     * GET /api/geocode?query=...
     * 프런트-엔드에서 직접 호출(공개키만 필요) – 그대로 사용
     */
    public function geocode()
    {
        $query = $this->request->getGet('query');
        $url   = 'https://naveropenapi.apigw.ntruss.com/map-geocode/v2/geocode?query='
               . urlencode($query);

        $res = $this->curlGet($url, [
            'X-NCP-APIGW-API-KEY-ID: ' . $this->keyId,
            'X-NCP-APIGW-API-KEY: '    . $this->secret,
            'Accept: application/json'
        ]);

        return $this->respond($res, 200);
    }

    /**
     * POST /api/naver/directions
     * body: { "start":"lng,lat", "goal":"lng,lat", "mode":"subway|bus|mix" }
     */
    public function directions()
    {
        $json  = $this->request->getJSON(true);
        $mode  = $json['mode'] ?? 'mix';

        // NAVER Directions5: public-transit
        //   - API path는 동일하고 옵션만 바꿔 호출
        $option = match($mode){
            'subway' => 'SUBWAY',
            'bus'    => 'BUS',
            default  => 'SUBWAY_BUS'
        };

        $url = 'https://naveropenapi.apigw.ntruss.com/map-direction/v1/public'
             . '?start=' . $json['start']
             . '&goal='  . $json['goal']
             . '&option=' . $option;

        $timestamp = floor(microtime(true) * 1000);
        $sig       = base64_encode(
            hash_hmac(
                'sha256',
                "GET {$url}\n{$timestamp}\n{$this->keyId}",
                $this->secret,
                true
            )
        );

        $res = $this->curlGet($url, [
            "X-NCP-APIGW-TIMESTAMP: {$timestamp}",
            "X-NCP-APIGW-API-KEY-ID: {$this->keyId}",
            "X-NCP-APIGW-SIGNATURE-V2: {$sig}",
            'Accept: application/json'
        ]);

        return $this->respond($res, 200);
    }

    /* ------------- 공통 cURL helper ------------- */
    private function curlGet(string $url, array $headers): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $headers
        ]);
        $out = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        return $err ? ['ok'=>false,'msg'=>$err]
                    : json_decode($out, true);
    }
}
