<!--課題2履歴確認用ページ-->
<?php
$key = $_GET['key'];
?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>課題2の履歴確認ページ</title>
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
    <h1 class="title">課題2のプログラム履歴確認ページ</h1>
    <nav>
      <ul>
        <li><a onClick="window.close();" class="back_btn">タブを閉じる</a></li>
      </ul>
    </nav>
  </header>

  <div id='contentSVG' class='content' style="height: 83%; width: 39.5%;"></div>
  <div id='blocklyDiv' class="tab_btn is-active-btn" mouse></div>
  <input type="range" id="seek" value="1" min="1" step="1" oninput="Seekbar()"></input>
  <output id="output">
    1
  </output>
  <output id="runoutput">
  </output>
  <output id="playtime">
  </output>
  <br>

  <button onclick="back()" class='arrow_btn' style='margin-left:5px; margin-top: 5px;'>◀</button>
  <button onclick="forward()" class='arrow_btn' style='margin-right:30px; margin-top: 5px;'>▶</button>

  <button onclick='runCode()' id='runButton' class='various_btn' style='margin-top: 5px;'>
    <i class="fas fa-cogs" style="margin-right:7px"></i>実行
  </button>
  <p></p>

  <!--<button onclick="kakunin()">確認</button>-->
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
      var code = Blockly.JavaScript.workspaceToCode(workspace);
      draw.clear();
      eval(code);
    }

    /*----------------------------------------------------------------　processを配列に格納し格納javascriptで使えるようにする　---------------------------------------------------------------------------------------*/
    function processget() {
      var split = 0;
      <?php

      //MariaDB接続
      $con = mysqli_connect('localhost', 'g231t034', 'Ku9Mm8gL');
      //データベース選択
      mysqli_select_db($con, 'prepinfo2');
      //文字コード指定
      mysqli_set_charset($con, "utf8");

      $xmls = array();
      $runs = array();
      $count = 0;

      $query = 'SELECT * FROM blockly2';
      $result = mysqli_query($con, $query);

      while ($row = mysqli_fetch_assoc($result)) {
        if (strpos($row["blockkey"], $key) !== false) {
          $xmls[] = $row['xml'];
          $runs[] = $row['action'];
          $count++;
        }
      }
      $xmls = json_encode($xmls);
      $runs = json_encode($runs);
      ?>
      xmls = <?php echo $xmls; ?>;
      runs = <?php echo $runs; ?>;
      prmax = <?php echo $count; ?>;

      setMax(prmax);
      checkStart(prmax);
    }

    /*---------------------------------------------------------------- 最初の状態を表示 ---------------------------------------------------------------------------------------*/
    function checkStart(count) {
      dummy = count;
      //キーに対応した課題のxmlを取得
      xmlText = xmls[count - 1];
      Blockly.mainWorkspace.clear();
      xmlDom = Blockly.Xml.textToDom(xmlText);
      Blockly.Xml.domToWorkspace(Blockly.mainWorkspace, xmlDom);
      document.getElementById('output').value = dummy;
      if (runs[count - 1] == "run") {
        document.getElementById('runoutput').value = "run";
      } else {
        document.getElementById('runoutput').value = "　　";
      }
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
      if (runs[seekcount - 1] == "run") {
        document.getElementById('runoutput').value = "run";
      } else {
        document.getElementById('runoutput').value = "　　";
      }
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
      }
    }
    //前のプロセスを表示
    function back() {
      if (pcount <= prmax && pcount > 1) {
        //if(pcount <= prmax && pcount >= 1){
        pcount--;
        process(pcount);
        workcontent = document.getElementById('output').value;
      }
    }

    /*---------------------------------------------------------------- 以下確認用 ---------------------------------------------------------------------------------------*/
    function kakunin() {
      //alert(xmls);
    }
  </script>
</body>

</html>
