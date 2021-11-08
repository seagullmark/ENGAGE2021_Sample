<?php
include_once('myFm.class.php');
session_start();

//エラーメッセージの初期化
$errors = array();

// 登録ボタンが押されたら
if(isset($_POST['create'])){

    if(empty($_POST['name'])){
        $errors['name'] = '氏名が未入力です。';
    } 
    if(empty($_POST['mail'])){
        $errors['mail'] = 'メールアドレスが未入力です。';
    }
    if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\?\*\[|\]%'=~^\{\}\/\+!#&\$\._-])*@([a-zA-Z0-9_-])+\.([a-zA-Z0-9\._-]+)+$/", $_POST['mail'])){
        $errors['mail'] = 'メールアドレスの形式ではありません。';
    }
    if(strcmp($_POST['pwd'], $_POST['pwd2']) !== 0){
        $errors['pwd2'] = '新しいパスワードが一致しません。';
    }
    if(!preg_match('/\A[a-z\d]{8,}+\z/i', $_POST['pwd'])){
        $errors['pwd'] = 'パスワードは半角英数字8文字以上です。';
    }
    
    if (count($errors) === 0) {

        $myFm = new myFm;

        // REQUEST 生成
        $data = array();
        $data['fieldData'] = array(
            'パスワード' => password_hash($_POST['pwd'], PASSWORD_BCRYPT),
            'メール' => $_POST['mail'],
            '氏名' => $_POST['name']
        );
        $data = json_encode($data, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP);
        //

        $result = $myFm->createRecord('users', $data);

        $err = $result['ret']['messages'][0]['code'];
        
        if($err === '0'){

            $recordId = $result['ret']['response']['recordId'];

            $userData = $myFm->getRecord($recordId, 'users');
            $fieldData = $userData['ret']['response']['data'][0]['fieldData'];
            $_SESSION['id'] = $fieldData['主キー'];
            $_SESSION['message'] = '新規会員登録が完了しました！';
            header('Location: user.php');
            exit();

        }else if($err === '504'){    
            
            $errors['user'] = '入力されたメールアドレスはすでに登録されています。';

        }else{

            $errors['user'] = 'エラー ' . $err;
        } 
    }    
}

?>

<h1>新規会員登録画面</h1>

<div class="form">
<form id="userCreateForm" name="userCreateForm" action="" method="POST">
    <?php
        foreach($errors as $error){
            print "<p class='error'>";
            print $error."<br>";
            print "</p>";
        }
    ?>

    <div>
        <label for="name">氏名
        <input type="text" id="name" name="name" placeholder="氏名を入力" 
        value="<?php if(!empty($_POST['name'])){echo htmlspecialchars($_POST['name'], ENT_QUOTES);} ?>">
        </label>
    </div>

    <div>
        <label for="mail">メールアドレス
        <input type="text" id="mail" name="mail" placeholder="メールアドレスを入力" 
        value="<?php if(!empty($_POST['mail'])){echo htmlspecialchars($_POST['mail'], ENT_QUOTES);} ?>">
        </label>
    </div>

    <div>
        <label for="name">パスワード
        <input type="password" id="pwd" name="pwd" placeholder="パスワードを入力" 
        value="">
        </label>
    </div>

    <div>
        <label for="mail">パスワード確認
        <input type="password" id="pwd2" name="pwd2" placeholder="パスワードをもう一度入力" 
        value="">
        </label>
    </div>

    <input type="submit" id="create" name="create" value="登録">

</form>

<a href="login.php">すでに会員の方はこちら</a>

</div>
