<?php
class ContactPage extends Page{

	static $db = array(
		'ContactMail'	=>	'Varchar',
		'ThankYouText'	=>	'HTMLText'
	);

	function emailHide($text, $token='[NOSPAM]'){
		return preg_replace(
				'/(?:<a href=[\'"]mailto:|)([a-zA-Z0-9._%+-]+)@([a-zA-Z0-9.-]+\.[a-zA-Z]{2,4})(?:[\'"]>.+?<\/a>|)/im',
				'<a rel="'.$token.'" href="mailto:$1'.$token.'@$2?'.
				'subject=EMAIL ADDRESS NEEDS EDITING&body=Please remove the text '.
				$token.' from the address before sending your email.">$1'.$token.'@$2</a>',
				$text);
	}

	function Summary($maxWords=10){
		return $this->emailHide($this->_summary($this->Content,$maxWords));
	}

	function  getCMSFields() {
		$fields = parent::getCMSFields();
		$field = new TextField('ContactMail', _t('ContactPage.CONTACTEMAIL','Contact\'s email'));
		$thankYou = new HtmlEditorField('ThankYouText', _t('ContactPage.THANKYOUTEXT','Text shown after user submits'),20);
		$fields->addFieldToTab("Root.Content.Main", $thankYou);
		$fields->addFieldToTab("Root.Content.Main", $field);
		return $fields;
	}

	function Form() {
		$r =  '<em>'._t('ContactPage.LABELREQUIRED','*').'</em>';
        // Create fields
        $fields = new FieldSet(
            new TextField('Name', _t('ContactPage.LABELNAME','Name').$r),
            new EmailField('Email', _t('ContactPage.LABELEMAIL','Email').$r),
            new TextareaField('MailBody',_t('ContactPage.LABELMESSAGE','Message').$r)
        );

        // Create action
        $actions = new FieldSet(
            new FormAction('ContactForm', _t('ContactPage.OKBUTTON','Ok'))
        );

        // Create Validators
        $validator = new RequiredFields('Name', 'Email', 'MailBody');

        return new Form($this,'ContactForm', $fields, $actions, $validator);
    }
}

class ContactPage_Controller extends Page_Controller {

	static $allowed_actions = array('ContactForm');


	function ContactForm(SS_HTTPRequest $data){
		$d = $data->postVars();
        $To = $this->ContactMail;
		if($To){
			$From = $d['Email'];
			$Subject = str_replace(array(
				'%%site%%',
				'%%title%%',
				'%%name%%',
				'%%email%%'
			),array(
				SiteConfig::current_site_config()->Title,
				$this->Title,
				$d['Name'],
				$d['Email']
			),_t('ContactPage.MAILTITLE','[%%site%%]][%%title%%] new mail from %%name%%'));
			$email = new Email($From, $To, $Subject);
			$email->setTemplate('EmailContact');
			$email->populateTemplate($d);
			$email->send();
			Session::set($this->Title.'ContactForm', TRUE);
			Director::redirectBack();
		}
    }

	public function ContactFormProcessed(){
        return Session::get($this->Title.'ContactForm');
    }

	public function Content(){
		return $this->emailHide($this->Content);
	}

	public function ThankYouText(){
		return $this->emailHide($this->ThankYouText);
	}
}
