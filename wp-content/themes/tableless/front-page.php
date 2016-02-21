<?php get_header();?>

<section class="tb-featured-posts">
  
  <div class="tb-featured-inner">
<?php
  $featuredPostsArgs = array(
    // 'post__in'  => get_option( 'sticky_posts' ), // Show only sticky-posts
    'order'=> 'DESC',
    'category_name' => 'destaques',
    'posts_per_page' => 5,
    'meta_query' => array(
      array(
       'key' => '_thumbnail_id',
       'compare' => 'EXISTS'
      ),
    )
  );
  $featurePost = new WP_Query($featuredPostsArgs);
  while($featurePost->have_posts()) : $featurePost->the_post();
  $urlImage = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );?>

    <?php if(has_post_thumbnail()) :?>
    <div class="tb-featured-post" id="<?php echo $post->ID; ?>" style="background-image: url(<?php echo $urlImage; ?>)">
    <?php else :?>
    <div class="tb-featured-post" id="<?php echo $post->ID; ?>">
    <?php endif;?>
      <a href="<?php the_permalink();?>" class="tb-lnk-featured">
        
        <h1 class="tb-title-1"><?php $tituloPersonalizado = get_post_meta($post->ID, 'titulo_personalizado', true); echo $tituloPersonalizado; ?></h1>

      </a>

    </div>
<?php endwhile; wp_reset_postdata(); ?>
  </div>

    <div class="tb-container tb-thumb-list">
      
      <div class="tb-box-title">
        <h3>Destaques</h3>
        <p>Você precisa ler!</p>
      </div>

        <?php
          $featuredPostsArgs = array(
            // 'post__in'  => get_option( 'sticky_posts' ), // Show only sticky-posts
            'order'=> 'DESC',
            'category_name' => 'destaques',
            'posts_per_page' => 5,
            'meta_query' => array(
              array(
               'key' => '_thumbnail_id',
               'compare' => 'EXISTS'
              ),
            )
          );
          $featurePost = new WP_Query($featuredPostsArgs);
          while($featurePost->have_posts()) : $featurePost->the_post();?>
            <a href="<?php the_permalink(); ?>" class="tb-thumb-box" data-target="<?php echo $post->ID; ?>">
              <?php echo get_the_post_thumbnail( $post_id, 'medium' ); ?>
            </a>
        <?php endwhile; wp_reset_postdata(); ?>

      <a href="#" class="tb-read-more">Ver todos</a>

    </div>

    <div class="tb-choose-category">
        
      <h1 class="tb-title-section">Encontre um assunto</h1>
      <p>Filtre pelo assunto do seu interesse</p>
      
      <ul class="tb-category-list">
        <li>
          <a href="#" class="tb-icon-ux">
            UX
          </a>
        </li>
        <li>
          <a href="#" class="tb-icon-html">HTML</a>
        </li>
        <li>
          <a href="#" class="tb-icon-css">CSS/SASS</a>
        </li>
        <li>
          <a href="#" class="tb-icon-responsive">Responsive</a>
        </li>
        <li>
          <a href="#" class="tb-icon-backend">Back-end</a>
        </li>
        <li>
          <a href="#" class="tb-icon-wordpress">Wordpress</a>
        </li>
        <li>
          <a href="#" class="tb-icon-design">Design</a>
        </li>
        <li>
          <a href="#" class="tb-icon-desenvolvimento">Desenvolvimento</a>
        </li>
        <li>
          <a href="#" class="tb-icon-seo">SEO</a>
        </li>
        <li>
          <a href="#" class="tb-icon-iniciantes">Iniciantes</a>
        </li>
      </ul>
    </div>


</section>

<?php get_footer();?>