# 会員サイトログインサンプル
FileMaker Data API を利用したシンプルなPHPアプリケーションサンプルです。
初めてPHPアプリケーションを作る方を対象にサンプルを作成しました。  
※注意  
CSRFトークン、例外処理などは組み込んでいません。ご自身で実装してみてください。

■機能
- 会員登録
- ログイン
- ログアウト
- パスワード変更

■ファイル構成
```
Sample/
  ├ engage2021.fmp12
  ├ login.php
  ├ logout.php
  ├ myFm.class.php
  ├ pwd_edit.php
  ├ user_create.php
  └ user.php
 ```
 ■engage2021.fmp12  
 完全アクセス  
　　アカウント名：Admin  
　　パスワード：demo  

■myFm.class.php  
データベースに接続処理するクラスファイルです。
```
<?php

const DB_USER = 'web'; // データベースユーザ名
const DB_PWD = 'demo'; // データベースパスワード
const DB_NAME = 'engage2021'; // データベース名
const DB_HOST = '192.168.3.16'; // ホスト
const API_SSL_VERIFY = FALSE; // SSLの検証を行うか
const API_VERSION = 'vLatest'; // FileMaker Data API のバージョン

class myFm
{
```
データベースへのアクセス情報を記載します。webユーザー（アカウント）には fmrest 拡張アクセス権(FileMaker Data API でのアクセス)が設定されています。

```
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
```
データベースにはPHPのcURL関数を利用して接続します。  
上記載の関数は以下の内容(ログイン成功例)を返却します。

```
array(2) {
  ["ret"]=>
  array(2) {
    ["response"]=>
    array(2) {
      ["dataInfo"]=>
      array(6) {
        ["database"]=>
        string(10) "engage2021"
        ["layout"]=>
        string(5) "users"
        ["table"]=>
        string(5) "users"
        ["totalRecordCount"]=>
        int(1)
        ["foundCount"]=>
        int(1)
        ["returnedCount"]=>
        int(1)
      }
      ["data"]=>
      array(1) {
        [0]=>
        array(4) {
          ["fieldData"]=>
          array(8) {
            ["主キー"]=>
            string(36) "DC1B6557-B772-43BA-91F5-D9BBEC173A39"
            ["メール"]=>
            string(23) "nishino@seagullmark.com"
            ["パスワード"]=>
            string(60) "$2y$10$yWiiNYYIlvRLBXrP.iT/bODeCj0FWiUeLrcJtOv1PmUSbaRm3wWyG"
            ["氏名"]=>
            string(15) "西野マサキ"
            ["作成情報タイムスタンプ"]=>
            string(19) "11/08/2021 11:09:08"
            ["作成者"]=>
            string(3) "web"
            ["修正情報タイムスタンプ"]=>
            string(19) "11/08/2021 11:09:08"
            ["修正者"]=>
            string(3) "web"
          }
          ["portalData"]=>
          array(0) {
          }
          ["recordId"]=>
          string(1) "7"
          ["modId"]=>
          string(1) "0"
        }
      }
    }
    ["messages"]=>
    array(1) {
      [0]=>
      array(2) {
        ["code"]=>
        string(1) "0"
        ["message"]=>
        string(2) "OK"
      }
    }
  }
  ["info"]=>
  array(37) {
    ["url"]=>
    string(78) "https://192.168.3.16/fmi/data/vLatest/databases/engage2021/layouts/users/_find"
    ["content_type"]=>
    string(31) "application/json; charset=utf-8"
    ["http_code"]=>
    int(200)
    ["header_size"]=>
    int(322)
    ["request_size"]=>
    int(235)
    ["filetime"]=>
    int(-1)
    ["ssl_verify_result"]=>
    int(19)
    ["redirect_count"]=>
    int(0)
    ["total_time"]=>
    float(0.205378)
    ["namelookup_time"]=>
    float(2.2E-5)
    ["connect_time"]=>
    float(0.004455)
    ["pretransfer_time"]=>
    float(0.039249)
    ["size_upload"]=>
    float(83)
    ["size_download"]=>
    float(596)
    ["speed_download"]=>
    float(2907)
    ["speed_upload"]=>
    float(404)
    ["download_content_length"]=>
    float(596)
    ["upload_content_length"]=>
    float(83)
    ["starttransfer_time"]=>
    float(0.039283)
    ["redirect_time"]=>
    float(0)
    ["redirect_url"]=>
    string(0) ""
    ["primary_ip"]=>
    string(12) "192.168.3.16"
    ["certinfo"]=>
    array(0) {
    }
    ["primary_port"]=>
    int(443)
    ["local_ip"]=>
    string(10) "172.18.0.3"
    ["local_port"]=>
    int(45456)
    ["http_version"]=>
    int(3)
    ["protocol"]=>
    int(2)
    ["ssl_verifyresult"]=>
    int(0)
    ["scheme"]=>
    string(5) "HTTPS"
    ["appconnect_time_us"]=>
    int(39112)
    ["connect_time_us"]=>
    int(4455)
    ["namelookup_time_us"]=>
    int(22)
    ["pretransfer_time_us"]=>
    int(39249)
    ["redirect_time_us"]=>
    int(0)
    ["starttransfer_time_us"]=>
    int(39283)
    ["total_time_us"]=>
    int(205378)
  }
}
```
