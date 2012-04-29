jQuery(document).ready(function($){

	$(document).mailHider();
	var   $wrappers = $('#Pages div.page')
		, exampleWrapper = $wrappers.first().find('.content-top-content').first()
		, wrapperHeight = $wrappers.first().outerHeight() 
		, assetsHeight = wrapperHeight - ($('div.simplegallery div.content-wrapper').first().outerHeight() || 0)
		, wrapperWidth = exampleWrapper.outerWidth()
		, $navigation = $('#Navigation')
		, $pagesLinks = $('a.page-link',$navigation)
		, w = $(window)
		, windowHeight = w.height()
		, urlAdditional = '?x=1&w='+wrapperWidth;
	;


	$navigation.on('click','a.page-link',function(e){
		$pagesLinks.removeClass('current');
		var rel = $(this).addClass('current').attr('rel'), obj = $(rel);
		if(!e.ctrlKey && obj.length){
			e.preventDefault();	
			w.stop().scrollTo(obj,2000,{
				easing: 'easeOutExpo'
			});
		}
	})
	$('#Pages div.simplegallery').on('click','a.asset-link',function(e){
		e.preventDefault();
		var l = $(this), href = l.attr('href'), rel = $(l.attr('rel')),loading = rel.children('.loading').first();
		l.addClass('current').siblings().removeClass('current');
		loading.animate({'opacity':0});
		$.ajax({
			url:href+urlAdditional
			, success:function(data){
				loading.html(data);
				loading.animate({'opacity':1});
				//rel.children().hide().fadeIn();
			}
			, error:function(){
				console.log('error');
			}
		})			
	});

	$('#Pages div.gallery').jScrollPane();

	var scrollorama = $.scrollorama({blocks:$wrappers})
		, halfWrapperHeight = wrapperHeight/2
		, quarterWrapperHeight = wrapperHeight/4
		, threeQuartersWrapperHeight = quarterWrapperHeight * 3;

	scrollorama.onBlockChange(function() {
		var   i = scrollorama.blockIndex
			, id = scrollorama.settings.blocks.eq(i).attr('id')
			, $link = $('[rel="#'+id+'"]',$navigation).first();
		$pagesLinks.not($link.addClass('current')).removeClass('current');
	});

	$wrappers.not('.footer').each(function(){
		var $this = $(this)
			, $content = $('div.content-wrapper',$this)
			, $bg = $('div.parralaxImage',$this)
			, $contentTop = $('div.content-top-content',$this)
			, contentHeight = $content.outerHeight();
			if(!$this.hasClass('simplegallery')){
				scrollorama.animate($contentTop,{
					  property:'opacity'
					, delay: threeQuartersWrapperHeight
					, duration:wrapperHeight
					, start:1
					, end:0
				})
			}else{
				scrollorama.animate('.asset',{
					  property:'margin-top'
					, delay: 0
					, duration:wrapperHeight
					, start:wrapperHeight
					, end:-20
				})
			}
			scrollorama.animate($content,{
				  property:'height'
				, delay: wrapperHeight//threeQuartersWrapperHeight
				, duration:wrapperHeight
				, start:contentHeight
				, end:0
				, baseline: 'bottom'
			});
			if($bg.length){	
				$bg.each(function(){
					var bg = $(this)
						, startX = bg.attr('data-startx') || 1
						, endX = bg.attr('data-endx') || 1
						, startY = bg.attr('data-starty') || 1
						, endY = bg.attr('data-startx') || 1
						, scaleStart = bg.attr('data-scalestart') || 1
						, scaleEnd = bg.attr('data-scaleend') || 1
						, opacityStart = bg.attr('data-opacitystart') || 1
						, opacityEnd = bg.attr('data-opacityend') || 1
						, easing = bg.attr('data-easing') || 'linear'
						, duration = bg.attr('data-duration') || 1
						, delay = bg.attr('data-delay') || 0;
					if(delay){delay = delay * wrapperHeight;}
					if(duration){duration = duration * wrapperHeight;}
					if(opacityStart !== opacityEnd){		
						scrollorama.animate(bg,{
							property:'opacity'
							, delay: delay
							, duration: duration
							, start:opacityStart
							, end: opacityEnd
						})
					}
					if(startX !== endX){	
						startX = startX * wrapperHeight;endX = endX & wrapperHeight;
						scrollorama.animate(bg,{
							property:'top'
							, delay: delay
							, duration: duration
							, start:startX
							, end: endX
						})
					}
					if(startY !== endY){	
						startY = startY * wrapperHeight;endY = endY & wrapperHeight;
						scrollorama.animate(bg,{
							property:'left'
							, delay: delay
							, duration: duration
							, start:startY
							, end: endY
						})
					}
				})
			}
	});

	var   $assets = $('#Pages div.content-top-wrapper div.asset');
	
	$assets.css({'overflow':'hidden',height:assetsHeight});
	$('#Pages div.galleryWrapper div.content-top-content').css({'height':assetsHeight});
	$('#Pages div.hasForm div.content-top-wrapper').css({'height':wrapperHeight - 350})
	$('#Pages div.galleryWrapper a.current').click();

	w.resize(function(e){
		windowHeight = w.height();
	});

	$('body').mousemove(function(e){
		var top = (e.pageY - w.scrollTop()) - 200
			, dist = (top/(windowHeight-200)) * assetsHeight;
		$assets.scrollTop(dist);
	});

})
