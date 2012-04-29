<?php
class Page extends SiteTree {

	public static $db = array(
	);

	public static $has_one = array(
	);

	public static $has_many = array(
	);

	public function getMaintenanceMode(){
		$c = SiteConfig::current_site_config();
		$state = $c->SiteState;
		if($state === 'maintenance'){
			return $c->MaintenanceMode;
		}
		return false;
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

	public function IsAjaxed(){
		return (Director::is_ajax() || isset($_GET['x']));
	}

	function getSummary($maxWords=30){
		return $this->_summary($this->Content, $maxWords);
	}

	protected function _summary($value, $maxWords=50, $append='...', $appendSentence='..', $allowedTags = '<a>'){
		$data = strip_tags($value, $allowedTags);
		if( !$data ){return "";};

		$data = preg_replace('/[\r\n]+/',"\n",$data);

		$words = explode( ' ', $data );
		if(count($words)<=$maxWords){return nl2br($data);}
		$length = 0;
		$result = '';
		while($words && $length<=$maxWords){
			$result.=' '.array_shift($words);
			$length++;
		}
		trim($result);
		if( preg_match( '/<a[^>]*>/', $result ) && !preg_match( '/<\/a>/', $result ) ){$result .= '</a>';}
		$result.=(substr($result, strlen($result), 1)==='.') ? $appendSentence : $append;
		$result = nl2br($result);
		return $result;
	}

	public function getClassNice(){
		if($this->class=='Page'){return 'page-regular';}
		return $this->escapeStringForHTML($this->class);
	}

	public function getTitleNice(){
		return $this->escapeStringForHTML($this->Title);
	}

	public function getUniqueId(){
		if($this->parent() && $this->Parent()->class!=='SiteTree'){
			return $this->parent()->getUniqueId().'-'.$this->URLSegment;
		}
		return $this->URLSegment;
	}

	public function getSiblings(){
		$sort = $this->Sort;
		if($this->ParentID){
			$a = DataObject::get('Page', 'ParentID = '.$this->ParentID);
			foreach($a as $n=>$p){
				if($p->Sort==$sort){
					$p->SortMe = true;
				}else{$p->SortMe=false;}
			//	$p->TopSort = intval($sort);
			}
			return $a;
		}
		return false;
	}

	public function getEmbedded(){
		if($this->class==='Page'){return $this->renderWith(array('PageEmbedded','Page'));}
		return $this->renderWith(array($this->class.'Embedded','PageEmbedded',$this->class,'Page'));
	}
}
class Page_Controller extends ContentController {

	/**
	 * An array of actions that can be accessed via a request. Each array element should be an action name, and the
	 * permissions or conditions required to allow the user to access it.
	 *
	 * <code>
	 * array (
	 *     'action', // anyone can access this action
	 *     'action' => true, // same as above
	 *     'action' => 'ADMIN', // you must have ADMIN permissions to access this action
	 *     'action' => '->checkAction' // you can only access this action if $this->checkAction() returns true
	 * );
	 * </code>
	 *
	 * @var array
	 */
	public static $allowed_actions = array (
	);

	public function init() {
		$jqueryVer = '1.7.2';
		$root = 'mysite/javascript/';
		if(Director::isDev()){
			Requirements::javascript($root.'jquery-'.$jqueryVer.'.js');
			$reqs = array(
				  $root.'jquery.scrollTo-1.4.2-min.js'	
				, $root.'jquery.localscroll-1.2.7-min.js'
				, $root.'jquery.inview.js'
				, $root.'jquery.easing.1.3.js'
				, $root.'mailHider.js'
				, $root.'jquery.scrollorama.js'
				, $root.'jquery.mousewheel.js'
				, $root.'jquery.jscrollpane.min.js'
				, $root.'main.js'
			);
			foreach($reqs as $r){
				Requirements::javascript($r);
			}
		}else{
			Requirements::javascript('http://ajax.googleapis.com/ajax/libs/jquery/'+$jqueryVer+'/jquery.min.js');
			Requirements::customScript("if(typeof jQuery === 'undefined'){document.write(unescape(\"%3Cscript src='".$root."jquery-".$jqueryVer.".min.js' type='text/javascript' %3E%3C/script%3E\"))}");
			Requirements::javascript($root.'javascript.js');
		}
		parent::init();
	}
}
