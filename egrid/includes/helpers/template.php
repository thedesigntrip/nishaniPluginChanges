<?php

function egrid_get_template($template_name, $args = array(), $template_path = '', $default_path = '') {
    if (!empty($args) && is_array($args)) {
        extract($args);
    }

    $located = egrid_locate_template($template_name, $template_path, $default_path);

    if (!file_exists($located)) {
        _doing_it_wrong(__FUNCTION__, sprintf('<code>%s</code> does not exist.', $located), '2.1');
        return;
    }

    $located = apply_filters('egrid_get_template', $located, $template_name, $args, $template_path, $default_path);

    do_action('egrid_before_template_part', $template_name, $template_path, $located, $args);

    include($located);

    do_action('egrid_after_template_part', $template_name, $template_path, $located, $args);
}

function egrid_locate_template($template_name, $template_path = '', $default_path = '') {

    if (!$template_path) {
        $template_path = EGRID_TEMPLATE_PATH;
    }

    if (!$default_path) {
        $default_path = EGRID_PATH . '/templates/';
    }

    // Look within passed path within the theme - this is priority.
    $template = locate_template(
        array(
            trailingslashit($template_path) . $template_name,
            $template_name
        )
    );

    // Get default template/
    if (!$template) {
        $template = $default_path . $template_name;
    }

    // Return what we found.
    return apply_filters('egrid_locate_template', $template, $template_name, $template_path);
}


function egrid_get_template_file_e($template, $data = array()) {
    extract($data);
    $template_file = egrid_get_template_file($template);
    if ($template_file !== false) {
        ob_start();
        include $template_file;
        echo ob_get_clean();
    }
}

function egrid_get_template_file__($template, $data = array()) {
    extract($data);
    $template_file = egrid_get_template_file($template);
    if ($template_file !== false) {
        ob_start();
        include $template_file;
        return ob_get_clean();
    }
    return false;
}

function egrid_get_template_file($template, $dir = null) {

    if ($dir === null) {
        $dir = EGRID_TEMPLATE_PATH;
    }

    $template_file = get_template_directory() . DIRECTORY_SEPARATOR . $dir . $template;

    if (file_exists($template_file)) {
        return $template_file;
    } else {
        $template_file = EGRID_PATH . '/templates' . DIRECTORY_SEPARATOR . $template;
        if (file_exists($template_file)) {
            return $template_file;
        }
    }

    return false;
}

?>