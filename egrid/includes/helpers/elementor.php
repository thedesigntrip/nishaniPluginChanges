<?php
	function egrid_find_element_by_type_recursive( $elements, $type ) {
		$elements_found = [];
		foreach ( $elements as $element ) {
			if ( isset($element['widgetType']) && $type === $element['widgetType'] ) {
				$elements_found[] = $element;
			}

			if ( ! empty( $element['elements'] ) ) {
				$found = egrid_find_element_by_type_recursive( $element['elements'], $type );

				if ( $found ) {
					$elements_found = array_merge($elements_found, $found);
				}
			}
		}

		return $elements_found;
	}
?>