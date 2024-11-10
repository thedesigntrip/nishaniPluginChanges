<?php
$content_classes = apply_filters('egrid_products_content_classes', []);
?>
<div class="egrid-products-content flex-basic flex-smobile-100 <?php echo esc_attr(implode(' ', $content_classes)); ?>">