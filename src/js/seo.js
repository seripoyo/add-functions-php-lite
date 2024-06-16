/** @format */

/**
 * 概要：DOMの読み込みが完了した後に実行される処理
 *
 * 詳細：メタディスクリプションの入力欄と文字数表示、プレビューの更新機能を実装する
 *
 * @var object - descriptionInput: メタディスクリプションの入力欄要素
 * @var object - descriptionDisplay: メタディスクリプションのプレビュー表示要素
 * @var object - descriptionLengthDisplay: メタディスクリプションの文字数表示要素
 **/

document.addEventListener("DOMContentLoaded", function() {
  var descriptionInput = document.getElementById("description_input");
  var descriptionDisplay = document.getElementById("description_display");
  var descriptionLengthDisplay = document.getElementById("description_length");
  /**
   * 関数：updateDescription()
   * 概要：メタディスクリプションの入力内容に基づいてプレビューと文字数表示を更新する
   *
   * 詳細：入力されたメタディスクリプションの文字数を表示し、70文字目と80文字目を赤と青で強調表示する。85文字以上の場合は省略記号を追加する
   **/
  function updateDescription() {
    var text = descriptionInput.value;
    var length = text.length;

    descriptionLengthDisplay.innerHTML =
      "現在の文字数: " +
      length +
      "文字" +
      "<span style='color: red; margin-left:15px;'>赤文字</span>:70文字目 <span style='margin-left:15px; color: blue;'>青文字</span>:80文字目";

    // 文字に色を適用して表示
    var formattedText = "";
    for (var i = 0; i < text.length; i++) {
      if (i >= 85) break; // 85文字を超えたらループを抜ける
      var colorStyle = "";
      if (i === 69) colorStyle = "color: red; font-weight: bold;";
      if (i === 79) colorStyle = "color: blue; font-weight: bold;";
      formattedText +=
        '<span style="' + colorStyle + '">' + text[i] + "</span>";
    }
    if (text.length > 85) formattedText += "..."; // 85文字以上の場合は...を追加
    descriptionDisplay.innerHTML = formattedText;
  }

  descriptionInput.addEventListener("input", updateDescription);

  // 初期ロード時に descriptionValue の値が存在する場合は実行
  if (descriptionValue) {
    descriptionInput.value = descriptionValue;
    updateDescription();
  }
});

/**
 * 関数：window.WebFontConfig = { ... }
 * 概要：Web Font LoaderによるGoogleフォントの読み込み設定
 *
 * 詳細：Noto Serif JPとNoto Sans JPのフォントを非同期で読み込む
 **/

window.WebFontConfig = {
  google: { families: ["Noto+Serif+JP:400,700", "Noto+Sans+JP:400,700"] },
  active: function() {
    sessionStorage.fonts = true;
  }
};
/**
 * 概要：Web Font Loaderスクリプトを動的に読み込む即時実行関数
 *
 * 詳細：Web Font Loaderスクリプトを動的に生成し、ドキュメントのhead要素に追加して非同期で読み込む
 **/
(function() {
  var wf = document.createElement("script");
  wf.src =
    "https://cdn.jsdelivr.net/npm/webfontloader@1.6.28/webfontloader.min.js"; // seo.js と同じディレクトリ内にあるため、直接ファイル名を指定
  wf.type = "text/javascript";
  wf.async = "true";
  var s = document.getElementsByTagName("script")[0];
  s.parentNode.insertBefore(wf, s);
})();
