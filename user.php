<?php
include_once('myFm.class.php');
session_start();

// ログイン状態チェック
if(!isset($_SESSION['id'])){
    header("Location: login.php");
    exit;
}

//エラーメッセージの初期化
$errors = array();
$id = $_SESSION['id'];

$myFm = new myFm;

// REQUEST 生成
$query = array();
$query[] = array(
    '主キー' => '==' . $id
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
$fieldData = $result['ret']['response']['data'][0]['fieldData'];

?>

<h1>会員情報画面</h1>

<?php if(isset($_SESSION['message'])): ?>
    <p class="message"><?php print $_SESSION['message']; ?></p>
    <?php $_SESSION['message'] = NULL ?>
<?php endif; ?>

<div>
    <div>
        <p>氏名：<?php echo htmlspecialchars($fieldData['氏名'], ENT_QUOTES); ?></p>
        <p>メールアドレス：<?php echo htmlspecialchars($fieldData['メール'], ENT_QUOTES); ?></p>
    </div>
</div>

<a href="logout.php">ログアウト</a>
<a href="pwd_edit.php">パスワード修正</a>
