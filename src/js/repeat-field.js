/** @format */

jQuery(document).ready(function($) {
  /**
   * 関数：initColorPicker
   * 概要：カラーピッカーを初期化する
   *
   * 詳細：指定されたウィジェット内のカラーピッカー入力欄にカラーピッカーを適用する
   *
   * @param object - widget: カラーピッカーを初期化するウィジェット要素
   **/
  function initColorPicker(widget) {
    widget.find(".alpha-color-picker").wpColorPicker({
      change: function(event, ui) {
        /* カラーが変更されたときの処理 */
      },
      clear: function() {
        /* クリアボタンが押されたときの処理 */
      }
    });
  }
  /**
   * 関数：addNewWidget
   * 概要：新しいウィジェットを追加する
   *
   * 詳細：指定されたインデックスとタイプに基づいて新しいウィジェットを作成し、ページに追加する
   *
   * @param number - index: 新しいウィジェットのインデックス
   * @param string - currentType: 現在選択されているウィジェットのタイプ
   **/
  function addNewWidget(index, currentType) {
    console.log(
      `ウィジェットの追加開始 - インデックス: ${index}, 現在のタイプ: ${currentType}`
    );
    try {
      // ウィジェットのHTMLテンプレートを作成
      var lastWidgetIndex = $(".widget_container:last").data("index") || 0;
      var newIndex = lastWidgetIndex + 1;
      //   var color = "アイコンを選択";
      var newWidget = $(`<div class="widget_container" data-index="${index}">
            <h3>作成するウィジェット大枠</h3>
			
            <div class="grid_form_contents_item">
                <label class="widget_text_input widget_name_input" for="widget_name_${index}">
                    	<input type="text"  placeholder="ウィジェット" name="add_functions_php_settings[widget_name][${index}][widget_name]" id="widget_name_${index}" value="">
                </label>
            </div>
      <ul class="widget_radio" data-selected-type="${currentType}">
            <li class="widget-01">
                <label class="radio-label widget-01">
 <input class="radio_input" id="widget_design_type_${newIndex}_widget_01" type="radio" name="add_functions_php_settings[widget_design_type][${newIndex}]" value="widget01">
        <span>widget01</span>
                </label>
				<ul class="widget01 add-custom-widget">
    <li>
        <a href="#">
            <span class="add-icon-edit_square"></span>
            <p>widget01</p>
        </a>
    </li>
</ul>
            </li>
            <li class="widget-02">
                <label class="radio-label widget-02">
        <input class="radio_input" id="widget_design_type_${newIndex}_widget_02" type="radio" name="add_functions_php_settings[widget_design_type][${newIndex}]" value="widget02">
        <span>widget02</span>
                </label>
				    <ul class="widget02 add-custom-widget">
        <li>
            <a href="#">
                <span class="add-icon-edit_square"></span>
                <p>widget02</p>
            </a>
        </li>
    </ul>
            </li>
            <li class="widget-03">
                <label class="radio-label widget-03">
        <input class="radio_input" id="widget_design_type_${newIndex}_widget_03" type="radio" name="add_functions_php_settings[widget_design_type][${newIndex}]" value="widget03">
        <span>widget03</span>
                </label>
				    <ul class="widget03 add-custom-widget">
        <li>
            <a href="#">
                <span class="add-icon-edit_square"></span>
                <p>widget03</p>
            </a>
        </li>
    </ul>
            </li>
        </ul>
      <div class="widget_items_wrapper">
        <div class="widget_item_container" data-item-index="0">
		<h4>ウィジェットに設定するボタン設定</h4>
          <div class="grid_form_contents_item">
	  <label class="widget_text_input"  for="widget_label_${index}_0">
			 	<input placeholder="ウィジェットに追加するボタンのラベルを入力してください" type="text" name="add_functions_php_settings[widget_items][${index}][0][widget_label]" id="widget_label_${index}_0" value="">
				<span></span>
            </label>
          </div>
<div class="widget_text_input grid_form_contents_item widget_url">
							<label class="widget_text_input" for="widget_url_${index}_0">
              <input placeholder="ボタンのリンク先にするURLを入力してください" type="text" name="add_functions_php_settings[widget_items][${index}][0][widget_url]" id="widget_url_${index}_0" value="">
			  <span></span>
            </label>
          </div>
          <div class="grid_form_contents_item">
            <label for="widget_color_${index}_0">
              <input type="hidden" class="alpha-color-picker" name="add_functions_php_settings[widget_items][${index}][0][widget_color]" id="widget_color_${index}_0" value="#1d2327" />
            </label>
          </div>

          <div class="grid_item custom txt">
		  <div class="grid_item setting_icon_spacer">
		  <span class="add-icon-add_circle"></span>
            <input id="setting_icon_${index}_0" class="button dashicons-picker" type="button" value="アイコンを選択" data-target="${index}_0">
			<span class="setting_icon_preview" id="icon_preview_${index}_0"></span>
			</div>
            <input type="hidden" name="add_functions_php_settings[widget_items][${index}][0][widget_icon]" id="menu_icon_${index}_0" value="">
          </div>
          <button type="button" class="remove_widget_item" data-container-index="${index}" data-item-index="0">ボタンを削除する</button>
        </div>
      </div>
      <button type="button" class="add_widget_item" data-container-index="${index}">
	  <span class="add-icon-add_ad"></span>ボタンを追加する			</button>
      <button type="button" class="remove_widget_container" data-container-index="${index}">ウィジェットを削除する</button>
    </div>`);

      console.log("ウィジェットHTMLの作成完了 - ウィジェット追加します。");

      // ウィジェットをページに追加
      $(".widgets_wrapper").append(newWidget);
      // 選択されたラジオボタンにcheckedを設定
      newWidget
        .find(
          `.widget_radio[data-selected-type="${currentType}"] input[value="${currentType}"]`
        )
        .prop("checked", true);
      // カラーピッカー初期化
      initColorPicker(newWidget);
      console.log("カラーピッカー初期化完了");

      // イベントハンドラの初期化
      newWidget.find(".add_widget_item").on("click", function() {
        var containerIndex = $(this).data("container-index");
        addNewWidgetItem(containerIndex);
        console.log(
          `ウィジェットアイテム追加 - コンテナインデックス: ${containerIndex}`
        );
      });

      newWidget.find(".remove_widget_container").on("click", function() {
        $(this)
          .closest(".widget_container")
          .remove();
        console.log("ウィジェット削除");
      });

      console.log("ウィジェット追加処理完了");
    } catch (error) {
      console.error("エラーが発生しました: ", error);
    }
  }
  /**
   * 概要：ウィジェット追加ボタンのクリック時の処理
   *
   * 詳細：ウィジェット追加ボタンがクリックされた際に、新しいウィジェットを追加する
   **/
  $("#add_widget").click(function() {
    var lastWidgetIndex = $(".widget_container:last").data("index") || 0;
    addNewWidget(lastWidgetIndex + 1);
  });

  /**
   * 概要：ウィジェットアイテム追加ボタンのクリック時の処理
   *
   * 詳細：ウィジェットアイテム追加ボタンがクリックされた際に、対応するウィジェットに新しいアイテムを追加する
   **/

  $(document).on("click", ".add_widget_item", function() {
    var containerIndex = $(this).data("container-index");
    addNewItem(containerIndex);
  });
  /**
   * 関数：addNewItem
   * 概要：新しいウィジェットアイテムを追加する
   *
   * 詳細：指定されたウィジェットコンテナに新しいウィジェットアイテムを作成し、追加する
   *
   * @param number - containerIndex: ウィジェットコンテナのインデックス
   **/
  function addNewItem(containerIndex) {
    var widgetItemsWrapper = $(
      `.widget_container[data-index="${containerIndex}"] .widget_items_wrapper`
    );
    var newItemIndex = widgetItemsWrapper.find(".widget_item_container").length;

    // 1つ前のwidget_item_containerのwidget_colorの値を取得
    var prevItemIndex = newItemIndex - 1;
    var prevWidgetColor = widgetItemsWrapper
      .find(
        `.widget_item_container[data-item-index="${prevItemIndex}"] input[name="add_functions_php_settings[widget_items][${containerIndex}][${prevItemIndex}][widget_color]"]`
      )
      .val();

    // 1つ前のwidget_colorの値が存在しない場合は、デフォルト値を設定
    if (!prevWidgetColor) {
      prevWidgetColor = "#1d2327";
    }

    var newItem = $(`
    <div class="widget_item_container" data-item-index="${newItemIndex}">
	<h4>ウィジェットに設定するボタン設定</h4>
      <div class="grid_form_contents_item">
	  <label class="widget_text_input" for="widget_label_${containerIndex}_${newItemIndex}">
          	<input placeholder="ウィジェットに追加するボタンのラベルを入力してください"type="text" name="add_functions_php_settings[widget_items][${containerIndex}][${newItemIndex}][widget_label]" id="widget_label_${containerIndex}_${newItemIndex}" value="">
			<span></span>
        </label>
      </div>
<div class="widget_text_input grid_form_contents_item widget_url">
<label class="widget_text_input" for="widget_url_${containerIndex}_${newItemIndex}">
          <input placeholder="ボタンのリンク先にするURLを入力してください" type="text" name="add_functions_php_settings[widget_items][${containerIndex}][${newItemIndex}][widget_url]" id="widget_url_${containerIndex}_${newItemIndex}" value="">
		  <span></span>
        </label>
      </div>
   <div class="widget_text_input grid_form_contents_item widget_url">
   <label class="widget_text_input"  for="widget_color_${containerIndex}_${newItemIndex}">
          <input type="hidden" class="alpha-color-picker" name="add_functions_php_settings[widget_items][${containerIndex}][${newItemIndex}][widget_color]" id="widget_color_${containerIndex}_${newItemIndex}" value="${prevWidgetColor}" />
        </label>
      </div>
          <div class="grid_item custom txt">
		  <div class="grid_item setting_icon_spacer">
		  <span class="add-icon-add_circle"></span>
        <input id="setting_icon_${containerIndex}_${newItemIndex}" class="button dashicons-picker" type="button" value="アイコンを選択" data-target="${containerIndex}_${newItemIndex}">
		<span class="setting_icon_preview" id="icon_preview_${containerIndex}_${newItemIndex}"></span>
      </div>
      <div class="grid_item custom txt">
        <input type="hidden" name="add_functions_php_settings[widget_items][${containerIndex}][${newItemIndex}][widget_icon]" id="menu_icon_${containerIndex}_${newItemIndex}" value="">
      </div>
      <button type="button" class="remove_widget_item" data-container-index="${containerIndex}" data-item-index="${newItemIndex}">ボタンを削除する</button>
    </div>`);

    widgetItemsWrapper.append(newItem);
    initColorPicker(newItem);

    // ボタンが最後の1つになった場合、自動的に.remove_widget_itemを非表示にする
    if (widgetItemsWrapper.find(".widget_item_container").length === 1) {
      widgetItemsWrapper.find(".remove_widget_item").hide();
    } else {
      widgetItemsWrapper.find(".remove_widget_item").show();
    }
  }

  $(document).on("click", ".remove_widget_item", function() {
    var containerIndex = $(this).data("container-index");
    var itemIndex = $(this).data("item-index");
    var widgetItemsWrapper = $(this)
      .closest(".widget_container")
      .find(".widget_items_wrapper");

    if (widgetItemsWrapper.find(".widget_item_container").length > 1) {
      // 削除対象の.widget_item_containerを取得
      var widgetItemContainer = widgetItemsWrapper.find(
        `.widget_item_container[data-item-index="${itemIndex}"]`
      );

      // 削除対象と同じdata-item-indexを持つ.remove_widget_itemを取得
      var removeButton = widgetItemsWrapper.find(
        `.remove_widget_item[data-item-index="${itemIndex}"]`
      );

      // .widget_item_containerと.remove_widget_itemの両方を削除
      widgetItemContainer.remove();
      removeButton.remove();
      // ボタンが最後の1つになった場合、自動的に.remove_widget_itemを非表示にする
      if (widgetItemsWrapper.find(".widget_item_container").length === 1) {
        widgetItemsWrapper.find(".remove_widget_item").hide();
      }
    } else {
      alert("You cannot remove the last widget item.");
    }
  });

  $(document).on("click", ".remove_widget_container", function() {
    var widgetsWrapper = $(this).closest(".widgets_wrapper");
    if (widgetsWrapper.find(".widget_container").length > 1) {
      $(this)
        .closest(".widget_container")
        .remove();
    } else {
      alert("You cannot remove the last widget container.");
    }
  });
});

document.querySelectorAll("ul.widget_radio").forEach(function(ul) {
  // `ul`の`data-selected-type`属性から選択済みのタイプを取得
  const selectedType = ul.getAttribute("data-selected-type");
  console.log(`選択済みタイプを復元 - data-selected-type: ${selectedType}`);

  // 該当する`input`要素のセレクタを生成
  const inputSelector = `input[type="radio"][value="${selectedType}"]`;

  // `ul`内でそのセレクタにマッチする`input`を見つけて`checked`に設定
  const inputElement = ul.querySelector(inputSelector);
  if (inputElement) {
    inputElement.checked = true;
    console.log(`ラジオボタンをチェック - value: ${selectedType}`);
  } else {
    console.warn(`ラジオボタンが見つかりませんでした - value: ${selectedType}`);
  }
});
// 選択された色を<span class="setting_icon_preview" id="icon_preview_${index}_0"></span>の背景色に設定する
