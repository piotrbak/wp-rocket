<?php
defined( 'ABSPATH' ) || die( 'Cheatin&#8217; uh?' );

if ( defined( 'WPSEO_VERSION' ) && class_exists( 'WPSEO_Sitemaps_Router' ) ) :
	$yoast_seo_xml = get_option( 'wpseo_xml' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals

	if ( version_compare( WPSEO_VERSION, '7.0' ) >= 0 ) {
		$yoast_seo                         = get_option( 'wpseo' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals
		$yoast_seo_xml['enablexmlsitemap'] = isset( $yoast_seo['enable_xml_sitemap'] ) && $yoast_seo['enable_xml_sitemap']; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals
	}

	/**
	 * Improvement with Yoast SEO: auto-detect the XML sitemaps for the preload option
	 *
	 * @since 2.8
	 * @author Remy Perona
	 */
	if ( true === $yoast_seo_xml['enablexmlsitemap'] ) {
		/**
		 * Add Yoast SEO sitemap option to WP Rocket default options
		 *
		 * @since 2.8
		 * @author Remy Perona
		 *
		 * @param array $options WP Rocket options array.
		 * @return array Updated WP Rocket options array
		 */
		function rocket_add_yoast_seo_sitemap_option( $options ) {
			$options['yoast_xml_sitemap'] = 0;

			return $options;
		}
		add_filter( 'rocket_first_install_options', 'rocket_add_yoast_seo_sitemap_option' );

		/**
		 * Sanitize Yoast SEO sitemap option value
		 *
		 * @since 2.8
		 * @author Remy Perona
		 *
		 * @param array $inputs WP Rocket inputs array.
		 * @return array Sanitized WP Rocket inputs array
		 */
		function rocket_yoast_seo_sitemap_option_sanitize( $inputs ) {
			$inputs['yoast_xml_sitemap'] = ! empty( $inputs['yoast_xml_sitemap'] ) ? 1 : 0;

			return $inputs;
		}
		add_filter( 'rocket_inputs_sanitize', 'rocket_yoast_seo_sitemap_option_sanitize' );

		/**
		 * Add Yoast SEO sitemap URL to the sitemaps to preload
		 *
		 * @since 2.8
		 * @author Remy Perona
		 *
		 * @param array $sitemaps Sitemaps to preload.
		 * @return array Updated Sitemaps to preload
		 */
		function rocket_add_yoast_seo_sitemap( $sitemaps ) {
			if ( get_rocket_option( 'yoast_xml_sitemap', false ) ) {
				$sitemaps[] = WPSEO_Sitemaps_Router::get_base_url( 'sitemap_index.xml' );
			}

			return $sitemaps;
		}
		add_filter( 'rocket_sitemap_preload_list', 'rocket_add_yoast_seo_sitemap' );

		/**
		 * Add Yoast SEO option to WP Rocket settings
		 *
		 * @since 2.8
		 * @author Remy Perona
		 *
		 * @param array $options WP Rocket settings array.
		 * @return array Updated WP Rocket settings array
		 */
		function rocket_sitemap_preload_yoast_seo_option( $options ) {
			$options['yoast_xml_sitemap'] = [
				'type'              => 'checkbox',
				'container_class'   => [
					'wpr-field--children',
				],
				'label'             => __( 'Yoast SEO XML sitemap', 'rocket' ),
				// translators: %s = Name of the plugin.
				'description'       => sprintf( __( 'We automatically detected the sitemap generated by the %s plugin. You can check the option to preload it.', 'rocket' ), 'Yoast SEO' ),
				'parent'            => 'sitemap_preload',
				'section'           => 'preload_section',
				'page'              => 'preload',
				'default'           => 0,
				'sanitize_callback' => 'sanitize_checkbox',
			];

			return $options;
		}
		add_filter( 'rocket_sitemap_preload_options', 'rocket_sitemap_preload_yoast_seo_option' );
	}
endif;
