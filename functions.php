<?php
load_theme_textdomain('horseman');

add_action('rest_api_init', 'rest_imporvements');

function rest_imporvements()
{
    register_rest_field(
        array('post'),
        'image',
        array(
            'get_callback'    => 'get_rest_image',
            'update_callback' => null,
            'schema'          => null,
        )
    );
    register_rest_field(
        array('post'),
        'author',
        array(
            'get_callback'    => 'get_rest_author',
            'update_callback' => null,
            'schema'          => null,
        )
    );
    register_rest_field(
        array('comment'),
        'post',
        array(
            'get_callback'    => 'get_rest_comment_post',
            'update_callback' => null,
            'schema'          => null,
        )
    );
    register_rest_route('hm/v1/', 'menus', array(
        'methods'  => 'GET',
        'callback' => 'get_rest_menus'
    ));
}

// Adds post thumbnail with all sizes to /posts request
function get_rest_image($object, $field_name, $request)
{
    if ($object['featured_media']) {
        $img = new stdClass();
        $img->thumbnail = wp_get_attachment_image_src($object['featured_media'], 'thumbnail')[0];
        $img->medium = wp_get_attachment_image_src($object['featured_media'], 'medium')[0];
        $img->medium_large = wp_get_attachment_image_src($object['featured_media'], 'medium_large')[0];
        $img->large = wp_get_attachment_image_src($object['featured_media'], 'large')[0];
        $img->post_thumbnail = wp_get_attachment_image_src($object['featured_media'], 'post-thumbnail')[0];
        $img->full = wp_get_attachment_image_src($object['featured_media'], 'full')[0];
        return $img;
    }

    return false;
}

// Expands author informations in /posts request
function get_rest_author($object, $field_name, $request)
{
    $author = new stdClass();
    $user = get_userdata($object['author']);

    $author->id = $user->ID;
    $author->name = $user->display_name;
    $author->avatar = get_avatar_url($user->ID, array('size' => 256));
    $author->description = $user->description;

    return $author;
}

// Expands post informations in /comments request
function get_rest_comment_post($object, $field_name, $request)
{
    $post = new stdClass();
    $postData = get_post($object['post']);

    $post->id = $postData->ID;
    $post->title = $postData->post_title;
    $post->slug = $postData->post_name;

    return $post;
}

// Strips unnecessary data from param and adds menu items
function formatMenuData($nav_menu_terms)
{
    $result = array();

    foreach ($nav_menu_terms as $term) {
        $menuData = new stdClass();
        $menuItems = wp_get_nav_menu_items($term);
        $items = array();

        $menuData->term_id = $term->term_id;
        $menuData->slug = $term->slug;
        $menuData->name = $term->name;
        $menuData->description = $term->description;
        $menuData->parent = $term->parent;

        if (get_option('hm_active_menu') == $term->term_id) $menuData->active = true;
        else $menuData->active = false;

        foreach ($menuItems as $item) {
            $menuItem = new stdClass();

            $menuItem->title = $item->title;
            $menuItem->url = $item->url;
            $menuItem->target = $item->target;
            $menuItem->description = $item->description;
            $menuItem->attr_title = $item->attr_title;
            $menuItem->classes = $item->classes;

            array_push($items, $menuItem);
        }

        $menuData->count = $term->count;
        $menuData->items = $items;

        array_push($result, $menuData);
    }

    return $result;
}

// Return menus array
function get_rest_menus(WP_REST_Request $request)
{
    $term_id = $request->get_param('term_id');
    $slug = $request->get_param("slug");
    $active = $request->get_param("active");

    if (!empty($term_id)) {
        $response = get_terms('nav_menu', array(
            'term_taxonomy_id' => $term_id
        ));
    } elseif (!empty($slug)) {
        $response = get_terms('nav_menu', array(
            'slug' => $slug
        ));
    } elseif (!empty($active) && ($active == true || $active == 1)) {
        $response = get_terms('nav_menu', array(
            'term_taxonomy_id' => get_option('hm_active_menu')
        ));
    } else {
        $response = get_terms('nav_menu');
    }

    return formatMenuData($response);
}

if (is_admin()) {
    // Add horseman settings hooks
    add_action('admin_menu', 'horseman_menu');
    add_action('admin_init', 'horseman_settings');
}

function horseman_menu()
{
    add_options_page('Horseman Settings', '🐎 Horseman', 'customize', 'horseman-settings', 'horseman_admin_page');
}

function horseman_admin_page()
{
    ?>
    <div class="wrap">
        <h1>
            🐎 <?= __("Horseman settings", "horseman") ?>
        </h1>
        <form action="options.php" method="post">
            <?php
                settings_fields('horseman-settings');
                do_settings_sections('horseman-settings');
                ?>

            <table class="form-table">
                <tbody>
                    <tr>
                        <th>
                            <?= __("Active menu", "horseman") ?>
                        </th>
                        <td>
                            <select name="hm_active_menu">
                                <option disabled selected>-- <?= __("Select menu", "horseman") ?> --</option>
                                <?php
                                    $active_menu = get_option('hm_active_menu');
                                    foreach (get_terms('nav_menu') as $menu) {
                                        if ($active_menu == $menu->term_id) echo "<option value='$menu->term_id' selected>$menu->name</option>";
                                        else echo "<option value='$menu->term_id'>$menu->name</option>";
                                    }
                                    ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?= __("Home page", "horseman") ?>
                        </th>
                        <td>
                            <select name="hm_home_page">
                                <option value="404" <?= get_option('hm_home_page') == '404' ? 'selected' : '' ?>>404 Error</option>
                                <option value="html" <?= get_option('hm_home_page') == 'html' ? 'selected' : '' ?>>HTML</option>
                                <option value="redirect" <?= get_option('hm_home_page') == 'redirect' ? 'selected' : '' ?>>Redirect</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?= __("Redirect URL", "horseman") ?>
                        </th>
                        <td>
                            <input type="url" name="hm_home_page_url" value="<?= get_option('hm_home_page_url') ?>">
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?= __("Home page HTML", "horseman") ?>
                        </th>
                        <td>
                            <textarea name="hm_home_page_html" class="large-text code" rows="10"><?= get_option('hm_home_page_html') ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?= __("Allow anonymous comments", "horseman") ?>
                        </th>
                        <td>
                            <input type="checkbox" name="hm_anonymous_comments" value="true" <?= get_option('hm_anonymous_comments') ? 'checked' : '' ?>>
                        </td>
                    </tr>
                </tbody>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
<?php
}

function horseman_settings()
{
    register_setting('horseman-settings', 'hm_active_menu');
    register_setting('horseman-settings', 'hm_home_page');
    register_setting('horseman-settings', 'hm_home_page_url');
    register_setting('horseman-settings', 'hm_home_page_html');
    register_setting('horseman-settings', 'hm_anonymous_comments');
}

if (get_option('hm_anonymous_comments')) {
    add_filter('rest_allow_anonymous_comments', '__return_true');
}
