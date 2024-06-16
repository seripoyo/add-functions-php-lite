/** @format */
/**
 * 概要：jQueryを使用してドキュメントの読み込みが完了した後に実行される処理
 *
 * 詳細：画像アップロードボタンと画像削除ボタンの動作を定義する
 *
 * @var object - mediaUploader: メディアアップローダーのオブジェクト
 **/
jQuery(document).ready(function($) {
  var mediaUploader;
  /**
   * 関数：$("#upload_ogp_image_button").click(function(e) { ... })
   * 概要：OGP画像アップロードボタンがクリックされた際の処理
   *
   * 詳細：メディアアップローダーを開き、画像を選択した際に画像のURLを入力欄に設定し、プレビューを表示する
   *
   * @param object - e: クリックイベントオブジェクト
   **/
  $("#upload_ogp_image_button").click(function(e) {
    e.preventDefault();

    if (mediaUploader) {
      mediaUploader.open();
      return;
    }

    mediaUploader = wp.media.frames.file_frame = wp.media({
      title: "OGP画像を選択",
      button: {
        text: "画像を選択"
      },
      multiple: false
    });

    mediaUploader.on("select", function() {
      var attachment = mediaUploader
        .state()
        .get("selection")
        .first()
        .toJSON();
      $("#ogp_image").val(attachment.url);
      $("#ogp_image_preview")
        .attr("src", attachment.url)
        .show();
    });

    mediaUploader.open();
  });
  /**
   * 関数：$("#remove_ogp_image_button").click(function(e) { ... })
   * 概要：OGP画像削除ボタンがクリックされた際の処理
   *
   * 詳細：画像のURLを空にし、プレビューを非表示にする
   *
   * @param object - e: クリックイベントオブジェクト
   **/
  $("#remove_ogp_image_button").click(function(e) {
    e.preventDefault();
    $("#ogp_image").val("");
    $("#ogp_image_preview")
      .attr("src", "")
      .hide();
  });
});
