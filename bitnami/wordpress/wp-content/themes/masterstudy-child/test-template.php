<?php /* Template Name: TEST Template */
get_header();



$forms = get_option('stm_lms_form_builder_forms', array());

$choices = $forms[2]['fields'][9]['choices'];

echo "<pre>";
print_r($forms);
echo "</pre>";

get_footer();
?>