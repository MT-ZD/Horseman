<?php
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
    $author->avatar = get_avatar_url($user->ID);

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
