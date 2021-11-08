<?php
include_once('myFm.class.php');
session_start();

// ログイン状態チェック
if(!isset($_SESSION['id'])){
    header('Location: login.php');
    exit;
}

//エラーメッセージの初期化
$errors = array();

$myFm = new myFm;

// 修正ボタンが押されたら
if(isset($_POST['edit'])){

    if(empty($_POST['old'])){
        $errors['old'] = '現在のパスワードが未入力です。';
    } 
    if(empty($_POST['new'])){
        $errors['new'] = '新しいパスワードが未入力です。';
    }
    if(strcmp($_POST['new'], $_POST['new2']) !== 0){
        $errors['new2'] = '新しいパスワードが一致しません。';
    }
    if(!preg_match('/\A[a-z\d]{8,}+\z/i', $_POST['new'])){
        $errors['new3'] = 'パスワードは半角英数字8文字以上です。';
    }
    
    if(count($errors) === 0){

        $userData = $myFm->getRecord($_POST['recordId'], 'users');

        $err = $userData['ret']['messages'][0]['code'];
        $fieldData = $userData['ret']['response']['data'][0]['fieldData'];

        if(password_verify($_POST['old'], $fieldData['パスワード']) && $err === '0'){ 

            // 新しいパスワードを登録
            // REQUEST 生成
            $data = array();
            $data['fieldData'] = array(
                'パスワード' => password_hash($_POST['new'], PASSWORD_BCRYPT)
            );
            $data['modId'] = $_POST['modId'];
            $data = json_encode($data, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP);
            //

            $result = $myFm->editRecord($_POST['recordId'], 'users', $data);

            $err = $result['ret']['messages'][0]['code'];
            if ($err === '0'){

                $_SESSION['message'] = 'パスワードを修正しました。';
                header('Location: user.php');
                exit();

            }else{

                $errors['old'] = 'エラー ' . $err;
            }

        }else{

            $errors['old'] = '現在のパスワードが一致しません。';
        }
    }    
}

// REQUEST 生成
$query = array();
$query[] = array(
    '主キー' => '==' . $_SESSION['id']
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
$recordId = $result['ret']['response']['data'][0]['recordId'];
$modId = $result['ret']['response']['data'][0]['modId'];

?>

<h1>パスワード修正画面</h1>

<div class="form">
<form id="pwdEditForm" name="pwdEditForm" action="" method="POST">
    <?php
        foreach($errors as $error){
            print "<p class='error'>";
            print $error."<br>";
            print "</p>";
        }
    ?>

    <div>
        <label for="name">現在のパスワード
        <input type="password" id="old" name="old" placeholder="現在のパスワードを入力" 
        value="">
        </label>
    </div>

    <div>
        <label for="name">新しいパスワード
        <input type="password" id="new" name="new" placeholder="新しいパスワードを入力" 
        value="">
        </label>
    </div>

    <div>
        <label for="mail">新しいパスワード確認
        <input type="password" id="new2" name="new2" placeholder="新しいパスワードをもう一度入力" 
        value="">
        </label>
    </div>

    <input type="hidden" name="recordId" value="<?php print $recordId; ?>">
    <input type="hidden" name="modId" value="<?php print $modId; ?>">

    <a href="user.php">キャンセル</a>
    <input type="submit" id="edit" name="edit" value="修正">

</form>
</div>
