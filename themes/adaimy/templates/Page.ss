<% if IsAjaxed %>
$Layout
<% else %>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
<head>
<% include Header %> 
<% base_tag %>
<title>$Title &raquo; $SiteConfig.Title</title>
$MetaTags(false)
<link rel="shortcut icon" href="/favicon.ico" />
<% require themedCSS(adaimy) %> 
</head><body class="$ClassNice">
<% include Navigation %>
<div id="Pages-Wrapper">
<div id="Pages">
	$Layout
</div>
</div>
</body></html>
<% end_if %>
