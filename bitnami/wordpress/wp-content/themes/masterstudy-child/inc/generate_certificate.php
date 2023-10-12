<?php

class STM_LMS_Certificate_Builder_Child
{
    public function __construct()
    {
        add_action('wp_ajax_stm_get_certificate_child', array($this, 'get_certificate'));
        add_action('wp_ajax_stm_get_area_del_curso_child', array($this, 'get_area_del_curso'));
    }

    public function get_area_del_curso()
    {
        if (!empty($_GET['course_id'])) {
            $course_id = intval($_GET['course_id']);
            $area_del_curso = get_post_meta($course_id, 'subject_area', true);
            wp_send_json($area_del_curso);
        }
    }

    public function get_certificate()
    {

        check_ajax_referer('stm_get_certificate', 'nonce');

        $id = '';
        $course_id = '';

        if (!empty($_GET['course_id']) && $_GET['course_id']) {

            $course_id = intval($_GET['course_id']);
            $certificate_id = get_post_meta($course_id, 'course_certificate', true);

            if (!empty($certificate_id)) {
                $id = $certificate_id;
            } else {
                $terms = wp_get_post_terms($course_id, 'stm_lms_course_taxonomy', array('fields' => 'ids'));
                $meta_query = array(
                    'relation' => 'OR',
                );

                foreach ($terms as $term) {
                    $meta_query[] = array(
                        'key' => 'stm_category',
                        'value' => $term,
                    );
                }

                $meta_query[] = array(
                    'key' => 'stm_category',
                    'value' => 'entire_site',
                );
                $args = array(
                    'post_type' => 'stm-certificates',
                    'posts_per_page' => 1,
                    'meta_query' => $meta_query,
                    'meta_key' => 'stm_category',
                    'orderby' => 'meta_value',
                    'order' => 'ASC',
                );

                $query = new WP_Query($args);

                if ($query->have_posts()) {
                    while ($query->have_posts()) {
                        $query->the_post();
                        $id = get_the_ID();
                    }
                }

                wp_reset_postdata();
            }
        }

        if (empty($id) && !empty($_GET['post_id'])) {
            $id = intval($_GET['post_id']);
        }

        if ($_GET['certificate_dc'] == 1) {
            $id = 9903;
        }

        if (!empty($id)) {
            $certificate = array();
            $orientation = get_post_meta($id, 'stm_orientation', true);
            $fields = get_post_meta($id, 'stm_fields', true);
            $image = get_post_thumbnail_id($id);
            $current_user_id = get_current_user_id();

            if (empty($fields)) {
                $fields = array();
            } else {
                $fields = json_decode($fields, true);
            }
            if (empty($orientation)) {
                $orientation = 'landscape';
            }

            $base64 = false;
            $image_size = false;

            if ($image) {
                $image_file = get_attached_file($image);
                $type = pathinfo($image_file, PATHINFO_EXTENSION);
                $image_data = file_get_contents($image_file); // phpcs:ignore WordPress.WP.AlternativeFunctions
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($image_data); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions
                $image_size = getimagesizefromstring($image_data);
            }

            $curp = $_GET['curp'];
            $puesta = $_GET['puesta'];
            $rfc = $_GET['rfc'];
            $ocupacion = $_GET['ocupacion'];
            $nombre_razon = $_GET['nombre_razon'];
            $nombre = $_GET['nombre'];
            $apellidos = $_GET['apellidos'];
            
            $fields_with_data = array();

            foreach ($fields as $field) {

                if ('image' === $field['type'] && !empty($field['imageId'])) {
                    $field['image_data'] = $this->encode_base64($field['imageId']);
                } elseif ('course_name' === $field['type'] && !empty($course_id)) {
                    $field['content'] = html_entity_decode(get_the_title($course_id));
                } elseif ('author' === $field['type'] && !empty($course_id)) {
                    $author = get_post_field('post_author', $course_id);
                    $author_name = get_the_author_meta('display_name', $author);
                    $field['content'] = $author_name;
                } elseif ('student_name' === $field['type'] && !empty($course_id)) {
                    $current_user = wp_get_current_user();
                    if (!empty($nombre)) {
                        update_user_meta($current_user->ID, 'first_name', $nombre);
                    }
                    if (!empty($apellidos)) {
                        update_user_meta($current_user->ID, 'last_name', $apellidos);
                    }
                    if (!empty($current_user)) {
                        $first_name = get_user_meta($current_user->ID, 'first_name', true);
                        $last_name = get_user_meta($current_user->ID, 'last_name', true);
                        $field['content'] = $current_user->display_name;
                        if (!empty($first_name) || !empty($last_name)) {
                            $field['content'] = !empty($first_name) ? "$first_name " : '';
                            $field['content'] .= $last_name;
                        }
                    }
                } elseif ('start_date' === $field['type'] && !empty($course_id)) {
                    $start_date = stm_lms_get_user_course($current_user_id, $course_id, array('start_time'));
                    if (!empty($start_date)) {
                        $start_date = STM_LMS_Helpers::simplify_db_array($start_date);
                        if (!empty($start_date['start_time'])) {
                            $field['content'] = date_i18n('Y-m-d', $start_date['start_time']);
                        }
                    }
                } elseif ('start_date_day' === $field['type'] && !empty($course_id)) {
                    $start_date = stm_lms_get_user_course($current_user_id, $course_id, array('start_time'));
                    if (!empty($start_date)) {
                        $start_date = STM_LMS_Helpers::simplify_db_array($start_date);
                        if (!empty($start_date['start_time'])) {
                            $field['content'] = date_i18n('d', $start_date['start_time']);
                        }
                    }
                } elseif ('start_date_month' === $field['type'] && !empty($course_id)) {
                    $start_date = stm_lms_get_user_course($current_user_id, $course_id, array('start_time'));
                    if (!empty($start_date)) {
                        $start_date = STM_LMS_Helpers::simplify_db_array($start_date);
                        if (!empty($start_date['start_time'])) {
                            $field['content'] =  date_i18n('m', $start_date['start_time']);
                        }
                    }
                }

                elseif ('start_date_years' === $field['type'] && !empty($course_id)) {
                    $start_date = stm_lms_get_user_course($current_user_id, $course_id, array('start_time'));
                    if (!empty($start_date)) {
                        $start_date = STM_LMS_Helpers::simplify_db_array($start_date);
                        if (!empty($start_date['start_time'])) {
                            $field['content'] = date_i18n('Y', $start_date['start_time']);
                        }
                    }
                } elseif ('end_date' === $field['type'] && !empty($course_id)) {
                    $end_date = get_user_meta($current_user_id, 'last_progress_time', true);
                    if (!empty($end_date[$course_id])) {
                        $field['content'] = date_i18n('Y-m-d', $end_date[$course_id]);
                    }
                }

                elseif ('end_date_years' === $field['type'] && !empty($course_id)) {
                    $end_date = get_user_meta($current_user_id, 'last_progress_time', true);
                    if (!empty($end_date[$course_id])) {
                        $field['content'] = date_i18n('Y', $end_date[$course_id]);
                    }
                }
                elseif ('end_date_month' === $field['type'] && !empty($course_id)) {
                    $end_date = get_user_meta($current_user_id, 'last_progress_time', true);
                    if (!empty($end_date[$course_id])) {
                        $field['content'] = date_i18n('m', $end_date[$course_id]);
                    }
                }
                elseif ('end_date_day' === $field['type'] && !empty($course_id)) {
                    $end_date = get_user_meta($current_user_id, 'last_progress_time', true);
                    if (!empty($end_date[$course_id])) {
                        $field['content'] = date_i18n('d', $end_date[$course_id]);
                    }
                }


                elseif ('current_date' === $field['type'] && !empty($course_id)) {
                    $field['content'] = date_i18n('j F Y', time());
                } elseif ('progress' === $field['type'] && !empty($course_id)) {
                    $progress = stm_lms_get_user_course($current_user_id, $course_id, array('progress_percent'));
                    if (!empty($progress)) {
                        $progress = STM_LMS_Helpers::simplify_db_array($progress);
                        if (!empty($progress['progress_percent'])) {
                            $field['content'] = $progress['progress_percent'] . '%';
                        }
                    }
                } elseif ('co_instructor' === $field['type'] && !empty($course_id)) {
                    $co_instructor = get_post_meta($course_id, 'co_instructor', true);
                    if (!empty($co_instructor)) {
                        $co_instructor_data = get_userdata($co_instructor);
                        if ($co_instructor_data) {
                            $co_instructor_name = $co_instructor_data->data->display_name;
                            $field['content'] = $co_instructor_name;
                        }
                    } else {
                        $field['content'] = '';
                    }
                } elseif ('details' === $field['type'] && !empty($course_id)) {
                    $curriculum_info = STM_LMS_Course::curriculum_info(get_post_meta($course_id, 'curriculum', true));
                    $field_content = esc_html__('0 Lessons 0, Quizzes', 'masterstudy-lms-learning-management-system-pro');
                    if (!empty($curriculum_info)) {
                        $lessons_count = !empty($curriculum_info['lessons']) ? $curriculum_info['lessons'] : '0';
                        $quizzes_count = !empty($curriculum_info['quizzes']) ? $curriculum_info['quizzes'] : '0';
                        $field_content = sprintf(
                        /* translators: %s: number */
                            esc_html__('%1$s Lessons, %2$s Quizzes', 'masterstudy-lms-learning-management-system-pro'),
                            $lessons_count,
                            $quizzes_count
                        );
                    }
                    $field['content'] = $field_content;
                } elseif ('code' === $field['type'] && !empty($course_id)) {
                    $field['content'] = get_post_meta($id, 'code', true);
                } elseif ('student_code' === $field['type'] && !empty($course_id)) {
                    $field['content'] = STM_LMS_Certificates::generate_certificate_user_code($current_user_id, $course_id);
                } elseif ('curp_user' === $field['type']) {
                    if ($curp) {
                        $field['content'] = $curp;
                        update_user_meta($current_user_id, '8g2bnvxu3mt', $curp);
                    } else {
                        $field['content'] = get_user_meta($current_user_id, '8g2bnvxu3mt', true);
                    }
                } elseif ('ocupacion_especifica_user' === $field['type']) {
                    if ($ocupacion) {
                        $field['content'] = $ocupacion;
                        update_user_meta($current_user_id, '7f8lxabexc9', $ocupacion);
                    } else {
                        $field['content'] = get_user_meta($current_user_id, '7f8lxabexc9', true);
                    }
                } elseif ('puesto_user' === $field['type']) {
                    if ($puesta) {
                        $field['content'] = $puesta;
                        update_user_meta($current_user_id, 'tnk9e6yw90a', $puesta);
                    } else {
                        $field['content'] = get_user_meta($current_user_id, 'tnk9e6yw90a', true);
                    }
                } elseif ('rfc_user' === $field['type']) {
                    if ($rfc) {
                        $field['content'] = $rfc;
                        update_user_meta($current_user_id, '2h9dxub26gw', $rfc);
                    } else {
                        $field['content'] = get_user_meta($current_user_id, '2h9dxub26gw', true);
                    }
                } elseif ('nombre_razon_social_user' === $field['type']) {
                    if ($nombre_razon) {
                        $field['content'] = $nombre_razon;
                        update_user_meta($current_user_id, '79p8r64z51r', $nombre_razon);
                    } else {
                        $field['content'] = get_user_meta($current_user_id, '79p8r64z51r', true);
                    }
                } elseif ('subject_area' === $field['type'] && !empty($course_id)) {
                    $field['content'] = get_post_meta($course_id, 'subject_area', true);
                } elseif ('name_agent' === $field['type'] && !empty($course_id)) {
                    $field['content'] = get_post_meta($course_id, 'name_agent', true);
                } elseif ('duration_course_calculated' === $field['type'] && !empty($course_id)) {
                    $end_date = get_user_meta($current_user_id, 'last_progress_time', true);
                    $duration_course = get_post_meta($course_id, 'duration_course', true);
                    if (!empty($end_date[$course_id])) {
                        if ($duration_course) {
                            $end_date = $end_date[$course_id] + 31556926 * $duration_course;
                            $newDate = date_i18n('Y-m-d', $end_date);
                        } else {
                            $newDate = date_i18n('Y-m-d', $end_date[$course_id]);
                        }
                        $field['content'] = $newDate;
                    }
                } elseif ('duration_info' === $field['type'] && !empty($course_id)) {
                    $field['content'] = get_post_meta($course_id, 'duration_info', true);
                }

                $fields_with_data[] = $field;
            }

            $this->store_certificate_data($id, $current_user_id, $fields_with_data);

            $data = array(
                'orientation' => $orientation,
                'fields' => $fields_with_data,
                'image' => $base64,
                'image_size' => $image_size,
            );
            $certificate['data'] = $data;

            wp_send_json($certificate);
        }
    }

    public function encode_base64($image_id)
    {
        $file = get_attached_file($image_id);

        if ($file) {
            $type = pathinfo($file, PATHINFO_EXTENSION);
            $image_data = file_get_contents($file); // phpcs:ignore WordPress.WP.AlternativeFunctions

            return 'data:image/' . $type . ';base64,' . base64_encode($image_data); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions
        } else {
            return false;
        }
    }

    public static function store_certificate_data($certificate_id, $user_id, &$data)
    {
        $save_data_keys = array(
            'student_name',
            'author',
        );

        foreach ($data as &$field_data) {
            if (!in_array($field_data['type'], $save_data_keys, true)) {
                continue;
            }

            $certificate_user_meta = "certificate_{$field_data['type']}_{$certificate_id}";
            $field = get_user_meta($user_id, sanitize_text_field($certificate_user_meta), true);

            if (!empty($field)) {
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                $author = get_post_field('post_author', intval(sanitize_text_field($_GET['course_id'])));
                $author_name = get_the_author_meta('display_name', $author);

                if ('author' === $field_data['type'] && $field === $author_name) {
                    $field_data['content'] = html_entity_decode($field);
                }
            } else {
                update_user_meta($user_id, sanitize_text_field($certificate_user_meta), $field_data['content']);
            }
        }
    }
}

new STM_LMS_Certificate_Builder_Child();