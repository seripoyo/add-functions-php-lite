/** @format */

jQuery(document).ready(function($) {
  var savedDashiconsClasses = {}; // dashiconsクラスを永続的に保存するオブジェクト

  /**
   * 関数：escapeSelector
   * 概要：CSSセレクタをエスケープする
   *
   * 詳細：CSSセレクタ内の特殊文字をエスケープして、安全に使用できる形式に変換する
   *
   * @param string - selector: エスケープするCSSセレクタ
   * @return string - エスケープされたCSSセレクタ
   **/
  function escapeSelector(selector) {
    return selector.replace(/(:|\.|\[|\]|,|=|@|\?|\/|&| )/g, "\\$1"); // スペースもエスケープ対象に含める
  }
  /**
   * 関数：updateMenuIcon
   * 概要：メニューアイコンを更新する
   *
   * 詳細：指定されたIDに基づいて、メニューアイコンのクラスを更新する。新しいアイコンクラスが指定されている場合はそれを適用し、指定されていない場合は保存されたクラスを適用する
   *　　　 dashicons-クラスを削除し、保存された新しいアイコンクラスを追加する関数
   *
   * @param string - rawId: メニューアイコンのID
   * @param string - iconClass: 適用するアイコンクラス
   **/
  function updateMenuIcon(rawId, iconClass) {
    var escapedId = escapeSelector(rawId);
    var $iconPreview = $("#icon_preview_" + escapedId);
    var $menuImage = $(
      "#adminmenu a[href*='" + escapedId + "'] .wp-menu-image"
    );

    if (iconClass) {
      $iconPreview.attr("class", "setting_icon_preview " + iconClass);
      $menuImage.attr("class", "wp-menu-image " + iconClass);
      console.log(
        "アイコンクラス " + iconClass + " を " + rawId + " に適用しました。"
      );
    }
  }

  // アイコン選択イベント
  // =============================================
  // アイコン選択イベントを動的要素にも適用
  $(document).on("click", ".dashicons-picker", function() {
    console.log("クリックされたよ");
    var $button = $(this);
    var target = $button.data("target");
    var escapedTarget = escapeSelector(target);
    var $iconPreview = $("#icon_preview_" + escapedTarget);
    var $menuIconInput = $("#menu_icon_" + escapedTarget);

    $(".dashicon-picker-container")
      .data("target", target)
      .show();

    // アイコン選択後の処理
    $(".dashicon-picker-container").on("click", "a", function(e) {
      e.preventDefault();
      var icon = $(this)
        .find("span")
        .attr("class");

      var target = $(".dashicon-picker-container").data("target");
      console.log("選択されたアイコン: " + icon);
      console.log("ターゲット: " + target);

      if (target) {
        var escapedTarget = escapeSelector(target);
        var $menuIconInput = $("#menu_icon_" + escapedTarget);
        var $iconPreview = $("#icon_preview_" + escapedTarget);

        console.log("$menuIconInput: ", $menuIconInput);
        console.log("$iconPreview: ", $iconPreview);

        if ($menuIconInput.length) {
          $menuIconInput.val(icon);
          $iconPreview.attr("class", "setting_icon_preview " + icon);
          console.log(
            "アイコン " + icon + " が " + target + " に設定されました。"
          );
        } else {
          console.log("$menuIconInput not found for target: " + target);
        }
      } else {
        console.log("ターゲットが見つかりませんでした。");
      }

      $(".dashicon-picker-container").hide();
    });
  });

  // ページの初期ロードとナビゲーション後にアイコンを更新
  // =============================================
  // ページの初期ロードとナビゲーション後にアイコンを更新
  $(window).on("load", function() {
    $('input[type="hidden"][name^="custom_menu_icons"]').each(function() {
      var rawId = $(this)
        .attr("id")
        .replace("menu_icon_", "");
      var iconClass = $(this).val();
      updateMenuIcon(rawId, iconClass);
    });
  });

  // 初期設定に戻すボタンの処理
  // =============================================

  function escapeSelector(selector) {
    return selector.replace(/(:|\.|\[|\]|,|=|@|\?|\/|&| )/g, "\\$1");
  }

  /**
   * 関数：resetIconPreview
   * 概要：アイコンプレビューを初期設定に戻す
   *
   * 詳細：指定されたターゲットのアイコンプレビューを初期設定に戻し、保存されたクラスを再適用する
   *
   * @param string - target: 初期設定に戻すターゲットのID
   **/

  function resetIconPreview(target) {
    var escapedTarget = escapeSelector(target);
    var $iconPreview = $("#icon_preview_" + escapedTarget);
    var $hiddenInput = $("#menu_icon_" + escapedTarget);

    if ($iconPreview.length) {
      // setting_icon_preview 以外の全てのクラスを削除
      var classesToRemove = $iconPreview
        .attr("class")
        .split(" ")
        .filter(function(cls) {
          return cls !== "setting_icon_preview";
        });
      $iconPreview.removeClass(classesToRemove.join(" "));

      // savedDashiconsClassesから保存されたクラスを追加
      if (savedDashiconsClasses[target]) {
        var newClasses = savedDashiconsClasses[target].join(" ");
        $iconPreview.addClass(newClasses);
        console.log(
          target + " に保存されたクラス " + newClasses + " を再適用しました。"
        );

        // 隠しフィールドの値を更新
        $hiddenInput.val(newClasses);
        // console.log("hidden input の value を更新しました: " + newClasses);
      }
    } else {
      //   console.error(
      //     "setting_icon_preview 要素が見つかりませんでした: #" + escapedTarget
      //   );
    }
  }

  /**
   * 概要：初期設定に戻すボタンがクリックされた際の処理
   *
   * 詳細：クリックされたボタンのターゲットを取得し、アイコンプレビューを初期設定に戻す
   **/
  $(document).on("click", ".reset-icon-selection", function() {
    var target = $(this)
      .closest(".dashicon-picker-container")
      .data("target");
    if (target) {
      resetIconPreview(target);
    } else {
      //   console.log("ターゲットがデータ属性から取得できませんでした。");
    }
  });

  /**
   * 概要：アイコン選択ボタンがクリックされた際の処理
   *
   * 詳細：クリックされたボタンのターゲットを取得し、アイコンピッカーコンテナを表示する
   **/
  $(document).on("click", ".dashicons-picker", function() {
    var $button = $(this);
    var target = $button.data("target");
    //   console.log("アイコン選択ボタンがクリックされました。ターゲット:", target);

    if (target) {
      $button.data("target", target);

      // #widget_icon_btn.dashicon-picker-containerが存在する場合
      if ($("#widget_icon_btn.dashicon-picker-container").length) {
        $("#widget_icon_btn.dashicon-picker-container")
          .data("target", target)
          .css({
            position: "absolute",
            top: $button.offset().top,
            left: $button.offset().left - 270
          })
          .show();
      }
    } else {
      // console.log("ターゲットが指定されていません。");
    }
  });

  /**
   * 概要：アイコンピッカーコンテナ外がクリックされた際の処理
   *
   * 詳細：アイコンピッカーコンテナ外がクリックされた場合、アイコンピッカーコンテナを非表示にする
   *
   * @param object - e: クリックイベントオブジェクト
   **/
  $(document).on("mouseup", function(e) {
    var startTime = performance.now();
    var container = $(".dashicon-picker-container");
    if (!container.is(e.target) && container.has(e.target).length === 0) {
      container.hide();
      //   console.log(
      //     "アイコンピッカーコンテナ外がクリックされました。コンテナを非表示にします。"
      //   );
    }
  });
});
/**
 * 概要：jQueryを使用してドキュメントの読み込みが完了した後に実行される処理
 *
 * 詳細：カラーピッカーを初期化する
 **/
jQuery(document).ready(function($) {
  $("input.alpha-color-picker").alphaColorPicker();
});
