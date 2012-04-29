(function($){
	$.fn.mailHider = function(replaceText){
		return $("a[href^=mailto]",this).each(function(){
			var token = this.rel;
			this.onmouseover = this.oncontextmenu = function(){
				this.href = this.href.split("?")[0].replace(token, "");
				this.onmouseover = this.oncontextmenu = null;
			}
			if(replaceText!==0){this.innerHTML = this.innerHTML.replace(token, '<b style="display:none">'+token+'</b>');}
		});
	}
})(jQuery);
