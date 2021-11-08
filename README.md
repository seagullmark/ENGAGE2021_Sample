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
データベースへのアクセス情報を記載します。

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
