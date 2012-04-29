<% if IsAjaxed %>
	<% if Asset %><% control Asset %>
		$Embedded
	<% end_control %><% end_if %>
<% else %>
<div class="page $ClassNice" id="$TitleNice">
	<% include PageBackground %>
	<div class="content-wrapper"><div class="content">
	<% include PageContent %>
	<% if Assets %>
	<div class="gallery">
		<div class="galleryWrapper">
		<% control SortedAssets %>
		<a href="$Top.URLSegment/show/$URLSegment" rel="#content-$Top.TitleNice" title="$Title" class="$FirstLast $EvenOdd $ParentTitleXML $Current asset-link <% if IframeURL %>videoThumbnail<% else %>imageThumbnail<% end_if %>" rel="$ParentTitleXML" <% control ThumbnailSized(100) %>style="background-image:url($URL);"<% end_control %>>
			<span class="overlay"></span>		
			<span class="title">$Title</span>
		</a>
		<% end_control %>
		</div>
	</div>
	<% end_if %>
	</div></div>
</div>
<% end_if %>
