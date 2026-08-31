<?php

namespace TSJIPPY\CAPTCHA;

use TSJIPPY;

if (! defined('ABSPATH')) {
    exit;
}

add_action('init', function() {
        $block = \WP_Block_Type_Registry::get_instance()->get_registered(
        'tsjippy-captcha/turnstile'
    );

    error_log(print_r($block, true));
});

function registerBlocks() {
    register_block_type(
        'tsjippy-captcha/recaptcha',
        array(
            'title'           => __( 'ReCaptcha', 'tsjippy' ),
            'attributes'      => array(
                'size'    => array(
                    'label'   => __( 'Size', 'tsjippy' ),
                    'type'    => 'string',
                    'enum'    => array( 'small', 'medium', 'large' ),
                    'default' => 'medium',
                ),
            ),
            'render_callback' => function ( $attributes ) {
                if(TSJIPPY\onBlockEditPage()){
                    return "ReCaptcha Block";
                }

                $captcha    = new ReCaptcha();

                return $captcha->addHtml(false);
            },
            'supports'        => array(
                'autoRegister' => true,
            ),
            "category" => "form-elements",
            "icon"     => "caution",
        )
    );

    register_block_type(
        'tsjippy-captcha/turnstile',
        array(
            'title'           => __( 'Turnstile', 'tsjippy' ),
            'attributes'      => array(
                'size'    => array(
                    'label'   => __( 'Size', 'tsjippy' ),
                    'type'    => 'string',
                    'enum'    => array( 'normal', 'compact', 'flexible' ),
                    'default' => 'normal',
                ),
                'appearance'    => array(
                    'label'   => __( 'Type', 'tsjippy' ),
                    'type'    => 'string',
                    'enum'    => array( 'always', 'execute', 'interaction-only' ),
                    'default' => 'always',
                ),
            ),
            'render_callback' => function ( $attributes ) {
                if(TSJIPPY\onBlockEditPage()){
                    return "Turnstile Block";
                }

                $extraData    = "data-appearance=\"{$attributes['appearance']}\" data-size=\"{$attributes['size']}\"";

                $captcha    = new Turnstile();

                return $captcha->addHtml(false, $extraData);
            },
            'supports'        => array(
                'autoRegister' => true,
            ),
            "category" => "form-elements",
            "icon"     => "caution",
        )
    );
}

add_action( 'init', __NAMESPACE__.'\registerBlocks' );