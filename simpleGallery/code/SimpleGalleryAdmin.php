<?php
class SimpleGalleryAdmin extends ModelAdmin {

  public static $managed_models = array('SimpleGalleryAsset');

  static $url_segment = 'simpleGallery';
  static $menu_title = 'Galleries Items';

  function init(){parent::init();Requirements::javascript('mysite/javascript/admin.js');}
}
