<?php
/** 
 * Configuración básica de WordPress.
 *
 * Este archivo contiene las siguientes configuraciones: ajustes de MySQL, prefijo de tablas,
 * claves secretas, idioma de WordPress y ABSPATH. Para obtener más información,
 * visita la página del Codex{@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} . Los ajustes de MySQL te los proporcionará tu proveedor de alojamiento web.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** Ajustes de MySQL. Solicita estos datos a tu proveedor de alojamiento web. ** //
/** El nombre de tu base de datos de WordPress */
define('DB_NAME', 'heroica');

/** Tu nombre de usuario de MySQL */
define('DB_USER', 'heroica');

/** Tu contraseña de MySQL */
define('DB_PASSWORD', 'heroica2018');

/** Host de MySQL (es muy probable que no necesites cambiarlo) */
define('DB_HOST', 'localhost');

/** Codificación de caracteres para la base de datos. */
define('DB_CHARSET', 'utf8mb4');

/** Cotejamiento de la base de datos. No lo modifiques si tienes dudas. */
define('DB_COLLATE', '');

/**#@+
 * Claves únicas de autentificación.
 *
 * Define cada clave secreta con una frase aleatoria distinta.
 * Puedes generarlas usando el {@link https://api.wordpress.org/secret-key/1.1/salt/ servicio de claves secretas de WordPress}
 * Puedes cambiar las claves en cualquier momento para invalidar todas las cookies existentes. Esto forzará a todos los usuarios a volver a hacer login.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', '!dUVab/K88R*PZz#`5WGRLXDAiQBZIg<E-H7SC[}Nuk1}lX7]zyyi5r86.k0X(_j');
define('SECURE_AUTH_KEY', '3zQx;Shf@ot8?29o>s3/gt$u TS{c X8Om|Zh.y=}wYf<SGL*r]4Jw)RtL53MXoI');
define('LOGGED_IN_KEY', '$Kc*DSPrg5ix=?EItL1nb@ov8]-rertctk?Y$!tN1K(zj&q:8?:rUK.G)LHV#*Rv');
define('NONCE_KEY', 'zsL!6?(= iiK>(;+mNBY!S-q+ EY1Fvrg(hL0RWfov1sjCQUd:<,culB?<vJkK0o');
define('AUTH_SALT', 'zHR`b2;$cY86ng,$PHh-&Ms@]-2Jb-.n0q^uw>D{0 copqGB*@F!}pTkq:]v]bD<');
define('SECURE_AUTH_SALT', ')2Fv] |zk]kbU`~P5dfHlh$X7#g{vCi,u@somj#qyX#+K6#iz>HaTj*qW~GUOp h');
define('LOGGED_IN_SALT', '?0v!Zs:?aryeVW@`X6y5sqa!2XQP|nHxig`anrbG0wJ<JS]n.k-.yp] _st|n>F<');
define('NONCE_SALT', 'UqFw]=qSw20.n1W7YslAL5LgF0l#$G. 3mV`y-<1`!,WA0kC/3*fXM=m?dzNKj05');

/**#@-*/

/**
 * Prefijo de la base de datos de WordPress.
 *
 * Cambia el prefijo si deseas instalar multiples blogs en una sola base de datos.
 * Emplea solo números, letras y guión bajo.
 */
$table_prefix  = 'wp_';


/**
 * Para desarrolladores: modo debug de WordPress.
 *
 * Cambia esto a true para activar la muestra de avisos durante el desarrollo.
 * Se recomienda encarecidamente a los desarrolladores de temas y plugins que usen WP_DEBUG
 * en sus entornos de desarrollo.
 */
define('WP_DEBUG', false);

/* ¡Eso es todo, deja de editar! Feliz blogging */

/** WordPress absolute path to the Wordpress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

