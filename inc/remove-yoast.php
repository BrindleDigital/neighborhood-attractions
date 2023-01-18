<?php

//* Remove Yoast
add_action( 'add_meta_boxes', 'na_remove_yoast_box', 100);
function na_remove_yoast_box() {
	remove_meta_box( 'wpseo_meta', 'attractions', 'normal');
}
