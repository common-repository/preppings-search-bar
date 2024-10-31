<?php
/**
 * Plugin Name: Preppings Search Bar
 * Plugin URI: http://preppings.com/api/preppings-search-bar-wordpress-plugin/
 * Description: Adds Preppings Search Bar widget to your blog.
 * Version: 1.1
 * Author: Stas Davydov
 * Author URI: http://stasdavydov.com/
 */

class psb_widget extends WP_Widget {
    function __construct() {
        parent::__construct(
            'psb_widget',
            'Preppings Search Bar',
            array('description' => 'Search panel for searching on best food database Preppings.com for your blog')
        );
    }

    public function widget($args, $instance) {
        $key = $instance['key'];
        $colorSchema = $instance['color_schema'];
// before and after widget arguments are defined by themes
        echo $args['before_widget']; ?>
        <script src="http://api.preppings.com/api/?key=<?php echo $key ?>" data-color-schema="<?php echo $colorSchema; ?>"></script>
        <?php
        echo $args['after_widget'];
    }

// Widget Backend
    public function form($instance) {
        if (isset($instance['key'])) {
            $key = $instance['key'];
        } else {
            $key = 'API Key';
        }
        if (isset($instance['color_schema'])) {
            $color_schema = $instance['color_schema'];
        } else {
            $color_schema = 'light';
        }
// Widget admin form
        ?>

        <div>
            <label for="<?php echo $this->get_field_id('key'); ?>">API Key: </label>
            <input class="widefat preppings-api-key" id="<?php echo $this->get_field_id('key') ?>"
                   name="<?php echo $this->get_field_name('key'); ?>" type="text"
                   value="<?php echo esc_attr($key); ?>"/>
            <a id="request-preppings-key" href="http://preppings.com/api/terms/?type=free">Request free API Key at Preppings.com</a> if you do not have one.

        </div>
        <script type="text/javascript" src="http://api.preppings.com/static/jquery.colorbox-min.js"></script>
        <script type="text/javascript">
            if (window.preppingsOnce == undefined) {
                window.preppingsOnce = true;

                var setCookie = function (name, value, options) {
                    options = options || {};
                    var expires = options.expires;
                    if (typeof expires == "number" && expires) {
                        var d = new Date();
                        d.setTime(d.getTime() + expires*1000);
                        expires = options.expires = d;
                    }
                    if (expires && expires.toUTCString) {
                        options.expires = expires.toUTCString();
                    }
                    value = encodeURIComponent(value);
                    var updatedCookie = name + "=" + value;
                    for(var propName in options) {
                        updatedCookie += "; " + propName;
                        var propValue = options[propName];
                        if (propValue !== true) {
                            updatedCookie += "=" + propValue;
                        }
                    }
                    document.cookie = updatedCookie;
                };

                jQuery(function() {
                    jQuery('head')
                        .append(jQuery('<link rel="stylesheet" type="text/css" href="http://preppings.com/wp-content/themes/preppings/css/colorbox/colorbox.css"/>'))
                        .append(jQuery('<style type="text/css">' +
                            'div.popup { padding: 10px 20px; }' +
                            'div.popup h1 { color: #d24200; font-weight: 300; font-size: 36px; text-decoration: none; white-space: pre-line; margin: 0 0 18px; }' +
                            'div.popup .terms { height: 200px; overflow-y: scroll; }' +
                            'div.popup .api .button { display: inline-block; margin: 15px 0 0 0; padding: 10px 10px 10px 30px; color: #000; border: 1px solid #CCC; text-transform: uppercase; box-shadow: 1px 1px 3px 1px #ccc; border-radius: 5px; cursor: pointer; height: auto; line-height: auto; }' +
                            'div.popup .api .button.accept { background: #FFF url("http://preppings.com/wp-content/themes/preppings/images/check.png") no-repeat left center; }' +
                            'div.popup .api .button.decline { background: #FFF url("http://preppings.com/wp-content/themes/preppings/images/x.png") no-repeat left center; }' +
                            '</style>'));


                    var $body = jQuery('body');
                    var ajaxDelegate = function(selector, data, onComplete) {
                        $body.delegate(selector, 'click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();

                            jQuery.ajax(jQuery(this).attr('href'), {
                                dataType: 'html',
                                type: 'GET',
                                crossDomain: true,
                                data: data,
                                success: function(result) {
                                    jQuery.colorbox({
                                        html: result,
                                        maxWidth: '50%',
                                        maxHeight: '80%',
                                        onComplete: function() {
                                            onComplete(result);
                                        }
                                    });
                                },
                                error: function(jqXHR) {
                                    alert('Cannot request API Key from Preppings.com: ' + jqXHR.responseText);
                                }
                            });
                        });
                    };

                    ajaxDelegate('#request-preppings-key', {}, function() {
                        jQuery('.api .accept').attr('href', 'http://preppings.com/wp-content/themes/preppings/api-url.php');
                        ajaxDelegate('.accept', {
                            url: '<?php echo parse_url(get_bloginfo('url'), PHP_URL_HOST); ?>'
                        }, function(result) {
                            jQuery('.preppings-api-key').val(result);
                            jQuery.colorbox.close();
                        });
                    });
                });
            }
        </script>
        <p>
            <label for="<?php echo $this->get_field_id('color_schema'); ?>">Color Schema: </label>
            <select class="widefat" id="<?php echo $this->get_field_id('color_schema'); ?>"
                   name="<?php echo $this->get_field_name('color_schema'); ?>">
                <option value="light"<?php if ($color_schema == 'light') { echo ' selected="selected"'; } ?>>Light</option>
                <option value="dark"<?php if ($color_schema == 'dark') { echo ' selected="selected"'; } ?>>Dark</option>
            </select>
        </p>
    <?php
    }

// Updating widget replacing old instances with new
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['key'] = (!empty($new_instance['key'])) ? strip_tags($new_instance['key']) : '';
        $instance['color_schema'] = (!empty($new_instance['color_schema'])) ? strip_tags($new_instance['color_schema']) : 'light';
        return $instance;
    }
} // Class psb_widget ends here

// Register and load the widget
function psb_load_widget() {
    register_widget('psb_widget');
}

add_action('widgets_init', 'psb_load_widget');
