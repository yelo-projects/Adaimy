var Slider = (function($){

function Slider($obj,options){
	this.slider = $obj;
	this.wrapper = $obj.parent();
	this.pages = $obj.children('div.page');
	this.currentPage = 1;
	this.totalPages = this.pages.length;
	this.controls = this.wrapper.children('div.controls').first();
	this.nextBtn = this.controls.find('a.next').first();
	this.previousBtn = this.controls.find('a.previous').first().hide();
	this.opts = $.extend({},Slider.defaults,options);
	this.percentPages = 0;
	this.hasPages = this.totalPages>1;
	var that=this;
	if(this.hasPages){
		this.percentPages = parseInt(((1 / (this.totalPages -1))*10).toFixed());
		//this.slider.css({'position':'absolute'});
		this.wrapper.on('click','a',function(e){
			var $l = $(this);
			if($l.hasClass('pagination') && !$l.hasClass('current')){
				e.preventDefault();
				if($l.hasClass('next')){
					that.next();
				}else if($l.hasClass('previous')){
					that.previous();
				}else{
					that.goto(parseInt($l.attr('data-pos')));
				}
				return false;
			}
		}).on('click','.page',function(e){
			e.preventDefault();
			that.goto(parseInt($(this).attr('data-pos')));
		}).on('sliderNext',function(e){
			that.next();
		}).on('sliderPrevious',function(e){
			that.previous();
		}).on('sliderPercent',function(e,percent){
			if(percent){that.gotoPercent(percent);}
		});
	}else{
		this.nextBtn.hide();
		return false;
	}
	$obj.data('slider',this);
	return this;
}

Slider.defaults = {

	callback:function(){}

}

Slider.prototype = {
	shift:920
	, next:function(){
		if(!this.hasPages){return this;}
		var $n = this.currentPage+1;
		if($n>this.totalPages){return;}
		return this.goto($n);
	}

	, previous:function(){
		if(!this.hasPages){return this;}
		var $n = this.currentPage-1;
		if($n<0){return;}
		return this.goto($n);
	}
	, gotoPercent:function($n){
		var $page = this.getPageFromPercent($n);
		return this.goto($page);
	}
	, getPageFromPercent:function($n){
		var $page = 0;
		if(!this.hasPages){return this;}
		if($n===0){$page = 1;}
		else if($n===100){$page = this.totalPages;}
		else{$page = Math.round((($n * 100) * (this.totalPages-1))/100) + 1;}
		//console.log('passed:'+$n,'page:'+$page,'pages:'+this.totalPages);
		return $page;
	}
	, goto:function($n){
		if($n == this.currentPage){return this;}
		if(!this.hasPages){return this;}
		var $pageShift = $n - this.currentPage
			, that = this;
		if(!$pageShift){return;}
		this.currentPage = $n;
		if(this.currentPage<=1){
			this.currentPage==1;
			this.previousBtn.fadeOut();
			this.nextBtn.fadeIn();
		}else if(this.currentPage>=this.totalPages){
			this.currentPage==this.totalPages;
			this.nextBtn.fadeOut();
			this.previousBtn.fadeIn();
		}else{
			this.nextBtn.fadeIn();
			this.previousBtn.fadeIn();
		}
		this.slider.animate(
			 {left:'-='+(this.shift*$pageShift)}
			,700,'easeOutExpo'
			, (this.opts.callback ?
				function(){that.opts.callback(that.currentPage,$pageShift);}
				: null
			)
		);
		return this;
	}

}
 
	return Slider;

})(jQuery);
