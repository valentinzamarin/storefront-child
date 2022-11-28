<?php

class LastBuy{
    public function __construct() {
        add_action( 'woocommerce_order_status_completed', [ $this, 'order_completed'] );

        add_filter( 'woocommerce_product_meta_end', [ $this, 'last_buy_display' ] );
    }
    public function order_completed( $order_id ){
        $order = wc_get_order( $order_id );
        $items = $order->get_items();
        foreach( $items as $item ) {
            $product = $item->get_product();

            $product_id = $product->get_id();
            $date = date('Y-m-d h:i:s A');
            update_post_meta( $product_id , 'last_buy', $date );
        }
    }

    public function last_buy_display() {
        global $post;
        $last_buy = get_post_meta( $post->ID, 'last_buy', true );
        if( $last_buy ) {
            echo '<div>';
            echo 'Дата последней покупки ';
            echo esc_html( $last_buy );
            echo '</div>';
        }
        
    }
}