<?php
/*
Template Name: Project Template
*/
get_header();
define("GOOGLE_API_KEY", "AIzaSyA5dV2w3xK95JXeOd4GfoBUYPXGnBleR7Q");
?>

<div class="container-holder">
	<div class="container-frame">
		<?php if (have_posts()) : ?>

			<?php while (have_posts()) : the_post(); ?>
				<div class="heading">
					<h2><?php the_title(); ?></h2>
				</div>
				<!-- section-map -->
				<div class="section-map">
					<?php $marker_array = array(); ?>
					<div id="map_canvas" style="width:630px; height:450px; margin-left: -60px"></div>
					<?php the_content(); ?>
				</div>
			<?php endwhile;?>
		<?php else: ?>
			<div class="heading">
				<h2>Not Found</h2>
			</div>
			<!-- section-map -->
			<div class="section-map">
				<p>Sorry, but you are looking for something that isn't here.</p>
			</div>
		<?php endif; ?>
		<div class="columns">
			 <?php 
			 		$pro_taxonomy = "project-categories";
					$project = "projects";
			 		$counter = 1;
			 		$terms = get_terms($pro_taxonomy,'hide_empty=0&parent=0');
					$total = count($terms); 
					$percol = (int)($total / 2);
					if($total % 2 != 0 && $total > 1 )  $percol++;
			?>
			 <div class="column">
			<?php 	if($terms)foreach($terms as $term):  ?>
					<h4><?php echo $term->name; ?></h4>
					<?php $termchildren = get_term_children( $term->term_id, $pro_taxonomy); ?>
					<?php $sub_child_posts ='';?>
					<?php if($termchildren): 
							$sub_child_posts ='<ul>';
								foreach ($termchildren as $child): 
									$subterm = get_term_by('id', $child, $pro_taxonomy); 
									$sub_child_posts .='<li class="bourses"><a href="'.get_term_link($child, $pro_taxonomy).'">'.$subterm->name.'</a>';
									$args= array('post_type' => $project,'orderby' => 'title', 'order' => 'ASC','tax_query' => array( array('taxonomy' => $pro_taxonomy,'field' => 'id','terms' => $child))); 
									query_posts($args);
									if (have_posts()) : 
										$sub_child_posts .='<ul>';	
										while (have_posts()) : the_post();
											$sub_child_posts .='<li><a href="'.get_permalink().'">'.get_the_title().'</a></li>';
											$post_not[] = get_the_ID();
										endwhile;
										$sub_child_posts .='</ul>';
									endif;
									$sub_child_posts .='</li>';
								endforeach;
							$sub_child_posts .='</ul>'; ?>
					<?php endif;  ?>
						<?php $args= array('post_type' => $project,'post__not_in' => $post_not,'orderby' => 'title', 'order' => 'ASC', 'tax_query' => array( array('taxonomy' => $pro_taxonomy,
										'field' => 'id','terms' => $term->term_id))); 
							query_posts($args);
						?>
						<?php if (have_posts() || $sub_child_posts) : ?>
							<ul>	
								<?php while (have_posts()) : the_post(); ?>
								<li><a href="<?php the_permalink();?>"><?php the_title(); ?></a></li>
								<?php
									array_push($marker_array, array(
										'title' => get_the_title(), 'link' => get_permalink(),
										'lat' => get_post_meta(get_the_ID(), "_wp_geo_latitude", true), 
										'lng' => get_post_meta(get_the_ID(), "_wp_geo_longitude", true),
										'excerpt' => get_post(get_the_ID())->post_excerpt
									));
								?>
								<?php endwhile;?>
								<?php if($sub_child_posts) echo $sub_child_posts;?>
							</ul>
						
						<?php endif;  ?>
						<?php if($counter == $percol && $percol > 1 ) echo '</div><div class="column">';?>
				<?php $counter++; endforeach;  wp_reset_query();?>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	var marker_array = <?php echo json_encode($marker_array); ?>
</script>
<?php 
	/* FIXME *\
	function bel_load_script_maps () {
		wp_deregister_script('jquery');
		wp_enqueue_script('bel_google_maps', get_template_directory_uri() . '/js/bel_google_maps.js', null, null, true);
		wp_enqueue_script('google_maps', 'http://maps.googleapis.com/maps/api/js?key=' . GOOGLE_API_KEY . '&sensor=false', null, null, true);
		wp_enqueue_script('jquery');
	}
	add_action('wp_enqueue_scripts', 'bel_load_script_maps');
	*/
?>
<?php get_footer(); ?>
<script type="text/javascript"
	src="http://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_API_KEY; ?>&sensor=false">
</script>
<script type="text/javascript"
	src="wp-content/themes/bel/js/bel_google_maps.js">
</script>