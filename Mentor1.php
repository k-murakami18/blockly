<!--課題1メンター確認用ページ-->
<?php
//ユーザ情報取得
$student = $_GET['student'];
$u_id = $_GET['u_id'];

require('PHP/dbconnect.php');

$users1 = $db->query('SELECT * FROM user');

while ($row = $users1->fetch(PDO::FETCH_ASSOC)) {
  if ($row['storagekey'] == $u_id) {
    $name = $row['name'];
    $key = $row['storagekey'];
    $team = $row['team'];
  }
}

$get_evaluation = $db->query('SELECT * FROM Evaluation1 WHERE studnet="' . $student . '" ORDER BY time DESC LIMIT 1');

while ($row = $get_evaluation->fetch(PDO::FETCH_ASSOC)) {
  $check1 = $row['check1'];
  $check2 = $row['check2'];
  $check3 = $row['check3'];
  $check4 = $row['check4'];
  $check5 = $row['check5'];
  $advice = $row['advice'];
}

?>

<!DOCTYPE html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>課題1のメンター用ページ</title>
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
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
    <h1 class="title">課題1のメンター用ページ</h1>
    <p style="color:white"><?php echo $name ?>&nbsp;さんの課題</p>
    <nav>
      <ul>
        <li><a href="https://ichi-lab.net/~g231t034/blockly/PE2022/MentorTop.php?task=1" class="back_btn">前のページへ戻る</a></li>
      </ul>
    </nav>
  </header>

  <div id='blocklyDiv' class="tab_btn is-active-btn" mouse></div>
  <div id='contentSVG' class='content' style="height: 50%; width: 39.5%;"></div>

  <div name='checkdata' class='check' style="font-size: 0.9em;">
    <input type="checkbox" id="check1" class="list-top list" name="check1" value="ok" onclick='return false;' <?= $check1 == "ok" ? 'checked' : '' ?>>
    和・差・積・商の順番で答えが表示されているか<br>
    <input type="checkbox" id="check2" class="list" name="check2" value="ok" onclick='return false;' <?= $check2 == "ok" ? 'checked' : '' ?>>
    数値は入力できるようになっているか<br>
    <input type="checkbox" id="check3" class="list" name="check3" value="ok" onclick='return false;' <?= $check3 == "ok" ? 'checked' : '' ?>>
    出力された答えは正しいか<br>
    <input type="checkbox" id="check4" class="list" name="check4" value="ok" onclick='return false;' <?= $check4 == "ok" ? 'checked' : '' ?>>
    変数は正しく使えているか<br>
    <input type="checkbox" id="check5" class="list" name="check5" value="ok" onclick='return false;' <?= $check5 == "ok" ? 'checked' : '' ?>>
    必要のない処理は行っていないか<br>
    <p></p>
    <label class="list list-inner">コメント</label><br>
    <textarea readonly id="sample" class="list-bottom list" name="advice" style="font-size: 1.1em; width:80%;"><?php echo $advice; ?></textarea>
  </div>

  <button onclick='runCode()' id='runButton' class='various_btn' style='margin-left:5px; margin-top: 10px;'>
    <i class="fas fa-cogs" style="margin-right:7px"></i>実行
  </button>
  <input type="range" id="seek" value="1" min="1" step="1" oninput="Seekbar()" onmouseup="workget()"></input>
  <output id="output">
    1
  </output>
  <output id="runoutput">
  </output>
  <output id="clearoutput">
  </output>
  <output id="suboutput">
  </output>
  <output id="similar" hidden>
  </output>
  <output id="playtime">
  </output>
  <br>

  <button onclick="backskip()" class='various_btn' style='margin-left:5px; margin-right:5px; margin-top: 5px;'>戻スキップ</button>
  <button onclick="back()" class='arrow_btn' style="margin-top: 5px;">◀</button>
  <button onclick="forward()" class='arrow_btn' style='margin-top: 5px;'>▶</button>
  <button onclick="forwardskip()" class='various_btn' style='margin:5px 15px 0 5px;'>次スキップ</button>
  <button onclick="samplePG()" class='sample_btn' style='margin-left:5px; margin-top: 5px;'><i class="fas fa-code" style="margin-right:7px"></i>サンプル</button>
  <!--
	<button onclick="Sendmail()">メール</button>
	<button onclick="printxml()">xml表示</button>
	<button onclick="showJS()">JS表示</button>
	<button onclick="kakunin()">確認用</button>
	-->



  <!-- スクリプト -->
  <script>
    var count = 0; //カウンター用変数
    var seekcount = 1; //forward(),back()用変数
    var streamcount = 1; //streamBlock()(再生用)かウント変数
    var timer; //タイマー用変数
    var savedBlockPrefix = 1;
    //var taskcount = "<?php echo $_SESSION['task']; ?>"; //ユーザーのプロセスの回数を取得
    var pcount = 1; //プロセス番号を取得
    //xml保存用配列
    var xmls = new Array();
    //最大値取得
    var prmax = 0;

    //javascriptの変数にPHPの値を入れる
    var student = "<?php echo $checker; ?>";

    /*---------------------------------------------------------------- ワークスペース設定 ---------------------------------------------------------------------------------------*/
    var workspace = Blockly.inject('blocklyDiv', {
      toolbox: document.getElementById('toolbox'),
      grid: {
        spacing: 18,
        length: 3,
        colour: '#ccc',
      },
      scrollbars: false,
      trashcan: false,
    });

    //ページを読み込んだとき，課題の初期状態を表示
    window.onload = function() {
      processget();
    }

    //SVGの出力
    draw = SVG("contentSVG");

    /*---------------------------------------------------------------- ブロックを実行する ---------------------------------------------------------------------------------------*/
    function runCode() {
      workcontent = document.getElementById('output').value;
      //TutorTaskSave(workcontent+"run");
      var code = Blockly.JavaScript.workspaceToCode(workspace);
      draw.clear();
      eval(code);
    }

    /*----------------------------------------------------------------　processを配列に格納し格納javascriptで使えるようにする　---------------------------------------------------------------------------------------*/
    function processget() {
      var split = 0;
      <?php
      $xmls = array();
      $time = array();
      $runs = array();
      $clear = array();
      $submit = array();
      $similar = array();
      $comment = array();
      $count = 0;

      $block = $db->query('SELECT * FROM comment1');

      while ($row = $block->fetch(PDO::FETCH_ASSOC)) {
        if (strpos($row["blockkey"], $key) !== false) {
          $xmls[] = $row['xml'];
          $time[] = $row['time'];
          $runs[] = $row['action'];
          $clear[] = $row['clear'];
          $submit[] = $row['submit'];
          $similar[] = $row['similar'];
          $comment[] = $row['comment'];
          $count++;
        }
      }
      $xmls = json_encode($xmls);
      $time = json_encode($time);
      $runs = json_encode($runs);
      $clear = json_encode($clear);
      $submit = json_encode($submit);
      $similar = json_encode($similar);
      $comment = json_encode($comment);
      ?>
      xmls = <?php echo $xmls; ?>;
      time = <?php echo $time; ?>;
      runs = <?php echo $runs; ?>;
      clear = <?php echo $clear; ?>;
      submit = <?php echo $submit; ?>;
      similar = <?php echo $similar; ?>;
      comment = <?php echo $comment; ?>;
      prmax = <?php echo $count; ?>;
      setMax(prmax);
      checkStart(prmax);
    }

    /*---------------------------------------------------------------- 最初の状態を表示 ---------------------------------------------------------------------------------------*/
    function checkStart(count) {
      dummy = count;
      //alert(count);
      //キーに対応した課題のxmlを取得
      xmlText = xmls[count - 1];
      //alert(xmlText);
      //xmlText = xmlText.split('\\\"').join('"');
      Blockly.mainWorkspace.clear();
      xmlDom = Blockly.Xml.textToDom(xmlText);
      Blockly.Xml.domToWorkspace(Blockly.mainWorkspace, xmlDom);
      document.getElementById('output').value = dummy;
      if (runs[count - 1] == "run") {
        document.getElementById('runoutput').value = "実行";
      } else {
        document.getElementById('runoutput').value = "　　";
      }
      if (clear[count - 1] == "clear") {
        document.getElementById('clearoutput').value = "消去";
      } else {
        document.getElementById('clearoutput').value = "　　";
      }
      if (submit[count - 1] == "submit") {
        document.getElementById('suboutput').value = "提出";
      } else {
        document.getElementById('suboutput').value = "　　";
      }
      document.getElementById('playtime').value = time[count - 1];
      document.getElementById('seek').value = dummy;
      pcount = dummy;
    }
    /*----------------------------------------------------------------　シークバーの設定　---------------------------------------------------------------------------------------*/

    function Seekbar() {
      //シークバーの値を取得
      var seekpoint = seek.value;
      pcount = seekpoint;
      //シークバーの値をoutputに出力
      document.getElementById('output').value = seekpoint;
      process(seekpoint);
    }
    //ワークスペースとシークバーの位置を関連付ける機能
    function process(seekcount) {
      document.getElementById('output').value = seekcount;
      //プログラムを実行したタイミングを表示
      if (runs[seekcount - 1] == "run") {
        document.getElementById('runoutput').value = "実行";
      } else {
        document.getElementById('runoutput').value = "　　";
      }
      //全消去前のタイミングを表示
      if (clear[seekcount - 1] == "clear") {
        document.getElementById('clearoutput').value = "消去";
      } else {
        document.getElementById('clearoutput').value = "　　";
      }
      //提出したタイミングを表示
      if (submit[seekcount - 1] == "submit") {
        document.getElementById('suboutput').value = "提出";
      } else {
        document.getElementById('suboutput').value = "　　";
      }
      document.getElementById('playtime').value = time[seekcount - 1];
      document.getElementById('seek').value = seekcount;
      xmlText = xmls[seekcount - 1];
      xmlText = xmlText.split('\\\"').join('"');
      if (xmlText) {
        Blockly.mainWorkspace.clear();
        xmlDom = Blockly.Xml.textToDom(xmlText);
        Blockly.Xml.domToWorkspace(Blockly.mainWorkspace, xmlDom);
      }
    }

    //シークバーの最大値を設定
    function setMax(count) {
      var elementReference = document.getElementById("seek");
      elementReference.max = prmax;
    }

    //次のプロセスを表示
    function forward() {
      if (prmax > pcount) {
        pcount++;
        process(pcount);
        workcontent = document.getElementById('output').value;
        //TutorTaskSave(workcontent);
      }
    }
    //前のプロセスを表示
    function back() {
      if (pcount <= prmax && pcount > 1) {
        //if(pcount <= prmax && pcount >= 1){
        pcount--;
        process(pcount);
        workcontent = document.getElementById('output').value;
        //TutorTaskSave(workcontent);
      }
    }
    //短縮して履歴の次を表示
    function forwardskip() {
      skipstart("forward");
      while (true) {
        simcount = document.getElementById('output').value;
        if (document.getElementById('runoutput').value == "実行") {
          break;
        } else if (document.getElementById('clearoutput').value == "消去") {
          break;
        } else if (document.getElementById('suboutput').value == "提出") {
          break;
        } else if (similar[simcount - 1] == "yes") {
          break;
        } else if (comment[simcount - 1] == "yes") {
          break;
        } else if (document.getElementById('output').value == prmax) {
          break;
        } else {
          if (prmax > pcount) {
            pcount++;
            process(pcount);
            workcontent = document.getElementById('output').value;
          }
        }
      }
      //TutorTaskSave(workcontent);
    }
    //短縮して履歴の前を表示
    function backskip() {
      skipstart("back");
      while (true) {
        simcount = document.getElementById('output').value;
        if (document.getElementById('runoutput').value == "実行") {
          break;
        } else if (document.getElementById('clearoutput').value == "消去") {
          break;
        } else if (document.getElementById('suboutput').value == "提出") {
          break;
        } else if (similar[simcount - 1] == "yes") {
          break;
        } else if (comment[simcount - 1] == "yes") {
          break;
        } else if (document.getElementById('output').value == 1) {
          break;
        } else {
          if (pcount <= prmax && pcount > 1) {
            pcount--;
            process(pcount);
            workcontent = document.getElementById('output').value;
          }
        }
      }
      //TutorTaskSave(workcontent);
    }
    //スキップ開始時に1個ずらす
    function skipstart(action) {
      if (action == "forward") {
        if (prmax > pcount) {
          pcount++;
          process(pcount);
          workcontent = document.getElementById('output').value;
        }
      } else {
        if (pcount <= prmax && pcount > 1) {
          pcount--;
          process(pcount);
          workcontent = document.getElementById('output').value;
        }
      }
    }

    //シークバーを離した時に作動
    function workget() {
      workcontent = document.getElementById('output').value;
      //TutorTaskSave(workcontent);
    }

    function samplePG() {
      window.open('DOC/sample1.png');
    }
  </script>
</body>

</html>
