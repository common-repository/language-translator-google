<?php
/*
Plugin Name: Google Language Translator
Plugin URI: https://wordpress.org/plugins/language-translator-google/
Version: 1.0
Description: The MOST SIMPLE Google Translator plugin.  This plugin adds Google Translator to your website by using a single shortcode, [google-translator]. Settings include: layout style, hide/show specific languages, hide/show Google toolbar, and hide/show Google branding. Add the shortcode to pages, posts, and widgets.
Author: Terence Williams
Author URI: http://www.terencewilliams.me/
License: GPL2+
*/

include( plugin_dir_path( __FILE__ ) . 'widget.php');

class language_translator_google {

  public $languages_array = array (
    'af' => 'Afrikaans',
    'sq' => 'Albanian',
    'ar' => 'Arabic',
    'hy' => 'Armenian',
    'az' => 'Azerbaijani',
    'eu' => 'Basque',
    'be' => 'Belarusian',
    'bn' => 'Bengali',
    'bs' => 'Bosnian',
    'bg' => 'Bulgarian',
    'ca' => 'Catalan',
    'ceb' => 'Cebuano',
    'ny' => 'Chichewa',
    'zh-CN' => 'Chinese',
    'zh-TW' => 'Chinese(Traditional)',
    'hr' => 'Croatian',
    'cs' => 'Czech',
    'da' => 'Danish',
    'nl' => 'Dutch',
    'en' => 'English',
    'eo' => 'Esperanto',
    'et' => 'Estonian',
    'tl' => 'Filipino',
    'fi' => 'Finnish',
    'fr' => 'French',
    'gl' => 'Galician',
    'ka' => 'Georgian',
    'de' => 'German',
    'el' => 'Greek',
    'gu' => 'Gujarati',
    'ht' => 'Haitian',
    'ha' => 'Hausa',
    'iw' => 'Hebrew',
    'hi' => 'Hindi',
    'hmn' => 'Hmong',
    'hu' => 'Hungarian',
    'is' => 'Icelandic',
    'ig' => 'Igbo',
    'id' => 'Indonesian',
    'ga' => 'Irish',
    'it' => 'Italian',
    'ja' => 'Japanese',
    'jw' => 'Javanese',
    'kn' => 'Kannada',
    'kk' => 'Kazakh',
    'km' => 'Khmer',
    'ko' => 'Korean',
    'lo' => 'Lao',
    'la' => 'Latin',
    'lv' => 'Latvian',
    'lt' => 'Lithuanian',
    'mk' => 'Macedonian',
    'mg' => 'Malagasy',
    'ms' => 'Malay',
    'ml' => 'Malayalam',
    'mt' => 'Maltese',
    'mi' => 'Maori',
    'mr' => 'Marathi',
    'mn' => 'Mongolian',
    'my' => 'Myanmar(Burmese)',
    'ne' => 'Nepali',
    'no' => 'Norwegian',
    'fa' => 'Persian',
    'pl' => 'Polish',
    'pt' => 'Portuguese',
    'pa' => 'Punjabi',
    'ro' => 'Romanian',
    'ru' => 'Russian',
    'sr' => 'Serbian',
    'st' => 'Sesotho',
    'si' => 'Sinhala',
    'sk' => 'Slovak',
    'sl' => 'Slovenian',
    'so' => 'Somali',
    'es' => 'Spanish',
    'su' => 'Sundanese',
    'sw' => 'Swahili',
    'sv' => 'Swedish',
    'tg' => 'Tajik',
    'ta' => 'Tamil',
    'te' => 'Telugu',
    'th' => 'Thai',
    'tr' => 'Turkish',
    'uk' => 'Ukranian',
    'ur' => 'Urdu',
    'uz' => 'Uzbek',
    'vi' => 'Vietnamese',
    'cy' => 'Welsh',
    'yi' => 'Yiddish',
    'yo' => 'Yoruba',
    'zu' => 'Zulu'
  );

  public function __construct() {
    register_activation_hook( __FILE__, array( &$this, 'glt_activate' ));
    register_uninstall_hook( __FILE__, 'glt_deactivate' );
    add_action( 'admin_menu', array( &$this, 'add_my_admin_menus'));
    add_action('admin_init',array(&$this, 'initialize_settings'));
    add_action('wp_head',array(&$this, 'load_css'));
    add_action('wp_footer',array(&$this, 'footer_script'));
    add_shortcode( 'google-translator',array(&$this, 'google_translator_shortcode'));
    add_shortcode( 'glt', array(&$this, 'google_translator_menu_language'));
    add_filter('widget_text','do_shortcode');
    add_filter('walker_nav_menu_start_el', array(&$this,'menu_shortcodes') , 10 , 2);
    add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array(&$this, 'glt_settings_link') );

    if (!is_admin()) {
      add_action('init',array(&$this, 'flags'));
    }
  }

  public function glt_activate() {
    add_option('googlelanguagetranslator_active', 1);
    add_option('googlelanguagetranslator_language','en');
    add_option('googlelanguagetranslator_language_option','all');
    add_option('googlelanguagetranslator_flags','show_flags');
    add_option('language_display_settings',array ('en' => 1));
    add_option('flag_display_settings',array ('flag-en' => 1));
    add_option('googlelanguagetranslator_translatebox','yes');
    add_option('googlelanguagetranslator_display','Vertical');
    add_option('googlelanguagetranslator_toolbar','Yes');
    add_option('googlelanguagetranslator_showbranding','Yes');
    add_option('googlelanguagetranslator_flags_alignment','flags_left');
    add_option('googlelanguagetranslator_analytics', 0);
    add_option('googlelanguagetranslator_analytics_id','');
    add_option('googlelanguagetranslator_css','');
    add_option('googlelanguagetranslator_multilanguage',0);
    add_option('googlelanguagetranslator_floating_widget','yes');
    add_option('googlelanguagetranslator_flag_size','18');
    add_option('googlelanguagetranslator_flags_order','');
    add_option('googlelanguagetranslator_english_flag_choice','');
    add_option('googlelanguagetranslator_spanish_flag_choice','');
    add_option('googlelanguagetranslator_portuguese_flag_choice','');
    delete_option('googlelanguagetranslator_manage_translations',0);
  }

  public function glt_deactivate() {
    delete_option('googlelanguagetranslator_active', 1);
    delete_option('googlelanguagetranslator_language','en');
    delete_option('googlelanguagetranslator_language_option','all');
    delete_option('googlelanguagetranslator_flags','show_flags');
    delete_option('language_display_settings',array ('en' => 1));
    delete_option('flag_display_settings',array ('flag-en' => 1));
    delete_option('googlelanguagetranslator_translatebox','yes');
    delete_option('googlelanguagetranslator_display','Vertical');
    delete_option('googlelanguagetranslator_toolbar','Yes');
    delete_option('googlelanguagetranslator_showbranding','Yes');
    delete_option('googlelanguagetranslator_flags_alignment','flags_left');
    delete_option('googlelanguagetranslator_analytics',1);
    delete_option('googlelanguagetranslator_analytics_id','');
    delete_option('googlelanguagetranslator_css','');
    delete_option('googlelanguagetranslator_multilanguage',0);
    delete_option('googlelanguagetranslator_floating_widget','yes');
    delete_option('googlelanguagetranslator_flag_size','18');
    delete_option('googlelanguagetranslator_flags_order','');
    delete_option('googlelanguagetranslator_english_flag_choice','');
    delete_option('googlelanguagetranslator_spanish_flag_choice','');
    delete_option('googlelanguagetranslator_portuguese_flag_choice','');
  }

  public function glt_settings_link ( $links ) {
    $settings_link = array(
      '<a href="' . admin_url( 'options-general.php?page=language_translator_google' ) . '">Settings</a>',
    );
   return array_merge( $links, $settings_link );
  }

  public function add_my_admin_menus(){
    $p = add_options_page('Google Language Translator', 'Google Language Translator', 'manage_options', 'language_translator_google', array(&$this, 'page_layout_cb'));

    add_action( 'load-' . $p, array(&$this, 'load_admin_js' ));
  }

  public function load_admin_js(){
    add_action( 'admin_enqueue_scripts', array(&$this, 'enqueue_admin_js' ));
    add_action('admin_footer',array(&$this, 'footer_script'));
  }

  public function enqueue_admin_js(){
    wp_enqueue_script( 'my-admin-script', plugins_url('js/admin.js',__FILE__), array('jquery'));
    wp_enqueue_script( 'my-flag-script', plugins_url('js/flags.js',__FILE__), array('jquery'));

    if (get_option ('googlelanguagetranslator_floating_widget') == 'yes') {
      wp_enqueue_script( 'my-toolbar-script', plugins_url('js/toolbar.js',__FILE__), array('jquery'));
      wp_enqueue_script( 'my-load-toolbar-script', plugins_url('js/load-toolbar.js',__FILE__), array('jquery'));
      wp_register_style( 'toolbar.css', plugins_url('css/toolbar.css', __FILE__) );
      wp_enqueue_style( 'toolbar.css' );
    }

    wp_enqueue_script( 'jquery-ui-core');
    wp_enqueue_script( 'jquery-ui-sortable');
    wp_enqueue_script( 'load_sortable_flags', plugins_url('js/load-sortable-flags.js',__FILE__), array('jquery'));
    wp_register_style( 'jquery-ui.css', plugins_url('css/jquery-ui.css',__FILE__) );
    wp_register_style( 'style.css', plugins_url('css/style.css', __FILE__) );
    wp_enqueue_style( 'style.css' );
    //wp_enqueue_style( 'jquery-ui.css' );
  }

  public function flags() {
    wp_enqueue_script( 'flags', plugins_url('js/flags.js',__FILE__), array('jquery'));

    if (get_option ('googlelanguagetranslator_floating_widget') == 'yes') {
      wp_enqueue_script( 'my-toolbar-script', plugins_url('js/toolbar.js',__FILE__), array('jquery'));
      wp_enqueue_script( 'my-load-toolbar-script', plugins_url('js/load-toolbar.js',__FILE__), array('jquery'));
      wp_register_style( 'toolbar.css', plugins_url('css/toolbar.css', __FILE__) );
      wp_enqueue_style( 'toolbar.css' );
    }

    wp_register_style( 'style.css', plugins_url('css/style.css', __FILE__) );
    wp_enqueue_style( 'style.css' );
  }

  public function load_css() {
    include( plugin_dir_path( __FILE__ ) . '/css/style.php');
  }

  public function google_translator_shortcode() {

    if (get_option('googlelanguagetranslator_display')=='Vertical' || get_option('googlelanguagetranslator_display')=='SIMPLE'){
        return $this->googlelanguagetranslator_vertical();
    }
    elseif(get_option('googlelanguagetranslator_display')=='Horizontal'){
        return $this->googlelanguagetranslator_horizontal();
    }
  }

  public function googlelanguagetranslator_included_languages() {
    if ( get_option('googlelanguagetranslator_language_option')=='specific') {
	  $get_language_choices = get_option ('language_display_settings');

	  foreach ($get_language_choices as $key=>$value) {
	    if($value == 1) {
		  $items[] = $key;
	    }
	  }

	  $comma_separated = implode(",",array_values($items));

	  if ( get_option('googlelanguagetranslator_display') == 'Vertical') {
	     $lang = ", includedLanguages:'".$comma_separated."'";
	       return $lang;
	  } elseif ( get_option('googlelanguagetranslator_display') == 'Horizontal') {
	     $lang = ", includedLanguages:'".$comma_separated."'";
	       return $lang;
      }
    }
  }

  public function analytics() {
    if ( get_option('googlelanguagetranslator_analytics') == 1 ) {
	  $analytics_id = get_option('googlelanguagetranslator_analytics_id');
	  $analytics = "gaTrack: true, gaId: '".$analytics_id."'";

          if (!empty ($analytics_id) ):
	    return ', '.$analytics;
          endif;
    }
  }

  public function menu_shortcodes( $item_output,$item ) {
    if ( !empty($item->description)) {
      $output = do_shortcode($item->description);

      if ( $output != $item->description )
        $item_output = $output;
      }
    return $item_output;
  }

  public function google_translator_menu_language($atts, $content = '') {
    extract(shortcode_atts(array(
      "language" => 'Spanish',
      "label" => 'Spanish'
    ), $atts));

    $default_language = get_option('googlelanguagetranslator_language');
    $english_flag_choice = get_option('googlelanguagetranslator_english_flag_choice');
    $language_code = array_search($language,$this->languages_array);
    $language_name = $language;
    $language_name_flag = $language_name;

    if ( $language_name == 'English' && $english_flag_choice == 'canadian_flag') {
      $language_name_flag = 'canada';
    }

    return "<a class='nturl notranslate ".$language_code."' single-language flag' title='".$language."'>".$label."</a>";
  }

  public function footer_script() {
    global $shortcode_started;
    $default_language = get_option('googlelanguagetranslator_language');
    $language_choices = $this->googlelanguagetranslator_included_languages();
    $new_languages_array_string = get_option('googlelanguagetranslator_flags_order');
    $new_languages_array = explode(",",$new_languages_array_string);
    $new_languages_array_codes = array_values($new_languages_array);
    $new_languages_array_count = count($new_languages_array);
    $english_flag_choice = get_option('googlelanguagetranslator_english_flag_choice');
    $spanish_flag_choice = get_option('googlelanguagetranslator_spanish_flag_choice');
    $portuguese_flag_choice = get_option('googlelanguagetranslator_portuguese_flag_choice');
    $get_language_option = get_option('googlelanguagetranslator_language_option');
    $get_flag_choices = get_option ('flag_display_settings');
    $get_flag_choices_count = count($get_flag_choices);
    $floating_widget = get_option ('googlelanguagetranslator_floating_widget');
    $is_active = get_option ( 'googlelanguagetranslator_active' );
    $is_multilanguage = get_option('googlelanguagetranslator_multilanguage');
    $str = ''; ?>
    <script>jQuery(document).ready(function(a){a("a.nturl").on("click",function(){function l(){doGoogleLanguageTranslator(default_lang+"|"+default_lang)}function n(){doGoogleLanguageTranslator(default_lang+"|"+lang_prefix)}default_lang="<?php echo get_option('googlelanguagetranslator_language'); ?>",lang_prefix=a(this).attr("class").split(" ")[2],lang_prefix==default_lang?l():n()}),a("a.flag").on("click",function(){function l(){doGoogleLanguageTranslator(default_lang+"|"+default_lang)}function n(){doGoogleLanguageTranslator(default_lang+"|"+lang_prefix)}default_lang="<?php echo get_option('googlelanguagetranslator_language'); ?>",lang_prefix=a(this).attr("class").split(" ")[2],a(".tool-container").hide(),lang_prefix==default_lang?l():n()}),0==a("body > #language_translator_google").length&&a("#glt-footer").html("<div id='language_translator_google'></div>")});</script>

    <?php

    if( $is_active == 1) {

      foreach ($get_flag_choices as $flag_choice_key) {}

      if ($floating_widget=='yes' && $get_language_option != 'specific') {
	$str.='<div id="glt-translate-trigger"><span class="notranslate">Translate &raquo;</span></div>';
        $str.='<div id="glt-toolbar"></div>';
      } //endif $floating_widget

      if ($shortcode_started != 'true') {
        $str.='<div id="flags" style="display:none">';
	$str.='<ul id="sortable" class="ui-sortable">';

	if ((empty($new_languages_array_string)) || ($new_languages_array_count != $get_flag_choices_count)) {
          foreach ($this->languages_array as $key=>$value) {
            $language_code = $key;
            $language_name = $value;
            $language_name_flag = $language_name;

            if ($flag_choice_key == '1') {
              if ( isset ( $get_flag_choices['flag-'.$language_code.''] ) ) {
	        if ( $language_name == 'English' && $english_flag_choice == 'canadian_flag') {
		  $language_name_flag = 'canada';
		}
		if ( $language_name == "English" && $english_flag_choice == 'us_flag') {
                  $language_name_flag = 'united-states';
		}
                if ( $language_name == 'Spanish' && $spanish_flag_choice == 'mexican_flag') {
		  $language_name_flag = 'mexico';
		}
                if ( $language_name == 'Portuguese' && $portuguese_flag_choice == 'brazilian_flag') {
	          $language_name_flag = 'brazil';
	        }
                if ( isset ( $get_flag_choices['flag-'.$language_code.''] ) ) {
		  $str.="<li id='".$language_name."'><a title='".$language_name."' class='notranslate flag ".$language_code." ".$language_name_flag."'></a></li>";
		}
              } //isset
            } //endif
          } //endforeach

	} else {
	    foreach ($new_languages_array_codes as $value) {
	      $language_name = $value;
	      $language_code = array_search ($language_name,$this->languages_array);
	      $language_name_flag = $language_name;

              if ($flag_choice_key == '1') {
		if (in_array($language_name,$this->languages_array)) {
                  if ( isset ( $get_flag_choices['flag-'.$language_code.''] ) ) {
	            if ( $language_name == 'English' && $english_flag_choice == 'canadian_flag') {
		      $language_name_flag = 'canada';
		    }
		    if ( $language_name == "English" && $english_flag_choice == 'us_flag') {
                      $language_name_flag = 'united-states';
		    }
                    if ( $language_name == 'Spanish' && $spanish_flag_choice == 'mexican_flag') {
		      $language_name_flag = 'mexico';
		    }
                    if ( $language_name == 'Portuguese' && $portuguese_flag_choice == 'brazilian_flag') {
	              $language_name_flag = 'brazil';
	            }
                    if ( isset ( $get_flag_choices['flag-'.$language_code.''] ) ) {
		      $str.="<li id='".$language_name."'><a title='".$language_name."' class='notranslate flag ".$language_code." ".$language_name_flag."'></a></li>";
		    }
                  } //isset
	        } //in_array
	      }//flag_choice_key
	    }//foreach
	  }//endif
      $str.='</ul>';
      $str.='</div>';
	  }
	}

    $language_choices = $this->googlelanguagetranslator_included_languages();
    $layout = get_option('googlelanguagetranslator_display');
    $is_multilanguage = get_option('googlelanguagetranslator_multilanguage');
    $horizontal_layout = ', layout: google.translate.TranslateElement.InlineLayout.HORIZONTAL';
	$simple_layout = ', layout: google.translate.TranslateElement.InlineLayout.SIMPLE';
    $auto_display = ', autoDisplay: false';
    $default_language = get_option('googlelanguagetranslator_language');

        if ($is_multilanguage == 1) {
          $multilanguagePage = ', multilanguagePage:true';
		  $str.="<div id='glt-footer'></div><script type='text/javascript'>function GoogleLanguageTranslatorInit() { new google.translate.TranslateElement({pageLanguage: '".$default_language."'".$language_choices . ($layout=='Horizontal' ? $horizontal_layout : ($layout=='SIMPLE' ? $simple_layout : '')) . $auto_display . $multilanguagePage . $this->analytics()."}, 'language_translator_google');}</script>";
          $str.="<script type='text/javascript' src='//translate.google.com/translate_a/element.js?cb=GoogleLanguageTranslatorInit'></script>";
        echo $str;

	} elseif ($is_multilanguage == 0) {
		  $str.="<div id='glt-footer'></div><script type='text/javascript'>function GoogleLanguageTranslatorInit() { new google.translate.TranslateElement({pageLanguage: '".$default_language."'".$language_choices . ($layout=='Horizontal' ? $horizontal_layout : ($layout=='SIMPLE' ? $simple_layout : '')) . $auto_display . $this->analytics()."}, 'language_translator_google');}</script>";
		  $str.="<script type='text/javascript' src='//translate.google.com/translate_a/element.js?cb=GoogleLanguageTranslatorInit'></script>";
	    echo $str;
	}
  }

  public function googlelanguagetranslator_vertical() {

	global $shortcode_started;

	$shortcode_started = 'true';
	$get_flag_choices = get_option ('flag_display_settings');
	$new_languages_array_string = get_option('googlelanguagetranslator_flags_order');
	$new_languages_array = explode(",",$new_languages_array_string);
	$new_languages_array_codes = array_values($new_languages_array);
	$new_languages_array_count = count($new_languages_array);
	$get_flag_choices_count = count($get_flag_choices);
	$get_language_choices = get_option ('language_display_settings');
	$flag_width = get_option('googlelanguagetranslator_flag_size');
	$default_language_code = get_option('googlelanguagetranslator_language');
        $english_flag_choice = get_option('googlelanguagetranslator_english_flag_choice');
        $spanish_flag_choice = get_option('googlelanguagetranslator_spanish_flag_choice');
        $portuguese_flag_choice = get_option('googlelanguagetranslator_portuguese_flag_choice');
	$is_active = get_option ( 'googlelanguagetranslator_active' );
	$get_language_option = get_option('googlelanguagetranslator_language_option');
	$language_choices = $this->googlelanguagetranslator_included_languages();
	$floating_widget = get_option ('googlelanguagetranslator_floating_widget');
	$str = '';

	if( $is_active == 1){

	  foreach ($get_flag_choices as $flag_choice_key) {}

	    $str.='<div id="flags" class="size'.$flag_width.'">';
	    $str.='<ul id="sortable" class="ui-sortable" style="float:left">';

	    if ((empty($new_languages_array_string)) || ($new_languages_array_count != $get_flag_choices_count)) {
	      foreach ($this->languages_array as $key=>$value) {
		$language_code = $key;
		$language_name = $value;
		$language_name_flag = $language_name;
	          if ($flag_choice_key == '1') {
                    if ( $language_name == 'English' && $english_flag_choice == 'canadian_flag') {
		      $language_name_flag = 'canada';
		    }
		    if ( $language_name == "English" && $english_flag_choice == 'us_flag') {
                      $language_name_flag = 'united-states';
		    }
                    if ( $language_name == 'Spanish' && $spanish_flag_choice == 'mexican_flag') {
		      $language_name_flag = 'mexico';
		    }
                    if ( $language_name == 'Portuguese' && $portuguese_flag_choice == 'brazilian_flag') {
	              $language_name_flag = 'brazil';
	            }
                    if ( isset ( $get_flag_choices['flag-'.$language_code.''] ) ) {
	              $str.="<li id='".$language_name."'><a title='".$language_name."' class='notranslate flag ".$language_code." ".$language_name_flag."'></a></li>";
		    }
	          } //$key
	      }//foreach
	    } else {
	      foreach ($new_languages_array_codes as $value) {
	        $language_name = $value;
		$language_code = array_search ($language_name,$this->languages_array);
		$language_name_flag = $language_name;
		  if ($flag_choice_key == '1') {
		    if (in_array($language_name,$this->languages_array)) {
                      if ( $language_name == 'English' && $english_flag_choice == 'canadian_flag') {
		        $language_name_flag = 'canada';
		      }
		      if ( $language_name == "English" && $english_flag_choice == 'us_flag') {
                        $language_name_flag = 'united-states';
		      }
                      if ( $language_name == 'Spanish' && $spanish_flag_choice == 'mexican_flag') {
		        $language_name_flag = 'mexico';
		      }
                      if ( $language_name == 'Portuguese' && $portuguese_flag_choice == 'brazilian_flag') {
	                $language_name_flag = 'brazil';
	              }
                      if ( isset ( $get_flag_choices['flag-'.$language_code.''] ) ) {
	                $str.="<li id='".$language_name."'><a title='".$language_name."' class='notranslate flag ".$language_code." ".$language_name_flag."'></a></li>";
		      } //isset
	            } //in_array
		  }//flag_choice_key
	      }//foreach
	    }//endif
      $str.='</ul>';
      $str.='</div>';
      $str.='<div id="language_translator_google"></div>';
        return $str;
      } //End is_active
  } // End glt_vertical

  public function googlelanguagetranslator_horizontal(){
    $shortcode_started = true;
    $get_flag_choices = get_option ('flag_display_settings');
    $new_languages_array_string = get_option('googlelanguagetranslator_flags_order');
    $new_languages_array = explode(",",$new_languages_array_string);
    $new_languages_array_codes = array_values($new_languages_array);
    $new_languages_array_count = count($new_languages_array);
    $get_flag_choices_count = count($get_flag_choices);
    $get_language_choices = get_option ('language_display_settings');
    $flag_width = get_option('googlelanguagetranslator_flag_size');
    $default_language_code = get_option('googlelanguagetranslator_language');
    $english_flag_choice = get_option('googlelanguagetranslator_english_flag_choice');
    $spanish_flag_choice = get_option('googlelanguagetranslator_spanish_flag_choice');
    $portuguese_flag_choice = get_option('googlelanguagetranslator_portuguese_flag_choice');
    $is_active = get_option ( 'googlelanguagetranslator_active' );
    $get_language_option = get_option('googlelanguagetranslator_language_option');
    $language_choices = $this->googlelanguagetranslator_included_languages();
    $floating_widget = get_option ('googlelanguagetranslator_floating_widget');
    $str = '';

    if( $is_active == 1) {
      foreach ($get_flag_choices as $flag_choice_key) {}
        $str.='<div id="flags" class="size'.$flag_width.'">';
	$str.='<ul id="sortable" class="ui-sortable" style="float:left">';

	if ((empty($new_languages_array_string)) || ($new_languages_array_count != $get_flag_choices_count)) {
	  foreach ($this->languages_array as $key=>$value) {
            $language_code = $key;
	    $language_name = $value;
            $language_name_flag = $language_name;

	    if ($flag_choice_key == '1') {
              if ( $language_name == 'English' && $english_flag_choice == 'canadian_flag') {
	        $language_name_flag = 'canada';
	      }
	      if ( $language_name == "English" && $english_flag_choice == 'us_flag') {
                $language_name_flag = 'united-states';
	      }
              if ( $language_name == 'Spanish' && $spanish_flag_choice == 'mexican_flag') {
		$language_name_flag = 'mexico';
              }
              if ( $language_name == 'Portuguese' && $portuguese_flag_choice == 'brazilian_flag') {
	        $language_name_flag = 'brazil';
	      }
              if ( isset ( $get_flag_choices['flag-'.$language_code.''] ) ) {
		$str.="<li id='".$language_name."'><a title='".$language_name."' class='notranslate flag ".$language_code." ".$language_name_flag."'></a></li>";
	      } //endif
            } //$key
	  } //foreach
	} else {
	  foreach ($new_languages_array_codes as $value) {
            $language_name = $value;
	    $language_code = array_search ($language_name,$this->languages_array);
	    $language_name_flag = $language_name;
	      if ($flag_choice_key == '1') {
                if (in_array($language_name,$this->languages_array)) {
                  if ( $language_name == 'English' && $english_flag_choice == 'canadian_flag') {
		    $language_name_flag = 'canada';
		  }
		  if ( $language_name == "English" && $english_flag_choice == 'us_flag') {
                    $language_name_flag = 'united-states';
		  }
                  if ( $language_name == 'Spanish' && $spanish_flag_choice == 'mexican_flag') {
		    $language_name_flag = 'mexico';
                  }
                  if ( $language_name == 'Portuguese' && $portuguese_flag_choice == 'brazilian_flag') {
	            $language_name_flag = 'brazil';
	          }
                  if ( isset ( $get_flag_choices['flag-'.$language_code.''] ) ) {
	            $str.="<li id='".$language_name."'><a title='".$language_name."' class='notranslate flag ".$language_code." ".$language_name_flag."'></a></li>";
		  } //isset
		} //in_array
              } //endif
	  }//foreach
	}//endif
      $str.='</ul>';
      $str.='</div>';
      $str.='<div id="language_translator_google"></div>';
        return $str;
    }
  } // End glt_horizontal

  public function initialize_settings() {
    add_settings_section('glt_settings','Settings','','language_translator_google');

    $settings_name_array = array (
'googlelanguagetranslator_active','googlelanguagetranslator_language','googlelanguagetranslator_language_option','language_display_settings','googlelanguagetranslator_flags','flag_display_settings','googlelanguagetranslator_translatebox','googlelanguagetranslator_display','googlelanguagetranslator_toolbar','googlelanguagetranslator_showbranding','googlelanguagetranslator_flags_alignment','googlelanguagetranslator_analytics','googlelanguagetranslator_analytics_id','googlelanguagetranslator_css','googlelanguagetranslator_multilanguage','googlelanguagetranslator_floating_widget','googlelanguagetranslator_flag_size','googlelanguagetranslator_flags_order','googlelanguagetranslator_english_flag_choice','googlelanguagetranslator_spanish_flag_choice','googlelanguagetranslator_portuguese_flag_choice'
    );

    $settings_callback_array = array (  'googlelanguagetranslator_active_cb','googlelanguagetranslator_language_cb','googlelanguagetranslator_language_option_cb','language_display_settings_cb','googlelanguagetranslator_flags_cb','flag_display_settings_cb','googlelanguagetranslator_translatebox_cb','googlelanguagetranslator_display_cb','googlelanguagetranslator_toolbar_cb','googlelanguagetranslator_showbranding_cb','googlelanguagetranslator_flags_alignment_cb','googlelanguagetranslator_analytics_cb','googlelanguagetranslator_analytics_id_cb','googlelanguagetranslator_css_cb','googlelanguagetranslator_multilanguage_cb','googlelanguagetranslator_floating_widget_cb','googlelanguagetranslator_flag_size_cb','googlelanguagetranslator_flags_order_cb','googlelanguagetranslator_english_flag_choice_cb','googlelanguagetranslator_spanish_flag_choice_cb','googlelanguagetranslator_portuguese_flag_choice_cb'
    );

    foreach ($settings_name_array as $setting) {
      add_settings_field( $setting,'',$setting.'_cb','language_translator_google','glt_settings');
      register_setting( 'language_translator_google',$setting);
    }
  }

  public function googlelanguagetranslator_active_cb() {
    $option_name = 'googlelanguagetranslator_active' ;
    $new_value = 1;
      if ( get_option( $option_name ) === false ) {

      // The option does not exist, so we update it.
      update_option( $option_name, $new_value );
	  }

	  $options = get_option (''.$option_name.'');

	  $html = '<input type="checkbox" name="googlelanguagetranslator_active" id="googlelanguagetranslator_active" value="1" '.checked(1,$options,false).'/> &nbsp; Check this box to activate';
	  echo $html;
	}

  public function googlelanguagetranslator_language_cb() {

	$option_name = 'googlelanguagetranslator_language';
    $new_value = 'en';

      if ( get_option( $option_name ) === false ) {

      // The option does not exist, so we update it.
      update_option( $option_name, $new_value );
	  }

	  $options = get_option (''.$option_name.''); ?>

      <select name="googlelanguagetranslator_language" id="googlelanguagetranslator_language">

	  <?php

        foreach ($this->languages_array as $key => $value) {
		  $language_code = $key;
		  $language_name = $value; ?>
		    <option value="<?php echo $language_code; ?>" <?php if($options==''.$language_code.''){echo "selected";}?>><?php echo $language_name; ?></option>
	      <?php } ?>
      </select>
    <?php
    }

    public function googlelanguagetranslator_language_option_cb() {

	$option_name = 'googlelanguagetranslator_language_option' ;
    $new_value = 'all';

      if ( get_option( $option_name ) === false ) {

      // The option does not exist, so we update it.
      update_option( $option_name, $new_value );
	  }

	  $options = get_option (''.$option_name.''); ?>

    <input type="radio" name="googlelanguagetranslator_language_option" id="googlelanguagetranslator_language_option" value="all" <?php if($options=='all'){echo "checked";}?>/> All Languages<br/>
	<input type="radio" name="googlelanguagetranslator_language_option" id="googlelanguagetranslator_language_option" value="specific" <?php if($options=='specific'){echo "checked";}?>/> Specific Languages
    <?php
	}

    public function language_display_settings_cb() {
	  $default_language_code = get_option('googlelanguagetranslator_language');
	  $option_name = 'language_display_settings';
      $new_value = array(''.$default_language_code.'' => 1);

	  if ( get_option( $option_name ) == false ) {
        // The option does not exist, so we update it.
        update_option( $option_name, $new_value );
	  }

	  $get_language_choices = get_option (''.$option_name.'');

	  foreach ($this->languages_array as $key => $value) {
		$language_code = $key;
		$language_name = $value;
		$language_code_array[] = $key;

		if (!isset($get_language_choices[''.$language_code.''])) {
		  $get_language_choices[''.$language_code.''] = 0;
		}

		$items[] = $get_language_choices[''.$language_code.''];
		$language_codes = $language_code_array;
		$item_count = count($items);

		if ($item_count == 1 || $item_count == 24 || $item_count == 47 || $item_count == 70) { ?>
          <div class="languages" style="width:25%; float:left">
	    <?php } ?>
		  <div><input type="checkbox" name="language_display_settings[<?php echo $language_code; ?>]" value="1"<?php checked( 1,$get_language_choices[''.$language_code.'']); ?>/><?php echo $language_name; ?></div>
        <?php
		if ($item_count == 23 || $item_count == 46 || $item_count == 69 || $item_count == 91) { ?>
          </div>
        <?php }
	  } ?>
     <div class="clear"></div>
    <?php
	}

    public function googlelanguagetranslator_flags_cb() {

	  $option_name = 'googlelanguagetranslator_flags' ;
      $new_value = 'show_flags';

      if ( get_option( $option_name ) === false ) {

      // The option does not exist, so we update it.
      update_option( $option_name, $new_value );
	  }

	  $options = get_option (''.$option_name.''); ?>

      <input type="radio" name="googlelanguagetranslator_flags" id="googlelanguagetranslator_flags" value="show_flags" <?php if($options=='show_flags'){echo "checked";}?>/> Yes, show flag images<br/>
	  <input type="radio" name="googlelanguagetranslator_flags" id="googlelanguagetranslator_flags" value="hide_flags" <?php if($options=='hide_flags'){echo "checked";}?>/> No, hide flag images
    <?php
	}

    public function flag_display_settings_cb() {
	  $default_language_code = get_option('googlelanguagetranslator_language');
	  $option_name = 'flag_display_settings';
      $new_value = array('flag-'.$default_language_code.'' => 1);

	  if ( get_option( $option_name ) == false ) {
        // The option does not exist, so we update it.
        update_option( $option_name, $new_value );
	  }

	  $get_flag_choices = get_option (''.$option_name.'');

	  foreach ($this->languages_array as $key => $value) {
		$language_code = $key;
		$language_name = $value;
		$language_code_array[] = $key;

		if (!isset($get_flag_choices['flag-'.$language_code.''])) {
		  $get_flag_choices['flag-'.$language_code.''] = 0;
		}

		$items[] = $get_flag_choices['flag-'.$language_code.''];
		$language_codes = $language_code_array;
		$item_count = count($items);

		if ($item_count == 1 || $item_count == 24 || $item_count == 47 || $item_count == 70) { ?>
          <div class="flagdisplay" style="width:25%; float:left">
	    <?php } ?>
		  <div><input type="checkbox" name="flag_display_settings[flag-<?php echo $language_code; ?>]" value="1"<?php checked( 1,$get_flag_choices['flag-'.$language_code.'']); ?>/><?php echo $language_name; ?></div>
        <?php
		if ($item_count == 23 || $item_count == 46 || $item_count == 69 || $item_count == 91) { ?>
          </div>
        <?php }
	  } ?>
     <div class="clear"></div>
    <?php
	}

    public function googlelanguagetranslator_floating_widget_cb() {

	$option_name = 'googlelanguagetranslator_floating_widget' ;
    $new_value = 'yes';

      if ( get_option( $option_name ) === false ) {

      // The option does not exist, so we update it.
      update_option( $option_name, $new_value );
	  }

	  $options = get_option (''.$option_name.''); ?>

          <select name="googlelanguagetranslator_floating_widget" id="googlelanguagetranslator_floating_widget" style="width:170px">
		      <option value="yes" <?php if($options=='yes'){echo "selected";}?>>Yes, show widget</option>
			  <option value="no" <?php if($options=='no'){echo "selected";}?>>No, hide widget</option>
		  </select>
  <?php }

  public function googlelanguagetranslator_translatebox_cb() {

    $option_name = 'googlelanguagetranslator_translatebox' ;
    $new_value = 'yes';

      if ( get_option( $option_name ) === false ) {

      // The option does not exist, so we update it.
      update_option( $option_name, $new_value );
	  }

	  $options = get_option (''.$option_name.''); ?>

          <select name="googlelanguagetranslator_translatebox" id="googlelanguagetranslator_translatebox" style="width:190px">
		      <option value="yes" <?php if($options=='yes'){echo "selected";}?>>Yes, show language box</option>
			  <option value="no" <?php if($options=='no'){echo "selected";}?>>No, hide language box</option>
		  </select>
  <?php }

  public function googlelanguagetranslator_display_cb() {

	$option_name = 'googlelanguagetranslator_display' ;
    $new_value = 'Vertical';

      if ( get_option( $option_name ) === false ) {

      // The option does not exist, so we update it.
      update_option( $option_name, $new_value );
	  }

	  $options = get_option (''.$option_name.''); ?>

          <select name="googlelanguagetranslator_display" id="googlelanguagetranslator_display" style="width:170px;">
             <option value="Vertical" <?php if(get_option('googlelanguagetranslator_display')=='Vertical'){echo "selected";}?>>Vertical</option>
             <option value="Horizontal" <?php if(get_option('googlelanguagetranslator_display')=='Horizontal'){echo "selected";}?>>Horizontal</option>
			 <option value="SIMPLE" <?php if (get_option('googlelanguagetranslator_display')=='SIMPLE'){echo "selected";}?>>Popup Style</option>
          </select>
  <?php }

  public function googlelanguagetranslator_toolbar_cb() {

	$option_name = 'googlelanguagetranslator_toolbar' ;
    $new_value = 'Yes';

      if ( get_option( $option_name ) === false ) {

      // The option does not exist, so we update it.
      update_option( $option_name, $new_value );
	  }

	  $options = get_option (''.$option_name.''); ?>

          <select name="googlelanguagetranslator_toolbar" id="googlelanguagetranslator_toolbar" style="width:170px;">
             <option value="Yes" <?php if(get_option('googlelanguagetranslator_toolbar')=='Yes'){echo "selected";}?>>Yes</option>
             <option value="No" <?php if(get_option('googlelanguagetranslator_toolbar')=='No'){echo "selected";}?>>No</option>
          </select>
  <?php }

  public function googlelanguagetranslator_showbranding_cb() {

	$option_name = 'googlelanguagetranslator_showbranding' ;
    $new_value = 'Yes';

      if ( get_option( $option_name ) === false ) {

      // The option does not exist, so we update it.
      update_option( $option_name, $new_value );
	  }

	  $options = get_option (''.$option_name.''); ?>

          <select name="googlelanguagetranslator_showbranding" id="googlelanguagetranslator_showbranding" style="width:170px;">
             <option value="Yes" <?php if(get_option('googlelanguagetranslator_showbranding')=='Yes'){echo "selected";}?>>Yes</option>
             <option value="No" <?php if(get_option('googlelanguagetranslator_showbranding')=='No'){echo "selected";}?>>No</option>
          </select>
  <?php }

  public function googlelanguagetranslator_flags_alignment_cb() {

	$option_name = 'googlelanguagetranslator_flags_alignment' ;
    $new_value = 'flags_left';

      if ( get_option( $option_name ) === false ) {

      // The option does not exist, so we update it.
      update_option( $option_name, 'flags_left' );
	  }

	  $options = get_option (''.$option_name.''); ?>

      <input type="radio" name="googlelanguagetranslator_flags_alignment" id="flags_left" value="flags_left" <?php if($options=='flags_left'){echo "checked";}?>/> Align Left<br/>
      <input type="radio" name="googlelanguagetranslator_flags_alignment" id="flags_right" value="flags_right" <?php if($options=='flags_right'){echo "checked";}?>/> Align Right
  <?php }

  public function googlelanguagetranslator_analytics_cb() {

	$option_name = 'googlelanguagetranslator_analytics' ;
    $new_value = 0;

      if ( get_option( $option_name ) === false ) {

      // The option does not exist, so we update it.
      update_option( $option_name, $new_value );
	  }

	  $options = get_option (''.$option_name.'');

    $html = '<input type="checkbox" name="googlelanguagetranslator_analytics" id="googlelanguagetranslator_analytics" value="1" '.checked(1,$options,false).'/> &nbsp; Activate Google Analytics tracking?';
    echo $html;
  }

  public function googlelanguagetranslator_analytics_id_cb() {

	$option_name = 'googlelanguagetranslator_analytics_id' ;
    $new_value = '';

      if ( get_option( $option_name ) === false ) {

      // The option does not exist, so we update it.
      update_option( $option_name, $new_value );
	  }

	  $options = get_option (''.$option_name.'');

    $html = '<input type="text" name="googlelanguagetranslator_analytics_id" id="googlelanguagetranslator_analytics_id" value="'.$options.'" />';
    echo $html;
  }

  public function googlelanguagetranslator_flag_size_cb() {

	$option_name = 'googlelanguagetranslator_flag_size' ;
    $new_value = '18';

      if ( get_option( $option_name ) === false ) {

      // The option does not exist, so we update it.
      update_option( $option_name, $new_value );
	  }

	  $options = get_option (''.$option_name.''); ?>

          <select name="googlelanguagetranslator_flag_size" id="googlelanguagetranslator_flag_size" style="width:110px;">
             <option value="16" <?php if($options=='16'){echo "selected";}?>>16px</option>
			 <option value="18" <?php if($options=='18'){echo "selected";}?>>18px</option>
             <option value="20" <?php if($options=='20'){echo "selected";}?>>20px</option>
			 <option value="22" <?php if($options=='22'){echo "selected";}?>>22px</option>
             <option value="24" <?php if($options=='24'){echo "selected";}?>>24px</option>
          </select>
  <?php }

  public function googlelanguagetranslator_flags_order_cb() {
	$option_name = 'googlelanguagetranslator_flags_order';
	$new_value = '';

	if ( get_option ( $option_name ) === false ) {

	  // The option does not exist, so we update it.
	  update_option( $option_name, $new_value );
	}

	$options = get_option ( ''.$option_name.'' ); ?>

    <input type="hidden" id="order" name="googlelanguagetranslator_flags_order" value="<?php print_r(get_option('googlelanguagetranslator_flags_order')); ?>" />
   <?php
  }

  public function googlelanguagetranslator_english_flag_choice_cb() {
	$option_name = 'googlelanguagetranslator_english_flag_choice';
	$new_value = 'us_flag';

	if ( get_option ( $option_name ) === false ) {

	  // The option does not exist, so we update it.
	  update_option( $option_name, $new_value );
	}

	$options = get_option ( ''.$option_name.'' ); ?>

    <select name="googlelanguagetranslator_english_flag_choice" id="googlelanguagetranslator_english_flag_choice">
      <option value="us_flag" <?php if($options=='us_flag'){echo "selected";}?>>U.S. Flag</option>
	  <option value="uk_flag" <?php if ($options=='uk_flag'){echo "selected";}?>>U.K Flag</option>
	  <option value="canadian_flag" <?php if ($options=='canadian_flag'){echo "selected";}?>>Canadian Flag</option>
    </select>
   <?php
  }

  public function googlelanguagetranslator_spanish_flag_choice_cb() {
	$option_name = 'googlelanguagetranslator_spanish_flag_choice';
	$new_value = 'spanish_flag';

	if ( get_option ( $option_name ) === false ) {

	  // The option does not exist, so we update it.
	  update_option( $option_name, $new_value );
	}

	$options = get_option ( ''.$option_name.'' ); ?>

    <select name="googlelanguagetranslator_spanish_flag_choice" id="googlelanguagetranslator_spanish_flag_choice">
      <option value="spanish_flag" <?php if($options=='spanish_flag'){echo "selected";}?>>Spanish Flag</option>
	  <option value="mexican_flag" <?php if ($options=='mexican_flag'){echo "selected";}?>>Mexican Flag</option>
    </select>
   <?php
  }

  public function googlelanguagetranslator_portuguese_flag_choice_cb() {
	$option_name = 'googlelanguagetranslator_portuguese_flag_choice';
	$new_value = 'portuguese_flag';

	if ( get_option ( $option_name ) === false ) {

	  // The option does not exist, so we update it.
	  update_option( $option_name, $new_value );
	}

	$options = get_option ( ''.$option_name.'' ); ?>

    <select name="googlelanguagetranslator_portuguese_flag_choice" id="googlelanguagetranslator_spanish_flag_choice">
      <option value="portuguese_flag" <?php if($options=='portuguese_flag'){echo "selected";}?>>Portuguese Flag</option>
	  <option value="brazilian_flag" <?php if ($options=='brazilian_flag'){echo "selected";}?>>Brazilian Flag</option>
    </select>
   <?php
  }

  public function googlelanguagetranslator_css_cb() {

    $option_name = 'googlelanguagetranslator_css' ;
    $new_value = '';

      if ( get_option( $option_name ) === false ) {

      // The option does not exist, so we update it.
      update_option( $option_name, $new_value );
	  }

	  $options = get_option (''.$option_name.'');

	  $html = '<textarea style="width:100%; height:200px" name="googlelanguagetranslator_css" id="googlelanguagetranslator_css">'.$options.'</textarea>';
    echo $html;
  }

  public function googlelanguagetranslator_multilanguage_cb() {

	$option_name = 'googlelanguagetranslator_multilanguage' ;
    $new_value = 0;

      if ( get_option( $option_name ) === false ) {

      // The option does not exist, so we update it.
      update_option( $option_name, $new_value );
	  }

	  $options = get_option (''.$option_name.'');

      $html = '<input type="checkbox" name="googlelanguagetranslator_multilanguage" id="googlelanguagetranslator_multilanguage" value="1" '.checked(1,$options,false).'/> &nbsp; Turn on multilanguage mode?';
      echo $html;
  }

  public function googlelanguagetranslator_exclude_translation_cb() {

	$option_name = 'googlelanguagetranslator_exclude_translation';
	$new_value = '';

	if (get_option($option_name) === false ) {
	  // The option does not exist, so we update it.
	  update_option( $option_name, $new_value );
	}

	$options = get_option (''.$option_name.'');

	$html = '<input type="text" name="'.$option_name.'" id="'.$option_name.'" value="'.$options.'" />';

	echo $html;
  }

  public function page_layout_cb() {
    include( plugin_dir_path( __FILE__ ) . '/css/style.php'); ?>
        <?php add_thickbox(); ?>
        <div class="wrap">
	      <div id="icon-options-general" class="icon32"></div>
	        <h2><span class="notranslate">Google Language Translator</span></h2>
		      <form action="<?php echo admin_url( '/options.php'); ?>" method="post">
	          <div class="metabox-holder has-right-sidebar" style="float:left; width:65%">
                <div class="postbox" style="width: 100%">
                  <h3 class="notranslate">Settings</h3>

			      <?php settings_fields('language_translator_google'); ?>
                    <table style="border-collapse:separate" width="100%" border="0" cellspacing="8" cellpadding="0" class="form-table">
                      <tr>
						<td style="width:60%" class="notranslate">Plugin Status:</td>
				        <td class="notranslate"><?php $this->googlelanguagetranslator_active_cb(); ?></td>
                      </tr>

					  <tr class="notranslate">
				        <td>Choose the original language of your website</td>
						<td><?php $this->googlelanguagetranslator_language_cb(); ?></td>
					  </tr>

					  <tr class="notranslate">
						<td>What translation languages will display in the language box?<br/>("All Languages" option <strong><u>must</u></strong> be chosen to show flags.)</td>
						<td><?php $this->googlelanguagetranslator_language_option_cb(); ?></td>
					  </tr>

					  <tr class="notranslate languages">
						<td colspan="2"><?php $this->language_display_settings_cb(); ?></td>
					  </tr>

					  <tr class="notranslate">
				        <td class="choose_flags_intro">Show flag images?<br/>(Display up to 91 flags above the translator)</td>
						<td class="choose_flags_intro"><?php $this->googlelanguagetranslator_flags_cb(); ?></td>
					  </tr>

					  <tr class="notranslate choose_flags">
				        <td class="choose_flags">Choose the flags you want to display:</td>
				        <td></td>
			          </tr>

					  <tr class="notranslate">
						<td colspan="2" class="choose_flags"><?php $this->flag_display_settings_cb(); ?></td>
					  </tr>

					  <tr class="notranslate">
						<td>Show floating translation widget?<br/>
						  <span>("All Languages" option <strong><u>must</u></strong> be chosen to show widget.)</span>
						</td>
						<td><?php $this->googlelanguagetranslator_floating_widget_cb(); ?></td>
					  </tr>

					  <tr class="notranslate">
				        <td>Show translate box?</td>
						<td><?php $this->googlelanguagetranslator_translatebox_cb(); ?></td>
					  </tr>

					  <tr class="notranslate">
						<td>Layout option: <span style="color:red; font-weight:bold">NEW!</span> (Popup layout just added!)</td>
						<td><?php $this->googlelanguagetranslator_display_cb(); ?></td>
					  </tr>

					  <tr class="notranslate">
                        <td>Show Google Toolbar?</td>
						<td><?php $this->googlelanguagetranslator_toolbar_cb(); ?></td>
					  </tr>

					  <tr class="notranslate">
				        <td>Show Google Branding? &nbsp;<a href="https://developers.google.com/translate/v2/attribution" target="_blank">Learn more</a></td>
						<td><?php $this->googlelanguagetranslator_showbranding_cb(); ?></td>
					  </tr>

					  <tr class="alignment notranslate">
				        <td class="flagdisplay">Align the translator left or right?</td>
						<td class="flagdisplay"><?php $this->googlelanguagetranslator_flags_alignment_cb(); ?></td>
					  </tr>



                      <tr class="multilanguage notranslate">
						<td>Multilanguage Page option? &nbsp;<a href="#TB_inline?width=200&height=150&inlineId=multilanguage-page-description" title="What is the Multi-Language Page Option?" class="thickbox">Learn more</a><div id="multilanguage-page-description" style="display:none"><p>If you activate this setting, Google will translate all text into a single language when requested by your user, even if text is written in multiple languages. In most cases, this setting is not recommended, although for certain websites it might be necessary.</p></div></td>
						<td><?php $this->googlelanguagetranslator_multilanguage_cb(); ?></td>
					  </tr>

					  <tr class="notranslate">
						<td>Google Analytics:</td>
						<td><?php $this->googlelanguagetranslator_analytics_cb(); ?></td>
					  </tr>

					  <tr class="analytics notranslate">
						<td>Google Analytics ID (Ex. 'UA-11117410-2')</td>
						<td><?php $this->googlelanguagetranslator_analytics_id_cb(); ?></td>
					  </tr>

					  <tr class="notranslate">
						<td>Full widget usage in pages/posts/sidebar:</td>
						<td><code>[google-translator]</code></td>
                                          </tr>
				  </table>

				  <table style="border-collapse:separate" width="100%" border="0" cellspacing="8" cellpadding="0" class="form-table">
					  <tr class="notranslate">
						<td style="width:40%">Full widget usage in header/footer or page template:</td>
						<td style="width:60%"><code>&lt;?php echo do_shortcode('[google-translator]'); ?&gt;</code></td>
					  </tr>

                      <tr class="notranslate">
						<td>Single language usage in<br/>nav menu/pages/posts</td>
						<td><code>[glt language="Spanish" label="Español"]</code></td>
                      </tr>

                      <tr class="notranslate">
						<td colspan="2">
						  <a href="#TB_inline?width=200&height=450&inlineId=single-language-shortcode-description" title="How to place a single language in your Wordpress menu" class="thickbox">How to place a single language in your Wordpress menu</a>
						  <div id="single-language-shortcode-description" style="display:none">
							<p>For menu usage, you need to create a new menu, or use an existing menu, by navigating to "Appearance > Menus".</p>
							<p>First you will need to enable "descriptions" for your menu items, which can be found in a tab labeled "Screen Options" in the upper-right area of the page.</p>
							<p>Once descriptions are enabled, follow these steps:<br/>
							  <ol>
								<li>Create a new menu item using "Link" as the menu item type.</li>
								<li>Use <code style="border:none">#</code> for the URL</li>
								<li>Enter a navigation label of your choice. This label does not appear on your website - it is meant only to help you identify the menu item.</li>
								<li>Place the following shortcode into the "description" field, and modify it to display the language and navigation label of your choice:</li>
						      </ol>
							<p><code>[glt language="Spanish" label="Español"]</code></p>
                          </div>
						</td>
                      </tr>

					  <tr class="notranslate">
						<td>
						  <?php
	                                            if (isset($_POST['submit'])) {
	                                              if (empty($_POST['submit']) && !check_admin_referer( 'glt-save-settings', 'glt-save-settings-nonce' )) {
	                                                wp_die();
	                                              } else {  }
	                                            }
	                                            wp_nonce_field('glt-save-settings, glt-save-settings-nonce', false);
                                                    submit_button();
						  ?>
						</td>
						<td></td>
					  </tr>
			      </table>
		    </div> <!-- .postbox -->
		  </div> <!-- .metbox-holder -->

		  <div class="metabox-holder" style="float:right; clear:right; width:33%">
		    <div class="postbox">
		      <h3 class="notranslate">Preview</h3>
			    <table style="width:100%">
		          <tr>
					<td style="box-sizing:border-box; -webkit-box-sizing:border-box; -moz-box-sizing:border-box; padding:15px 15px; margin:0px"><span class="notranslate"> Drag &amp; drop flags to change their position.<br/><br/>(Note: flag order resets when flags are added/removed)</span><br/><br/><?php echo do_shortcode('[google-translator]'); ?><p class="hello"><span class="notranslate">Translated text:</span> &nbsp; <span>Hello</span></p></td>
				  </tr>

				  <tr>
					<td></td>
				  </tr>


		        </table>
		    </div> <!-- .postbox -->
	      </div> <!-- .metabox-holder -->

<div id="glt_advanced_settings" class="metabox-holder notranslate" style="float: right; width: 33%;">
  <div class="postbox">
    <h3>Advanced Settings</h3>
    <div class="inside">
      <table style="border-collapse:separate" width="100%" border="0" cellspacing="8" cellpadding="0" class="form-table">
        <tr class="notranslate">
	  <td class="advanced">Select flag size:</td>
	  <td class="advanced"><?php $this->googlelanguagetranslator_flag_size_cb(); ?></td>
        </tr>

        <tr class="notranslate">
	  <td class="advanced">Flag for English:</td>
	  <td class="advanced"><?php $this->googlelanguagetranslator_english_flag_choice_cb(); ?></td>
        </tr>

        <tr class="notranslate">
	  <td class="advanced">Flag for Spanish:</td>
	  <td class="advanced"><?php $this->googlelanguagetranslator_spanish_flag_choice_cb(); ?></td>
        </tr>

        <tr class="notranslate">
	  <td class="advanced">Flag for Portuguese:</td>
	  <td class="advanced"><?php $this->googlelanguagetranslator_portuguese_flag_choice_cb(); ?></td>
        </tr>
      </table>
    </div> <!-- .inside -->
  </div> <!-- .postbox -->
</div> <!-- #glt_advanced_settings -->


	   <div class="metabox-holder notranslate" style="float: right; width: 33%;">
          <div class="postbox">
            <h3>Add CSS Styles</h3>
			<div class="inside">
			  <p>You can apply any necessary CSS styles below:</p>
			      <?php $this->googlelanguagetranslator_css_cb(); ?>
			 </div>
          </div>
	   </div>
	  <?php $this->googlelanguagetranslator_flags_order_cb(); ?>
	</form>

</div> <!-- .wrap -->
<?php
  }
}
$language_translator_google = new language_translator_google();
