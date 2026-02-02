<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package bootspress
 */
?>

		<?php if ( ! is_page_template( 'full-width-without-container.php' ) ): ?>
			</div><!-- .content-area .row -->
		</div><!-- .container -->
		<?php endif; ?>
	</div><!-- #content -->

	<footer id="footer" class="site-footer" role="contentinfo">
	
		<aside id="sidebar-colophon" class="widget-area" role="complementary" aria-label="Colophon">
			<div class="container">
				<div class="sidebar-colophon-area row">
				
					<div class="widget-container">
						<section id="text-colophon" class="widget widget_custom_html">
							<div class="textwidget">
								<div class="site-info">
									Copyright © <?php echo date("Y"); ?> | LFR Lending
									</div><!-- .site-info -->	
							</div>
						</section>
					</div>
					
									
								
				</div><!-- .row -->
			</div><!-- .container -->
		</aside>
		
	</footer><!-- #footer -->
</div><!-- #page -->

<?php wp_footer(); ?>

<div class="overlay"></div>

</body>
</html>
