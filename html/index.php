<?php

session_start();

//ログインしていない場合、login.phpを表示
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once('db.php');
require_once('functions.php');

/**
 * @param String $tweet_textarea
 * つぶやき投稿を行う。
 */
function newtweet($tweet_textarea)
{
    // 汎用ログインチェック処理をルータに作る。早期リターンで
    createTweet($tweet_textarea, $_SESSION['user_id']);
}
/**
 * ログアウト処理を行う。
 */
function logout()
{
    $_SESSION = [];
    $msg = 'ログアウトしました。';
}

if ($_POST) { /* POST Requests */
    if (isset($_POST['logout'])) { //ログアウト処理
        logout();
    } else if (isset($_POST['tweet_textarea'])) { //投稿処理
        if (isset($_POST['reply_post_id'])) {
            newReplyTweet($_POST['tweet_textarea'], $_POST['reply_post_id']);
        } else {
            newtweet($_POST['tweet_textarea']);
        }
    }
    header("Location: index.php");
}

if ($_GET) {
    if (isset($_GET['favorite'])) {
        if (isMyFavorite($_GET['favorite'], $_SESSION['user_id'])) {
        deleteMyFavorite($_GET['favorite'], $_SESSION['user_id']);
      } else {
        insertMyFavorite($_GET['favorite'], $_SESSION['user_id']);
    }
  }
  header("Location: index.php");
}

$tweets = getTweets();
$tweet_count = count($tweets);
/* 返信課題はここからのコードを修正しましょう。 */
function newReplyTweet($tweet_textarea, $reply_id) {
    createReplyTweet($tweet_textarea, $reply_id, $_SESSION['user_id']);
}
/* 返信課題はここからのコードを修正しましょう。 */
?>

<!DOCTYPE html>
<html lang="ja">

<?php require_once('head.php'); ?>

<body>
  <div class="container">
    <h1 class="my-5">新規投稿</h1>
    <div class="card mb-3">
      <div class="card-body">
        <form method="POST">
          <textarea class="form-control" type=textarea name="tweet_textarea"><?php if (isset($_GET['reply'])) { echo getUserReplyText($_GET['reply']); } ?></textarea>
          <!-- 返信課題はここからのコードを修正しましょう。 -->
          <?php if (isset($_GET['reply'])) { ?>
          <input type="hidden" name="reply_post_id" value="<?= $_GET['reply'] ?>" />
          <?php } ?>
          <!-- 返信課題はここからのコードを修正しましょう。 -->
          <br>
          <input class="btn btn-primary" type=submit value="投稿">
        </form>
      </div>
    </div>
    <h1 class="my-5">コメント一覧</h1>
    <?php foreach ($tweets as $t) { ?>
      <div class="card mb-3">
        <div class="card-body">
          <p class="card-title"><b><?= "{$t['id']}" ?></b> <?= "{$t['name']}" ?> <small><?= "{$t['updated_at']}" ?></small></p>
          <p class="card-text"><?= "{$t['text']}" ?></p>
          <!--返信課題はここから修正しましょう。-->
          <p><a href = "/index.php?reply=<?= "{$t['id']}" ?>">[返信する]</a>
          <?php if (isset($t['reply_id'])) { ?>
            <a href="/view.php?id=<?= "{$t['reply_id']}" ?>">[返信元のメッセージ]</a></p>
          <?php } ?>
          <?php if (isMyFavorite($t['id'], $_SESSION['user_id'])) { ?>
            <a href="/index.php?favorite=<?= "{$t['id']}" ?>"><img class="favorite-image" src='/images/heart-solid-red.svg'></a>
          <?php } else { ?>
            <a href="/index.php?favorite=<?= "{$t['id']}" ?>"><img class="favorite-image" src='/images/heart-solid-gray.svg'></a>
          <?php } ?>
          <?php if (countFavorite($t['id']) > 0) {
                    echo countFavorite($t['id']);
                } ?>
          <!--返信課題はここまで修正しましょう。-->
        </div>
      </div>
    <?php } ?>
    <form method="POST">
      <input type="hidden" name="logout" value="dummy">
      <button class="btn btn-primary">ログアウト</button>
    </form>
    <br>
  </div>
</body>

</html>
