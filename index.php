<?php
if (get_option('hm_home_page') == '404') header('HTTP/1.0 404 Not Found');
elseif (get_option('hm_home_page') == 'redirect') header("Location: " . get_option('hm_home_page_url'));
elseif (get_option('hm_home_page') == 'html') echo get_option('hm_home_page_html');
