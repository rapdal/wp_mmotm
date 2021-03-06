<?php
// Template Name: Portfolio One Column Text
get_header(); ?>
	<?php
	$content_css = 'width:100%';
	$sidebar_css = 'display:none';
	if(get_post_meta($post->ID, 'pyre_portfolio_full_width', true) == 'yes') {
		$content_css = 'width:100%';
		$sidebar_css = 'display:none';
	}
	elseif(get_post_meta($post->ID, 'pyre_portfolio_sidebar_position', true) == 'left') {
		$content_css = 'float:right;';
		$sidebar_css = 'float:left;';
		$content_class = 'portfolio-one-sidebar';
	} elseif(get_post_meta($post->ID, 'pyre_portfolio_sidebar_position', true) == 'right') {
		$content_css = 'float:left;';
		$sidebar_css = 'float:right;';
		$content_class = 'portfolio-one-sidebar';
	} elseif(get_post_meta($post->ID, 'pyre_sidebar_position', true) == 'default') {
		$content_class = 'portfolio-one-sidebar';
		if($data['default_sidebar_pos'] == 'Left') {
			$content_css = 'float:right;';
			$sidebar_css = 'float:left;';
		} elseif($data['default_sidebar_pos'] == 'Right') {
			$content_css = 'float:left;';
			$sidebar_css = 'float:right;';
		}
	}
	?>
	<div id="content" class="portfolio portfolio-one portfolio-one-text <?php echo $content_class; ?>" style="<?php echo $content_css; ?>">
		<?php while(have_posts()): the_post(); ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="post-content">
				<?php the_content(); ?>
				<?php wp_link_pages(); ?>
			</div>
		</div>
		<?php $current_page_id = $post->ID; ?>
		<?php endwhile; ?>
		<?php
		if(is_front_page()) {
			$paged = (get_query_var('page')) ? get_query_var('page') : 1;
		} else {
			$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		}
		$args = array(
			'post_type' => 'avada_portfolio',
			'paged' => $paged,
			'posts_per_page' => $data['portfolio_items'],
		);
		$pcats = get_post_meta(get_the_ID(), 'pyre_portfolio_category', true);
		if($pcats && $pcats[0] == 0) {
			unset($pcats[0]);
		}
		if($pcats){
			$args['tax_query'][] = array(
				'taxonomy' => 'portfolio_category',
				'field' => 'ID',
				'terms' => $pcats
			);
		}
		$gallery = new WP_Query($args);
		if(is_array($gallery->posts) && !empty($gallery->posts)) {
			foreach($gallery->posts as $gallery_post) {
				$post_taxs = wp_get_post_terms($gallery_post->ID, 'portfolio_category', array("fields" => "all"));
				if(is_array($post_taxs) && !empty($post_taxs)) {
					foreach($post_taxs as $post_tax) {
						$portfolio_taxs[$post_tax->slug] = $post_tax->name;
					}
				}
			}
		}
		$portfolio_category = get_terms('portfolio_category');
		if(is_array($portfolio_taxs) && !empty($portfolio_taxs) && get_post_meta($post->ID, 'pyre_portfolio_filters', true) != 'no'):
		?>
		<ul class="portfolio-tabs clearfix">
			<li class="active"><a data-filter="*" href="#"><?php echo __('All', 'Avada'); ?></a></li>
			<?php foreach($portfolio_taxs as $portfolio_tax_slug => $portfolio_tax_name): ?>
			<li><a data-filter=".<?php echo $portfolio_tax_slug; ?>" href="#"><?php echo $portfolio_tax_name; ?></a></li>
			<?php endforeach; ?>
		</ul>
		<?php endif; ?>
		<div class="portfolio-wrapper">
			<?php
			while($gallery->have_posts()): $gallery->the_post();
				if($pcats) {
					$permalink = tf_addUrlParameter(get_permalink(), 'portfolioID', $current_page_id);
				} else {
					$permalink = get_permalink();
				}
				if(has_post_thumbnail() || get_post_meta($post->ID, 'pyre_video', true)):
			?>
			<?php
			$item_classes = '';
			$item_cats = get_the_terms($post->ID, 'portfolio_category');
			if($item_cats):
			foreach($item_cats as $item_cat) {
				$item_classes .= $item_cat->slug . ' ';
			}
			endif;
			?>
			<div class="portfolio-item <?php echo $item_classes; ?>">
				<?php if(has_post_thumbnail()): ?>
				<div class="image">
					<?php if($data['image_rollover']): ?>
					<?php the_post_thumbnail('portfolio-full'); ?>
					<?php else: ?>
					<a href="<?php echo $permalink; ?>"><?php the_post_thumbnail('portfolio-full'); ?></a>
					<?php endif; ?>
					<?php
					if(get_post_meta($post->ID, 'pyre_image_rollover_icons', true) == 'link') {
						$link_icon_css = 'display:inline-block;';
						$zoom_icon_css = 'display:none;';
					} elseif(get_post_meta($post->ID, 'pyre_image_rollover_icons', true) == 'zoom') {
						$link_icon_css = 'display:none;';
						$zoom_icon_css = 'display:inline-block;';
					} elseif(get_post_meta($post->ID, 'pyre_image_rollover_icons', true) == 'no') {
						$link_icon_css = 'display:none;';
						$zoom_icon_css = 'display:none;';
					} else {
						$link_icon_css = 'display:inline-block;';
						$zoom_icon_css = 'display:inline-block;';
					}

					$icon_url_check = get_post_meta(get_the_ID(), 'pyre_link_icon_url', true); if(!empty($icon_url_check)) {
						$icon_permalink = get_post_meta($post->ID, 'pyre_link_icon_url', true);
					} else {
						$icon_permalink = $permalink;
					}
					?>
					<div class="image-extras">
						<div class="image-extras-content">
							<?php $full_image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full'); ?>
							<a style="<?php echo $link_icon_css; ?>" class="icon link-icon" href="<?php echo $icon_permalink; ?>">Permalink</a>
							<?php
							if(get_post_meta($post->ID, 'pyre_video_url', true)) {
								$full_image[0] = get_post_meta($post->ID, 'pyre_video_url', true);
							}
							?>
							<a style="<?php echo $zoom_icon_css; ?>" class="icon gallery-icon" href="<?php echo $full_image[0]; ?>" rel="prettyPhoto[gallery]" title="<?php echo get_post_field('post_content', get_post_thumbnail_id($post->ID)); ?>"><img style="display:none;" alt="<?php echo get_post_field('post_excerpt', get_post_thumbnail_id($post->ID)); ?>" />Gallery</a>
							<h3><?php the_title(); ?></h3>
							<h4><?php echo get_the_term_list($post->ID, 'portfolio_category', '', ', ', ''); ?></h4>
						</div>
					</div>
				</div>
				<?php elseif(!has_post_thumbnail() && get_post_meta($post->ID, 'pyre_video', true)): ?>
				<div class="image video full-video">
					<?php echo get_post_meta($post->ID, 'pyre_video', true); ?>
				</div>
				<?php endif; ?>
				<div class="portfolio-content clearfix">
					<h2><a href="<?php echo $permalink; ?>"><?php the_title(); ?></a></h2>
					<h4><?php echo get_the_term_list($post->ID, 'portfolio_category', '', ', ', ''); ?></h4>

					<div class="post-content">
					<?php
					if(get_post_meta($current_page_id, 'pyre_portfolio_excerpt', true)) {
						$excerpt_length = get_post_meta($current_page_id, 'pyre_portfolio_excerpt', true);
					} else {
						$excerpt_length = $data['excerpt_length_portfolio'];
					}
					?>
					<?php
					if($data['portfolio_content_length'] == 'Excerpt') {
						$stripped_content = strip_shortcodes( tf_content( $excerpt_length, $data['strip_html_excerpt'] ) );
						echo $stripped_content;
					} else {
						the_content();
					}
					?>
					</div>

					<div class="buttons">
						<a href="<?php echo $permalink; ?>" class="green button small"><?php echo __('Learn More', 'Avada'); ?></a>
						<?php if(get_post_meta($post->ID, 'pyre_project_url', true)): ?>
						<a href="<?php echo get_post_meta($post->ID, 'pyre_project_url', true); ?>" class="green button small"><?php echo __('View Project', 'Avada'); ?></a>
						<?php endif; ?>
					</div>
				</div>
				<div class="demo-sep sep-double" style="margin-top:50px;"></div>
			</div>
			<?php endif; endwhile; ?>
		</div>
		<?php themefusion_pagination($gallery->max_num_pages, $range = 2); ?>
	</div>
	<div id="sidebar" style="<?php echo $sidebar_css; ?>"><?php generated_dynamic_sidebar(); ?></div>
<?php get_footer(); ?>