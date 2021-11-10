<?php
include_once('myFm.class.php');
session_start();

if(isset($_SESSION['id'])){
    header('Location: user.php');
    exit;
}

// エラーメッセージの初期化
$errors = array();

// ログインボタンが押されたら
if(isset($_POST['login'])){

    if(empty($_POST['mail'])){
        $errors['mail'] = 'メールアドレスが未入力です。';
    } 
    if(empty($_POST['password'])){
        $errors['password'] = 'パスワードが未入力です。';
    }
    
    if(!empty($_POST['mail']) && !empty($_POST['password'])){

        $myFm = new myFm;

        // REQUEST 生成
        $query = array();
        $query[] = array(
            'メール' => '==' . $_POST['mail']
        );
        $data = array(
            'query' => $query,
            'offset' => 1,
            'limit' => 1
        );
        $data = json_encode($data, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP);
        //
        
        $result = $myFm->find($data, 'users');

        $err = $result['ret']['messages'][0]['code'];

        if($err === '0'){

            $fieldData = $result['ret']['response']['data'][0]['fieldData'];
            if(password_verify($_POST['password'], $fieldData['パスワード'])){ 
                $_SESSION['id'] = $fieldData['主キー'];
                $_SESSION['message'] = 'ログインしました。';
                header('Location: user.php');
                exit();
            }
        }

        $errors['login'] = 'メールアドレスまたはパスワードに誤りがあります。';
    }
}

?>

<h1>ログイン画面</h1>

<div class="form">
<form id="loginForm" name="loginForm" action="" method="POST" autocomplete="off">
    <?php
        foreach($errors as $error){
            print "<p class='error'>";
            print $error."<br>";
            print "</p>";
        }
    ?>
    
    <div>
        <label for="mail">メールアドレス
        <input type="text" id="mail" name="mail" placeholder="メールアドレスを入力" 
        value="<?php if(!empty($_POST['mail'])){echo htmlspecialchars($_POST['mail'], ENT_QUOTES);} ?>">
        </label>
    </div>
    
    <div>
        <label for="password">パスワード
        <input type="password" id="password" name="password" value="" placeholder="パスワードを入力">
        </label>
    </div>
    
    <input type="submit" id="login" name="login" value="ログイン">
</form>

<a href="user_create.php">新規会員登録</a>

</div>