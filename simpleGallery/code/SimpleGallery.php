<?php
class SimpleGallery extends Page{

	static $allowed_children = 'none';
	static $db = array(
		  'DefaultWidth'	=>	'int'
		, 'DefaultHeight'	=>	'int'
	);
	static $has_many = array (
		  'Assets' => 'SimpleGalleryAsset'
	);
	static $has_one = array('Cover'=>'Image');
	static $DEFAULT_WIDTH = 560;
	static $DEFAULT_HEIGHT = 315;


	public function getCMSFields(){
		$f = parent::getCMSFields();
		$assets = new ImageDataObjectManager(
			$this,
			'Assets',
			'SimpleGalleryAsset',
			null,
			null,
			'getCMSFields_forPopup',
			'GalleryID='.$this->ID
		);
		$assets->setAddTitle('Asset');
		$cover = new ImageUploadField('Image', 'Cover');
		$f->addFieldToTab("Root.Content.Assets", $assets);
		$f->addFieldToTab("Root.Content.GalleryProperties", $cover);
		$f->addFieldToTab("Root.Content.GalleryProperties", new NumericField('DefaultWidth','default width'));
		$f->addFieldToTab("Root.Content.GalleryProperties", new NumericField('DefaultHeight','default height'));
		return $f;
	}

	public function getCover(){
		$cover = $this->Cover();
		if(!$cover){$cover = $this->getFirstAsset();}
		return $cover;
	}

	public function getWidth(){
		if(isset($_GET['w']) && $_GET['w']){return $_GET['w'];}
		if($this->DefaultWidth){return $this->DefaultWidth;}
		return self::$DEFAULT_WIDTH;
	}

	public function getHeight(){
		if(isset($_GET['h']) && $_GET['h']){return $_GET['h'];}
		if($this->DefaultHeight){return $this->DefaultHeight;}
		return self::$DEFAULT_HEIGHT;
	}

	public function getAsset(){
		return $this->getFirstAsset();
	}

	public function getFirstAsset(){
		$assets = $this->Assets();
		if($assets){
			return $assets->First();
		}
	}

	public function getRandomAsset(){
		$asset = DataObject::get_one('SimpleGalleryAsset', 'GalleryID="'.$this->ID.'"', true, 'RANDOM()');
		if($asset){
			return $asset;
		}
	}

	public function getTitleXML(){
		return strtolower(str_replace(' ','_',Convert::raw2xml($this->getTitle())));
	}

	public function getSortedAssets(){
		$assets = $this->Assets();
		if($assets){
			foreach($assets as $asset){
				$asset->Current = 'current';break;
			}
		}
		return $assets;
	}

}

class SimpleGallery_Controller extends Page_Controller{

	static $allowed_actions = array(
		'show'
	);

	protected $_current = null;
	protected $_currentAsset = null;

	public function getAsset(){
		if($this->_currentAsset){return $this->_currentAsset;}
		$Params = $this->getURLParams();
		$URLSegment = Convert::raw2sql($Params['ID']);  
		if($URLSegment){
			if($Asset = DataObject::get_one('SimpleGalleryAsset', "'SimpleGalleryAsset'.'URLSegment'='".$URLSegment."'")){
				$this->_current = $URLSegment;
				$this->_currentAsset = $Asset;
				return $Asset;
			}
			return false;
		}
		$Asset = $this->getFirstAsset();
		if($Asset){
			$this->_current = $Asset->URLSegment;
			$this->_currentAsset = $Asset;
		}
		return $Asset;
	}

	public function getSortedAssets(){
		$assets = $this->Assets();
		if($assets){
			$this->getAsset();
			foreach($assets as $asset){
				if($asset->URLSegment === $this->_current){$asset->Current = 'current';break;}
			}
			return $assets;
		}
		return null;
	}

	public function getCurrent(){
		return $this->_current;
	}

	function show(){
		if($Asset= $this->getAsset()){
            return $this;
        }else{
			return $this->httpError(404, 'not found');
		}
	}



}
