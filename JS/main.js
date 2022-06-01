/* テキストエリアの初期設定. */

// [1] height:30pxで指定
$("#sample").height(30);
// [2] lineHeight:20pxで指定<ユーザ定義>(※line-heightと間違えないように)
$("#sample").css("lineHeight","20px");

/**
 * 高さ自動調節イベントの定義.
 * autoheightという名称のイベントを追加します。
 * @param evt
 */
$("#sample").on("autoheight", function(evt) {
  // 対象セレクタをセット
  var target = evt.target;

  // CASE1: スクロールする高さが対象セレクタの高さよりも大きい場合
  // ※スクロール表示される場合
  if (target.scrollHeight > target.offsetHeight) {
    // スクロールする高さをheightに指定
    $(target).height(target.scrollHeight);
  }
  // CASE2: スクロールする高さが対象セレクタの高さよりも小さい場合
  else {
    // lineHeight値を数値で取得
    var lineHeight = Number($(target).css("lineHeight").split("px")[0]);

    while (true) {
      // lineHeightずつheightを小さくする
      $(target).height($(target).height() - lineHeight);
      // スクロールする高さが対象セレクタの高さより大きくなるまで繰り返す
      if (target.scrollHeight > target.offsetHeight) {
        $(target).height(target.scrollHeight);
        break;
      }
    }
  }
});

$(document).ready(function() {
  // autoheightをトリガする
  $("#sample").trigger('autoheight');
});
