<?php

class FileDecorator extends Extension{

	protected $_SVGContent = null;

	function extraStatics(){
		return array();
	}

	function getSVGContentWidth($width){
		return $this->getSVGContent($width,null);
	}

	function getSVGContentHeight($height){
		return $this->getSVGContent(null,$height);
	}

	function getSVGContentNoSize(){
		return $this->getSVGContent(0,0);
	}

	function getSVGContent($width=null,$height=null){
		if($this->_SVGContent!==null){return $this->_SVGContent;}
		if($this->getIsSVG()){
			$content = file_get_contents($this->owner->getFullPath());
			$content = preg_replace('/<\?xml .*?\?>|<svg.*?>|<defs.*?>|<metadata.*?metadata>|<!--.*?-->/s','',$content);
			$header = '<svg id="'.$this->owner->getTitle().'"';
			if($width!==null || $height!==null){
				if($width){
					$header.= ' width="'.$width.'"';
				}else{$header.=' width="100%"';}
				if($height){
					$header.= ' height="'.$height.'"';
				}else{$header.=' height="100%"';}
			}
			$header.='>';
			$this->_SVGContent = '<script type="image/svg+xml">'.$header.$content.'</script>';
		}else{
			$this->_SVGContent = '';
		}
		return $this->_SVGContent;
	}


	function getIsSVG(){
		$url = $this->owner->URL;
		if($url){
			$ext = pathinfo($url, PATHINFO_EXTENSION);
			if($ext === 'svg' || $ext === 'SVG'){
				return true;
			}
		}
		return false;
	}
}
