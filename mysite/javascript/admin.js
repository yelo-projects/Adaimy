(function($) {
	$(document).ready(function(){
		$('#LeftPane .leftbottom ul.tabstrip li a').each(function(){$(this).click(function(){$('#Form_Search'+this.href.split('#')[1]).submit();});}).first().click();
	})
})(jQuery);