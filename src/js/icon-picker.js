/** @format */

jQuery(document).ready(function($) {
  var savedDashiconsClasses = {}; // dashiconsクラスを永続的に保存するオブジェクト

  /**
   * 関数：escapeSelector
   * 概要：CSSセレクタをエスケープする
   *
   * 詳細：CSSセレクタ内の特殊文字をエスケープし、セレクタとして使用できるようにする
   *
   * @param string - selector: エスケープ対象のCSSセレクタ
   * @return string - エスケープ後のCSSセレクタ
   **/
  function escapeSelector(selector) {
    return selector.replace(/(:|\.|\[|\]|,|=|@|\?|\/|&| )/g, "\\$1"); // スペースもエスケープ対象に含める
  }
  /**
   * 関数：updateMenuIcon
   * 概要：メニューアイコンを更新する
   *
   * 詳細：指定されたメニューIDに対応するアイコンクラスを更新し、プレビューとメニュー画像に反映する
   * 　　　dashicons-クラスを削除し、保存された新しいアイコンクラスを追加する関数
   *
   * @param string - rawId: メニューのID
   * @param string - iconClass: 新しいアイコンクラス
   **/
  function updateMenuIcon(rawId, iconClass) {
    var escapedId = escapeSelector(rawId);
    var $iconPreview = $("#icon_preview_" + escapedId);
    var $menuImage = $(
      "#adminmenu a[href*='" + escapedId + "'] .wp-menu-image"
    );

    if (iconClass) {
      $iconPreview
        .removeClass(function(index, className) {
          return (className.match(/(^|\s)add-icon-\S+/g) || []).join(" ");
        })
        .addClass(iconClass);
      $menuImage
        .removeClass(function(index, className) {
          return (className.match(/(^|\s)dashicons-\S+/g) || []).join(" ");
        })
        .addClass(iconClass)
        .addClass("icon-loaded");
    } else if (savedDashiconsClasses[rawId]) {
      $iconPreview
        .removeClass(function(index, className) {
          return (className.match(/(^|\s)add-icon-\S+/g) || []).join(" ");
        })
        .addClass(savedDashiconsClasses[rawId].join(" "));
      $menuImage
        .removeClass(function(index, className) {
          return (className.match(/(^|\s)dashicons-\S+/g) || []).join(" ");
        })
        .addClass(savedDashiconsClasses[rawId].join(" "))
        .addClass("icon-loaded");
    }
  }

  /**
   * 関数：$(".dashicon-picker-container").on("click", "a", function(e) { ... })
   * 概要：アイコン選択時の処理
   *
   * 詳細：アイコンがクリックされた際に、選択されたアイコンクラスを取得し、対応する入力欄とプレビューを更新する
   *
   * @param object - e: クリックイベントオブジェクト
   **/
  $(".dashicon-picker-container").on("click", "a", function(e) {
    e.preventDefault();
    var icon = $(this)
      .find("span")
      .attr("class");
    var target = $(".dashicon-picker-container").data("target");
    var escapedTarget = escapeSelector(target);
    var $menuIconInput = $('input[name="custom_menu_icons[' + target + ']"]');
    var $iconPreview = $("#icon_preview_" + escapedTarget);

    if ($menuIconInput.length) {
      $menuIconInput.val(icon); // hidden inputの値を更新
      var currentClasses = $iconPreview
        .attr("class")
        .split(" ")
        .filter(c => !c.startsWith("setting_icon_preview"));
      $iconPreview
        .removeClass(currentClasses.join(" "))
        .addClass("setting_icon_preview")
        .addClass(icon);
    }

    $(".dashicon-picker-container").hide();
  });
  /**
   * 関数：$(window).on("load", function() { ... })
   * 概要：ページの初期ロードとナビゲーション後の処理
   *
   * 詳細：ページの初期ロード時とナビゲーション後に、各メニューアイコンの設定を読み込み、アイコンを更新する
   **/
  $(window).on("load", function() {
    $('input[type="hidden"][name^="custom_menu_icons"]').each(function() {
      var rawId = $(this)
        .attr("id")
        .replace("menu_icon_", "");
      var iconClass = $(this).val();
      var $menuImage = $(
        "#adminmenu a[href*='" + escapeSelector(rawId) + "'] .wp-menu-image"
      );

      if ($menuImage.length) {
        var dashiconsClasses =
          $menuImage.attr("class").match(/dashicons-\S+/g) || [];
        savedDashiconsClasses[rawId] = dashiconsClasses;
      }

      updateMenuIcon(rawId, iconClass);
    });

    /**
     * 関数：resetIconPreview
     * 概要：アイコンプレビューを初期設定に戻す
     *
     * 詳細：指定されたメニューIDに対応するアイコンプレビューを初期設定に戻し、入力欄の値をクリアする
     *
     * @param string - target: メニューのID
     **/
    function resetIconPreview(target) {
      var escapedTarget = escapeSelector(target);
      var $iconPreview = $("#icon_preview_" + escapedTarget);
      var $hiddenInput = $("#menu_icon_" + escapedTarget);

      if ($iconPreview.length) {
        var classesToRemove = $iconPreview
          .attr("class")
          .split(" ")
          .filter(function(cls) {
            return cls !== "setting_icon_preview";
          });
        $iconPreview.removeClass(classesToRemove.join(" "));

        if (savedDashiconsClasses[target]) {
          var newClasses = savedDashiconsClasses[target].join(" ");
          $iconPreview.addClass(newClasses);
          $hiddenInput.val(newClasses);
        }
      }
    }
    /**
     * 概要：初期設定に戻すボタンのクリック時の処理
     *
     * 詳細：初期設定に戻すボタンがクリックされた際に、対応するメニューIDのアイコンプレビューを初期設定に戻す
     **/
    $(document).on("click", ".reset-icon-selection", function() {
      var target = $(this)
        .closest(".dashicon-picker-container")
        .data("target");
      if (target) {
        resetIconPreview(target);
      }
    });

    /**
     * 概要：アイコン選択ボタンのクリック時の処理
     *
     * 詳細：アイコン選択ボタンがクリックされた際に、対応するアイコンピッカーコンテナを表示する
     **/
    $(".dashicons-picker").on("click", function() {
      var $button = $(this);
      var target = $button.data("target");

      if (target) {
        $button.data("target", target);

        if ($("#side_menu_icon.dashicon-picker-container").length) {
          $("#side_menu_icon.dashicon-picker-container")
            .data("target", target)
            .css({
              position: "absolute",
              top: $button.offset().top,
              left: $button.offset().left - 270
            })
            .show();
        }

        if ($("#widgets_icon.dashicon-picker-container").length) {
          $("#widgets_icon.dashicon-picker-container")
            .data("target", target)
            .css({
              position: "absolute",
              top: $button.offset().top,
              right: $button.offset().right
            })
            .show();
        }
      }
    });

    /**
     * 概要：アイコンピッカーコンテナ外のクリック時の処理
     *
     * 詳細：アイコンピッカーコンテナ外がクリックされた際に、アイコンピッカーコンテナを非表示にする
     **/
    $(document).on("mouseup", function(e) {
      var container = $(".dashicon-picker-container");
      if (!container.is(e.target) && container.has(e.target).length === 0) {
        container.hide();
      }
    });
  });

  /**
   * 関数：$("input.alpha-color-picker").alphaColorPicker()
   * 概要：カラーピッカーの初期化
   *
   **/
  $("input.alpha-color-picker").alphaColorPicker();
});
