<?php

class AdaimyDecorator extends DataObjectDecorator{

	function extraStatics() {
        return array(
             'db'		=>	array(
				'ShortText'	=>	'Text'
			)
			, 'has_many'	=>	array(
				  'Backgrounds'			=>	'ParralaxImageBackground'
				, 'Foregrounds'			=>	'ParralaxImageForeground'
			)
       );
    }
	
	public function updateCMSFields(FieldSet &$fields) {
		//$allowed = array('svg','png','gif','jpg','jpeg');
		$Backgrounds = new ImageDataObjectManager(
			$this->owner,
			'Backgrounds',
			'ParralaxImageBackground',
			null,
			null,
			'getCMSFields_forPopup',
			'`ParralaxImageBackground`.`PageID`='.$this->owner->ID
		);
		$Foregrounds = new ImageDataObjectManager(
			$this->owner,
			'Foregrounds',
			'ParralaxImageForeground',
			null,
			null,
			'getCMSFields_forPopup',
			'`ParralaxImageForeground`.`PageID`='.$this->owner->ID
		);
		$fields->addFieldToTab('Root.Content.Display', new TextAreaField('ShortText','ShortText'));
		$fields->addFieldToTab("Root.Content.Backgrounds", $Backgrounds);
		$fields->addFieldToTab("Root.Content.Foregrounds", $Foregrounds);
    }

}
