/**
 * 概要：フォーム送信時に送信ボタンを押すために下げた位置でリロードされず、リロードしたらページトップで出力するためのJS
 *
 * @format
 */

(function($) {
  // jQueryプラグインの定義
  $.fn.keepPosition = function() {
    // クッキーのキーを定義。ホスト名を含めて一意にする
    var key = "keep-position-" + location.hostname;
    // フォームが送信されたかどうかを示すフラグ
    var now_submit;
    // ウィンドウオブジェクトを取得
    var win = $(window);

    // 各要素に対して処理を実行
    this.each(function() {
      // フォームが送信されたときの処理
      $(this).on("submit", function() {
        // フォームが送信されたことを示すフラグを設定
        now_submit = true;
        // 現在のスクロール位置をクッキーに保存
        $.cookie(key, win.scrollTop());
      });
    });

    // ウィンドウがアンロード（閉じる、リロードなど）されたときの処理
    win.on("unload", function() {
      // フォームが送信されていない場合、クッキーを削除
      if (!now_submit) {
        $.removeCookie(key);
      }
    });

    // ページロード後にスクロール位置を復元
    setTimeout(function() {
      // クッキーからスクロール位置を取得し、ウィンドウをその位置にスクロール
      win.scrollTop($.cookie(key));
    }, 0);
  };
})(jQuery);
