<?php

namespace EACCustomWidgets\Proxy;

$parse_uri = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
require_once($parse_uri[0] . 'wp-load.php');

if(!defined('ABSPATH')) { exit; } // Exit if accessed directly

if(!isset($_REQUEST["id"]) || !isset($_REQUEST["nonce"]) || !wp_verify_nonce($_REQUEST["nonce"], "eac_file_osm_nonce_" . $_REQUEST["id"])) {
	header('Content-Type: text/plain');
	echo esc_html__('Jeton invalide. Actualiser la page courante...', 'eac-components');
	exit;
}

if(!ini_get('allow_url_fopen') || !isset($_REQUEST['url'])) {
	header('Content-Type: text/plain');
	echo esc_html__('"allow_url_fopen" est désactivé', 'eac-components');
	exit;
}

$file = filter_var(urldecode($_REQUEST['url']), FILTER_SANITIZE_STRING);

$ctx = stream_context_create(array('http'=> array('timeout' => 15)));

if(($file_source = @file_get_contents($file, false, $ctx)) === false) {
		header('Content-Type: text/plain');
		$error = error_get_last();
		if(preg_match("/(404)/", $error['message'])) {
			preg_match('/\(([^\)]+)\)/', $error['message'], $match);
			echo "\"" . urldecode($match[1]) . "\": " . esc_html__("La page demandée n'existe pas.", 'eac-components');
		} else if(preg_match("/(403)/", $error['message'])) {
			preg_match('/\(([^\)]+)\)/', $error['message'], $match);
			echo "\"" . urldecode($match[1]) . "\": " . esc_html__("Accès refusé.", 'eac-components');
		} else if(preg_match("/(401)/", $error['message'])) {
			preg_match('/\(([^\)]+)\)/', $error['message'], $match);
			echo "\"" . urldecode($match[1]) . "\": " . esc_html__("Non autorisé.", 'eac-components');
		} else if(preg_match("/(503)/", $error['message'])) {
			preg_match('/\(([^\)]+)\)/', $error['message'], $match);
			echo "\"" . urldecode($match[1]) . "\": " . esc_html__("Service indisponible. Réessayer plus tard.", 'eac-components');
		} else if(preg_match("/(405)/", $error['message'])) {
			preg_match('/\(([^\)]+)\)/', $error['message'], $match);
			echo "\"" . urldecode($match[1]) . "\": " . esc_html__("Méthode non autorisée.", 'eac-components');
		} else if(preg_match("/(429)/", $error['message'])) {
			preg_match('/\(([^\)]+)\)/', $error['message'], $match);
			echo "\"" . urldecode($match[1]) . "\": " . esc_html__("Trop de requêtes.", 'eac-components');
		} else if(preg_match("/(495)/", $error['message'])) {
			preg_match('/\(([^\)]+)\)/', $error['message'], $match);
			echo "\"" . urldecode($match[1]) . "\": " . esc_html__("Certificat SSL invalide.", 'eac-components');// SSL Certificate Error
		} else if(preg_match("/(496)/", $error['message'])) {
			preg_match('/\(([^\)]+)\)/', $error['message'], $match);
			echo "\"" . urldecode($match[1]) . "\": " . esc_html__("Certificat SSL requis.", 'eac-components');//SSL Certificate Required
		} else if(preg_match("/(500)/", $error['message'])) {
			preg_match('/\(([^\)]+)\)/', $error['message'], $match);
			echo "\"" . urldecode($match[1]) . "\": " . esc_html__("Erreur Interne du Serveur.", 'eac-components');
		} else {
			echo esc_html__("HTTP: La requête a échoué.", 'eac-components');
		}
		error_clear_last();
		
		exit;
}

header('Content-Type: application/json');
echo $file_source;