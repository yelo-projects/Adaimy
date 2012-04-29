<div id="Content" class="content text contactPage">
    <% if ContactFormProcessed %>
		<div id="MainText">
		$ThankYouText
		</div>
    <% else %>
		<div id="MainText">
		$Content
		</div>
		<div class="clear"></div>
		$Form
    <% end_if %>
    <div class="clear"></div>
    $PageComments
</div>