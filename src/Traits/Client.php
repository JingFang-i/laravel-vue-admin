<?php


namespace Jmhc\Admin\Traits;


use Jmhc\Admin\Exceptions\HttpRequestClientException;
use Illuminate\Support\Facades\Log;

trait Client
{


    /**
     * 获取GuzzleHttpClient实例
     * @return \GuzzleHttp\Client
     */
    protected function client()
    {
        return new \GuzzleHttp\Client([
            'base_uri' => $this->baseUrl,
        ]);
    }

    /**
     * 发送get请求
     * @param $urlPath
     * @param array $params
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function httpGetRequest($urlPath, $params = [], $headers = [])
    {
        $options = [];
        if (!empty($params)) {
            $options['query'] = $params;
        }
        if (!empty($headers)) {
            $options['headers'] = $headers;
        }
        return $this->request($urlPath, $options, 'GET');
    }

    /**
     * 发送post请求
     * @param $urlPath
     * @param array $params
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function httpPostRequest($urlPath, $params = [], $headers = [])
    {
        $options = [];
        if (!empty($params)) {
            $options['form_params'] = $params;
        }
        if (!empty($headers)) {
            $options['headers'] = $headers;
        }
        return $this->request($urlPath, $options, 'POST');
    }

    /**
     * 发出请求
     * @param $urlPath
     * @param $params
     * @param string $method
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function request($urlPath, $params, $method = 'GET')
    {
        $response = $this->client()->request($method, $urlPath, $params);
        if ($response->getStatusCode() !== 200) {
            throw new HttpRequestClientException('请求失败');
        }
        $body = (string) $response->getBody();
        $content = json_decode($body, true);
        if (empty($content)) {
            $content = $body;
        }
        return $content;
    }
}
