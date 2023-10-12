<?php


function get_product_attributes_options() {
    $attributes = wc_get_attribute_taxonomies();
    $options = array();

    foreach ($attributes as $attribute) {
        $options[$attribute->attribute_name] = $attribute->attribute_label;
    }

    return $options;
}



add_filter('stm_wpcfto_fields', function ($fields) {
    $old_settings = $fields['stm_courses_settings'];
    $new_settings = array(
        'attributes' => array(
            'name' => esc_html__('Attributes', 'masterstudy-lms-learning-management-system'),
            'label' => esc_html__('Attributes', 'masterstudy-lms-learning-management-system'),
            'icon' => 'fa fa-list-alt',
            'fields' => array(
                'attribute' => array(
                    'type' => 'select',
                    'label' => esc_html__('WooCommerce Attribute', 'masterstudy-lms-learning-management-system'),
                    'options' => get_product_attributes_options(), // Получаем список атрибутов WooCommerce
                ),
                'stm_course_attribute_value' => array(
                    'type' => 'textarea',
                    'label' => esc_html__('Attribute Value', 'masterstudy-lms-learning-management-system'),
                ),
            )
        )
    );

    $fields['stm_courses_settings'] = array_merge($old_settings, $new_settings);
    return $fields;
},15);

add_filter('stm_wpcfto_fields', function ($fields) {
    $old_settings_fields = $fields['stm_courses_settings']['section_settings']['fields'];
    $new_settings_fields = array(
        'course_title_for_certification' => array(
            'type'  => 'text',
            'label' => esc_html__( 'Título del curso para certificación', 'masterstudy-lms-learning-management-system' ),
        ),
    );

    $fields['stm_courses_settings']['section_settings']['fields'] = array_merge($new_settings_fields, $old_settings_fields);
    return $fields;
},15);



add_action('save_post', 'save_course_attribute_data');

function save_course_attribute_data($post_id) {
    $attribute_value = sanitize_text_field($_POST['stm_course_attribute_value']);
    $attribute_name = sanitize_text_field($_POST['attribute']);

    if (!empty($attribute_name)) {
        $attribute_value = sanitize_text_field($_POST['stm_course_attribute_value']);
        $attribute_name = sanitize_text_field($_POST['attribute']);
        if (isset($_POST['stm_course_attribute_value']) && isset($_POST['attribute'])) {
            // Получаем текущие атрибуты из метаполя _product_attributes
            $product_attributes = get_post_meta($post_id, '_product_attributes', true);

            // Создаем новый атрибут или обновляем существующий
            if (!empty($product_attributes) && isset($product_attributes[$attribute_name])) {

                $product_attributes[$attribute_name]['value'] = $attribute_value;
            } else {
                $product_attributes = [];
                $product_attributes[$attribute_name] = array(
                    'name' => $attribute_name,
                    'value' => $attribute_value,
                );
            }
            // Обновляем метаполе _product_attributes
            update_post_meta($post_id, '_product_attributes', $product_attributes);
        }
    }
}



