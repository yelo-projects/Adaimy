<% if Backgrounds %><% control Backgrounds %>$Me<% end_control %><% end_if %>
<div class="content-top">
	<div class="content-top-wrapper">
	<div class="content-top-content">
	<% if Asset %><% control Asset %>
		<div class="asset" id="content-$Top.TitleNice"><div class="loading">$Embedded</div></div>
	<% end_control %><% else %>
	<h1>$Title</h1><br>
	<% if Quote %><span class="quote">$Quote</span><br><% end_if %>
	<% if ShortText %><p class="shortText">$ShortText</p><br><% end_if %>
	<% end_if %>
	</div></div>
</div>
<% if Foregrounds %><% control Foregrounds %>$Me<% end_control %><% end_if %>
