/** @format */

jQuery(document).ready(function($) {
  // ボタンを動的に追加
  $("body").append(
    '<button id="toggle-admin-bar" style="position: fixed; bottom: 20px; left: 20px; z-index: 1000;">アドミンバー表示切り替え</button>'
  );

  function adjustHtmlMargin() {
    if ($("#wpadminbar").is(":visible")) {
      $("style#admin-bar-style").remove(); // 管理バーが表示されている場合、スタイルを削除
    } else {
      // 管理バーが非表示の場合、スタイルを追加
      if (!$("style#admin-bar-style").length) {
        $("head").append(
          '<style id="admin-bar-style">html { margin-top: 0px !important; } body.admin-bar { --ark-adminbar_height: 0px; --ark-header_height: 0px; --ark-header_height--fixed: 0px; }</style>'
        );
      }
    }
  }

  $("#toggle-admin-bar").on("click", function() {
    // 管理バーの表示を切り替え
    $("#wpadminbar").toggle();
    adjustHtmlMargin();
  });

  // 初期ロード時にもマージンを調整
  adjustHtmlMargin();
});
