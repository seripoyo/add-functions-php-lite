<div style="margin:20px 0;">
	<p>検索時タイトル</p>
	<input id="title_input" type="text" name="seo_title" value="<?php echo esc_attr( $title ); ?>" size="60" />
	<div style="clear:both;"></div>
</div>

<div style="margin:20px 0;">
	<p>キーワード（「、」で区切ってOK）</p>
	<input id="keyword_input" type="text" name="keywords" value="<?php echo esc_html( $keywords ); ?>" size="60" />
	<div style="clear:both;"></div>
</div>

<div style="margin:20px 0;">
	<p>メタディスクリプション：検索結果に出力するページ説明文</p>
	<textarea name="description" id="description_input" cols="60" rows="4" style="width: 100%;"><?php echo esc_html( $description ); ?></textarea>
	<div style="clear:both;"></div>
	<div id="description_length"></div>
	<div id="description_display"></div>
</div>

<div style="margin:20px 0;">
	<input type="checkbox" name="noindex" value="1" <?php checked( $noindex, '1' ); ?>>
	<span>noindex（検索結果への表示をブロックする）</span>
	<div style="clear:both;"></div>
</div>

<div style="margin:20px 0;">
	<input type="checkbox" name="nofollow" value="1" <?php checked( $nofollow, '1' ); ?>>
	<span>nofollow（リンクを除外して辿れないようにする）ほとんどの場合、チェックを入れる必要はありません。</span>
	<div style="clear:both;"></div>
</div>
<script>
	var descriptionValue = '<?php echo esc_js( $description ); ?>';
</script>