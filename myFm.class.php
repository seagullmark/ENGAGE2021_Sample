<?php

const DB_USER = 'web'; // データベースユーザ名
const DB_PWD = 'demo'; // データベースパスワード
const DB_NAME = 'engage2021'; // データベース名
const DB_HOST = '192.168.3.16'; // ホスト
const API_SSL_VERIFY = FALSE; // SSLの検証を行うか
const API_VERSION = 'vLatest'; // FileMaker Data API のバージョン

class myFm
{
    public $options;
    private $httpHeader;
    private $token;

    function __construct(){

        $this->options = array();
        $this->options['ssl_verify'] = API_SSL_VERIFY;

        $authorization = 'Basic ' . base64_encode(DB_USER . ':' . DB_PWD);
        $this->httpHeader = array(
            'Authorization: ' . $authorization,
            'Content-Type: application/json'
        );

        $result = $this->fmLogin();
        $this->token = $result['ret']['response']['token'];
    }

    function __destruct(){
        
        $this->fmLogOut();
    }

    public function curl($url, $method, $data = ''){

        $conn = curl_init();
        curl_setopt($conn, CURLOPT_HTTPHEADER, $this->httpHeader);
        curl_setopt($conn, CURLOPT_CUSTOMREQUEST, $method);
        if($method === 'POST' || $method === 'PATCH'){
            curl_setopt($conn, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($conn, CURLOPT_URL, $url);
        curl_setopt($conn, CURLOPT_RETURNTRANSFER, TRUE);

        if($this->options['ssl_verify'] === FALSE){
            curl_setopt($conn, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($conn, CURLOPT_SSL_VERIFYHOST, FALSE);
        }

        $ret = curl_exec($conn);
        $info = curl_getinfo($conn);

        $errno = curl_errno($conn);
        $error = curl_error($conn);
        // echo $errno.' - '.$error;

        curl_close($conn);

        $result = array(
            'ret' => json_decode($ret, TRUE),
            'info' => $info
        );

        return $result;
    }

    public function fmLogin(){

        //fmi/data/{version}/databases/{database}/sessions
        $sUrl = 'https://' . DB_HOST . '/fmi/data/' . API_VERSION . '/databases/' . DB_NAME . '/sessions';
        $result = $this->curl($sUrl, 'POST', '', $options);

        return $result;
    }

    public function fmLogOut(){

        //fmi/data/{version}/databases/{database}/sessions/{sessionToken}
        $sUrl = 'https://' . DB_HOST . '/fmi/data/' . API_VERSION . '/databases/' . DB_NAME . '/sessions/' . $this->token;
        $this->httpHeader = array(
            'Content-Type: application/json'
        );
        $result = $this->curl($sUrl, 'DELETE', '');

        return $result;
    }

    public function find($data, $layout){

        //fmi/data/{version}/databases/{database}/layouts/{layout}/_find
        $sUrl = 'https://' . DB_HOST . '/fmi/data/' . API_VERSION . '/databases/' . DB_NAME . '/layouts/' . $layout . '/_find';
        $authorization = 'Bearer ' . $this->token;
        $this->httpHeader = array(
            'Authorization: ' . $authorization,
            'Content-Type: application/json'
        );
        $result = $this->curl($sUrl, 'POST', $data);
        
        return $result;
    }
    
    public function getRecord($recordId, $layout){
        
        //fmi/data/{version}/databases/{database}/layouts/{layout}/records/{recordId}
        $sUrl = 'https://' . DB_HOST . '/fmi/data/' . API_VERSION . '/databases/' . DB_NAME . '/layouts/' . $layout . '/records/' . $recordId;
        $authorization = 'Bearer ' . $this->token;
        $this->httpHeader = array(
            'Authorization: ' . $authorization,
            'Content-Type: application/json'
        );
        $result = $this->curl($sUrl, 'GET', '');
        
        return $result;
    }

    public function editRecord($recordId, $layout, $data){
        
        //fmi/data/{version}/databases/{database}/layouts/{layout}/records/{recordId}
        $sUrl = 'https://' . DB_HOST . '/fmi/data/' . API_VERSION . '/databases/' . DB_NAME . '/layouts/' . $layout . '/records/' . $recordId;
        $authorization = 'Bearer ' . $this->token;
        $this->httpHeader = array(
            'Authorization: ' . $authorization,
            'Content-Type: application/json'
        );
        $result = $this->curl($sUrl, 'PATCH', $data);
        
        return $result;
    }

    public function createRecord($layout, $data){
        
        //fmi/data/{version}/databases/{database}/layouts/{layout}/records
        $sUrl = 'https://' . DB_HOST . '/fmi/data/' . API_VERSION . '/databases/' . DB_NAME . '/layouts/' . $layout . '/records';
        $authorization = 'Bearer ' . $this->token;
        $this->httpHeader = array(
            'Authorization: ' . $authorization,
            'Content-Type: application/json'
        );
        $result = $this->curl($sUrl, 'POST', $data);
        
        return $result;
    }

}
