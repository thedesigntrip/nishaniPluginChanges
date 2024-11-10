<?php
$enable_filter = $widget->get_settings_for_display('enable_filter', 'no');

egrid_get_template('wrapper-start.php', apply_filters('egrid_products_wrapper_start_template_args', []), self::TEMPLATE_PATH, self::DEFAULT_PATH);
egrid_get_template('loader.php', apply_filters('egrid_products_loader_template_args', []), self::TEMPLATE_PATH, self::DEFAULT_PATH);
egrid_get_template('inner-start.php', apply_filters('egrid_products_inner_start_template_args', []), self::TEMPLATE_PATH, self::DEFAULT_PATH);
egrid_get_template('content-start.php', apply_filters('egrid_products_content_start_template_args', [
    'enable_filter' => $enable_filter,
]), self::TEMPLATE_PATH, self::DEFAULT_PATH);
?>
    <div class="egrid-products-categories-filter">
        <div class="egrid-placeholder-item egrid-placeholder-widgettitle"></div>
        <div class="egrid-placeholder-items">
            <div class="egrid-placeholder-item"></div>
            <div class="egrid-placeholder-item"></div>
            <div class="egrid-placeholder-item"></div>
            <div class="egrid-placeholder-item"></div>
        </div>
    </div>
<?php
egrid_get_template('content-end.php', apply_filters('egrid_products_content_end_template_args', []), self::TEMPLATE_PATH, self::DEFAULT_PATH);
if ($enable_filter) {
    ?>
    <div id="egrid-products-filters-<?php echo esc_attr($widget->get_id()); ?>"
         class="egrid-products-filters <?php echo esc_attr(implode(' ', $filter_classes)); ?>">
        <div class="egrid-products-categories-filter">
            <div class="egrid-placeholder-item egrid-placeholder-widgettitle"></div>
            <div class="egrid-placeholder-items">
                <div class="egrid-placeholder-item"></div>
                <div class="egrid-placeholder-item"></div>
                <div class="egrid-placeholder-item"></div>
                <div class="egrid-placeholder-item"></div>
            </div>
        </div>
    </div>
    <?php
}
egrid_get_template('inner-end.php', apply_filters('egrid_products_inner_end_template_args', []), self::TEMPLATE_PATH, self::DEFAULT_PATH);
egrid_get_template('wrapper-end.php', apply_filters('egrid_products_wrapper_end_template_args', []), self::TEMPLATE_PATH, self::DEFAULT_PATH);
?>