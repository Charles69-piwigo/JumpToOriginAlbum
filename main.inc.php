<?php
/*
Plugin Name: Jump To Origin Album
Version: 1.0
Description: Sur la page d'edition d'une photo (back-office), fait pointer le lien "Ouvrir dans la galerie" vers l'album depuis lequel l'edition a ete ouverte (parametre cat_id fourni par AdminTools), au lieu de dependre du contexte stocke en session ou d'un album tire au hasard.
Plugin URI:
Author: Charles69
Author URI:
Has Settings: false
*/

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

add_event_handler('loc_end_picture_modify', 'jtoa_fix_jumpto');

/**
 * Reecrit U_JUMPTO en se basant sur le cat_id passe dans l'URL de la page
 * d'edition (admin.php?page=photo-<id>&cat_id=<catid>).
 *
 * Le core (admin/picture_modify.php) construit ce lien ainsi :
 *   1. contexte de navigation memorise en session ($_SESSION['edit_context'])
 *   2. sinon : un album AU HASARD parmi les albums associes (array_rand)
 *
 * Ce plugin ajoute une source fiable et deterministe (l'URL) qui prime sur
 * les deux, sans modifier le core.
 */
function jtoa_fix_jumpto()
{
  global $template;

  if (empty($_GET['cat_id']) or empty($_GET['image_id']))
  {
    return;
  }

  $cat_id   = (int)$_GET['cat_id'];
  $image_id = (int)$_GET['image_id'];

  if ($cat_id <= 0 or $image_id <= 0)
  {
    return;
  }

  // la photo doit reellement etre associee a cet album
  $linked = query2array('
SELECT category_id
  FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE image_id = '.$image_id.'
    AND category_id = '.$cat_id.'
  LIMIT 1
;');

  if (count($linked) == 0)
  {
    return;
  }

  $cats = query2array('
SELECT id, name, permalink
  FROM '.CATEGORIES_TABLE.'
  WHERE id = '.$cat_id.'
;', 'id');

  if (!isset($cats[$cat_id]))
  {
    return;
  }

  $template->assign(
    'U_JUMPTO',
    make_picture_url(array(
      'image_id' => $image_id,
      'category' => $cats[$cat_id],
      ))
    );
}
