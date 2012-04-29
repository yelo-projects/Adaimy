<?php
class SimpleGalleryAsset extends DataObject{

	static $db = array(
		  'URLSegment'		=>	'varchar'
		, 'VideoURL'		=>	'varchar'
		, 'VideoProvider'	=>	'Enum(\'none,youtube,vimeo\',\'none\')'
		, 'Token'			=>	'varchar'
		, 'EmbeddedURL'		=>	'varchar'
		, 'Title'			=>	'varchar'
	);

	static $has_one = array(
		'Gallery'	=>	'SimpleGallery'
		, 'Image'		=>	'Image'
	);
	static $plural_name = 'Assets';
	static $singular_name = 'Asset';

	static $summary_fields = array(
		  'Image.CMSThumbnail.Tag'	=> 'Thumbnail'
		, 'Title'				=> 'Title'
		, 'URLSegment'			=>	'URL Segment'
		, 'VideoProvider'		=>	'Video Provider'
	);

	static $search_fields = array(
		  'Title'
		, 'VideoURL'
		, 'VideoProvider'
		, 'URLSegment'
	);

	public function getThumbnailSized($size=100){
		if($Image = $this->Image()){return $Image->CroppedImage($size,$size);}
		return '<span class="noImage">(no thumbnail)</span>';
	}

	public function getCMSThumbnail(){
		return $this->getThumbnailSized();
	}

	public function getDefaultWidth(){
		if($g = $this->Gallery()){return $g->getWidth();}
		return SimpleGallery::$DEFAULT_WIDTH;
	}

	public function getDefaultHeight(){
		if($g = $this->Gallery()){return $g->getHeight();}
		return SimpleGallery::$DEFAULT_HEIGHT;
	}

	public function getDefaultImage(){
		if($i = $this->Image()){
			return $i->SetBestSize($this->getDefaultWidth(),$this->getDefaultHeight());
		}
	}

	public function getDefaultWidthImage(){
		if($i = $this->Image()){
			return $i->SetWidth($this->getDefaultWidth());
		}
	}

	public function getDefaultHeightImage(){
		if($i = $this->Image()){
			return $i->SetHeight($this->getDefaultHeight());
		}
	}

	function getCMSFields_forPopup($params = null){
		$fields = new fieldSet();
		$fields->push(new TextField('Title','Title'));
		$fields->push(new TextField('VideoURL','Video URL'));
		$fields->push(new DropDownField('VideoProvider','Provider (will be detected automatically if left blank)',$this->dbObject('VideoProvider')->enumValues()));
		return $fields;
	}

	function getIsVideo(){
		if($this->VideoURL){
			return true;
		}
		return false;
	}

	public function extractToken(){
		if($this->VideoURL){
			switch($this->VideoProvider){
				case 'youtube':	return preg_replace('/.+(\?|&)v=([a-zA-Z0-9]+).*/', '$2', $this->VideoURL);	break;
				case 'vimeo':	return preg_replace('/.*?vimeo\.com\/(\d+)/', '$1', $this->VideoURL);		break;
				default:		break;
			}
		}
	}

	public function extractEmbeddedURL(){
		if($this->VideoURL){
			switch($this->VideoProvider){
				case 'youtube':	return 'http://www.youtube.com/embed';	break;
				case 'vimeo':	return 'http://player.vimeo.com/video';	break;
				default:		break;
			}
		}	
	}

	public function getIframeURL(){
		if($this->VideoURL){return $this->extractEmbeddedURL().'/'.$this->extractToken();}
	}

	public function getTemplates($embedded=false){
		$arr = array();
		if($this->VideoURL){
			if($embedded){$arr[] = 'SimpleGalleryVideo'.ucwords($this->VideoProvider).'Embedded';}
			$arr[] = 'SimpleGalleryVideo'.ucwords($this->VideoProvider);
			if($embedded){$arr[] = 'SimpleGalleryVideoEmbedded';}
			$arr[] = 'SimpleGalleryVideo';
		}else if($this->Image()){
			if($embedded){$arr[] = 'SimpleGalleryImageEmbedded';}
			$arr[] = 'SimpleGalleryImage';
		}
		if($embedded){$arr[] = 'SimpleGalleryItemEmbedded';}
		$arr[] = 'SimpleGalleryItem';
		$arr[] = 'Page';
		return $arr;
	}

	public function getEmbedded(){
		if($templates = $this->getTemplates(true)){
			return $this->renderWith($templates);
		}
		if($Image = $this->Image()){
			return $Image->forTemplate();
		}
		return '';	
	}

	public function forTemplate(){
		if($templates = $this->getTemplates()){
			return $this->renderWith($templates);
		}
		if($Image = $this->Image()){
			return $Image->forTemplate();
		}
		return '';
	}

	public function escapeStringForHTML($string){
		return preg_replace(
			 '/[^A-Za-z0-9 ]+/'
			,'-'
			, str_replace(
				  array(' ','controller','page')
				, array('_','')
				, strtolower($string)
			)
		);
	}

	public function getTitleNice(){
		return $this->escapeStringForHTML($this->Title);
	}

	function onBeforeWrite(){      
		// If there is no URLSegment set, generate one from Title
		if(!$this->Title){
			if($Image = $this->Image()){
				$this->Title = $Image->Title();
			}else{
				$this->Title = $this->Class.'_'.$this->ID;
			}
		}
		if(!$this->URLSegment || $this->URLSegment == ''){			
			$this->URLSegment = ($this->Title != '')? SiteTree::generateURLSegment($this->Title) : SiteTree::generateURLSegment($this->Class.'-'.$this->ID);
		}else if($this->isChanged('URLSegment')){
			if(!$segment) {
				$segment = $this->Title;
			}
			if(!$segment) {
				$segment = $this->Class.'-'.$this->ID;
			}
			$segment = preg_replace('/[^A-Za-z0-9]+/','-',$this->URLSegment);
			$segment = preg_replace('/-+/','-',$segment);
			$this->URLSegment = $segment;
		}
		$count = 2;
		while($this->LookForExistingURLSegment($this->URLSegment)){
			$this->URLSegment = preg_replace('/-[0-9]+$/', null, $this->URLSegment) . '-' . $count;
			$count++;
		}
		if($this->VideoURL){
			if($this->isChanged('VideoURL') || (!$this->VideoProvider || $this->VideoProvider == 'none')){
				$parse = parse_url($this->VideoURL);
				if($parse){
					$url = strtolower(str_replace(array('www.','.org','.com','.co.uk','.net','.tv'),'',$parse['host']));
					$providers = $this->dbObject('VideoProvider')->enumValues();
					if($providers[$url]){
						$this->VideoProvider = $url;
					}
				}
			}	
			if($this->ischanged('VideoURL') || !$this->Token){
				$this->Token = $this->extractToken();
			}
			if($this->ischanged('VideoURL') || !$this->EmbeddedURL){
				$this->EmbeddedURL = $this->extractEmbeddedURL();
			}
		}
		parent::onBeforeWrite();
	}

	//Test whether the URLSegment exists already on another Product
	function LookForExistingURLSegment($URLSegment){	
		return (DataObject::get_one($this->ClassName, "URLSegment = '" . $URLSegment ."' AND  '".$this->ClassName."'.'ID' != " . $this->ID));
	}

	function canCreate(){return true;}
	function canEdit(){return true;}
	function canDelete(){return true;}
	function canPublish(){return true;}

}
