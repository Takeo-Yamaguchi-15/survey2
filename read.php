<?php // read.php // ファイル識別用コメント
require __DIR__ . '/config.php'; // 本番接続情報の読み込み
function h($v){ return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); } // HTMLエスケープ関数定義

try { // 例外処理開始
  $pdo = new PDO($dsn,$user,$pass,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]); // PDO接続確立
  $pdo->exec("CREATE TABLE IF NOT EXISTS responses (id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(100) NOT NULL,email VARCHAR(255) NOT NULL,phone VARCHAR(30),book VARCHAR(255) NOT NULL,rating TINYINT NOT NULL,reason TEXT NOT NULL,created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"); // テーブル自動作成
  $stmt = $pdo->query('SELECT id,name,email,phone,book,rating,reason,created_at FROM responses ORDER BY created_at DESC,id DESC'); // 一覧取得クエリ実行
  $rows = $stmt->fetchAll(); // 取得結果の配列化
} catch (PDOException $e) { // 例外捕捉
  http_response_code(500); // ステータスコード設定
  echo '<meta charset="UTF-8">DBエラー: '.h($e->getMessage()); // エラーメッセージ出力
  exit; // 後続処理の中断
} // 例外処理終了
?>
<!doctype html> <!-- HTML5宣言 -->
<html lang="ja"> <!-- 文書の言語指定 -->
<head> <!-- ヘッダ領域開始 -->
  <meta charset="UTF-8"> <!-- 文字エンコーディング指定 -->
  <title>アンケート一覧</title> <!-- ページタイトル -->
</head> <!-- ヘッダ領域終了 -->
<body> <!-- 本文開始 -->
  <h1>アンケート一覧</h1> <!-- 見出し表示 -->
  <?php if (empty($rows)): ?> <!-- データ空判定開始 -->
    <p>データなし</p> <!-- 未登録時の表示 -->
    <p><a href="post.php">入力へ</a></p> <!-- 入力ページへの導線 -->
  <?php else: ?> <!-- データあり分岐 -->
  <table border="1" cellpadding="6"> <!-- 表表示の定義 -->
    <thead><tr> <!-- テーブルヘッダ行開始 -->
      <th>ID</th><th>日時</th><th>名前</th><th>Email</th><th>電話番号</th><th>対象の書籍</th><th>評価</th><th>評価の理由</th> <!-- 見出しセル群 -->
    </tr></thead> <!-- テーブルヘッダ行終了 -->
    <tbody> <!-- テーブルボディ開始 -->
      <?php foreach ($rows as $r): ?> <!-- 行ループ開始 -->
      <tr> <!-- データ行開始 -->
        <td><?= (int)$r['id'] ?></td> <!-- ID表示 -->
        <td><?= h($r['created_at']) ?></td> <!-- 送信日時表示 -->
        <td><?= h($r['name']) ?></td> <!-- 名前表示 -->
        <td><?= h($r['email']) ?></td> <!-- Email表示 -->
        <td><?= h($r['phone']) ?></td> <!-- 電話番号表示 -->
        <td><?= h($r['book']) ?></td> <!-- 書籍名表示 -->
        <td><?= (int)$r['rating'] ?></td> <!-- 評価表示 -->
        <td><?= nl2br(h($r['reason'])) ?></td> <!-- 理由表示（改行保持） -->
      </tr> <!-- データ行終了 -->
      <?php endforeach; ?> <!-- 行ループ終了 -->
    </tbody> <!-- テーブルボディ終了 -->
  </table> <!-- 表表示の終了 -->
  <?php endif; ?> <!-- 分岐終了 -->
</body> <!-- 本文終了 -->
</html> <!-- HTML終了 -->
