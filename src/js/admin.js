/** @format */



/**
 * 関数：document.addEventListener("DOMContentLoaded", function() { ... })
 * 概要：DOMの読み込みが完了した後に実行される処理
 *
 * 詳細：フォームの送信時に、 <select> 要素の選択内容を隠しフィールドにコピーし、隠しフィールドのデータをサーバーに送信する
 *
 * @var object - form: フォーム要素
 * @var object - selectedOption: 選択されたオプションの要素
 * @var object - includeTerm: 用語を含めるかどうかの選択要素
 **/
document.addEventListener("DOMContentLoaded", function() {
  // フォーム要素を取得
  var form = document.querySelector('form[method="post"]');
  // 選択されたオプションのセレクト要素を取得
  var selectedOption = document.querySelector('select[name="selected_option"]');
  // 用語を含めるかどうかのセレクト要素を取得
  var includeTerm = document.querySelector('select[name="include_term"]');

  // フォーム送信時に選択内容を隠しフィールドにコピーする処理
  form.addEventListener("submit", function() {
    // 選択されたオプションの値を持つ隠しフィールドを作成
    var hiddenFieldSelectedOption = document.createElement("input");
    hiddenFieldSelectedOption.setAttribute("type", "hidden");
    hiddenFieldSelectedOption.setAttribute("name", "selected_option");
    hiddenFieldSelectedOption.setAttribute("value", selectedOption.value);
    // 隠しフィールドをフォームに追加
    form.appendChild(hiddenFieldSelectedOption);

    // 用語を含めるかどうかの値を持つ隠しフィールドを作成
    var hiddenFieldIncludeTerm = document.createElement("input");
    hiddenFieldIncludeTerm.setAttribute("type", "hidden");
    hiddenFieldIncludeTerm.setAttribute("name", "include_term");
    hiddenFieldIncludeTerm.setAttribute("value", includeTerm.value);
    // 隠しフィールドをフォームに追加
    form.appendChild(hiddenFieldIncludeTerm);
  });
});

/**
 * 概要：jQueryを使用してドキュメントの読み込みが完了した後に実行される処理
 *
 * 詳細：特定のinput要素（カラーピッカー）を非表示にする
 **/

jQuery(document).ready(function($) {
  // 特定のinput要素にstyle属性を追加して非表示にする
  $("input.alpha-color-picker.wp-color-picker").css("display", "none");
});

/**
 * 概要：jQueryを使用してドキュメントの読み込みが完了した後に実行される処理
 *
 * 詳細：画像のプリロード処理を一度だけ実行する
 *
 * @var object - img: 新しく作成された画像要素
 **/
jQuery(document).ready(function($) {
  console.log("Document is ready.");
  var img = new Image();
  img.onload = function() {
    console.log("Image has been loaded.");
  };
  img.onerror = function() {
    console.error("Image failed to load.");
  };
  img.src = "/wp-content/plugins/add-functions-php/assets/img/sep.webp";
  if (img.complete) {
    // console.log("Image loaded from cache.");
  } else {
    console.log("Image not in cache, loading...");
  }
});

jQuery(".wp-picker-container").prepend('<div class="color_picker_inner">');
jQuery(".wp-picker-input-wrap").after("</div>");

/**
 * 概要：カラーピッカーのコンテナ要素の前に要素を追加する
 *
 * 詳細：カラーピッカーのコンテナ要素の前にカラーコードを表示するための要素を追加する
 **/
jQuery(document).ready(function($) {
  $("#copy_widgets_container").click(function() {
    var $container = $(".input_item_wrapper")
      .first()
      .clone();
    $container.find("input").val("");
    $("#widget_making_container").append($container);
  });
});
jQuery(document).ready(function($) {
  $("input.alpha_color_picker").alphaColorPicker();
});


