<?php

class ParralaxImage extends DataObject{

	static $db = array(
		  'EndingPositionX'		=>	'Int'
		, 'EndingPositionY'		=>	'Int'
		, 'ScaleStart'			=>	'Int'
		, 'ScaleEnd'			=>	'Int'
		, 'StartingPositionX'	=>	'Int'
		, 'StartingPositionY'	=>	'Int'
		, 'OpacityStart'		=>	'Int'
		, 'OpacityEnd'			=>	'Int'
		, 'Delay'				=>	'Int'
		, 'Duration'			=>	'Int'
		, 'Repeat'				=>	'Enum(\'repeat,repeat-x,repeat-y,no-repeat\',\'repeat\')'
		, 'Easing'				=>	'Enum(\'linear,swing,easeInExpo,easeOutExpo,easeInOutExpo,easeInOutElastic\',\'linear\')'
		, 'CustomCSS'			=>	'Varchar'
	);
	static $has_one = array(
		 'Background'	=>	'Image'
	);

	static $defaults = array(
		'SpeedX'				=>	100
		, 'SpeedY'				=>	100
		, 'ScaleStart'			=>	100
		, 'ScaleEnd'			=>	100
		, 'StartingPositionX'	=>	0
		, 'StartingPositionY'	=>	0
		, 'EndingPositionX'		=>	100
		, 'EndingPositionY'		=>	100
		, 'OpacityStart'		=>	100
		, 'OpacityEnd'			=>	100
		, 'Repeat'				=>	'repeat'
		, 'CustomCSS'			=>	''
		, 'Easing'				=>	'linear'
		, 'Delay'				=>	0
		, 'Duration'			=>	100
	);

	static $singular_name = 'Parralax Image';
	static $plural_name = 'Parralax Images';

	function getCMSFields($params=null){
		$fields = $this->getCMSFields_forPopup();
		$fieldSet = new FieldSet();
		$fieldSet->push(new TabSet('Root','Root',new TabSet('Content')),'Root');
		$fieldSet->addFieldsToTab('Root.Content.Main', $fields);
		return $fieldSet;
	}

	function getCMSFields_forPopup($params = null){
		$fields = new fieldSet();
		$fields->push(new NumericField('StartingPositionX','Starting Position X (0 is normal, 100 is aligned on bottom)'));
		$fields->push(new NumericField('EndingPositionX','Ending Position X (0 is normal, 100 is aligned on bottom)'));
		$fields->push(new NumericField('OpacityStart','Opacity Starting Value (100 is fully opaque, 0 is invisible)'));
		$fields->push(new NumericField('OpacityEnd','Opacity Ending Value (100 is fully opaque, 0 is invisible)'));
		$fields->push(new NumericField('StartingPositionY','Starting Position Y (0 is normal, 100 is aligned on bottom)'));
		$fields->push(new NumericField('EndingPositionY','Ending Position Y (0 is normal, 100 is aligned on bottom)'));
		$fields->push(new NumericField('Delay','Delay before starting the animation (0 is no delay, 90 is at the end)'));
		$fields->push(new NumericField('Duration','Duration of the animation (0 is no duration, 100 is for the whole length of the page)'));
		$fields->push(new NumericField('ScaleStart','Starting Scale (100 is normal scale, 200 means twice as big)'));
		$fields->push(new NumericField('ScaleEnd','Ending Scale (100 is normal scale, 200 means twice as big)'));
		$fields->push(new TextField('CustomCSS','Custom CSS (for advanced usage)'));
		$fields->push(new DropDownField('Repeat','Repeating Pattern',array(
			'repeat'	=>	'Repeat'
			, 'repeat-x'	=>	'Repeat on X axis'
			, 'repeat-y'	=>	'Repeat on Y axis'
			, 'no-repeat'	=>	'Do not repeat'
		)));
		$fields->push(new DropDownField('Easing','Easing Equation',array(
			  'linear'			=>	'Linear (no easing)'
			, 'swing'			=>	'Swing (some easing)'
			, 'easeInExpo'		=>	'Ease In'
			, 'easeOutExpo'		=>	'Ease Out'
			, 'easeInOutExpo'	=>	'Ease In and Out'
			, 'easeInOutElastic'=>	'Elastic'
		)));
		$fields->push(new ImageUploadField('Image','Image'));
		return $fields;
	}

	function forTemplate(){
		return $this->getHTML();	
	}

	function getSetWidth($width){
		return $this->getHTML($width);
	}

	function getSetHeight($height){
		return $this->getHTML(null,$height);
	}

	function percentToFraction($percent){
		if($percent===null){return 1;}
		if($percent===0 || $percent==='0'){return 0;}
		return $percent/100;
	}

	function getHTML($width=0,$height=0){
		$t = $this; 
		$str = '<div class="parralaxImage"';
		$i = $t->Background();
		if($i){
			if($width){
				if($height){
					$i = $i->SetBestSize($width,$height);
				}else{
					$i = $i->SetWidthIfLarger($width);
				}
			}else if($height){
				$i = $i->SetHeightIfLarger($width);
			}
			$posX = $t->StartingPositionX ? ($t->StartingPositionX==100)? 'right': $t->StartingPositionX==50?'center':$t->StartingPositionX.'%' : 'left';
			$posY = $t->StartingPositionY ? ($t->StartingPositionY==100)? 'bottom': $t->StartingPositionY==50?'middle':$t->StartingPositionY.'%' : 'top';
			$str.= ' style="'
				.'background-position:'.$posY.' '.$posX.';'
				.'background-repeat:'.$t->Repeat.';'
				.'background-image:url('.$i->URL.');'
				.'opacity:'.$this->percentToFraction($t->OpacityStart).';';
			if($t->EndingPositionX!=100 || $t->EndingPositionY!=100){
				$str.='background-attachment:fixed;';
			}
			if($t->ScaleStart!=100){
				$str.='background-size:'.$t->ScaleStart.'%;';
			}
			$str.=$t->CustomCSS.'"'
				.' data-startX="'.$this->percentToFraction($t->StartingPositionX).'"'
				.' data-startY="'.$this->percentToFraction($t->StartingPositionY).'"'
				.' data-endX="'.$this->percentToFraction($t->EndingPositionX).'"'
				.' data-endY="'.$this->percentToFraction($t->EndingPositionY).'"'
				.' data-scaleStart="'.$this->percentToFraction($t->ScaleStart).'"'
				.' data-scaleEnd="'.$this->percentToFraction($t->ScaleEnd).'"'
				.' data-easing="'.$t->Easing.'"'
				.' data-opacityStart="'.$this->percentToFraction($t->OpacityStart).'"'
				.' data-opacityEnd="'.$this->percentToFraction($t->OpacityEnd).'"'
				.' data-duration="'.$this->percentToFraction($t->Duration).'"'
				.' data-delay="'.$this->percentToFraction($t->Delay).'"';
		}
		$str.='></div>';
		return $str;
	}

}
