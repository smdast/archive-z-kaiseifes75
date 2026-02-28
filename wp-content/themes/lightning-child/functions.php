<?php
/**
 * Lightning Child theme functions
 *
 * @package lightning
 */

/************************************************
 * 独自CSSファイルの読み込み処理
 *
 * 主に CSS を SASS で 書きたい人用です。 素の CSS を直接書くなら style.css に記載してかまいません.
 */

// 独自のCSSファイル（assets/css/）を読み込む場合は true に変更してください.
$my_lightning_additional_css = false;

if ( $my_lightning_additional_css ) {
	// 公開画面側のCSSの読み込み.
	add_action(
		'wp_enqueue_scripts',
		function() {
			wp_enqueue_style(
				'my-lightning-custom',
				get_stylesheet_directory_uri() . '/assets/css/style.css',
				array( 'lightning-design-style' ),
				filemtime( dirname( __FILE__ ) . '/assets/css/style.css' )
			);
		}
	);
	// 編集画面側のCSSの読み込み.
	add_action(
		'enqueue_block_editor_assets',
		function() {
			wp_enqueue_style(
				'my-lightning-editor-custom',
				get_stylesheet_directory_uri() . '/assets/css/editor.css',
				array( 'wp-edit-blocks', 'lightning-gutenberg-editor' ),
				filemtime( dirname( __FILE__ ) . '/assets/css/editor.css' )
			);
		}
	);
}

/************************************************
 * 独自の処理を必要に応じて書き足します
 */

//&がなぜか&#038;になる文字化けを修正
function my_replace_amp($content) {
	return str_replace('&#038;', '&', $content);
}
add_filter('the_content', 'my_replace_amp');




/**
 * Lightning Child theme functions
 *
 * @package lightning
 */

// Adobe Fonts：Futura Light（Kit ID: edn2oar）
function enqueue_adobe_futura() {
    wp_enqueue_style(
        'adobe-futura',
        'https://use.typekit.net/edn2oar.css',
        array(),
        null
    );
}
add_action( 'wp_enqueue_scripts', 'enqueue_adobe_futura' );

// Adobe Fonts：Zen Kaku Gothic New（Kit ID: gxo3lby）
function print_adobe_zen_kit() {
    ?>
    <script>
      (function(d) {
        var config = {
          kitId: 'gxo3lby',
          scriptTimeout: 3000,
          async: true
        },
        h=d.documentElement,
        t=setTimeout(function(){
          h.className = h.className.replace(/\bwf-loading\b/g,"")+" wf-inactive";
        }, config.scriptTimeout),
        tk = d.createElement("script"),
        f = false,
        s = d.getElementsByTagName("script")[0],
        a;
        h.className += " wf-loading";
        tk.src = 'https://use.typekit.net/' + config.kitId + '.js';
        tk.async = true;
        tk.onload = tk.onreadystatechange = function() {
          a = this.readyState;
          if (f || (a && a !== "complete" && a !== "loaded")) return;
          f = true;
          clearTimeout(t);
          try { Typekit.load(config); } catch(e) {}
        };
        s.parentNode.insertBefore(tk, s);
      })(document);
    </script>
    <?php
}
add_action( 'wp_head', 'print_adobe_zen_kit' );


function enqueue_wrap_text_js() {
    wp_enqueue_script('wrap-text', get_template_directory_uri() . '/js/wrap-text.js', [], null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_wrap_text_js');






/* カラーパレットの色追加 */
function aktk_add_my_editor_color_palette() {
    $palette = get_theme_support( 'editor-color-palette' );
    if ( ! empty( $palette ) ) {
        $palette = array_merge( $palette[0], array(
            array(
                'name'  => 'Color1',
                'slug'  => 'color1',
                'color' => '##34b5da',
            ),
            array(
                'name'  => 'Color2',
                'slug'  => 'color2',
                'color' => '#e88727',
            ),
            array(
                'name'  => 'Color3',
                'slug'  => 'color3',
                'color' => '#f2d729',
            ),
            array(
                'name'  => 'Color4',
                'slug'  => 'color4',
                'color' => '#91e079',
            ),
            array(
                'name'  => 'Color5',
                'slug'  => 'color5',
                'color' => '#3fc1c9',
            ),
            array(
                'name'  => 'Color6',
                'slug'  => 'color6',
                'color' => '#0000bb',
            ),
            array(
                'name'  => 'Color7',
                'slug'  => 'color7',
                'color' => '#ac5eb5',
            ),
        ) );
        add_theme_support( 'editor-color-palette', $palette );
    }
}

add_action( 'after_setup_theme', 
		   'aktk_add_my_editor_color_palette', 11 );








/* ウィジェット・トップページエリア上部 */
function my_lightning_widgets_init_home_top() {
	register_sidebar(
		array(
			'name'          => __( 'Home content top', 'lightning' ),
			'id'            => 'home-content-top-widget-area',
			'before_widget' => '<div class="widget %2$s" id="%1$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2>',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'my_lightning_widgets_init_home_top' );

function my_lightning_widgets_add_home_top() {
	if ( is_front_page() ) {
		if ( is_active_sidebar( 'home-content-top-widget-area' ) ) {
			dynamic_sidebar( 'home-content-top-widget-area' );
		}
	}
}
add_action( 'lightning_main_section_prepend', 'my_lightning_widgets_add_home_top' );



/*-------------------------------------------*/
/*  フッターのウィジェットエリアの数を増やす
/*  ※ Lightning Pro や G3 Pro Unit の Lカスタマイズ > ightning フッター設定 から指定できるカラム数を上書きするので注意してください。
/*-------------------------------------------*/
add_filter('lightning_footer_widget_area_count','lightning_footer_widget_area_count_custom',11);
function lightning_footer_widget_area_count_custom($footer_widget_area_count){
    $footer_widget_area_count = 2; // ← 1~4の半角数字で設定してください。
    return $footer_widget_area_count;
}







function custom_loading_screen() {
  if ( is_front_page() ) {
    ?>
    <div class="my_loading" id="my_loading">
      <img src="https://z-kaiseifes75.com/wp-content/uploads/2025/08/logo_gradation_whitebg.png" alt="Loading Logo" />
    </div>
    <script>
    (function(){
      const loading = document.getElementById('my_loading');
      const minDisplay = parseInt(getComputedStyle(document.documentElement).getPropertyValue('--min-display-time'), 10) || 3000;
      const fadeDuration = parseFloat(getComputedStyle(document.documentElement).getPropertyValue('--fade-duration')) * 1000 || 1500;

      window.addEventListener('load', () => {
        setTimeout(() => {
          loading.style.display = 'none';
        }, minDisplay + fadeDuration);
      });
    })();
    </script>
    <?php
  }
}
add_action('wp_body_open', 'custom_loading_screen');