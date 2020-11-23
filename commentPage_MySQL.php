<?php
$db_host = 'localhost';
$db_user = 'root';
$db_password = 'root';
$db_db = 'information_schema';
$db_port = 3306;

$mysqli = new mysqli(
$db_host,
$db_user,
$db_password,
$db_db
);

//コメントを追加
$comment="";
$commentId="";
$commentData=[];
$commentArray=[];
$commentErrorMesseage="";
$postArray=[];
$arrayData=[];
$uniqueId=uniqid();
$postID=$_GET['id'];


 
 //エスケープ処理
 function h($s){
  return htmlspecialchars($s,ENT_QUOTES,'UTF-8');
  }

  
  $select_article_query="SELECT * FROM keiziban . articleData";
  if ($results = $mysqli->query($select_article_query)) {
    // 連想配列を取得
    while ($rows = $results->fetch_assoc()) {
      $postArray= [$rows["id"],$rows["title"],$rows["article"]];
      $arrayData[]=$postArray;
    }
     // 結果セットを閉じる
       //$result->close();
  }


  $select_comment_query="SELECT * FROM keiziban . commentData";
  if ($result = $mysqli->query($select_comment_query)) {
    // 連想配列を取得
    while ($row = $result->fetch_assoc()) {
      $commentData= [$row["uniqueId"],$row["id"],$row["comment"]];
      $commentArray[]=$commentData;
    }
     // 結果セットを閉じる
       //$result->close();
  }



  //コメントが送信されたら
  if($_SERVER['REQUEST_METHOD']==='POST'){
    

    //コメントが入力されていたら
      if(!empty($_POST['comment'])){

       //コメントのデータを取得
        $comment=$_POST['comment'];

        $insert_query="INSERT INTO keiziban . commentData (uniqueId, id, comment) VALUES ('$uniqueId', '$postID', '$comment')";
        $mysqli->query($insert_query);
      
      
       //header()で指定したページにリダイレクト
        //今回は今と同じ場所にリダイレクト（つまりWebページを更新）
          header('Location: ' . $_SERVER['REQUEST_URI']);
        //プログラム終了
          exit;

      //コメントを消すボタンが押された時
      }else if(isset($_POST['del'])){
        //変数定義
        $postDeleteComment=$_POST['del'];
        
        $delete_query="DELETE FROM keiziban . commentData WHERE uniqueId = '$postDeleteComment'";
        $mysqli->query($delete_query);


        //header()で指定したページにリダイレクト
        //今回は今と同じ場所にリダイレクト（つまりWebページを更新）
           header('Location: ' . $_SERVER['REQUEST_URI']);
        //プログラム終了
           exit;

      }else if(empty($_POST['comment'])){
         $commentErrorMesseage="・コメントは必須です。";
     }
  }
  
  $mysqli->close();
?>


<!DOCTYPE html>
<html>
  <head>
    <meta charset='utf-8'>
    <link rel="stylesheet" type="text/css" href="keiziban.css">
    <title>Laravel News</title>
  </head>
  <body>
  　 <p class="commentPageTopTitle">Laravel News</p>
     <?php 
      //配列arrayDataの中にデータが入っていると
        if(!empty($arrayData)){  

          //二次元配列arrayDataから配列Data取り出しそれぞれのタイトル、記事を表示する
          foreach ( $arrayData as $data ) {  
          
           if($postID === $data[0]){ ?>
             <div class="articleArea">
                <div class="title">
                  <p><?php echo h($data[1]); ?></p>
                </div>
                <div class="srticle">
                  <p><?php echo h($data[2]); ?></p>
                </div>
             </div>
          <?php }
          } 
        } 
        ?>
       <div class="intermediate"></div>

      <p　class="commentErrorMesseage"><?php echo h($commentErrorMesseage) ?></p>
    
    <section class="commentArea">
     <div class="form-comment">
      
        <form method="POST" >
         <div class="pushCommentArea">
           <textarea class="commentInput" name="comment" cols="26" rows="9" value=""></textarea>
           <input class="commentPushBotton" type="submit" value="コメントを書く"> 
         </div>
        </form>
      

      
       <?php 
       //$commentArray配列からデータ(コメントIDとコメント)を取り出す
          foreach($commentArray as $array){ 
             if($postID === $array[1]){ ?>
               <form method="POST">
               <div class="pushedCommentArea">
                 <textarea class="pushedCommentInput" name="" cols="26" rows="9" readonly><?php echo $array[2]; ?></textarea>
                 <input type="hidden" name='del' value="<?php echo h($array[0]); ?>">
                 <input class="commentDeleteBotton" type="submit" value="コメントを消す">
               </div>
             </form>
         
           <?php 
             }
          }
        ?>
      
     </div>
    </section>　

     <div class="backHome">
       <a href="keiziban_MySQL.php">ホームへ戻る</a>
     </div>


    <script type="text/javascript" src="keiziban.js"></script>
  </body>
</html>