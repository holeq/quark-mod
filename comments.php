<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains both current comments
 * and the comment form. The actual display of comments is
 * handled by a callback to quark_comment() which is
 * located in the functions.php file.
 *
 * @package Quark
 * @since Quark 1.0
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() )
	return;
?>

<section id="comments" class="comments-area">

	<?php if ( have_comments() ) : ?>
		<div class="comments-title">
		    <div>Комментариев: <?php echo get_comments_number(); ?></div>
		</div>

		<ol class="commentlist">
			<?php wp_list_comments( array( 'callback' => 'quark_comment', 'style' => 'ol' ) ); ?>
		</ol> <!-- /.commentlist -->

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
			<nav id="comment-nav-below" class="navigation" role="navigation">
				<h1 class="assistive-text section-heading"><?php esc_html_e( 'Comment navigation', 'quark' ); ?></h1>
				<div class="nav-previous"><?php previous_comments_link( esc_html__( '&larr; Older Comments', 'quark' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments &rarr;', 'quark' ) ); ?></div>
			</nav>
		<?php endif; // check for comment navigation ?>

	<?php // If comments are closed and there are comments, let's leave a little note.
	elseif ( ! comments_open() && '0' != get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
		<p class="nocomments"><?php esc_html_e( 'Comments are closed.', 'quark' ); ?></p>
	<?php endif; ?>

<?php
    //Форма комментирования.
    //http://codex.wordpress.org/Function_Reference/comment_form#.24args
    
    $commenter = wp_get_current_commenter();
    $req = get_option('require_name_email');
    $aria_req = ($req ? " aria-required='true'" : '');

    $args = array(
        'id_form'           => 'commentform',
        'id_submit'         => 'submit',
        'class_submit'      => 'submit',
        'name_submit'       => 'submit',
        
        'title_reply'       => 'Оставить комментарий', //Заголовок формы комментирования.
        'title_reply_to'    => 'Ответ для %s',
        'cancel_reply_link' => 'Отменить комментарий',
        'label_submit'      => 'Комментировать',
        'format'            => 'xhtml',
    );

    //Список всех полей для заполнения. Удалять ничего не нужно - только добавлять или изменять.
    $fields =  array(
        'author' =>
            '<p class="comment-form-author"><label for="author">Ваше имя</label> ' .
            ( $req ? '<span class="required">*</span>' : '' ) .
            '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) .
            '" size="30"' . $aria_req . ' /></p>',
        
        'email' =>
            '<p class="comment-form-email"><label for="email">Ваш email</label> ' .
            ( $req ? '<span class="required">*</span>' : '' ) .
            '<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) .
            '" size="30"' . $aria_req . ' /></p>',
        
        'url' =>
            '<p class="comment-form-url"><label for="url">Веб-сайт</label>' .
            '<input id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) .
            '" size="30" /></p>',
    );
    
    //Отключаем ненужные поля.
    //unset($fields['author']);
    unset($fields['email']);
    unset($fields['url']);

    //Добавляем итоговые поля для отображения.
    $args['fields'] = $fields;

    //Вывод перед формой комментирования.
    $comment_notes_before = '';
    if (isset($fields['email'])) {
        $comment_notes_before .= '<p class="comment-notes">Ваш e-mail не будет опубликован.'.($req ? $required_text : '').'</p>';
    }
    $args['comment_notes_before'] = $comment_notes_before;
    
    //Вывод после формы комментирования.
    $comment_notes_after = '';
    $comment_notes_after .= '<noindex><p class="form-allowed-tags">'.sprintf('Можно использовать следующие <abbr title="HyperText Markup Language">HTML</abbr>-теги и атрибуты: %s', ' <code>'.allowed_tags().'</code>').'</p></noindex>';
    $args['comment_notes_after'] = $comment_notes_after;
    
    //Поле для ввода текста комментария.
    $comment_field = '';
    $comment_field = '<p class="comment-form-comment"><label for="comment">Ваш комментарий<span class="required">*</span></label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>';
    $args['comment_field'] = $comment_field;

    //Сообщение - вы должны быть зареганы прежде чем оставлять комментарии.
    $must_log_in = '';
    $must_log_in = '<p class="must-log-in">'.sprintf('Для написания комментариев необходима <a href="%s">регистрация</a>.', wp_login_url(apply_filters('the_permalink', get_permalink()))).'</p>';
    $args['must_log_in'] = $must_log_in;

    //Сообщение - вы вошли как Bobik17.
    $logged_in_as = '';
    $logged_in_as = '<p class="logged-in-as">'.sprintf('Вы вошли как <a href="%1$s">%2$s</a>. <a href="%3$s" title="Выйти из аккаунта">Выйти?</a>', admin_url('profile.php'), $user_identity, wp_logout_url(apply_filters('the_permalink', get_permalink()))).'</p>';
    $args['logged_in_as'] = $logged_in_as;

    comment_form($args);
?>

</section> <!-- /#comments.comments-area -->
