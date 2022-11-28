<?php

class Views{
    public function __construct() {
        add_action('add_meta_boxes', [ $this, 'product_views_meta_field' ] );
        add_action('save_post', [ $this, 'product_save_views_count' ] );
        add_action( 'wp_head', [ $this, 'product_views_append' ]);
        
        add_filter( 'woocommerce_product_meta_end', [ $this, 'product_views_display' ] );
        add_filter( 'woocommerce_after_shop_loop_item', [ $this, 'product_views_display' ] );
    }
    public function product_views_meta_field() {
        add_meta_box( 
            'views_meta', 
            'Количество просмотров', 
            [ $this, 'product_views_meta_display' ],
            'product', 
            'normal', 
            'high'  
        );
    }

    public function product_views_meta_display( $post ) { 
        $views_count = get_post_meta( $post->ID, 'views_count', true );

        if( empty($views_count) ) {
            $views_count = 0;
        }
		wp_nonce_field( 'views_save_meta', 'views_nonce' );
        ?>
        <div>
			<label>
				<input type="number"
						name="views_count"
						value="<?php echo esc_attr( $views_count ); ?>"
						class="widefat"/>
			</label>
		</div>
    <?php }

    public function product_save_views_count( $post ) {
        global $post;

        if ( empty( $_POST['views_nonce'] ) || ! wp_verify_nonce( $_POST['views_nonce'], 'views_save_meta' ) ) {
            return true;
        }

        $keys = array(
            'views_count',
        );

        foreach ( $keys as $key ) {
            if ( ! empty( $_POST[ $key ] ) ) {
                update_post_meta( $post->ID, $key, $_POST[ $key ] );
            } else {
                delete_post_meta( $post->ID, $key );
            }
        }

        return true;
    }

    public function product_views_append() {
        global $post;

        if( ! $post || ! is_product() )
		return;

        $views_count = get_post_meta( $post->ID, 'views_count', true );
        $views_count++;
        update_post_meta( $post->ID, 'views_count', $views_count );
    }

    public function product_views_display() {
        global $post;
        $views_count = get_post_meta( $post->ID, 'views_count', true );
        echo '<div>';
        echo 'Кол-во просмотров: ';
        if( !empty( $views_count ) ) {
            echo esc_html( $views_count );
        } else {
            echo '0';
        } 
        echo '</div>';
    }
}