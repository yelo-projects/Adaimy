<?php
class ImageDecorator extends DataObjectDecorator{

	public function Title(){
		if(!$this->owner->Title){
			preg_match('/(?:\d{0,3}-?)([\s\w-.]*?)\.(?:jpg|jpeg|png|gif)/i', $this->owner->Filename,$t);
			$this->owner->Title = str_replace('-',' ',$t[1]);
		}
		return $this->owner->Title;
	}

	public function SetFixedSize($width, $height) {
		return $this->owner->getFormattedImage('SetReSize', $width, $height);
	}

	public function generateSetFixedSize(GD $gd, $width, $height) {
		return $gd->resize($width, $height);
	}

	public function SetWidthIfNotSame($width) {
        if($width == $this->owner->getWidth()){
            return $this->owner;
        }
        return $this->owner->SetWidth($width);
    }

	public function SetWidthIfLarger($width) {
        if($width >= $this->owner->getWidth()){
            return $this->owner;
        }
        return $this->owner->SetWidth($width);
    }

    public function SetHeightIfNotSame($height) {
        if($height == $this->owner->getHeight()){
            return $this->owner;
        }
        return $this->owner->SetHeight($height);
    }

    public function SetHeightIfLarger($height) {
        if($height >= $this->owner->getHeight()){
            return $this->owner;
        }
        return $this->owner->SetHeight($height);
    }

    public function SetSizeIfNotSame($width, $height) {
        if($width == $this->owner->getWidth() && $height == $this->owner->getHeight()){
            return $this->owner;
        }
        return $this->owner->SetSize($width, $height);
    }

    public function SetSizeIfLarger($width, $height) {
        if($width >= $this->owner->getWidth() && $height >= $this->owner->getHeight()){
            return $this->owner;
        }
        return $this->owner->SetSize($width, $height);
    }

    public function SetRatioSizeIfNotSame($width, $height) {
        if($width == $this->owner->getWidth() && $height == $this->owner->getHeight()){
            return $this->owner;
        }
        return $this->owner->SetRatioSize($width, $height);
    }

    public function SetRatioSizeIfLarger($width, $height) {
        if($width >= $this->owner->getWidth() && $height >= $this->owner->getHeight()){
            return $this->owner;
        }
        return $this->owner->SetRatioSize($width, $height);
    }

    public function getFormattedImageIfNotSame($format, $arg1 = null, $arg2 = null) {
        if($this->owner->ID && $this->owner->Filename && Director::fileExists($this->owner->Filename)) {
            $size = getimagesize(Director::baseFolder() . '/' . $this->owner->getField('Filename'));
            $preserveOriginal = false;
            switch(strtolower($format)){
                case 'croppedimage':
                    $preserveOriginal = ($arg1 == $size[0] && $arg2 == $size[1]);
                    break;
            }
            if($preserveOriginal){
                return $this->owner;
            } else {
                return $this->owner->getFormattedImage($format, $arg1, $arg2);
            }
        }
    }

	public function SetRandomSize($min=100,$max=200,$inc=50){
		$w = $this->owner->getWidth();
		$h = $this->owner->getHeight();
		if($h>$w){
			return $this->owner->SetRandomHeight($min, $max, $inc);
		}
		return $this->owner->SetRandomWidth($min, $max, $inc);
	}

	public function SetRandomWidth($min=50,$max=200,$inc=50){
		$size = $this->_getRandomSize($min, $max, $inc);
		return $this->owner->SetWidthIfNotSame($size);
	}

	public function SetRandomHeight($min=50,$max=200,$inc=50){
		$height = $this->_getRandomSize($min, $max, $inc);
		return $this->owner->SetHeightIfNotSame($height);
	}

	protected function _getRandomSize($min=100,$max=300,$inc=100){
		$range = range($min, $max, $inc);
		$rnd = rand(0, count($range)-1);
		$size = $range[$rnd];
		if(!$size){$size = $min;}
		return $size;
	}

	public function SetBestSize($width,$height){
		$w = $this->owner->getWidth();
		$h = $this->owner->getHeight();
		if($h>$w){
			return $this->owner->SetHeightIfLarger($height);
		}
		return $this->owner->SetWidthIfLarger($width);
	}

	public function GreyscaleImage($RGB = '100 100 100'){
		return $this->owner->getFormattedImage('GreyscaleImage', $RGB);
	}
 
	public function generateGreyscaleImage(GD $gd, $RGB){
		$Vars = explode(' ', $RGB);    
		return $gd->greyscale( $Vars[0], $Vars[1], $Vars[2]);
	}
}
