<?php

use Elementor\Utils;

if (!defined('ABSPATH')) {
    die();
}

if (!class_exists('EGrid_Ajax_Handle')) {
    class EGrid_Ajax_Handle
    {
        public function __construct()
        {
            add_action('wp_ajax_egrid_products', array($this, 'egrid_products'));
            add_action('wp_ajax_nopriv_egrid_products', array($this, 'egrid_products'));
        }

        function send_response($data = [], $message = 'Successfully!', $success = true)
        {
            wp_send_json(
                [
                    'success' => $success,
                    'message' => $message,
                    'data' => $data
                ]
            );
            die;
        }

        function egrid_products()
        {
            $result = '';

            $element_id = isset($_POST['element_id']) ? $_POST['element_id'] : '';
            $post_id = isset($_POST['post_id']) ? $_POST['post_id'] : '';
            $post = get_post($post_id);
            if ($post && $post->ID && $element_id) {
                $document = \Elementor\Plugin::$instance->documents->get_doc_for_frontend($post_id);
                if ($document && $document->is_built_with_elementor()) {
                    \Elementor\Plugin::$instance->documents->switch_to_document($document);
                    $elements_data = $document->get_elements_data();
                    $element_data = Utils::find_element_recursive($elements_data, $element_id);
                    if ($element_data) {
                        $element = \Elementor\Plugin::$instance->elements_manager->create_element_instance($element_data);
                        if ($element) {
                            ob_start();
                            $element->print_element();
                            $result = ob_get_clean();
                        }
                    }
                    \Elementor\Plugin::$instance->documents->restore_document();
                }
            }

            $this->send_response($result, esc_html__('Successfully!', EGRID_TEXT_DOMAIN));
        }
    }
}