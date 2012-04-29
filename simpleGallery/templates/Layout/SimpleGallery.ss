<h2>$Title</h2>
<div id="Text">
	$Content
	$Form
</div>
<div id="Asset">
<% if Asset %><% control Asset %>
	$Embedded
<% end_control %><% else %>
	not found
<% end_if %>
</div>
<% if Assets %>
<div class="gallery">
	<div class="galleryWrapper">
	<% control Assets %>
		<a href="$Top.URLSegment/show/$URLSegment" title="$Title" class="$FirstLast $EvenOdd $ParentTitleXML" rel="$ParentTitleXML" <% control ThumbnailSized(100) %>style="background-image:url($URL);"<% end_control %>>
			<span class="title">$Title</span>
		</a>
	<% end_control %>
	</div>
</div>
<% end_if %>
