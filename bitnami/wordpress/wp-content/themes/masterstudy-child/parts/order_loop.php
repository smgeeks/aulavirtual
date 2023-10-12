<?php
extract($args);
$post_thumbnail_id = get_the_post_thumbnail($course_id);
$categories = get_the_terms($course_id, 'stm_lms_course_taxonomy')[0]->name;
?>

<div class="stm__course_item">
    <div class="stm__course_info">
        <div class="stm__course_img">
          <?php if (!empty($post_thumbnail_id)) {
                echo $post_thumbnail_id;
            }else { ?>
                <img src="/wp-content/themes/masterstudy-child/assets/image/woocommerce-placeholder-300x300.png" alt="">
           <?php } ?>
        </div>
        <div>
              <h3><?php echo get_the_title($course_id) ?></h3>
            <?php if(!empty($categories)) { ?>
            <p><i class="fa-icon-stm_icon_category"></i><?php echo $categories; ?></p>
            <?php } ?>
        </div>
    </div>
    <div class="stm__course_btn">
        <a href="<?php echo get_the_permalink($course_id) ?>">Empezar</a>
    </div>
</div>