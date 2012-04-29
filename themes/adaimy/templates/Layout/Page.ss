<div class="page $ClassNice" id="$TitleNice">
<% if MaintenanceMode %>
<div class="content-top">
	<div class="content-top-wrapper">
	<div class="content-top-content">
	<h1>Sorry!</h1>
		<p class="shortText">$MaintenanceMode</p>
	</div></div>
</div>
<% else %>
	<% include PageBackground %>
	<div class="content-wrapper"><div class="content">
	<% include PageContent %>
	</div></div>
<% end_if %>
</div>
