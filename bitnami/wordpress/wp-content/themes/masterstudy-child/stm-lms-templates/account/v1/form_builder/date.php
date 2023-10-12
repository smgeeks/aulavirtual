<?php
/**
 * @var $model
 * @var $value
 */

if(!empty($value)) {
    $value = "v-bind:value=\"{$value}\"";
} else {
    $value = '';
}

if(empty($model)) $model = "field.value";
?>

<input class="form-control"
       v-if="field.type === 'date'"
       :placeholder="field.placeholder ? field.placeholder : ''"
       type="date"
       <?php echo stm_lms_filtered_output($value); ?>
       v-model="<?php echo stm_lms_filtered_output($model); ?>"/>
