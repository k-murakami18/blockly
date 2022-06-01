<!--課題3確認用ページ-->
<?php
//ユーザ情報取得
$checker = $_GET['checker'];
$student = $_GET['student'];
$tutorkey = $_GET['key'];
$u_id = $_GET['u_id'];
$identifier = $_GET['identifier'];
$selection = $_GET['selection'];

//MariaDB接続
$con = mysqli_connect('localhost', 'g231t034', 'Ku9Mm8gL');
//データベース選択
mysqli_select_db($con, 'prepinfo2');
//文字コード指定
mysqli_set_charset($con, "utf8");

$query = 'SELECT * FROM user';
$result = mysqli_query($con, $query);

while ($row = mysqli_fetch_assoc($result)) {
  if ($row['storagekey'] == $u_id) {
    $name = $row['name'];
    $key = $row['storagekey'];
    $team = $row['team'];
  }
}

?>

<!DOCTYPE html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>課題3の評価ページ</title>
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
    <h1 class="title">課題3の評価ページ</h1>
    <p style="color:white"><?php echo $name ?>&nbsp;さんの課題</p>
    <nav>
      <ul>
        <li><a href="https://ichi-lab.net/~g231t034/blockly/PE2022/TutorTop.php?task=3&selection=<?php echo $selection ?>&u_id=<?php echo $identifier ?>" class="back_btn">前のページへ戻る</a></li>
      </ul>
    </nav>
  </header>

  <p style="margin:0"><b>ブロックに直接コメントを付けていくことができます。ぜひこの機能を使ってアドバイスをしてあげてください。</b><i class="far fa-question-circle" style="margin:0 5px 0 10px"></i>詳しくは<a href="https://ichi-lab.net/~g231t034/blockly/PE2022/DOC/%e3%82%b7%e3%82%b9%e3%83%86%e3%83%a0%e3%81%ae%e4%bd%bf%e3%81%84%e6%96%b9%ef%bc%88%e3%83%97%e3%83%ad%e3%82%b0%e3%83%a9%e3%83%a0%e4%b8%ad%e3%81%ae%e3%82%b3%e3%83%a1%e3%83%b3%e3%83%88%ef%bc%89.pdf" target="_blank" rel="noopener noreferrer">こちら</a></p>
  <div id='blocklyDiv' class="tab_btn is-active-btn" mouse></div>
  <div id='contentSVG' class='content' style="height: 50%; width: 39.5%;"></div>

  <form action="PHP/CheckData.php" method="POST" enctype="multipart/form-date" name='checkdata' class='check' style="font-size: 0.9em;">
    <input type="checkbox" id="check1" name="check1" value="ok" onclick="checkbox1('item1')" style="margin:5px">
    反復処理が使われているか<br>
    <input type="checkbox" id="check2" name="check2" value="ok" onclick="checkbox2('item2')" style="margin:5px">
    複数の星が描けているか<br>
    <input type="checkbox" id="check3" name="check3" value="ok" onclick="checkbox3('item3')" style="margin:5px">
    1箇所に重複していないか<br>
    <input type="checkbox" id="check4" name="check4" value="ok" onclick="checkbox4('item4')" style="margin:5px">
    星の形はすべて同じであるか<br>
    <input type="checkbox" id="check5" name="check5" value="ok" onclick="checkbox5('item5')" style="margin:5px">
    必要のない処理は行っていないか<br>
    <textarea name="advice" value="advice" rows="4" cols="50" placeholder="コメントやアドバイスを記入してください。ここの内容は学習者も確認します。「合格なので次に進んでください」といった内容でも良いので、何かしらのコメントはつけるようにしましょう。" style="margin:5px" required></textarea><br>
    <input type="checkbox" id="check6" name="check6" value="ok" onclick="checkbox6('item6')" style="margin:5px" required>
    学習者の履歴を確認したか<br>
    <input type="checkbox" id="check7" name="check7" value="ok" onclick="checkbox7('item7')" style="margin:5px" required>
    学習者に対して適切なコメントができているか<br>
    <input type="checkbox" id="check8" name="check8" value="ok" onclick="checkbox8('item8')" style="margin:5px" required>
    チェック漏れは無いか<br>
    <input type="hidden" name="confirmed" value="済">
    <input type="hidden" name="kadai" value="3">
    <input type="hidden" name="cname" value="<?php echo $checker ?>">
    <input type="hidden" name="sname" value="<?php echo $student ?>">
    <input type="hidden" name="ckey" value="<?php echo $tutorkey ?>">
    <input type="hidden" name="skey" value="<?php echo $u_id ?>">
    <input type="hidden" name="identifier" value="<?php echo $identifier ?>">
    <button onclick="clickevent()" name="submit" class='submit_btn' style='margin-left:5px; margin-top: 10px;'><i class="fas fa-check" style="margin-right:7px"></i>完了</button>
    <button onclick="guide()" class='guide_btn' style='margin-top: 10px;'><i class="fas fa-file-alt" style="margin-right:7px"></i>チェックガイド</button>
  </form>

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
  <xml id="toolbox">
    <category>
    </category>
  </xml>



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
    //ユーザー識別用のキーを取得
    var saveKey = "<?php echo $key; ?>";


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

    $(function() {
      //ページを読み込んだとき，前回までのブロックの状態を復元する,学習時間の計測開始
      window.onload = function() {
        workcnt = localStorage.getItem('workcnt');
        if (workcnt >= 1) {
          processget(workcnt);
          localStorage.clear();
        } else if (workcnt === null) {
          processget(0);
        }
        // workcnt = 0;
      }

      //ページ読み込みから1秒後にプロセスの取得を開始する
      setTimeout(function() {
        workspace.addChangeListener(commentEvent);
      }, 500);
    });

    //SVGの出力
    draw = SVG("contentSVG");

    /*---------------------------------------------------------------- ブロックを使ったときに起動 ---------------------------------------------------------------------------------------*/
    //コメントを追加・編集・削除したとき
    function commentEvent(event) {
      if (event.type == Blockly.Events.BLOCK_CHANGE && event.element == 'comment') {
        workcontent = document.getElementById('output').value;
        var xmlDom = Blockly.Xml.workspaceToDom(Blockly.mainWorkspace);
        var xmlText = Blockly.Xml.domToPrettyText(xmlDom);
        saveWorkspace(workcontent, xmlText);
        TutorTaskSave(workcontent + "comment");
        localStorage.setItem('workcnt', workcontent);
        window.setTimeout(function() {
          location.reload(false);
        }, 300);
        // console.log("プロセス数は".workcnt);
      }
    }

    /*---------------------------------------------------------------- ブロックを実行する ---------------------------------------------------------------------------------------*/
    function runCode() {
      workcontent = document.getElementById('output').value;
      TutorTaskSave(workcontent + "run");
      var code = Blockly.JavaScript.workspaceToCode(workspace);
      draw.clear();
      eval(code);
    }

    /*---------------------------------------------------------------- processを配列に格納し格納javascriptで使えるようにする ---------------------------------------------------------------------------------------*/
    function processget(workcnt) {
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

      $query2 = 'SELECT * FROM comment3';
      $result2 = mysqli_query($con, $query2);

      while ($row = mysqli_fetch_assoc($result2)) {
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
      if (workcnt !== 0) {
        checkStart(workcnt);
        console.log("workcnt" + workcnt);
      } else {
        checkStart(prmax);
      }
      // } else {
      // checkStart(workcnt);
      // console.log(comment);
      // }
      // prmax = workcnt;
      // checkStart(prmax);
      // } else {
      // checkStart(prmax);
      // }
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

    /*---------------------------------------------------------------- シークバーの設定 ---------------------------------------------------------------------------------------*/
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
        TutorTaskSave(workcontent);
      }
    }
    //前のプロセスを表示
    function back() {
      if (pcount <= prmax && pcount > 1) {
        //if(pcount <= prmax && pcount >= 1){
        pcount--;
        process(pcount);
        workcontent = document.getElementById('output').value;
        TutorTaskSave(workcontent);
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
      TutorTaskSave(workcontent);
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
      TutorTaskSave(workcontent);
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
      TutorTaskSave(workcontent);
    }

    /*---------------------------------------------------------------- チューターの作業データベースに記録する ---------------------------------------------------------------------------------------*/
    function TutorTaskSave(content) {
      $.ajax({
        url: 'PHP/Tutortask.php',
        type: 'POST',
        data: {
          'tname': "<?php echo $checker; ?>", //チューターの名前
          'tkey': "<?php echo $tutorkey; ?>", //チューターのストレージキー
          'sname': "<?php echo $student; ?>", //学習者の名前
          'tasknumber': "3", //課題の番号
          'content': content, //作業の内容
        }
      });
    }

    function saveWorkspace(content, xmlText) {
      $.ajax({
        url: 'PHP/CommentSave.php',
        type: 'POST',
        data: {
          'blockkey': "<?php echo $u_id; ?>",
          'content': content,
          'blockXML': xmlText,
          'student': "<?php echo $student; ?>", //学習者の名前
          'checker': "<?php echo $checker; ?>", //チューターの名前
          'tasknumber': 3,
        },
      });
    }

    // 解答例を表示
    function samplePG() {
      window.open('DOC/sample3.png');
    }

    /*---------------------------------------------------------------- 以下確認用 ---------------------------------------------------------------------------------------*/
    function kakunin() {
      alert("<?php echo $tutorkey; ?>")
    }
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



    /*---------------------------------------------------------------- チェックリストの処理 ---------------------------------------------------------------------------------------*/
    var box1 = 0;
    var box2 = 0;
    var box3 = 0;
    var box4 = 0;
    var box5 = 0;
    var box6 = 0;
    var box7 = 0;
    var box8 = 0;

    /*---------------------------------------------------------------- 	各種チェックボックスでON/OFFが行われたことを判別（ONならチューターの作業として記録） ---------------------------------------------------------------------------------------*/
    function checkbox1(boxnum) {
      if (boxnum == "item1" && box1 == 0) {
        //alert(boxnum);
        box1 = 1;
        TutorTaskSave(boxnum);
      } else {
        box1 = 0;
      }
    }

    function checkbox2(boxnum) {
      if (boxnum == "item2" && box2 == 0) {
        //alert(boxnum)
        box2 = 1;
        TutorTaskSave(boxnum);
      } else {
        box2 = 0;
      }
    }

    function checkbox3(boxnum) {
      if (boxnum == "item3" && box3 == 0) {
        //alert(boxnum)
        box3 = 1;
        TutorTaskSave(boxnum);
      } else {
        box3 = 0;
      }
    }

    function checkbox4(boxnum) {
      if (boxnum == "item4" && box4 == 0) {
        //alert(boxnum)
        box4 = 1;
        TutorTaskSave(boxnum);
      } else {
        box4 = 0;
      }
    }

    function checkbox5(boxnum) {
      if (boxnum == "item5" && box5 == 0) {
        //alert(boxnum)
        box5 = 1;
        TutorTaskSave(boxnum);
      } else {
        box5 = 0;
      }
    }

    function checkbox6(boxnum) {
      if (boxnum == "item6" && box6 == 0) {
        //alert(boxnum)
        box6 = 1;
        TutorTaskSave(boxnum);
      } else {
        box6 = 0;
      }
    }

    function checkbox7(boxnum) {
      if (boxnum == "item7" && box7 == 0) {
        //alert(boxnum)
        box7 = 1;
        TutorTaskSave(boxnum);
      } else {
        box7 = 0;
      }
    }

    function checkbox8(boxnum) {
      if (boxnum == "item8" && box8 == 0) {
        //alert(boxnum)
        box8 = 1;
        TutorTaskSave(boxnum);
      } else {
        box8 = 0;
      }
    }

    /*---------------------------------------------------------------- 	各種チェックボックスでON/OFFが行われたことを判別（ONならチューターの作業として記録） ---------------------------------------------------------------------------------------*/
    function clickevent() {
      TutorTaskSave("complete");
      var xmlDom = Blockly.Xml.workspaceToDom(Blockly.mainWorkspace);
      var xmlText = Blockly.Xml.domToPrettyText(xmlDom);
      $.ajax({
        url: 'PHP/CheckData.php',
        type: 'POST',
        data: {
          'blockXML': xmlText,
        },
      });
    }

    function guide() {
      window.open('DOC/guide3.pdf');
    }

    function kakunin() {
      alert("<?php echo $tmail; ?>");
      //alert("aaa");
    }
  </script>
</body>

</html>
