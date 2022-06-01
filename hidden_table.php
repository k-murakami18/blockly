<!DOCTYPE html>
<html lang="ja">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>テーブル非表示</title>
  <link rel="icon" type="image/x-icon" href="/favicon.ico" />
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
  <style type="text/css">
    table,
    tr,
    td,
    th {
      border: solid 1px;
      border-collapse: collapse;
    }

    /* .list-item {
      display: contents;
    } */

    .list-item.is-hidden {
      display: none;
    }

    .list-btn.is-btn-hidden {
      display: none;
    }
  </style>
</head>

<body>

  <table>
    <thead>
      <tr>
        <th>県</th>
        <th>市</th>
        <th>人口[千人]</th>
      </tr>
    </thead>
    <tbody class="list">
      <tr class="list-item">
        <td>愛知県</td>
        <td>名古屋市</td>
        <td>2200</td>
      </tr>
      <tr class="list-item">
        <td>愛知県</td>
        <td>一宮市</td>
        <td>300</td>
      </tr>
      <tr class="list-item">
        <td>愛知県</td>
        <td>岡崎市</td>
        <td>300</td>
      </tr>
      <tr class="list-item">
        <td>愛知県</td>
        <td>名古屋市</td>
        <td>2200</td>
      </tr>
      <tr class="list-item">
        <td>愛知県</td>
        <td>名古屋市</td>
        <td>2200</td>
      </tr>
      <tr class="list-item">
        <td>愛知県</td>
        <td>名古屋市</td>
        <td>2200</td>
      </tr>
      <tr class="list-item">
        <td>愛知県</td>
        <td>名古屋市</td>
        <td>2200</td>
      </tr>
      <tr class="list-item">
        <td>愛知県</td>
        <td>名古屋市</td>
        <td>2200</td>
      </tr>
      <tr class="list-item">
        <td>愛知県</td>
        <td>名古屋市</td>
        <td>2200</td>
      </tr>
    </tbody>
  </table>
  <div class="list-btn">
    <button>もっと見る</button>
  </div>


  <script>
    /* ここには、表示するリストの数を指定します。 */
    var moreNum = 2;

    /* 表示するリストの数以降のリストを隠しておきます。 */
    $('.list-item:nth-child(n + ' + (moreNum + 1) + ')').addClass('is-hidden');

    /* 全てのリストを表示したら「もっとみる」ボタンをフェードアウトします。 */
    $('.list-btn').on('click', function() {
      $('.list-item.is-hidden').slice(0, moreNum).removeClass('is-hidden');
      if ($('.list-item.is-hidden').length == 0) {
        $('.list-btn').fadeOut();
      }
    });

    /* リストの数が、表示するリストの数以下だった場合、「もっとみる」ボタンを非表示にします。 */
    $(function() {
      var list = $(".list tr").length;
      if (list < moreNum) {
        $('.list-btn').addClass('is-btn-hidden');
      }
    });
  </script>
</body>

</html>
