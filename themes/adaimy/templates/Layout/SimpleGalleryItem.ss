<% if IframeURL %>
	<iframe width="$DefaultWidth" height="$DefaultHeight" src="$IframeUrl" frameborder="0" allowfullscreen></iframe>
<% else %>
	<% if DefaultWidthImage %><% control DefaultWidthImage %>
		<img width="$Width" height="$Height" src="$URL">
	<% end_control %><% end_if %>
<% end_if %>
