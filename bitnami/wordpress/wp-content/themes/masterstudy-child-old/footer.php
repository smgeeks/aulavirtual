	<?php do_action('masterstudy_before_footer'); ?>

	<footer id="footer" class="<?php echo ( stm_option('footer_parallax_option') ) ? '' : 'parallax-off' ?>">
		<div class="footer_wrapper">
			<?php get_template_part('partials/footers/footer', 'top'); ?>
			<?php get_template_part('partials/footers/footer', 'bottom'); ?>
			<?php get_template_part('partials/footers/copyright'); ?>
		</div>
	</footer>
	<div id="course_add_popup" class="modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <h2><?php esc_html_e('Añadido a la cesta', 'masterstudy'); ?></h2>
                <div class="details">
                    <img src="" alt="" class="img">
                    <a href="" class="name"></a>
                    <a href="" class="btn btn-default"><?php esc_html_e('Ir al carrito', 'masterstudy'); ?></a>
                </div>
                <div class="close"><i class="fas fa-times"></i></div>
            </div>
        </div>
    </div>

	<?php do_action('masterstudy_after_footer'); ?>

	<?php wp_footer(); ?>
	</body>
</html>
