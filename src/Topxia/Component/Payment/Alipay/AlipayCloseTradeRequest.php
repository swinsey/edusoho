<?php
namespace Topxia\Component\Payment\Alipay;

use Topxia\Component\Payment\Request;

class AlipayCloseTradeRequest {

    protected $url = 'https://mapi.alipay.com/gateway.do';

    public function __construct(array $options = null)
    {
        $this->options = $options;
    }

    public function setParams(array $params)
    {
        $this->params = $params;
        return $this;
    }

    public function closeTrade() {
        $result = $this->postRequest($this->url, $this->convertParams($this->params));
        var_dump($result);
        return json_decode($result);
    }

    private function postRequest($url, $params)
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Topxia Payment Client 1.0');
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 0);

        if (!empty($params)) {
            $url = $url . (strpos($url, '?') ? '&' : '?') . http_build_query($params);
        }
var_dump($url);
        curl_setopt($curl, CURLOPT_URL, $url );

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }


    private function signParams($params) {
        unset($params['sign_type']);
        unset($params['sign']);

        ksort($params);

        $sign = '';
        foreach ($params as $key => $value) {
            if (empty($value)) {
                continue;
            }
            $sign .= $key . '=' . $value . '&';
        }
        $sign = substr($sign, 0, - 1);
        $sign .=$this->options['secret'];

        return md5($sign);
    }

    private function convertParams($params)
    {
        $converted = array();

        $converted['_input_charset'] = 'utf-8';
        $converted['partner'] = $this->options['key'];
        $converted['trade_role'] = 'B';
        $converted['sign_type'] = 'MD5';
        $converted['out_trade_no'] = $params['sn'];
        $converted['service'] = 'close_trade';

        $converted['sign'] = $this->signParams($converted);
var_dump(json_encode($converted));
        return $converted;
    }

}