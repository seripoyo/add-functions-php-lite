/**
 * 概要：jQueryを使用してドキュメントの読み込みが完了した後に実行される処理
 *
 * 詳細：キャッシュクリアボタンがクリックされた際の処理を定義する
 *
 * @format
 */

jQuery(document).ready(function($) {
  /**
   * 概要：キャッシュクリアボタンがクリックされた際の処理
   *
   * 詳細：キャッシュクリアのAjaxリクエストを送信し、成功した場合はメッセージを表示して画面をリロードする。失敗した場合はエラーメッセージを表示する。
   *
   * @param object - e: クリックイベントオブジェクト
   **/
  $(".clear-cache-button").on("click", function(e) {
    e.preventDefault();

    $.ajax({
      url: clearCacheAjax.ajaxurl,
      type: "POST",
      data: {
        action: "clear_cache",
        nonce: clearCacheAjax.nonce
      },
      /**
       * 関数：$.ajax({ ... })
       * 概要：Ajaxリクエストを送信する
       *
       * 詳細：キャッシュクリアのAjaxリクエストを送信し、成功した場合はメッセージを表示して画面をリロードする。失敗した場合はエラーメッセージを表示する。
       *
       * @param string - url: リクエスト先のURL
       * @param string - type: リクエストのHTTPメソッド
       * @param object - data: リクエストに含めるデータ
       * @param function - success: リクエストが成功した場合のコールバック関数
       * @param function - error: リクエストが失敗した場合のコールバック関数
       **/
      success: function(response) {
        if (response.success) {
          alert(response.data);
          location.reload(); // キャッシュ削除後に画面をリロード
        } else {
          alert("Failed to clear cache: " + response.data);
        }
      },
      error: function(xhr, status, error) {
        alert("Error: " + error);
      }
    });
  });
});
