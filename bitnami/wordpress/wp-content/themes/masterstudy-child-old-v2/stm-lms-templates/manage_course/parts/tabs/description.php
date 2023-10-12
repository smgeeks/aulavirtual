<div class="stm_lms_course__image stm_lms_wizard_step_3">
	<?php STM_LMS_Templates::show_lms_template(
		'manage_course/forms/image',
		array(
			'field_key' => 'image',
		)
	) ?>
</div>

<div class="stm_lms_course__content stm_lms_wizard_step_4">
	<?php
	STM_LMS_Templates::show_lms_template(
		'manage_course/forms/editor',
		array(
			'field_key' => 'content',
		)
	)
	?>
</div>

<div class="stm_lms_course__content stm_lms_course__input">
    <input type="text" id="subject_area" placeholder="Area tematica del curso..." v-model="fields['subject_area']" v-html="fields['subject_area']">
</div>

<div class="stm_lms_course__content stm_lms_course__input">
    <input type="text" id="name_agent" placeholder="Nombre de agente capacitador..." v-model="fields['name_agent']" v-html="fields['name_agent']">
</div>

<div class="stm_lms_course__content stm_lms_course__input">
    <input type="number" id="duration_course" placeholder="Tiempo de vigencia..." v-model="fields['duration_course']" v-html="fields['duration_course']">
</div>

<div class="stm_lms_course__file_attachment">
	<?php
	STM_LMS_Templates::show_lms_template(
		'manage_course/forms/course_files',
		array(
			'field_key' => 'course_files',
		)
	)
	?>
</div>
