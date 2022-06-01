<!--課題4学習者用ページ-->
<?php
//ユーザ名取得
$u_id = $_GET["u_id"];

//MariaDB接続
$con = mysqli_connect('localhost', 'g231t034', 'Ku9Mm8gL');
//データベース選択
mysqli_select_db($con, 'prepinfo2');
//文字コード指定
mysqli_set_charset($con, "utf8");

//学習者の情報を取得
$query = 'SELECT * FROM user';
$result = mysqli_query($con, $query);
while ($row = mysqli_fetch_assoc($result)) {
  if ($row['identifier'] == $u_id) {
    $name = $row['name'];
    $key = $row['storagekey'];
    $team = $row['team'];
  }
}
//チューターのメールアドレスを取得
$query2 = 'SELECT * FROM user';
$result2 = mysqli_query($con, $query2);
while ($row = mysqli_fetch_assoc($result2)) {
  if ($row['team'] == $team && $row['tutor'] == "yes") {
    $mail = $row['mail'];
  }
}


?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>課題4作成ページ</title>
  <script src="../blockly_compressed.js"></script>
  <script src="../blocks_compressed.js"></script>
  <script src="../javascript_compressed.js"></script>
  <script src="../msg/js/ja.js"></script>
  <script src="../appengine/storage.js"></script>
  <script src="JS/PEblockly.js"></script>
  <script src="JS/PEblocks.js"></script>
  <script src="JS/jquery-2.2.4.min.js"></script>
  <script src="JS/jquery.query.js"></script>
  <script src="JS/svg.js"></script>
  <script src="JS/illust.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <!-- CSS -->
  <link rel="stylesheet" href="CSS/PEtask.css" />
  <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
  <!-- faviconの設定 -->
  <link rel="apple-touch-icon" sizes="180x180" href="IMG/favicon/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="IMG/favicon/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="IMG/favicon/favicon-16x16.png">
  <link rel="manifest" href="/site.webmanifest">
  <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
  <meta name="msapplication-TileColor" content="#da532c">
  <meta name="theme-color" content="#ffffff">
</head>

<body>
  <header>
    <h1 class="title">課題4のプログラム作成ページ</h1>
    <nav>
      <ul>
        <li><a href="https://prep.ipusoft-el.jp/course/view.php?id=20&section=7" class="back_btn">前のページへ戻る</a></li>
      </ul>
    </nav>
  </header>

  <div id='blocklyDiv' class="tab_btn is-active-btn" mouse></div>
  <div id='contentSVG' class='content' style="height: 50%; width: 39.5%;"></div>
  <div id='checklist' class='check'>
    <h3 style='margin:10px 5px'>チューターのチェック項目</h3>
    <p style='margin:0 5px'>
      ・数値は入力できるようになっているか<br>
      ・分岐処理を使っているか<br>
      ・反復処理を使っているか<br>
      ・変数は正しく使えているか<br>
      ・作品は正常に動いているか<br>
    </p>
    <textarea id="comment" name="comment" value="comment" rows="6" cols="60" style='margin:5px' placeholder="自由記述欄です。ここに記述した内容はチューターも確認します。未完成の状態で提出しても構いません。その際、分からないことなどがある場合はここからチューターに質問してみましょう。"></textarea>
    <p></p>
  </div>
  <xml id="toolbox" style="display: none">
    <category name="開始" colour="100">
      <block type="start_block"></block>
    </category>
    <category name="文字列" colour="160">
      <block type='text'></block>
      <block type="text_print"></block>
      <block type="text_prompt_ext"></block>
      <block type="text_join"></block>
    </category>
    <category name="計算・数字" colour="230">
      <block type="math_number"></block>
      <block type="math_arithmetic"></block>
      <block type="math_single"></block>
      <block type="math_trig"></block>
      <block type="math_constant"></block>
      <block type="math_round"></block>
      <block type="math_modulo"></block>
      <block type="math_constrain"></block>
    </category>
    <category name="線引き" colour="150">
      <block type="set_colour"></block>
      <block type="line_colour">
        <value name="COLOUR">
          <block type="set_colour">
            <field name="COLOUR">
              #fff
            </field>
          </block>
      </block>
      <block type="pen_down"></block>
      <block type="pen_up"></block>
      <block type="pen_forward"></block>
      <block type="pen_right_turn"></block>
      <block type="pen_left_turn"></block>
      <block type="pen_get_angle"></block>
    </category>
    <category name="条件分岐" colour="210">
      <block type="controls_if"></block>
      <block type="controls_if">
        <mutation else="1"></mutation>
      </block>
      <block type="logic_compare"></block>
      <block type="logic_operation"></block>
      <block type="logic_negate"></block>
      <block type="logic_boolean"></block>
      <block type="math_number_property"></block>
    </category>
    <category name="繰り返し" colour="120">
      <block type="controls_repeat_ext"></block>
      <block type="controls_flow_statements"></block>
      <block type="controls_whileUntil"></block>
    </category>
    <category name="変数" custom="VARIABLE" colour="330"></category>
  </xml>
  <xml id="startBlocks" style="display: none;">
    <block type="start_block" x="50" y="50"></block>
  </xml>

  <div class='btn_area'>
    <button onclick='runCode()' id='runButton' class='various_btn' style='margin-left:5px; margin-top: 10px;'>
      <i class="fas fa-cogs" style="margin-right:7px"></i>実行
    </button>
    <button onclick='blockclear()' id='clear' class='various_btn' style='margin-top: 10px;'>
      <i class="fas fa-trash" style="margin-right:7px"></i>全消去
    </button>
    <button onclick='history()' id='history' class='various_btn' style='margin-right:50px; margin-top: 10px;'>
      <i class="fas fa-history" style="margin-right:7px"></i>履歴確認
    </button>
    <button onclick='submit()' id='clear' class=' submit_btn' style='margin-left:5px; margin-top: 10px;'>
      <i class="fas fa-file-upload" style="margin-right:7px"></i>提出
    </button>
  </div>
  <!-- <button onclick='kakunin()'>
		確認
	</button> -->
  <!-- <button onclick='printxml()'>
    XML
  </button> -->
  <!-- <button onclick='showJS()'>
		JS
	</button> -->
  <!-- <button onclick="Sendmail()">メール送信</button> -->
  <!-- <?php echo $mail; ?> -->

  <script>
    var count = 1; //カウント用変数
    var deletecount = 0; //削除用カウント変数
    var taskcount = 0; //プロセス回数用
    var draw; //SVG出力用変数
    var res = 0; //ページ読み込み時と離脱時の識別用
    var workspace = null;
    /*---------------------------------------------------------------- ユーザー識別用のキーを取得 ---------------------------------------------------------------------------------------*/
    var saveKey = "<?php echo $key; ?>";
    /*---------------------------------------------------------------- ユーザーごとに課題1のプロセスの回数を取得 ---------------------------------------------------------------------------------------*/
    // var user = "<?php echo $name; ?>";
    // var mail = "<?php echo $mail; ?>"; //メールアドレスを取得

    /*---------------------------------------------------------------- ワークスペース設定 ---------------------------------------------------------------------------------------*/
    var workspace = Blockly.inject('blocklyDiv', {
      toolbox: document.getElementById('toolbox'),
      grid: {
        spacing: 18,
        length: 3,
        colour: '#ccc',
      },
      scrollbars: false,
      zoom: false,
      trashcan: false,
    });

    //ブロックの無効・有効が関係
    workspace.addChangeListener(Blockly.Events.disableOrphans);

    $(function() {
      //ページを読み込んだとき，前回までのブロックの状態を復元する,学習時間の計測開始
      window.onload = function() {
        processget();
      }

      //ページ読み込みから1秒後にプロセスの取得を開始する
      setTimeout(function() {
        workspace.addChangeListener(listenEvent);
        workspace.addChangeListener(deleteEvent);
        workspace.addChangeListener(changeEvent);
      }, 500);
    });
    /*----------------------------------------------------------------　最後のエディタの状態を格納しjavascriptで使えるようにする　---------------------------------------------------------------------------------------*/
    //最後の状態を記録
    function processget() {
      <?php
      $xmls = "";

      $query = 'SELECT * FROM blockly4';
      $result = mysqli_query($con, $query);

      while ($row = mysqli_fetch_assoc($result)) {
        if (strpos($row["blockkey"], $key) !== false) {
          $xmls = $row['xml'];
        }
      }
      $xmls = json_encode($xmls);
      ?>
      xmls = <?php echo $xmls; ?>;
      taskStart(xmls);
    }
    //最初の状態を表示
    function taskStart(xml) {
      //キーに対応した課題のxmlを取得
      xmlText = xml;
      Blockly.mainWorkspace.clear();
      xmlDom = Blockly.Xml.textToDom(xmlText);
      Blockly.Xml.domToWorkspace(Blockly.mainWorkspace, xmlDom);
    }

    /*---------------------------------------------------------------- ブロックを実行する ---------------------------------------------------------------------------------------*/
    function runCode() {
      runpoint();
      var code = Blockly.JavaScript.workspaceToCode(workspace);
      draw.clear();
      eval(code);
    }

    /*---------------------------------------------------------------- ワークスペースをすべて削除し，それを記録する ---------------------------------------------------------------------------------------*/
    function blockclear() {
      ret = window.confirm("ブロックをすべて消去します。よろしいですか？");
      if (ret == true) {
        clearcount();
        Blockly.mainWorkspace.clear();
        var xmlDom = Blockly.Xml.workspaceToDom(Blockly.mainWorkspace);
        var xmlText = Blockly.Xml.domToPrettyText(xmlDom);
      }
    }

    /*---------------------------------------------------------------- 提出完了時のワークスペースの最後の状態を記録する ---------------------------------------------------------------------------------------*/
    function submit() {
      if (window.confirm("課題を提出してよろしいですか？")) {
        var xmlDom = Blockly.Xml.workspaceToDom(Blockly.mainWorkspace);
        var xmlText = Blockly.Xml.domToPrettyText(xmlDom);
        submitWorkspace(xmlText);
        confirmcount();
        Sendmail();
        alert("課題4終了です。お疲れ様でした。\nチューターのチェックをお待ちください。\nチェックを受け、すべての項目が合格になったら次の課題へ進んでください。");
        location.href = "https://prep.ipusoft-el.jp/course/view.php?id=20&section=7";
      } else {
        return false;
      }
    }

    /*---------------------------------------------------------------- SVGを出力する ---------------------------------------------------------------------------------------*/
    draw = SVG("contentSVG");

    /*---------------------------------------------------------------- ブロックを使ったときに起動 ---------------------------------------------------------------------------------------*/
    //ブロックを動かしたとき
    function listenEvent(event) {
      if (event.type == Blockly.Events.BLOCK_MOVE) {
        var xmlDom = Blockly.Xml.workspaceToDom(Blockly.mainWorkspace);
        var xmlText = Blockly.Xml.domToPrettyText(xmlDom);
        saveWorkspace(xmlText);
      }
    }

    //ブロックを消したとき
    function deleteEvent(event) {
      if (event.type == Blockly.Events.BLOCK_DELETE) {
        var xmlDom = Blockly.Xml.workspaceToDom(Blockly.mainWorkspace);
        var xmlText = Blockly.Xml.domToPrettyText(xmlDom);
        if (deletecount == 0) {
          saveWorkspace(xmlText);
          deletecount++;
        }
        //連続で記録されないようにするためのタイムアウト
        setTimeout(function() {
          deletecount = 0;
        }, 50);
      }
    }

    //ブロックにテキストを入力したとき（現状：変更時は記録されない）
    function changeEvent(event) {
      if (event.type == Blockly.Events.BLOCK_CHANGE && event.element == 'field') {
        var xmlDom = Blockly.Xml.workspaceToDom(Blockly.mainWorkspace);
        var xmlText = Blockly.Xml.domToPrettyText(xmlDom);
        saveWorkspace(xmlText);
        deletecount++;
      }
    }

    /*---------------------------------------------------------------- ワークスペースをMysqlに保管 ---------------------------------------------------------------------------------------*/
    function saveWorkspace(xmlText) {
      $.ajax({
        url: 'PHP/PEtasksave.php',
        type: 'POST',
        data: {
          'blockkey': saveKey + ".4",
          'blockXML': xmlText,
          'tasknumber': 4,
        },
      });
    }

    /*---------------------------------------------------------------- 各種アクションが起きた時を記録する ---------------------------------------------------------------------------------------*/
    //実行したかどうか取得する
    function runpoint() {
      var xmlDom = Blockly.Xml.workspaceToDom(Blockly.mainWorkspace);
      var xmlText = Blockly.Xml.domToPrettyText(xmlDom);
      $.ajax({
        url: 'PHP/RunCount.php',
        type: 'POST',
        data: {
          'blockkey': saveKey + ".4",
          'xml': xmlText,
          'tasknumber': 4,
        }
      });
    }
    //履歴を記録するデータベースに提出したかどうかとその時のエディタの状態を取得する
    function submitWorkspace(xmlText) {
      $.ajax({
        url: 'PHP/submit.php',
        type: 'POST',
        data: {
          'blockkey': saveKey + ".4",
          'blockXML': xmlText,
          'tasknumber': 4,
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
          console.log("ajax通信に失敗しました");
          console.log("XMLHttpRequest : " + XMLHttpRequest.status);
          console.log("textStatus     : " + textStatus);
          console.log("errorThrown    : " + errorThrown.message);
        },
      });
    }
    //全消去したかどうかを取得する
    function clearcount() {
      $.ajax({
        url: 'PHP/ClearCount.php',
        type: 'POST',
        data: {
          'blockkey': saveKey + ".4",
          'tasknumber': 4,
        }
      });
    }
    //提出したかどうかと提出時のコメントを取得
    function confirmcount() {
      const comment = document.getElementById("comment").value;
      $.ajax({
        url: 'PHP/confirm.php',
        type: 'POST',
        data: {
          'blockkey': saveKey,
          'tasknumber': 4,
          'comment': comment,
        }
      });
    }

    /*---------------------------------------------------------------- 終了のメールを送る（whiteboxから） ---------------------------------------------------------------------------------------*/
    function Sendmail() {
      $.ajax({
        url: 'PHP/sendmail.php',
        type: 'POST',
        data: {
          'student': "<?php echo $name; ?>",
          'mail': "<?php echo $mail; ?>",
          'tasknumber': 4,
        }
      });
    }

    /*　--------------------------------------------------------------- 学習者が履歴を確認する用のタブを開く ---------------------------------------------------------------------------------------*/
    function history() {
      window.open('History4.php?key=' + saveKey);
    }

    /*　--------------------------------------------------------------- 以下確認用（実践では使用しない） ---------------------------------------------------------------------------------------*/

    //数値チェック
    function kakunin() {
      alert("<?php echo $mail; ?>");
    }


    //xml表示
    function printxml() {
      var xmlDom2 = Blockly.Xml.workspaceToDom(Blockly.mainWorkspace);
      var xmlText2 = Blockly.Xml.domToPrettyText(xmlDom2);
      alert(xmlText2);
    }
    //javascript表示
    function showJS() {
      alert(saveKey);
    }
  </script>
</body>

</html>
