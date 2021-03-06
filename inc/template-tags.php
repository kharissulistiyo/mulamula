<?php
/**
 * Prints blog name | blog description in wp_title on homepage
 */
function mulamula_home_wptitle( $title ){
	global $paged, $page;

	$blog_name = get_bloginfo( 'name' );
	$blog_desc = get_bloginfo( 'description' );

	if ( is_home() || is_front_page() ){
		$title = $blog_name;

		if( $paged >= 2 || $page >= 2 ){
			$title .= ' | ' . sprintf( __( 'Page %s', 'mulamula' ), max( $paged, $page ) );
		} else {
			$title .= ' | ' . get_bloginfo( 'description' );
		}
	}

	return $title;
}
add_filter( 'wp_title', 'mulamula_home_wptitle' );

/**
 * Prints HTML of categories of current post
 */
function mulamula_the_category(){
	echo '<ul class="entry-category"><li>';
	the_category( '</li><li>', 'single' );
	echo '</li></ul>';
}

/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function mulamula_posted_on() {
	if( is_page() ){
		printf(
			__( '
				<div class="entry-posted-on">
					<span class="entry-author author vcard"><a href="%1$s" class="url fn n" title="%2$s" rel="author">%3$s</a> </span> 
					<span class="entry-author-action">updated this</span> 
					<a href="%4$s" title="%5$s" class="entry-date">
						<span class="conventional-time">%6$s</span>
					</a>
				</div>
			', 'mulamula' ),
			esc_url( get_permalink() ),
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			get_the_author(),
			esc_url( get_permalink() ),
			sanitize_text_field( get_the_modified_date() ),
			esc_html( get_the_modified_date() )
		);
	} else {
		printf(
			__( '
				<div class="entry-posted-on">
					<span class="entry-author author vcard"><a href="%1$s" class="url fn n" title="%2$s" rel="author">%3$s</a> </span> 
					<span class="entry-author-action">published this</span> 
					<a href="%4$s" title="%5$s" class="entry-date">
						<span class="conventional-time">%6$s</span>
					</a>
				</div>
			', 'mulamula' ),
			esc_url( get_permalink() ),
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			get_the_author(),
			esc_url( get_permalink() ),
			sanitize_text_field( get_the_title() ),
			esc_html( get_the_date() )
		);
	}
}

/**
 * Prints content. On single page, prints the tags
 */
function mulamula_content(){
	if( is_attachment() ){
		if( isset( $_SERVER['HTTP_REFERER'] ) ){
			$parent_post_url = esc_url( $_SERVER['HTTP_REFERER'] );
		} else {
			$parent_post_url = get_permalink( $post->post_parent );
		}
		?>
			<a href="<?php $attachment_src = wp_get_attachment_image_src( get_the_ID(), 'fullsize' ); echo $attachment_src[0]; ?>" class="entry-featured-image" rel="bookmark">
				<?php echo mulamula_remove_width_attribute( wp_get_attachment_image( get_the_ID(), 'large' ) ); ?>
			</a>
		<?php
	}

	the_content( __( 'Continue Reading &rarr;', 'mulamula' ) );

	if( is_page() ){
		wp_link_pages( array( 'before' => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'mulamula' ) . '</span>', 'after' => '</div>', 'link_before' => '<span>', 'link_after' => '</span>' ) ); 
	}

	if( is_single() ){
			the_tags( '<div class="entry-tags">' . __( 'Tags: ', 'mulamula' ), ', ', '</div>' );
	}
}

/**
 * Returns human readable time
 */
function mulamula_get_the_human_time($post_time = 0){
	$timestamp = current_time( 'timestamp' ) - $post_time;

	$human_time = '';
	$minute 	= 60;
	$hour 		= 60 * $minute;
	$day 		= 24 * $hour;
	$week 		= 7 * $day;
	$month 		= 30 * $day;
	$year 		= 365 * $day;

	if( ( $timestamp / $year ) >= 1 ){
		// Calculate time
		$post_year 	= floor( $timestamp / $year );
		$post_month = floor( ( $timestamp % $year ) / $month );

		// Convert into text
		$post_year_text = mulamula_plural_sensitive_noun( 'year', $post_year );
		$post_month_text = mulamula_plural_sensitive_noun( 'month', $post_month );

		// Set Time
		$human_time = sprintf( __( '%s and %s ago', 'mulamula' ), $post_year_text, $post_month_text);
	} elseif( ( $timestamp / $month ) >= 1 ) {
		// Calculate time
		$post_month = floor( $timestamp / $month );

		// Convert into text
		$post_month_text = mulamula_plural_sensitive_noun('month', $post_month);

		// Set Time
		$human_time = sprintf( __( '%s ago', 'mulamula' ), $post_month_text );
	} elseif( ( $timestamp / $week ) >= 1 ) {	
		// Calculate time
		$post_week = floor( $timestamp / $week );	

		// Convert into text
		$post_week_text = mulamula_plural_sensitive_noun('week', $post_week);

		// Set Time
		$human_time = sprintf( __( '%s ago', 'mulamula' ), $post_week_text );
	} elseif( ( $timestamp / $day ) >= 1 ) {
		// Calculate time
		$post_day = floor( $timestamp / $day );	

		// Convert into text
		$post_day_text = mulamula_plural_sensitive_noun('day', $post_day);

		// Set Time
		$human_time = sprintf( __( '%s ago', 'mulamula' ), $post_day_text );
	} elseif( ( $timestamp / $hour ) >= 1 ) {
		// Calculate time
		$post_hour = floor( $timestamp / $hour );	

		// Convert into text
		$post_hour_text = mulamula_plural_sensitive_noun('hour', $post_hour);

		// Set Time
		$human_time = sprintf( __( '%s ago', 'mulamula' ), $post_hour_text );
	} else {
		// Calculate time
		$post_minute = floor( $timestamp / $minute );	

		// Convert into text
		$post_minute_text = mulamula_plural_sensitive_noun('minute', $post_minute);

		// Set Time
		$human_time = sprintf( __( '%s ago', 'mulamula' ), $post_minute_text );
	}

	return $human_time;
}

/**
 * Returns time sensitive noun
 */
function mulamula_plural_sensitive_noun( $noun = 'apple', $number = 0){
	$first_char 		= substr( $noun, 0, 1 );
	$last_char 			= substr( $noun, -1);
	$vocal 				= array('a', 'i', 'u', 'e', 'o');
	$article_exception  = array('hour');

	// Get correct article
	if(in_array($first_char, $vocal) || in_array($noun, $article_exception)){
		$article = 'an';
	} else {
		$article = 'a';
	}

	// Get noun
	if($number > 1){
		$word_number = mulamula_convert_number_to_words($number);

		if($last_char == 's'){
			$noun = $noun . 'es';
		} else {
			$noun = $noun . 's';
		}
	} elseif($number == 1){
		$word_number = $article;
	} else{
		$word_number = 'less then '.$article;
	}

	return $word_number . ' ' . $noun;
}

/*
 * Translate number into words by Karl Rixon
 * Source: http://www.karlrixon.co.uk/writing/convert-numbers-to-words-with-php/
 */
function mulamula_convert_number_to_words($number) {
    
    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ', ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary  = array(
        0                   => 'zero',
        1                   => 'one',
        2                   => 'two',
        3                   => 'three',
        4                   => 'four',
        5                   => 'five',
        6                   => 'six',
        7                   => 'seven',
        8                   => 'eight',
        9                   => 'nine',
        10                  => 'ten',
        11                  => 'eleven',
        12                  => 'twelve',
        13                  => 'thirteen',
        14                  => 'fourteen',
        15                  => 'fifteen',
        16                  => 'sixteen',
        17                  => 'seventeen',
        18                  => 'eighteen',
        19                  => 'nineteen',
        20                  => 'twenty',
        30                  => 'thirty',
        40                  => 'fourty',
        50                  => 'fifty',
        60                  => 'sixty',
        70                  => 'seventy',
        80                  => 'eighty',
        90                  => 'ninety',
        100                 => 'hundred',
        1000                => 'thousand',
        1000000             => 'million',
        1000000000          => 'billion',
        1000000000000       => 'trillion',
        1000000000000000    => 'quadrillion',
        1000000000000000000 => 'quintillion'
    );
    
    if (!is_numeric($number)) {
        return false;
    }
    
    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . convert_number_to_words(abs($number));
    }
    
    $string = $fraction = null;
    
    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }
    
    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . convert_number_to_words($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convert_number_to_words($remainder);
            }
            break;
    }
    
    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }
    
    return $string;
}

/**
 * Returns domain name of given URL
 */
function mulamula_get_domain_name( $url ){
	$parsed_url = parse_url( $url );

	if( !isset( $parsed_url['scheme'] ) ){
		$url = 'http://' . $url;
	}

	return parse_url( $url , PHP_URL_HOST );	
}

/**
 * Prints HTML for comments popup link section. For the sake of DRY
 */
function mulamula_comments_popup_link(){
	comments_popup_link( __( 'No Response Yet. <strong>Add Yours</strong>', '_s' ), __( '1 Response Shared. <strong>Add Yours</strong>', '_s' ), __( '% Responses Shared. <strong>Add Yours</strong>', '_s' ) );
}

/**
 * Display navigation to next/previous pages when applicable
 */
function mulamula_content_nav( $nav_id ) {
	global $wp_query, $post;

	// Don't print empty markup on single pages if there's nowhere to navigate.
	if ( is_single() ) {
		$previous = ( is_attachment() ) ? get_post( $post->post_parent ) : get_adjacent_post( false, '', true );
		$next = get_adjacent_post( false, '', false );

		if ( ! $next && ! $previous )
			return;
	}

	// Don't print empty markup in archives if there's only one page.
	if ( $wp_query->max_num_pages < 2 && ( is_home() || is_archive() || is_search() ) )
		return;

	$nav_class = ( is_single() ) ? 'navigation-post' : 'navigation-paging';

	?>
	<nav role="navigation" id="<?php echo esc_attr( $nav_id ); ?>" class="<?php echo $nav_class; ?>">
		<h1 class="screen-reader-text"><?php _e( 'Post navigation', 'mulamula' ); ?></h1>

	<?php if ( is_single() ) : // navigation links for single posts ?>
	
		<?php previous_post_link( '<div class="nav-previous">%link</div>', '<span class="meta-nav">' . _x( '', 'Previous post link', 'mulamula' ) . '</span> <span class="label">Older</span>' ); ?>
		<?php next_post_link( '<div class="nav-next">%link</div>', '<span class="label">Newer</span> <span class="meta-nav">' . _x( '', 'Next post link', 'mulamula' ) . '</span>' ); ?>

	<?php elseif ( $wp_query->max_num_pages > 1 && ( is_home() || is_archive() || is_search() ) ) : // navigation links for home, archive, and search pages ?>

		<?php if ( get_next_posts_link() ) : ?>
		<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav"></span> Older', 'mulamula' ) ); ?></div>
		<?php endif; ?>

		<?php if ( get_previous_posts_link() ) : ?>
		<div class="nav-next"><?php previous_posts_link( __( 'Newer <span class="meta-nav"></span>', 'mulamula' ) ); ?></div>
		<?php endif; ?>

	<?php endif; ?>

	</nav><!-- #<?php echo esc_html( $nav_id ); ?> -->
	<?php
}

/**
 * Display the comments section
 */
function mulamula_comment($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment; ?>
	<li <?php comment_class(); ?> id="comment-<?php comment_ID() ?>">	
		<div id="div-comment-<?php comment_ID() ?>" class="comment-wrap">
			<div class="comment-wrap-inside">
				<div class="comment-avatar">
					<span class="comment-tail"></span>
					<?php echo get_avatar($comment, 60, ''); ?>
				</div>
				<div class="comment-content">
					<div class="tail"></div>
					
					<div class="comment-meta">						
						<div class="comment-author"><?php comment_author_link(); ?></div>
					</div>

					<div class="entry-content">
						<?php if ($comment->comment_approved == '0') : ?>
						<p><em><?php _e('comment will appear after being approved by admin.', 'mulamula') ?></em> </p>
						<?php endif; ?>						
						<?php comment_text() ?>
					</div>
				</div>
				<div class="comment-action">
					<?php comment_reply_link(array_merge( $args, array('reply_text' => __('<span class="label">Reply</span>', 'mulamula'), 'add_below' => 'div-comment', 'depth' => $depth, 'max_depth' => $args['max_depth']))) ?>

					<div class="comment-date">
						<span class="conventional-time"><?php printf( get_comment_time('F d, Y')) ?></span>
					</div>						
				</div>				
			</div>
		</div><!-- .comment-wrap -->
	<?php
}

/**
 * Display the comment form
 */
function mulamula_comment_form(){
	global $user_identity, $id;

	if ( ! comments_open() ) : ?>
	<div id="respond" class="closed-comment">
		<header id="respond-header">
			<h2 id="respond-title"><?php _e( 'Comment is closed.', 'mulamula' ); ?></h2>
		</header>
		<p><?php _e('Contact us if you have something important to say about this topic.', "mulamula"); ?></p>
	</div>
	<?php elseif (comments_open()) : ?>

	<!-- Comment Form -->
	<div id="respond" class="comment-form">
		<header id="respond-header">
			<h2 id="respond-title"><?php _e( 'What Do You Think?', 'mulamula' ); ?></h2>
		</header>

		<div class="cancel-comment-reply"> 
			<?php cancel_comment_reply_link(); ?>
		</div>

		<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
			<p class="comment-loggedin">
				You have to be <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php echo urlencode(get_permalink()); ?>">logged in</a> to be able to comment.
			</p>
		<?php else : ?>
			<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="comment-form">
			

				<div class="submit-comment-avatar">
					<span class="comment-tail"></span>
					<?php if ( is_user_logged_in() ) : ?>
					<div class="comment-logged-in info left-info">
						<?php echo get_avatar( wp_get_current_user()->ID, 82, get_template_directory_uri() . '/images/default-avatar.jpg' ); ?>
					</div>						
					<div class="comment-logged-in info right-info">
						<p class="logged-in-as"><span class="subtitle">Logged in as</span> <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a></p>	
						<p><a href="<?php echo wp_logout_url(get_permalink()); ?>" title="Log out of this account">Log out</a></p>
					</div>
					<?php else : ?>
					<input type="text" name="author" class="comment-input author info left-info" placeholder="Name" id="author" size="22" tabindex="1" <?php if (isset($req)) echo "aria-required='true'"; ?> />
					<input type="text" name="email"  class="comment-input email info right-info" placeholder="Email" id="email" size="22" tabindex="2" <?php if (isset($req)) echo "aria-required='true'"; ?> />
					<input type="text" name="url"  class="comment-input url info right-info" placeholder="URL" id="url" size="22" tabindex="2" <?php if (isset($req)) echo "aria-required='true'"; ?> />
					<?php endif; ?>						
				</div>
				<div class="submit-comment-content clearfix">
					<textarea name="comment" id="comment" rows="10" tabindex="4" placeholder="Type your comment here..." class="the-content"></textarea>
					<div id="submit-wrap">
							<span class="icon"></span>
							<input name="submit" type="submit" id="submit" tabindex="5" value="Submit Comment" />
					</div>						
					<?php comment_id_fields(); ?>
					<?php do_action('comment_form', isset($post->ID)); ?>							
				</div>
			

			</form>
		<?php endif; // If registration required and not logged in ?>
	</div>
	<?php endif;
}