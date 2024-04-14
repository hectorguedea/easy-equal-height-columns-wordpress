<?php
/*
Plugin Name: Easy Equal Height Columns
Description: Optimize your site with Easy Equal Height Columns. Sync heights, manage containers, and customize CSS for design harmony
Version: 1.0
Author: Héctor Guedea
Author URI: https://hectorguedea.com
Plugin URI: https://hectorguedea.com/easy-equal-height-columns-plugin
*/

function easy_equal_height_columns_plugin_settings_link( $links ) {
    $settings_link = '<a href="tools.php?page=easy-equal-height-columns-settings">Settings</a>';
    array_unshift( $links, $settings_link );
    return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'easy_equal_height_columns_plugin_settings_link' );

function equal_height_columns_script() {
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            let equalHeight = function (containerClass, itemClass) {
                let $containers = jQuery(containerClass);
                let maxHeight = 0;

                $containers.each(function () {
                    let $items = jQuery(this).find(itemClass);
                    $items.css('height', 'auto');
                    $items.each(function() {
                        maxHeight = Math.max(maxHeight, jQuery(this).height());
                    });
                });

                $containers.each(function () {
                    let $items = jQuery(this).find(itemClass);
                    $items.css('height', maxHeight);
                });
            };

            <?php

            $container_classes = get_option('equal_height_container_classes');
            $item_classes = get_option('equal_height_item_classes');

            if ($container_classes && $item_classes) {
                $count = count($container_classes);
                for ($i = 0; $i < $count; $i++) {
                    echo "equalHeight('" . esc_js($container_classes[$i]) . "', '" . esc_js($item_classes[$i]) . "');";
                }
            }
            ?>

            jQuery(window).resize(function () {
                <?php

               if ($container_classes && $item_classes) {
                    $count = count($container_classes);
                    for ($i = 0; $i < $count; $i++) {
                        echo "equalHeight('" . esc_js($container_classes[$i]) . "', '" . esc_js($item_classes[$i]) . "');";
                    }
                }
                ?>
            });
        });
    </script>
    <?php
}


add_action('wp_footer', 'equal_height_columns_script');


function easy_equal_height_columns_settings_page() {
    ?>
    <style>
    

    .announcement,
    .equal-height-settings-row {
        max-width: 650px;
        min-width: 255px;
        width: 100%; 
    }

    /* A continuación, el resto de tus estilos... */
    
    .announcement {
        background-color: #f8f9fa;
        border: 1px solid #d1d3e2;
        border-radius: 5px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .announcement-text {
        color: #495057;
        font-size: 16px;
        line-height: 1.5;
    }

    .equal-height-settings-row {
        background-color: #fff; 
        border: 1px solid #d1d3e2;
        border-radius: 5px;
        padding: 20px; 
        margin-bottom: 20px; 
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
    }

    .equal-height-settings-column {
        margin-bottom: 10px; 
        position: relative;
    }

    .equal-height-settings-column label {
        display: block; 
        margin-bottom: 5px; 
        font-weight:bold;
    }

    .equal-height-settings-column input[type="text"] {
        width: calc(100% - 24px); 
        padding: 8px; 
        border: 1px solid #ccc; 
        border-radius: 4px; 
    }

    .delete-row {
        position: absolute; 
        bottom: -15px;
        right: -10px;
        background: none; 
        border: none; 
        cursor: pointer;
    }

    #add-settings-row, #add-settings-row span{
        vertical-align: middle;
    }

    
    </style>
    <div class="wrap">
        <h2>Easy Equal Height Columns Plugin</h2>
        <h3>Settings</h2>
        <div class="announcement">
            <p class="announcement-text">Add multiple sets of containers and items to accommodate different sections of your page. Each set allows you to define a main container and its item containers.</p>
            <p class="announcement-text">Additionally, you can delete existing sets if they are no longer needed. This provides a flexible way to manage and customize column height matching on your site.</p>
        
        </div> 
        <form method="post" action="options.php">
            <?php settings_fields('equal_height_columns_group'); ?>
            <div id="equal-height-settings-container">
                <?php
                $container_classes = get_option('equal_height_container_classes');
                $item_classes = get_option('equal_height_item_classes');

                if ($container_classes && $item_classes) {
                    $count = count($container_classes);
                    for ($i = 0; $i < $count; $i++) {
                        ?>
                        <div class="equal-height-settings-row">
                      
                        <?php if ($i > 0) { ?>
                                <div class="equal-height-settings-column">
                                    <button type="button" class="delete-row"><span class="dashicons dashicons-dismiss" style="color: #a00;"></span></button>
                                </div>
                            <?php } ?>

                            <div class="equal-height-settings-column">
                                <label>Main Container</label>
                                <input type="text" name="equal_height_container_classes[]" value="<?php echo esc_attr($container_classes[$i]); ?>" />
                            </div>
                            <div class="equal-height-settings-column">
                                <label>Item Element</label>
                                <input type="text" name="equal_height_item_classes[]" value="<?php echo esc_attr($item_classes[$i]); ?>" />
                            </div>
                           
                        </div>
                        <?php
                    }
                } else { ?>
                    <div class="equal-height-settings-row">
                        <div class="equal-height-settings-column">
                            <label>Main Container</label>
                            <input type="text" name="equal_height_container_classes[]" value="" />
                        </div>
                        <div class="equal-height-settings-column">
                            <label>Item Element</label>
                            <input type="text" name="equal_height_item_classes[]" value="" />
                        </div>
                    </div>
                <?php } ?>
            </div>

            <button type="button" id="add-settings-row" class="button">
             <span class="dashicons dashicons-plus"></span> Add
          </button>
            <?php submit_button(); ?>
        </form>
    </div>

   <script type="text/javascript">
    jQuery(document).ready(function($) {
        // Function to add a new configuration row
        $('#add-settings-row').on('click', function() {
            let newRow = $('.equal-height-settings-row').first().clone();
            newRow.find('input').val(''); // Clear input values
            newRow.find('.delete-row').remove(); // Remove delete button from the first row
            newRow.find('label').each(function() {
                let labelText = $(this).text().trim();
                if (labelText === 'Main Container') {
                    let count = $('.equal-height-settings-row').length + 1;
                    $(this).text(labelText + ' ' + count)
                }
            });
            newRow.prepend('<div class="equal-height-settings-column"><button type="button" class="delete-row"><span class="dashicons dashicons-dismiss" style="color: #a00;"></span></button></div>'); // Agregar botón de borrar con icono de X roja
            $('#equal-height-settings-container').append(newRow);
        });

        // Function to delete a configuration row
        $(document).on('click', '.delete-row', function() {
            if ($('.equal-height-settings-row').length > 1) {
                $(this).closest('.equal-height-settings-row').remove();
            } else {
                alert('You cannot delete the original row.');
            }
        });
    });
</script>


       
    <?php
}

function register_equal_height_columns_settings() {
    register_setting('equal_height_columns_group', 'equal_height_container_classes');
    register_setting('equal_height_columns_group', 'equal_height_item_classes');
}

add_action('admin_menu', 'equal_height_columns_admin_menu');
function equal_height_columns_admin_menu() {
    add_submenu_page('tools.php', 'Easy Equal Height Columns Settings', 'Easy Equal Height Columns', 'manage_options', 'easy-equal-height-columns-settings', 'easy_equal_height_columns_settings_page');
    add_action('admin_init', 'register_equal_height_columns_settings');
}
?>