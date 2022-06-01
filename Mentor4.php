<!--課題1確認用ページ-->
<!DOCTYPE html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>課題1</title>
  <link rel="shortcut icon" href="favicon.png" type="image/png" />
  <script src="../blockly_compressed.js"></script>
  <script src="../blocks_compressed.js"></script>
  <script src="../javascript_compressed.js"></script>
  <!-- <script src="JS/prettify.js"></script> -->
  <script src="../msg/js/ja.js"></script>
  <script src="../appengine/storage.js"></script>
  <script src="JS/PEblockly.js"></script>
  <script src="JS/PEblocks.js"></script>
  <script src="JS/jquery-2.2.4.min.js"></script>
  <script src="JS/jquery.query.js"></script>
  <script src="JS/svg.js"></script>
  <script src="JS/illust.js"></script>
  <!-- ajax用 -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <!-- CSS -->
  <link rel="stylesheet" href="CSS/PEtask.css" />
  <link rel="stylesheet" href="CSS/CheckList.css" />
</head>

<body>
  <?php
  //ユーザ情報取得
  $checker = $_GET['checker'];
  $student = $_GET['student'];
  $tutorkey = $_GET['key'];
  //$tmail = $_GET['tmail'];
  //id取得
  $u_id = $_GET['u_id'];

  //MariaDB接続
  $con = mysqli_connect('localhost', 'g231t034', 'Ku9Mm8gL');
  //データベース選択
  mysqli_select_db($con, 'g231t034');
  //文字コード指定
  mysqli_set_charset($con, "utf8");

  $query = 'SELECT * FROM user';
  $result = mysqli_query($con, $query);

  while ($row = mysqli_fetch_assoc($result)) {
    if ($row['storagekey'] == $u_id) {
      echo $row['name'] . "さんの課題です。";
      $name = $row['name'];
      $key = $row['storagekey'];
      //$mail = $row['mail'];
      $team = $row['team'];
    }
  }

  ?>
  <div id='contentSVG' class='content' style='height: 50%;width: 45%;'></div>
  <div id='blocklyDiv' class="tab_btn is-active-btn" style='height: 90%;width: 54%;' mouse></div>
  <!--
	<iframe id='checklist' src='PHP/checklist1.php?checker=<?php echo $checker ?>&student=<?php echo $student ?>&tutorkey=<?php echo $tutorkey ?>&studentkey=<?php echo $key ?>' name='checkdata' class='check' style='height: 45%;width: 45%;'>
	</iframe>
	-->
  <button onclick='runCode()' id='runButton'>
    実行
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
  <!-- <button onclick='sample()' id='sample'>正解例</button> -->
  <button onclick="backskip()">戻スキップ</button>
  <button onclick="back()">◀</button>
  <button onclick="forward()">▶</button>
  <button onclick="forwardskip()">次スキップ</button>
  　
  <button onclick="samplePG()">サンプル</button>
  <!--
	<button onclick="Sendmail()">メール</button>
	<button onclick="printxml()">xml表示</button>
	<button onclick="showJS()">JS表示</button>
	<button onclick="kakunin()">確認用</button>
	-->
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
      $count = 0;

      $query = 'SELECT * FROM blockly4';
      $result = mysqli_query($con, $query);

      while ($row = mysqli_fetch_assoc($result)) {
        if (strpos($row["blockkey"], $key) !== false) {
          $xmls[] = $row['xml'];
          $time[] = $row['time'];
          $runs[] = $row['action'];
          $clear[] = $row['clear'];
          $submit[] = $row['submit'];
          $similar[] = $row['similar'];
          $count++;
        }
      }
      $xmls = json_encode($xmls);
      $time = json_encode($time);
      $runs = json_encode($runs);
      $clear = json_encode($clear);
      $submit = json_encode($submit);
      $similar = json_encode($similar);
      ?>
      xmls = <?php echo $xmls; ?>;
      time = <?php echo $time; ?>;
      runs = <?php echo $runs; ?>;
      clear = <?php echo $clear; ?>;
      submit = <?php echo $submit; ?>;
      similar = <?php echo $similar; ?>;
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
      window.open('DOC/sample4.png');
    }

    /*---------------------------------------------------------------- 以下確認用 ---------------------------------------------------------------------------------------*/
    function kakunin() {
      alert("<?php echo $tutorkey; ?>")
    }
    //数値チェック
    /*
    function kakunin(){
    	var value = xmls[0];
    	alert(<?php echo $_SESSION['key']; ?>);
    	alert(runs[2]);
    }
    */
    //xml表示
    function printxml() {
      var xmlDom2 = Blockly.Xml.workspaceToDom(Blockly.mainWorkspace);
      var xmlText2 = Blockly.Xml.domToPrettyText(xmlDom2);
      alert(xmlText2);
    }
    //javascript表示
    function showJS() {
      /*
      var jstext = Blockly.JavaScript.workspaceToCode(workspace);
      alert(jstext);*/
      alert(saveKey);
    }
  </script>
</body>

</html>
